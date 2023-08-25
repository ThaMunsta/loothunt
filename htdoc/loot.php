<?php
$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => '../cache',
));
$filter = new Twig_Filter('time2str', function ($string) {
    return time2str($string);
});
$twig->addFilter($filter);
$filter = new Twig_Filter('db2str', function ($string) {
    return db2str($string);
});
$twig->addFilter($filter);
$filter = new Twig_Filter('d2h', function ($string) {
    return dechex($string);
});
$twig->addFilter($filter);
$joyMsg = $notiMsg = $errMsg = '';

$bits = explode ("/", $_SERVER['REQUEST_URI']);
$depth = sizeof($bits)-$subdirs;
$lookup = $bits[(sizeof($bits)-2)];
if ($depth > 4) $hunt = $bits[(sizeof($bits)-3)];
if ($depth > 5) {
	try {
	echo $twig->render('404.html', array(
		'auth' => $auth,
		'home' => $home,
		'tracking' => $tracking,
		'notification' => $notification
		));
	} catch (Exception $e) {
	    echo $e->getMessage();
	    exit(1);
	}
	die;
}
$sqldate = date("Y-m-d", time());
$conn = new PDO("mysql:host=$servername;dbname=$database",$username,$password);

// LIST CURRENT HUNTS
if ($lookup == 'loot'){
	$sql = "SELECT * FROM `packages` WHERE `expiry` > :sqldate GROUP BY hunt, expiry";
	$result = $conn->prepare($sql);
	$result->bindParam(':sqldate', $sqldate, PDO::PARAM_STR);
	$result->execute();
	$out = [];
	if ($result) if ($result->rowCount() > 0) {
		while($row = $result->fetch(PDO::FETCH_ASSOC)) {
			if ($row['hunt'] != "LOTT" && $row['hunt'] != "BURN") $out[] = $row;
		}
	}
	else {
		$out = 0;
	}
	try {
	echo $twig->render('loot.html', array(
		'rows' => $out,
		'auth' => $auth,
		'home' => $home,
		'tracking' => $tracking,
		'notification' => $notification
		));
	} catch (Exception $e) {
	    echo $e->getMessage();
	    exit(1);
	}
}

// LOOT LIST
if ($lookup != "loot" && !isset($hunt)) {
	$sql = "SELECT * FROM `packages` WHERE `hunt` = ?";
	$stmt = $conn->prepare($sql);
  $stmt->execute([$lookup]);
  $result = $stmt->fetchAll();
	$list = [];
	$count=0;
	if ($result) if (count($result) > 0) {
		foreach($result as $row) {
			$count++;
			$row['pos'] = $count;
			$list[] = $row;
		}
	}
	else {
		$list = 0;
	}
	if ($list == 0 || strcasecmp($lookup,"BURN") == 0 || strcasecmp($lookup,"LOTT") == 0) {
  	try {
    	echo $twig->render('404.html', array(
    		'auth' => $auth,
    		'home' => $home,
    		'tracking' => $tracking,
		'notification' => $notification
    		));
    	} catch (Exception $e) {
  	    echo $e->getMessage();
  	    exit(1);
  	}
  	die;
  }

	$sql = "SELECT activity.player as display, SUM(activity.points) as score, count(DISTINCT activity.package) as found, players.id FROM activity LEFT JOIN players ON activity.player = players.display WHERE activity.package IN (SELECT id FROM packages WHERE hunt = ?) group by activity.player order by score desc";
	$stmt = $conn->prepare($sql);
	  $stmt->execute([$lookup]);
	  $result = $stmt->fetchAll();
	$leaderboard = [];
	$count=0;
	$last=0;
	if ($result) if (count($result) > 0) {
		foreach($result as $row) {
			if ($count > 9) break;
			if ($row["score"] == 0) continue;
			if ($last != $row["score"]) $count++;
			$last = $row["score"];
			$row['pos'] = $count;
			$leaderboard[] = $row;
		}
	}
	else {
		$leaderboard = 0;
	}
	try {
	echo $twig->render('loot.details.html', array(
		'leaderboard' => $leaderboard,
		'list' => $list,
		'name' => $lookup,
		'auth' => $auth,
		'home' => $home,
		'tracking' => $tracking,
		'notification' => $notification
		));
	} catch (Exception $e) {
	    echo $e->getMessage();
	    exit(1);
	}
	die;
}

if ($lookup != "loot" && isset($hunt)) {
	if (!$auth) {
		$lootErr = "You found points! Just log in to collect them. <br>First time? <a href='".$home."help/'>How to play</a>";
		$_SESSION['ref'] = $_SERVER['REQUEST_URI'];
		try {
		echo $twig->render('login.html', array(
			'auth' => $auth,
			'lootErr' => $lootErr,
			'home' => $home,
			'tracking' => $tracking
			));
		} catch (Exception $e) {
		    echo $e->getMessage();
		    exit(1);
		}
		die;
  }

	if (strcasecmp($hunt,"LOTT") == 0) $lookup = obsToken($lookup,'focus');
	$sql = "SELECT * FROM `packages` WHERE `HUNT` = '$hunt' AND `GUID` = '$lookup'";
	$validLoot = getRow($conn->query($sql));
  if ($validLoot == false && strcasecmp($hunt,"LOTT") != 0) header("Location: ../../../404/"); //add stuff for lott here later
	$expiry = strtotime($validLoot['expiry']);
	$now = time();
	$userDetail = (array) jwtDecode($_SESSION['user']);
	$sql = "SELECT * FROM `players` WHERE `display` = '".$userDetail['user']."'";
	$validPlayer = getRow($conn->query($sql));
	if (strcasecmp($hunt,"BURN") == 0) {
		if ($expiry >= $now && $validLoot['found'] > 0) {
			$sql = "SELECT * FROM `activity` WHERE `player` = '".$validPlayer['display']."' AND `package` = '".$validLoot['id']."'";
			$hasActivity = getRow($conn->query($sql));
			if ($hasActivity) $errMsg = 'You already got those points. Each Bonus Drop can only be redeemed once per user. ';
			else {
				$worth = $validLoot['worth'];
				$score = $validPlayer['score']+$worth;
				$finds = $validPlayer['found']+1;
				$update = "UPDATE `players` SET `found` = '$finds', `score` = '$score' WHERE `display` = '".$userDetail["user"]."'";
				$conn->query($update);
				$finds = $validLoot['found']-1;
				if ($validLoot['mayor'] == ""){
					$mayor = $validPlayer['display'];
					$owner = $validPlayer['display'];
				}
				else {
					$mayor = $validLoot['mayor'];
					$owner = $validPlayer['display'];
				}
				$update = "UPDATE `packages` SET `found` = '$finds', `owner` = '$owner', `mayor` = '$mayor', `IP` = '$ip' WHERE `GUID` = '".$validLoot["GUID"]."'";
				$conn->query($update);
				$insert = "INSERT INTO `activity` (`player`, `package`, `points`, `stamp`, `IP`)
				VALUES ('".$validPlayer['display']."', '".$validLoot['id']."', '$worth', '".time()."', '$ip')";
				$conn->query($insert);
				$joyMsg = "<b>YAY!</b> Good job getting this Bonus Drop. $worth points have been added to your score. ";
			}
		}
		else $errMsg = "Too late! No points left. Either the Bonus Drop was consumed by other users or we're past the expiry date. ";
	}
	elseif (strcasecmp($hunt,"LOTT") == 0) {
		if ($validLoot['expiry'] == $sqldate) {
		$sql = "SELECT * FROM `activity` WHERE `player` = '".$validPlayer['display']."' AND `package` = '".$validLoot['id']."'";
		$hasActivity = getRow($conn->query($sql));
		if ($hasActivity){
			$errMsg = "The lottery has already spoken... ";
		}
		else{
			$worth = $validLoot['worth'];
			$score = $validPlayer['score']+$worth;
			$finds = $validPlayer['found']+1;
			$update = "UPDATE `players` SET `found` = '$finds', `score` = '$score' WHERE `display` = '".$userDetail["user"]."'";
			$conn->query($update);
			$finds = $validLoot['found']+1;
			if ($validLoot['mayor'] == ""){
				$mayor = $validPlayer['display'];
				$owner = $validPlayer['display'];
			}
			else {
				$mayor = $validLoot['mayor'];
				$owner = $validPlayer['display'];
			}
			$update = "UPDATE `packages` SET `found` = '$finds', `owner` = '$owner', `mayor` = '$mayor', `IP` = '$ip' WHERE `GUID` = '".$validLoot["GUID"]."'";
			$conn->query($update);
			$insert = "INSERT INTO `activity` (`player`, `package`, `points`, `stamp`, `IP`)
			VALUES ('".$validPlayer['display']."', '".$validLoot['id']."', '$worth', '".time()."', '$ip')";
			$conn->query($insert);
			$joyMsg = "<b>WOAH!!</b> You hit the jackpot! $worth points added to your score. ";
		}
	}
	else $notiMsg = "Not that one! Better luck next time. ";
	$update = "UPDATE `players` SET `lotto` = '$sqldate' WHERE `display` = '".$userDetail["user"]."'";
	$conn->query($update);
	}
	else {
		if ($expiry >= $now) {
			$process = true;
			if ($validLoot['owner'] == $validPlayer['display']){
				$errMsg = 'You already found this loot. <br>If someone else finds it they can rehide it for you to find later! <br>Try <a href="'.$home.'myloot/">setting a hint</a> to help someone else find it! ';
				$process = false;
			}
			$cooltime = time() - 900;
			$sql = "SELECT * FROM `activity` WHERE `player` = '".$validPlayer['display']."' AND `package` = '".$validLoot['id']."' and `stamp` > '$cooltime'";
			$cooldown = getRow($conn->query($sql));
			if ($cooldown == true && $process == true){
				$errMsg = "It's still on cooldown though. You need to wait at least 15 minutes for the new owner to hide it again. <br>How many other Loots can you find while you wait?";
				$process = false;
			}
			if ($process == true){
				$worth = calcWorth($validLoot['found']);
				$score = $validPlayer['score']+$worth;
				$finds = $validPlayer['found']+1;
				$update = "UPDATE `players` SET `found` = '$finds', `score` = '$score' WHERE `display` = '".$userDetail["user"]."'";
				$conn->query($update);
				$finds = $validLoot['found']+1;
				if ($validLoot['mayor'] == ""){
					$mayor = $validPlayer['display'];
					$owner = $validPlayer['display'];
				}
				else {
					$mayor = $validLoot['mayor'];
					$owner = $validPlayer['display'];
				}
				$update = "UPDATE `packages` SET `found` = '$finds', `owner` = '$owner', `mayor` = '$mayor', `IP` = '$ip' WHERE `GUID` = '".$validLoot["GUID"]."'";
				$conn->query($update);
				$insert = "INSERT INTO `activity` (`player`, `package`, `points`, `stamp`, `IP`)
				VALUES ('".$validPlayer['display']."', '".$validLoot['id']."', '$worth', '".time()."', '$ip')";
				$conn->query($insert);
				$joyMsg = "Great job! $worth points added to your score!<br>".($validLoot['mayor'] == "" ? 'You are the first to find this, that makes you a mayor! As a mayor you can <a href="'.$home.'myloot/">add a tag line, upload an image or add a hint here!</a>' : 'As current owner you can <a href="'.$home.'myloot/">add a hint to this loot here!</a>');
			}
		}
		else $errMsg = 'Sorry, that expired and isn\'t worth points anymore. <a href="'.$home.'loot/">Here are some active Hunts!</a>';
	}
	try {
		echo $twig->render('loot.response.html', array(
			'auth' => $auth,
			'success' => $joyMsg,
			'notice' => $notiMsg,
			'error' => $errMsg,
			'home' => $home,
			'tracking' => $tracking,
			'notification' => $notification
			));
		} catch (Exception $e) {
		    echo $e->getMessage();
		    exit(1);
		}
		die;
}
