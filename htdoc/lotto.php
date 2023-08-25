<?php
$sqldate = date("Y-m-d", time());
$errMsg = '';
$conn = new PDO("mysql:host=$servername;dbname=$database",$username,$password);

if (!$auth) header("Location: ".$home."login");
$detail = (array) jwtDecode($_SESSION['user']);
$sql = "SELECT * FROM `players` WHERE `display` = :user";
$result = $conn->prepare($sql);
$result->bindParam(':user', $detail['user'], PDO::PARAM_STR);
$result->execute();
$row = getRow($result);
$sql = "SELECT * FROM `packages` WHERE `hunt` = 'LOTT' AND `expiry` > :sqldate ORDER BY `expiry` LIMIT 1";
$result = $conn->prepare($sql);
$result->bindParam(':sqldate', $sqldate, PDO::PARAM_STR);
$result->execute();
$next = getRow($result);
if ($next['expiry']){
	$time = strtotime($next['expiry']);
	$next['strtime'] = time2str($time);
	$nextMsg = "Next lottery is ". $next['strtime'] .".";
}
else $nextMsg = "";
if ($row['lotto'] == $sqldate){
	$errMsg = "You already played today. $nextMsg Good luck for next time!";
}
$sql = "SELECT * FROM `packages` WHERE `expiry` = :sqldate AND `hunt` = 'LOTT'";
$result = $conn->prepare($sql);
$result->bindParam(':sqldate', $sqldate, PDO::PARAM_STR);
$result->execute();
$lotto = getRow($result);

$link = $GLOBALS['home']."loot/LOTT/";
$winner = mt_rand(0, 4);
$GUID = obsToken($lotto["GUID"], "blur");
$out = "Click on a box to see if you won!<br><h1>";
for ($i = 0; $i < 5; $i++) {
	if ($i == $winner){
		$out.= '<a href="'.$link.$GUID.'" style="text-decoration:none"><i class="fas fa-box"></i></a> ';
	}
	else $out.= '<a href="'.$link.makeToken(20).'" style="text-decoration:none"><i class="fas fa-box"></i></a> ';
}
$out.="</h1>";
$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => '../cache',
));
try {
echo $twig->render('lotto.html', array(
	'errMsg' => $errMsg,
	'boxes' => $out,
	'lotto' => $lotto,
	'next' => $next,
	'auth' => $auth,
	'home' => $home,
	'tracking' => $tracking,
	'notification' => $notification
	));
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}
