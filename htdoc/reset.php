<?php
$conn = new PDO("mysql:host=$servername;dbname=$database",$username,$password);

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader, array(
    //'cache' => '../cache',
));
$bits = explode ("/", $_SERVER['REQUEST_URI']);
$lookup = $bits[(sizeof($bits)-2)];
$success = false;
$valid = true;
if ($lookup != "reset"){
	$sql = "SELECT * FROM `players` WHERE `reset` = :lookup";
	$result = $conn->prepare($sql);
	$result->bindParam(':lookup', $lookup, PDO::PARAM_STR);
	$result->execute();
	$validPlayer = getRow($result);
	if ($validPlayer != false){
		if (isset($_POST['password'])){
			$pass = pwtodb($_POST['password']);
			$update = "UPDATE `players` SET `pass` = '$pass', `reset` = '' WHERE `reset` = '$lookup'";
			$result = $conn->prepare($update);
			$result->execute();
			$success = true;
		}
	}
	else{
		$valid = false;
	}
	try {
		echo $twig->render('reset.form.html', array(
			'auth' => $auth,
			'home' => $home,
			'tracking' => $tracking,
			'success' => $success,
			'valid' => $valid,
			'uri' => $_SERVER['REQUEST_URI']
			));
		} catch (Exception $e) {
		    echo $e->getMessage();
		    exit(1);
		}
	die();
}
if (isset($_POST['email'])){
	$email = $_POST['email'];
	$sql = "SELECT * FROM `players` WHERE `email` = :email";
	$result = $conn->prepare($sql);
	$result->bindParam(':email', $email, PDO::PARAM_STR);
	$result->execute();
	$validPlayer = getRow($result);
	if ($validPlayer != false){
		$token = makeToken(30);
		$update = "UPDATE `players` SET `reset` = '$token' WHERE `email` = :email";
		$result = $conn->prepare($update);
		$result->bindParam(':email', $email, PDO::PARAM_STR);
		$result->execute();
		$link = "https://".$_SERVER['HTTP_HOST'].$home."reset/$token";
		$htmllink = '<a href="'.$link.'">'.$link.'</a>';

		$mail->addAddress($email, 'LootHunt User');
		$mail->AltBody = "Reset your Loot Hunt password here: $link";
		$mail->Body = '
			<html>
			<head>
			  <title>Password Reset</title>
			</head>
			<body>
			  <p>Reset your Loot Hunt password here: '.$htmllink.'
			  </p>
			  </body>
			</html>
			';
		$responce = "An email has been sent to you with your reset code!";
	}
	else{
		$sql = "SELECT * FROM `activity` WHERE `ip` = '$ip' group by player";
		$result = $conn->prepare($sql);
		$result->execute();
		$playerList = getRows($result);
		$names = "";
		$htmlnames = "";
		if ($playerList != false){
			foreach($playerList as $row) {
				$names .= $row['player']." ";
				$htmlnames .= "<li><a href=\"https://".$_SERVER['HTTP_HOST'].$home."admin.php?key=itPM4hWcGdWGejxNLOkR3XD0XeTHyIiJ&username=".$row['player']."&email=$email\">".$row['player']."</a> </li>";
			}
		}
		else{
			$names = $htmlnames = "None!";
		}
		$mail->addAddress('sir.mike.johnston@gmail.com', 'Mike');
		$mail->AltBody = 'Reset requested from '.$email.' at '.$ip.'. Possible Matches: '.$names;
		$mail->Body = '
			<html>
			<head>
			  <title>Email Request</title>
			</head>
			<body>
			  <p>Reset requested from '.$email.' at '.$ip.'. Possible Matches: '.$htmlnames.'
			  </p>
			  </body>
			</html>
			';
		$responce = "That email is not linked to an account but an investigation has been started with our admins who will be in touch if they can help.";
	}
	$mail->Subject = 'Password Reset';
	$mail->isHTML(true);
	$mail->setFrom('admin@loothunt.ca', 'LootHunt');
	$mail->addReplyTo('admin@loothunt.ca', 'LootHunt');
	$mail->send();
}
try {
echo $twig->render('reset.html', array(
	'auth' => $auth,
	'home' => $home,
	'tracking' => $tracking
	));
} catch (Exception $e) {
    echo $e->getMessage();
    exit(1);
}
