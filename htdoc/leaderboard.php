<?php
$conn = new mysqli($servername, $username, $password, $database);
$sql = "SELECT * FROM `players` ORDER BY `score` desc";
$result = $conn->query($sql);
$out = [];
if ($result) if ($result->num_rows > 0) {
	$count=0;
	$last=0;
	while($row = $result->fetch_assoc()) {
		if ($count > 9) break;
		if ($row["score"] == 0) continue;
		if ($last != $row["score"]) $count++;
		$last = $row["score"];
		$row['pos'] = $count;
		$out[] = $row;
	}
}
else {
	$out = 0;
}

$auth = checkLogin();
$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => '../cache',
));
$filter = new Twig_Filter('db2str', function ($string) {
    return db2str($string);
});
$twig->addFilter($filter);
$filter = new Twig_Filter('d2h', function ($string) {
    return dechex($string);
});
$twig->addFilter($filter);
try {
echo $twig->render('leaderboard.html', array(
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
