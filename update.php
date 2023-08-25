<?php
require_once './vendor/autoload.php';
require_once './app/autoload.php';
$conn = new mysqli($servername, $username, $password, $database);

//SYSTEM CHECK
$sql = "SELECT * FROM `config` WHERE `name` = 'db_version'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	die("Already updated");
}
//CHANGE PIN TO PASS
$alter = "ALTER TABLE `players` CHANGE `pin` `pass` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;";
echo "<li>".$alter;
mysqli_query($conn, $alter);

//FIX IP ADDRESS LENGTH
$alter = "ALTER TABLE `images` CHANGE `ip` `ip` VARCHAR(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;";
echo "<li>".$alter;
mysqli_query($conn, $alter);

//ENCRYPT PASSWORD OF USERS
$sql = "SELECT * FROM `players`";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$id = $row['id'];
		//$name = str2db($row['display']);
		$pass = pwtodb($row['pass']);
		$email = str2db($row['email']);
		$update = "UPDATE `players` SET `pass` = '$pass', email = $email WHERE `id` = '$id'";
		echo "<li>".$update;
		mysqli_query($conn, $update);
	}
}

//CREATE CONFIG TABLE
$create = "CREATE TABLE `loot`.`config` ( `name` VARCHAR(255) NOT NULL , `value` LONGTEXT NOT NULL ) ENGINE = InnoDB;";
echo "<li>".$create;
mysqli_query($conn, $create);

$insert = "INSERT INTO `config` (`name`, `value`) VALUES ('db_version', '2.0')";
echo "<li>".$insert;
mysqli_query($conn, $insert);



///////////////////////TBD///////////////////////
//WORK ABOVE LINE 
$sql = "SELECT * FROM `activity`";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$id = $row['id'];
		$name = str2db($row['player']);
		$update = "UPDATE `activity` SET `player` = '$name' WHERE `id` = '$id'";
		echo "<li>".$update;
		// mysqli_query($conn, $update);
	}
}
$sql = "SELECT * FROM `packages` WHERE `IP` IS NOT NULL";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		$id = $row['id'];
		$owner = str2db($row['owner']);
		$mayor = str2db($row['mayor']);
		$update = "UPDATE `packages` SET `owner` = '$owner', `mayor` = '$mayor' WHERE `id` = '$id'";
		echo "<li>".$update;
		// mysqli_query($conn, $update);
	}
}
