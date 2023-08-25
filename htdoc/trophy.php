<?php
$conn = new PDO("mysql:host=$servername;dbname=$database",$username,$password);

//LOTTERY WINNERS
$trophy = "Hit the Jackpot";
$img = "lucky.png";
$alert = "You just unlocked a trophy for scoring points in the lottery! <a href='../profile/#trophies'>Check out your trophy case</a>.";
$sql = "SELECT activity.player as display FROM activity LEFT JOIN players ON activity.player = players.display LEFT JOIN trophies ON players.display = trophies.player WHERE activity.package IN (SELECT id FROM packages WHERE hunt = 'LOTT') AND display NOT IN (SELECT player FROM trophies WHERE trophy = 'Hit the Jackpot') group by activity.player order by score desc";
$winners = getRows($conn->query($sql));
if ($winners != false){
	foreach ($winners as $key => $winner) {
		$name = $winner['display'];
		$insert = "INSERT INTO `trophies` (`player`, `trophy`, `img`) VALUES (:player, :trophy, :img)";
		$result = $conn->prepare($insert);
		$result->bindParam(':player', $name, PDO::PARAM_STR);
		$result->bindParam(':trophy', $trophy, PDO::PARAM_STR);
		$result->bindParam(':img', $img, PDO::PARAM_STR);
		$result->execute();

		$insert = "INSERT INTO `notifications` (`player`, `text`) VALUES (:player, :alert)";
		$result = $conn->prepare($insert);
		$result->bindParam(':player', $name, PDO::PARAM_STR);
		$result->bindParam(':alert', $alert, PDO::PARAM_STR);
		$result->execute();
	}	
}

//BONUS DROPS
$trophy = "Off the Beaten Path";
$img = "bonus.png";
$alert = "You just unlocked a trophy for scoring points on a Bonus Drop! <a href='../profile/#trophies'>Check out your trophy case</a>.";
$sql = "SELECT activity.player as display FROM activity LEFT JOIN players ON activity.player = players.display LEFT JOIN trophies ON players.display = trophies.player WHERE activity.package IN (SELECT id FROM packages WHERE hunt = 'BURN') AND display NOT IN (SELECT player FROM trophies WHERE trophy = 'Off the Beaten Path') group by activity.player order by score desc";
$winners = getRows($conn->query($sql));
if ($winners != false){
	foreach ($winners as $key => $winner) {
		$name = $winner['display'];
		$insert = "INSERT INTO `trophies` (`player`, `trophy`, `img`) VALUES (:player, :trophy, :img)";
		$result = $conn->prepare($insert);
		$result->bindParam(':player', $name, PDO::PARAM_STR);
		$result->bindParam(':trophy', $trophy, PDO::PARAM_STR);
		$result->bindParam(':img', $img, PDO::PARAM_STR);
		$result->execute();

		$insert = "INSERT INTO `notifications` (`player`, `text`) VALUES (:player, :alert)";
		$result = $conn->prepare($insert);
		$result->bindParam(':player', $name, PDO::PARAM_STR);
		$result->bindParam(':alert', $alert, PDO::PARAM_STR);
		$result->execute();
	}	
}

//AN2018
$trophy = "AN2018 Winner";
$img = "an2018.png";
$alert = "You just unlocked a trophy for finishing a Hunt in first place! <a href='../profile/#trophies'>Check out your trophy case</a>.";
$sqldate = date("Y-m-d", time());
$sql = "SELECT * FROM `trophies` WHERE trophy = '$trophy' LIMIT 1";
$row = getRow($conn->query($sql));
if (!$row){
	$sql = "SELECT * FROM `packages` WHERE hunt = 'AN2018' AND `expiry` > '$sqldate' LIMIT 1";
	$row = getRow($conn->query($sql));
	if(!$row){
		$sql = "SELECT activity.player as display FROM activity LEFT JOIN players ON activity.player = players.display WHERE activity.package IN (SELECT id FROM packages WHERE hunt = 'AN2018') AND display NOT IN (SELECT player FROM trophies WHERE trophy = 'AN2018 Winner') group by activity.player order by score desc LIMIT 1";
		$winners = getRows($conn->query($sql));
		if ($winners != false){
			foreach ($winners as $key => $winner) {
				$name = $winner['display'];
				$insert = "INSERT INTO `trophies` (`player`, `trophy`, `img`) VALUES (:player, :trophy, :img)";
				$result = $conn->prepare($insert);
				$result->bindParam(':player', $name, PDO::PARAM_STR);
				$result->bindParam(':trophy', $trophy, PDO::PARAM_STR);
				$result->bindParam(':img', $img, PDO::PARAM_STR);
				$result->execute();

				$insert = "INSERT INTO `notifications` (`player`, `text`) VALUES (:player, :alert)";
				$result = $conn->prepare($insert);
				$result->bindParam(':player', $name, PDO::PARAM_STR);
				$result->bindParam(':alert', $alert, PDO::PARAM_STR);
				$result->execute();
			}	
		}
	}
}




$conn = null;