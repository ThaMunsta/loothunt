<?php
$sqldate = date("Y-m-d", time());
$conn = new mysqli($servername, $username, $password, $database);
$sql = "SELECT * FROM `packages` WHERE `expiry` < '$sqldate' GROUP BY hunt, expiry";
$result = $conn->query($sql);
$out = [];
if ($result) if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		if ($row['hunt'] != "LOTT" && $row['hunt'] != "BURN") $out[] = $row;
	}
}
else {
	$out = 0;
}
$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => '../cache',
));
$filter = new Twig_Filter('time2str', function ($string) {
    return time2str($string);
});
$twig->addFilter($filter);
try {
echo $twig->render('archive.html', array(
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
