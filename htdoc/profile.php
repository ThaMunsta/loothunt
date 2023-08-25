<?php
$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => '../cache',
));
$tag = '';
$pic = '';
$conn = new PDO("mysql:host=$servername;dbname=$database",$username,$password);

$bits = explode ("/", $_SERVER['REQUEST_URI']);
$lookup = $bits[(sizeof($bits)-2)];


if ($lookup == 'profile'){
	if ($auth){
		$detail = (array) jwtDecode($_SESSION['user']);
		$sql = "SELECT * FROM `players` WHERE `display` = :user";
		$result = $conn->prepare($sql);
		$result->bindParam(':user', $detail['user'], PDO::PARAM_STR);
		$result->execute();
	}
	else header("Location: ".$home."login");
}
else {
	$sql = "SELECT * FROM `players` WHERE `display` = :lookup";
	$result = $conn->prepare($sql);
	$result->bindParam(':lookup', $lookup, PDO::PARAM_STR);
	$result->execute();
}
$row = getRow($result);
$transport = 'https://';
if (!isset($_SERVER['HTTPS'])) $transport = 'http://';
if ($row['img']) $pic = file_get_contents($transport.$_SERVER['HTTP_HOST'].$home.'store/!'.$row['img']);
if ($row['tag']) $tag = db2str($row['tag']);
$sql = "SELECT * FROM `players` ORDER BY `score` desc";
$rank = getRank($conn->query($sql), $row['display']);
$score = $row['score'];
$found = $row['found'];
$filter = new Twig_Filter('db2str', function ($string) {
    return db2str($string);
});
$twig->addFilter($filter);
$filter = new Twig_Filter('d2h', function ($string) {
    return dechex($string);
});
$filter = new Twig_Filter('time2str', function ($string) {
    return time2str($string);
});
$twig->addFilter($filter);
$filter = new Twig_Filter('d2h', function ($string) {
    return dechex($string);
});
$twig->addFilter($filter);
if ($lookup == 'profile'){
	$sql ="SELECT activity.*, packages.hunt FROM activity LEFT JOIN packages ON activity.package = packages.id WHERE activity.player ='".$detail['user']."' ORDER BY id DESC";
	$result = $conn->query($sql);
	$out = [];
	if ($result->rowCount() > 0) {
		while($rows = getRow($result)) {
			$id = $rows['package'];
			$sql = "SELECT id FROM packages WHERE hunt IN (SELECT hunt FROM packages WHERE id = '$id')";
			$rows['packagecount'] = id2Count($conn->query($sql), $id);
			$out[] = $rows;
		}
	}
	else {
		$out = 0;
	}

	$sql ="SELECT * FROM trophies WHERE player = '".$detail['user']."'";
	$trophies = getRows($conn->query($sql));
	
	try {
	echo $twig->render('profile.html', array(
		'name' => "You",
		'detail' => $detail,
		'pic' => $pic,
		'tag' => $tag,
		'rank' => $rank,
		'rows' => $out,
		'score' => $score,
		'found' => $found,
		'auth' => $auth,
		'home' => $home,
		'tracking' => $tracking,
		'notification' => $notification,
		'trophies' => $trophies
		));
	} catch (Exception $e) {
	    echo $e->getMessage();
	    exit(1);
	}
}
else{
	$sql ="SELECT activity.*, packages.hunt FROM activity LEFT JOIN packages ON activity.package = packages.id WHERE activity.player ='".$row['display']."' ORDER BY id DESC";
	$result = $conn->query($sql);
	$out = [];
	if ($result->rowCount() > 0) {
		while($rows = getRow($result)) {
			$id = $rows['package'];
			$sql = "SELECT id FROM packages WHERE hunt IN (SELECT hunt FROM packages WHERE id = '$id')";
			$rows['packagecount'] = id2Count($conn->query($sql), $id);
			$out[] = $rows;
		}
	}
	else {
		$out = 0;
	}

	$sql ="SELECT * FROM trophies WHERE player = '".$row['display']."'";
	$trophies = getRows($conn->query($sql));

	try {
	echo $twig->render('profile.guest.html', array(
		'name' => $row['display'],
		'pic' => $pic,
		'tag' => $tag,
		'rank' => $rank,
		'score' => $score,
		'found' => $found,
		'rows' => $out,
		'trophies' => $trophies,
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
