<?php
include_once("connect.php");

$name = $_POST["username"];
$pass = $_POST["password"];
$pass2 = $_POST["password_2"];
$email = $_POST["mail"];
$i=0;

if(strlen($name) < 5){
	echo("Lo username deve essere di almeno 5 caratteri!");
	$i=$i+1;
}
if(strlen($name) > 20){
	echo("Lo username non può contenere più di 20 caratteri!");
	$i=$i+1;
}
if(strlen($pass) < 4) {
	echo("La password deve essere di almeno 4 caratteri!");
	$i=$i+1;
}
if(strlen($email) == 0){
	echo("La mail è obbligatoria!");
	$i=$i+1;
}
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	echo("L'indirizzo email non è valido!");
	$i=$i+1;
}
if($pass != $pass2) {
	echo("Le password devono coincidere!");
	$i=$i+1;
}

if($i==0){
	$resultset_email = mysqli_query($link, "SELECT Email FROM utente WHERE Email='$email'") or die("database error:". mysqli_error($link));

	while($row = mysqli_fetch_assoc($resultset_email)){
		if($row['Email']){
			echo("L'indirizzo email risulta già registrato.");
			$i=$i+1;
			break;
		}
	}
}

if($i==0){
	$resultset_name = mysqli_query($link, "SELECT Username FROM utente WHERE Username='$name'") or die("database error:". mysqli_error($link));

	while($row = mysqli_fetch_assoc($resultset_name)){
		if($row['Username']){
			echo("Il nome utente è già in uso.");
			$i=$i+1;
			break;
		}
	}
}


if($i==0){
	$new_account = "INSERT INTO utente(IdUtente, Username, Passw, Email, Premium) VALUES (NULL, '$name', '$pass', '$email', 0)";
	mysqli_query($link, $new_account) or die("database error:".mysqli_error($link)."qqq".$new_account);
	echo "OK";
}
?>
