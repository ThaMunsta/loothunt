<?php
$conn = new PDO("mysql:host=$servername;dbname=$database",$username,$password);

$bits = explode ("/", $_SERVER['REQUEST_URI']);
$lookup = $bits[(sizeof($bits)-2)];

if (!$auth) header("Location: ".$home."login");
if ($lookup == 'status'){
	if ($auth){
		$detail = (array) jwtDecode($_SESSION['user']);
		$update = "UPDATE `players` SET `tag` = '".str2db($_POST['status'])."' WHERE `display` = :user";
		$result = $conn->prepare($update);
		$result->bindParam(':user', $detail['user'], PDO::PARAM_STR);
		$result->execute();
	}
	else header("Location: ".$home."login");
	header("Location: ".$home."profile");
}
if ($lookup == 'email'){
	if ($auth){
		$detail = (array) jwtDecode($_SESSION['user']);
		$update = "UPDATE `players` SET `email` = '".str2db($_POST['email'])."' WHERE `display` = :user";
		$result = $conn->prepare($update);
		$result->bindParam(':user', $detail['user'], PDO::PARAM_STR);
		$result->execute();
	}
	else header("Location: ".$home."login");
	header("Location: ".$home."update");
}
if ($lookup == 'tag'){
	if ($auth){
		$detail = (array) jwtDecode($_SESSION['user']);
		$update = "UPDATE `packages` SET `tag` = '".str2db($_POST['tag'])."' WHERE `id` = :id";
		$result = $conn->prepare($update);
		$result->bindParam(':id', $_POST['id'], PDO::PARAM_STR);
		$result->execute();
	}
	else header("Location: ".$home."login");
	header("Location: ".$home."myloot");
}
if ($lookup == 'hint'){
	if ($auth){
		$detail = (array) jwtDecode($_SESSION['user']);
		$update = "UPDATE `packages` SET `hint` = '".str2db($_POST['hint'])."' WHERE `id` = :id";
		$result = $conn->prepare($update);
		$result->bindParam(':id', $_POST['id'], PDO::PARAM_STR);
		$result->execute();
	}
	else header("Location: ".$home."login");
	header("Location: ".$home."myloot");
}

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
echo $twig->render('profile.update.html', array(
	'auth' => $auth,
	'home' => $home,
	'email' => db2str($row['email']),
	'uploadUrl' => $uploadUrl,
	'tracking' => $tracking,
	'notification' => $notification
	));
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}
