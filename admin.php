<?php
require_once 'app/GoogleAuthenticator.php';
require_once './vendor/autoload.php';
require_once './app/autoload.php';
$ga = new PHPGangsta_GoogleAuthenticator();
$secret = 'CHANGE ME';
if (isset($_GET['text']) || isset($_GET['key'])){
	if ($_GET['key'] == "LMMSUzGa52BAvJ CHANGE ME CMOjdX37K5RZEDLqBJ") $checkResult = true;
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
	echo '<form method="get" action="admin.php">
	<input type="password" name="text" autofocus/>
	<input class="button" type="submit" value="Punch it!" />
	</form>
	';
	die;
}
if ($_SESSION["IZj6ALgXJjTe7TvKN CHANGE ME JqJbdMwpsvn0cae"] != "Ugr99FyesDru3HD CHANGE ME BwKO90SgDc5n5mZu2") die;
$conn = new PDO("mysql:host=$servername;dbname=$database",$username,$password);
$sql = "SELECT display FROM players";
$allUsers = getRows($conn->query($sql));
$htmlUsers = "";
foreach ($allUsers as $key => $user) {
	$htmlUsers .= "<option value=\"".$user['display']."\">".$user['display']."</option>";
}
echo '<form method="post" action="admin.php">
Hunt: <input class="button" type="text" name="c" size="15"/>
Make: <input class="button" type="number" name="n" min="1" max="512" />
Exp: <input class="button" type="date" name="x" />
<select id="type" name="t"><option selected value="e">Expire</option><option value="b">Burn</option><option value="l">Lottery</option></select>
<div id="burns"  style="display:none;">
Burn count: <input class="button" type="number" name="bc" min="0" max="512" />
Burn worth: <input class="button" type="number" name="bw" min="0" max="10000" />
</div>
<div id="lotto"  style="display:none;">
Lottery worth: <input class="button" type="number" name="lw" min="0" max="100000" />
</div>
<input class="button" type="submit" value="Punch it!" />
<!-- Generate QR <input type="checkbox" id="qr" name="qr" value="true"> -->
</form>
Password reset:
<form method="get" action="admin.php">
Username: <input class="button" type="text" name="username" size="28"/>
email: <input class="button" type="text" name="email" size="28"/>
<input class="button" type="submit" value="Reset it!" />
</form>
<form method="get" action="admin.php">
Notify all users: <input class="button" type="text" name="notify" size="28"/>
<input class="button" type="submit" value="Notify!" />
</form>
<form method="get" action="admin.php">
Notify: <select name="user"> '.$htmlUsers.'
  </select> <input class="button" type="text" name="notify" size="28"/> 
  <input class="button" type="submit" value="Notify!" />
</form>

<script type="text/javascript">
var now = new Date();
var day = ("0" + now.getDate()).slice(-2);
var month = ("0" + (now.getMonth() + 1)).slice(-2);
var today = now.getFullYear()+"-"+(month)+"-"+(day);
document.getElementsByTagName("input")[2].value=today;

var select = document.getElementById("type");

select.onchange=function(){
    if(select.value=="b"){
       document.getElementById("burns").style.display="inline";
       document.getElementById("lotto").style.display="none";
       document.getElementsByTagName("input")[0].value="BURN";
       document.getElementsByTagName("input")[0].readOnly = true;
    }else if(select.value=="l"){
       document.getElementsByTagName("input")[0].value="LOTT";
       document.getElementsByTagName("input")[0].readOnly = true;
       document.getElementById("lotto").style.display="inline";
       document.getElementById("burns").style.display="none";
    }else
    {
       document.getElementById("burns").style.display="none";
       document.getElementById("lotto").style.display="none";
       document.getElementsByTagName("input")[0].readOnly = false;
    }

}
</script>

<a href="?text=dead">Logout</a>
';

if (isset($_GET['notify'])){
	$notice = urldecode($_GET['notify']);
	if ($notice == "") die();
    $conn = new PDO("mysql:host=$servername;dbname=$database",$username,$password);
    if (isset($_GET['user'])){
    	notify($conn,$_GET['user'],$notice);
    	echo $_GET['user']." notifiied: $notice";
    }
    else {
	    $sql = "SELECT * FROM players";
	    $allPlayers = getRows($conn->query($sql));
	    foreach ($allPlayers as $key => $player) {
	    	notify($conn,$player['display'],$notice);
	    }
	    echo "All users notifiied: $notice";
    }
}
if (isset($_GET['username']) && isset($_GET['email'])){
	$conn = new mysqli($servername, $username, $password, $database);
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	manualReset($_GET['username'], $_GET['email'], $mail);
	die;
}
if (!isset($_POST['t']) && !isset($_GET['pull'])){
	die;
}
if ($_POST['t'] == "b"){
	if ($_POST['n'] == "" || $_POST['x'] == "" || $_POST['bc'] == "" || $_POST['bw'] == ""){
		echo "Fill out all  fields";
		die;
	}
	$found = $_POST['bc'];
	$worth = $_POST['bw'];
}
elseif ($_POST['t'] == "e"){
	if ($_POST['c'] == "" || $_POST['n'] == "" || $_POST['x'] == ""){
		echo "Fill out all fields";
		die;
	}
	$found = 0;
	$worth = 0;
}
elseif ($_POST['t'] == "l"){
	if ($_POST['c'] == "" || $_POST['n'] == "" || $_POST['x'] == "" || $_POST['lw'] == ""){
		print_r($_POST);
		echo "Fill out all fields";
		die;
	}
	$found = 0;
	$worth = $_POST['lw'];
}
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$hunt = $_POST['c'];
$exp = $_POST['x'];
$qr = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=";
if ($_POST['c'] == "BURN" || $_POST['c'] == "LOTT"){
	$plex = 10;
}
else $plex = 5;

if ($_POST['c'] == "LOTT"){
	for ($i = 0; $i < $_POST['n']; $i++) {
		$date = date('Y-m-d', strtotime($exp. " + $i days"));
		$token = makeToken($plex);
		$sql = "SELECT * FROM `packages` WHERE `expiry` = '$date' AND `hunt` = 'LOTT'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$id = $row['id'];
				$token = $row['GUID'];
				$update = "UPDATE `packages` SET `GUID` = '$token', `hunt` = '$hunt', `expiry` = '$date', `found` = '$found', `worth` = '$worth' WHERE `id` = '$id'";
				mysqli_query($conn, $update);
				$nLoot = "Updated existing: https://".$_SERVER['HTTP_HOST'].$GLOBALS['home']."loot/".$_POST['c']."/".$token;
				echo $nLoot."<br>";
				if (isset($_POST['qr'])) echo "<img src='".$qr.$nLoot."'/><br>";
			}
		}
		else{
			$insert = "INSERT INTO `packages` (`GUID`, `hunt`, `expiry`, `found`, `worth`)
				VALUES ('$token', '$hunt', '$date', '$found', '$worth')";
			mysqli_query($conn, $insert);
			$nLoot = "https://".$_SERVER['HTTP_HOST'].$GLOBALS['home']."loot/".$_POST['c']."/".$token;
			echo $nLoot."<br>";
			if (isset($_POST['qr'])) echo "<img src='".$qr.$nLoot."'/><br>";
		}
	}
	die;
}
for ($i = 0; $i < $_POST['n']; $i++) {
	$token = makeToken($plex);
	$sql = "SELECT * FROM `packages` WHERE `GUID` = '$token' AND `hunt` = '$hunt'";
	$result = $conn->query($sql);
	while ($result->num_rows > 0) {
		echo "Notice: Duplicate caught! '$token'<br>";
		$token = makeToken($plex);
		$sql = "SELECT * FROM `packages` WHERE `GUID` = '$token' AND `hunt` = '$hunt'";
		$result = $conn->query($sql);
	}
	$insert = "INSERT INTO `packages` (`GUID`, `hunt`, `expiry`, `found`, `worth`)
		VALUES ('$token', '$hunt', '$exp', '$found', '$worth')";
	mysqli_query($conn, $insert);
	$nLoot = "https://".$_SERVER['HTTP_HOST'].$GLOBALS['home']."loot/".$_POST['c']."/".$token;
	echo $nLoot."<br>";
	if (isset($_POST['qr'])) echo "<img src='".$qr.$nLoot."'/><br>";
}

mysqli_close($conn);

function manualReset($player, $email, $mail){
	global $conn;
	$sql = "SELECT * FROM `players` WHERE `display` = '$player'";
	$validPlayer = verify($conn->query($sql));
	if ($validPlayer != false){
		$token = makeToken(30);
		$update = "UPDATE `players` SET `reset` = '$token' WHERE `display` = '$player'";
		mysqli_query($conn, $update);
		$link = "https://".$_SERVER['HTTP_HOST'].$GLOBALS['home']."reset/$token";
		// Message
		$mail->addAddress($email, $player);
		$mail->Body = '
		<html>
		<head>
		  <title>Password Reset</title>
		</head>
		<body>
		  <p>Reset your Loot Hunt password here: <a href="'.$link.'">'.$link.'</a></p>
		</body>
		</html>
		';

		// Mail it
		$mail->Subject = 'Password Reset';
		$mail->isHTML(true);
		$mail->setFrom('admin@loothunt.ca', 'LootHunt');
		$mail->addReplyTo('admin@loothunt.ca', 'LootHunt');
		$mail->send();
		echo "Sent an email to $email for $player's password reset.";
	}
	else echo "Couldn't find a player with that username! Nothing sent.";

}

?>