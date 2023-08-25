<?php
require_once './vendor/autoload.php';
require_once './app/autoload.php';
$auth = checkLogin();
$notification = false;
if ($auth){
    require __DIR__ . '/htdoc/trophy.php';
    $conn = new PDO("mysql:host=$servername;dbname=$database",$username,$password);
    $detail = (array) jwtDecode($_SESSION['user']);
    $name = $detail['user'];
    $notification = notificationCount($conn, $name);
    $conn = null;
}   
$regex = str_replace('/', '\/', $home);
$from = '/'.$regex.'/';
$to = '';
$content = $_SERVER['REQUEST_URI'];
$request = preg_replace($from, $to, $content, 1);
//if ($request[strlen($request) - 1] != '/') $request .= '/';
if (strlen($request) > 1 && $request[strlen($request) - 1] != '/') header("Location: $home$request/");

// ROUTER
switch ($request) {
    case '/' :
    case '' :
        require __DIR__ . '/htdoc/index.php';
        break;
    case stristr($request, 'loot') :
        require __DIR__ . '/htdoc/loot.php';
        break;
    case stristr($request, 'archive') :
        require __DIR__ . '/htdoc/archive.php';
        break;
    case 'leaderboard/' :
    case 'score/' :
        require __DIR__ . '/htdoc/leaderboard.php';
        break;
    case stristr($request, 'notifications') :
        require __DIR__ . '/htdoc/notifications.php';
        break;
    case stristr($request, 'activity') :
        require __DIR__ . '/htdoc/activity.php';
        break;
    case stristr($request, 'profile') :
        require __DIR__ . '/htdoc/profile.php';
        break;
    case stristr($request, 'myloot') :
        require __DIR__ . '/htdoc/myloot.php';
        break;
    case stristr($request, 'update') :
        require __DIR__ . '/htdoc/update.php';
        break;
    case stristr($request, 'spray') :
        require __DIR__ . '/htdoc/spray.php';
        break;
    case stristr($request, 'tag') :
        require __DIR__ . '/htdoc/tag.php';
        break;
    case stristr($request, 'hint') :
        require __DIR__ . '/htdoc/hint.php';
        break;
    case 'lottery/' :
    case 'lotto/' :
        require __DIR__ . '/htdoc/lotto.php';
        break;
    case 'chat/' :
    case 'social/' :
        require __DIR__ . '/htdoc/social.php';
        break;
    case 'faq/' :
    case 'help/' :
        require __DIR__ . '/htdoc/help.php';
        break;
    case 'login/' :
    case 'register/' :
        require __DIR__ . '/htdoc/login.php';
        break;
    case 'logout/' :
        require __DIR__ . '/htdoc/logout.php';
        break;
    case stristr($request, 'reset') :
        require __DIR__ . '/htdoc/reset.php';
        break;
    case 'scan/' :
    case 'qr/' :
        require __DIR__ . '/htdoc/scan.php';
        break;
    case 'img/upload/' :
        require __DIR__ . '/htdoc/img.php';
        break;
    case 'cron/' :
        require __DIR__ . '/htdoc/cron.php';
        break;
    default:
        require __DIR__ . '/htdoc/404.php';
        break;
}