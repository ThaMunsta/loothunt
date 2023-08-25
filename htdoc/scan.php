<?php
$secure = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if (!isset($_SERVER['HTTPS'])) header('Location: '.$secure);

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => '../cache',
));
try {
echo $twig->render('scan.html', array(
	'auth' => $auth,
	'home' => $home,
	'tracking' => $tracking,
	'notification' => $notification
	));
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}