<?php
require_once 'app/GoogleAuthenticator.php';
require_once './vendor/autoload.php';
require_once './app/autoload.php';
$ga = new PHPGangsta_GoogleAuthenticator();
$secret = 'CHANGE ME';
if (isset($_GET['text']) || isset($_GET['key'])){
	if ($_GET['key'] == "UwmuVUsbro60mQr CHANGE ME E1mTjX6evSGrm5lYl") $checkResult = true;
	else $checkResult = $ga->verifyCode($secret, $_GET['text'], 2);
	if ($checkResult){
		$_SESSION["IZj6ALgXJjTe7TvKN CHANGE ME JqJbdMwpsvn0cae"] = "Ugr99FyesDru3HD CHANGE ME BwKO90SgDc5n5mZu2";
		if (!isset($_GET['email']) && !isset($_GET['username'])) header("Location: " . $_SERVER["PHP_SELF"]);
	}
	elseif ($_GET['text'] == "dead"){
		session_destroy();
		header("Location: " . $_SERVER["PHP_SELF"]);
	}
}
if (!isset($_SESSION["IZj6ALgXJjTe7TvKN CHANGE ME JqJbdMwpsvn0cae"])) {
	echo '<form method="get" action="imgadmin.php">
	<input type="password" name="text" autofocus/>
	<input class="button" type="submit" value="Punch it!" />
	</form>
	';
	die;
}
if ($_SESSION["IZj6ALgXJjTe7TvKN CHANGE ME JqJbdMwpsvn0cae"] != "b8d28f20c34cdb1d229574d8d19f3e37") die;
echo '<form method="post" action="imgadmin.php">
<select id="type" name="t"><option selected value="l">List</option><option value="a">Archive</option></select>
 older than: <select id="type" name="d"><option selected value="0">Now</option><option value="86400">1 day</option><option value="604800">1 week</option><option value="2592000">1 month</option></select>
<input class="button" type="submit" value="Punch it!" />
Clean DB <input type="checkbox" id="clean" name="clean" value="true">
</form>
</script>
';
if (!isset($_POST['t'])){
	die;
}
$time = $_POST['d'];
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$files = glob("img/uploads/*.*");
foreach ($files as $file) {
	$name = substr($file, strpos($file, "/") + 1);
	$name = substr($name, strpos($name, "/") + 1);
	$image = $name;
	$name = substr($name, 0,  (strpos($name, ".")));
	$sql = "SELECT * FROM `images` WHERE `private` = '$name'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$public = $row['public'];
	$sql = "SELECT * FROM `players` WHERE `img` = '$public'";
	$result = $conn->query($sql);
	$sql2 = "SELECT * FROM `packages` WHERE `img` = '$public'";
	$result2 = $conn->query($sql2);
	if ($result->num_rows == 0 && $result2->num_rows == 0) {
		if (filectime($file) < time() - $time){
			echo "<a href=\"".$home."img/$image\" target=\"_blank\">$image</a> Orphaned ";
			if ($_POST['t'] == "a"){
				rename($file, "uploads/archive/".$image);
				echo "Archived. ";
				if ($_POST['clean']){
					$sql = "DELETE FROM `images` WHERE `public` = '$public'";
					$conn->query($sql);	
					echo "Deleted from DB. ";
				}
			}
			echo "<br>";
		}
	}
}

echo "End of list.";
?>