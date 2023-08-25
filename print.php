<?php
require_once 'app/GoogleAuthenticator.php';
require_once './vendor/autoload.php';
require_once './app/autoload.php';
use chillerlan\QRCode\{QROptions, QRCode};
$options = new QROptions([
	'imageTransparent'    => false,
	'quietzoneSize' => 8,
	'scale' => 4,
	'eccLevel'   => QRCode::ECC_L,
]);
$qrcode = new QRCode($options);
$ga = new PHPGangsta_GoogleAuthenticator();
$secret = 'CHANGE ME';
if (!empty($_GET) && !isset($_SESSION["eBiX92Y5ddbdstF8 CHANGE ME PWOiLtcEMzctHWGk"])){
	$checkResult = $ga->verifyCode($secret, $_GET['text'], 2);
	if ($checkResult){
		$_SESSION["Pahcf7yqJhrBHfUXM CHANGE ME Uds4nqmp27oTt45"] = "bZF29sK4LTPcORmV3 CHANGE ME SykFP2iWi2hOrFt";
		if (!isset($_GET['email']) && !isset($_GET['username'])) header("Location: " . $_SERVER["PHP_SELF"]);
	}
	elseif ($_GET['text'] == "dead"){
		session_destroy();
		header("Location: " . $_SERVER["PHP_SELF"]);
	}
}
if (!isset($_SESSION["Pahcf7yqJhrBHfUXM CHANGE ME Uds4nqmp27oTt45"])) {
	echo '<form method="get" action="print.php">
	<input type="password" name="text" autofocus/>
	<input class="button" type="submit" value="Punch it!" />
	</form>
	';
	die;
}
if ($_SESSION["Pahcf7yqJhrBHfUXM CHANGE ME Uds4nqmp27oTt45"] != "bZF29sK4LTPcORmV3 CHANGE ME SykFP2iWi2hOrFt") die;
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_GET['hunt'])){
	$hunt = $_GET['hunt'];
	$sql = "SELECT * FROM `packages` WHERE `hunt` = '$hunt'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$doc = file_get_contents('templates/print/header.html');
		$count = 0;
		foreach($result as $row) {
			$count++;
			$nLoot = "https://".$_SERVER['HTTP_HOST'].$GLOBALS['home']."loot/".$row['hunt']."/".$row['GUID'];
			$img = '<img style="image-rendering: pixelated;" height="150" width="150" src="'.$qrcode->render($nLoot).'" />';
			if ($count % 2 == 0) {
				$right = file_get_contents('templates/print/right.html');
				$copy = $img ."</p><p class=\"AveryStyle1\" ><br>LOOT HUNT<br>$count OF $result->num_rows<br> ".$row['hunt']."<br>".$row['GUID'];
				$right = str_replace('&nbsp;', $copy, $right);
				$doc .= $right;
			}
			else {
				$left = file_get_contents('templates/print/left.html');
				$copy = $img ."</p><p class=\"AveryStyle1\" ><br>LOOT HUNT<br>$count OF $result->num_rows<br> ".$row['hunt']."<br>".$row['GUID'];
				$left = str_replace('&nbsp;', $copy, $left);
				$doc .= $left;
			}
			if ($count % 10 == 0 && ($result->num_rows - $count) > 0) {
				//new page? seems to be handled by chrome
				$doc .= file_get_contents('templates/print/pager.html');
			}
		}
		$doc .= file_get_contents('templates/print/footer.html');
		echo $doc;
	}
	else {
		echo "No results";
	}
}
else{
	echo 'Dude, what Hunt tho? <form method="get" action="print.php">
	<input type="text" name="hunt" autofocus/>
	<input class="button" type="submit" value="This one bro!" />
	</form>';
}
