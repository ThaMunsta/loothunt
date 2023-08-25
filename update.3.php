<?php
require_once './vendor/autoload.php';
require_once './app/autoload.php';
$conn = new mysqli($servername, $username, $password, $database);

//SYSTEM CHECK
$sql = "SELECT * FROM `config` WHERE `name` = 'db_version'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		if ($row['name'] == "db_version") {
			if ($row['value'] != "2.1") die("DB Version Not Compatible");
		}
	}
}


//ADD TROPHY TABLE
$create = "CREATE TABLE `loot`.`trophies` ( `id` INT NOT NULL AUTO_INCREMENT , PRIMARY KEY (`id`) , `player` VARCHAR(20) NOT NULL , `trophy` VARCHAR(255) NOT NULL , `img` VARCHAR(255) NOT NULL , `createdate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ) ENGINE = InnoDB;";
echo "<li>".$create;
mysqli_query($conn, $create);
echo mysqli_error($conn);

$update = "UPDATE `config` SET `value` = '2.2' WHERE `name` = 'db_version'";
echo "<li>".$update;
mysqli_query($conn, $update);
echo mysqli_error($conn);
