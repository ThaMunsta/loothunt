<?php

if (!$auth) header("Location: ".$home."login");
$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => '../cache',
));
$filter = new Twig_Filter('db2str', function ($string) {
    return db2str($string);
});
$twig->addFilter($filter);
$conn = new PDO("mysql:host=$servername;dbname=$database",$username,$password);
$userDetail = (array) jwtDecode($_SESSION['user']);
$sql = "SELECT * FROM `packages` WHERE `mayor` = :user OR `owner` = :user";
$result = $conn->prepare($sql);
$result->bindParam(':user', $userDetail['user'], PDO::PARAM_STR);
$result->execute();
$out = [];
if ($result) if ($result->rowCount() > 0) {
	while($row = getRow($result)) {
		$id = $row['id'];
		$sql = "SELECT id FROM packages WHERE hunt IN (SELECT hunt FROM packages WHERE id = '$id')";
		$row['packagecount'] = id2Count($conn->query($sql), $id);
		$row['lootURL'] = '<a href="'.$home.'activity/~'.dechex($id).'">';
		$sql = "SELECT img FROM packages WHERE hunt IN (SELECT hunt FROM packages WHERE id = '$id')";
	    if ($row['mayor'] == $userDetail['user']){
	      $row['imageURL'] = '<a href="'.$home.'spray/~'.dechex($id).'">';
	      $row['tagURL'] = '<a href="'.$home.'tag/~'.dechex($id).'">';
	    }
	    if ($row['owner'] == $userDetail['user']){
	      $row['hintURL'] = '<a href="'.$home.'hint/~'.dechex($id).'">';
	    }
	    if ($row['hunt'] != 'BURN' && $row['hunt'] != 'LOTT') $out[] = $row;
	}
}
//var_dump($out);
try {
echo $twig->render('myloot.html', array(
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
