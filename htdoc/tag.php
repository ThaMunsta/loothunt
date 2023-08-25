<?php
$conn = new PDO("mysql:host=$servername;dbname=$database",$username,$password);

$bits = explode ("/", $_SERVER['REQUEST_URI']);
$lookup = $bits[(sizeof($bits)-2)];

if (!$auth) header("Location: ".$home."login");
$lookup = hexdec(substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "~") + 1));

$detail = (array) jwtDecode($_SESSION['user']);
$sql = "SELECT * FROM `players` WHERE `display` = :user";
$result = $conn->prepare($sql);
$result->bindParam(':user', $detail['user'], PDO::PARAM_STR);
$result->execute();
$row = getRow($result);

$transport = 'https://';
if (!isset($_SERVER['HTTPS'])) $transport = 'http://';
$uploadUrl = $transport.$_SERVER['HTTP_HOST'].$home;
$_SESSION["update"] = "0";

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => '../cache',
));
try {
echo $twig->render('tag.update.html', array(
	'auth' => $auth,
	'home' => $home,
	'tagid' => $lookup,
	'uploadUrl' => $uploadUrl,
	'tracking' => $tracking,
	'notification' => $notification
	));
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}
