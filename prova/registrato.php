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
if(strlen($pass) < 8) {
	echo("La password deve essere di almeno 8 caratteri!");
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
	$stmt_email=mysqli_prepare($link, "SELECT Email FROM utente WHERE Email=?");
	mysqli_stmt_bind_param($stmt_email,"s",$email);
	mysqli_stmt_execute($stmt_email);
	$result_email=mysqli_stmt_get_result($stmt_email);

	while($row = mysqli_fetch_assoc($result_email)){
		if($row["Email"]){
			echo("L'indirizzo email risulta già registrato.");
			$i=$i+1;
			mysqli_stmt_close($stmt_email);
			break;
		}
	}
}

if($i==0){
	$stmt_name=mysqli_prepare($link, "SELECT Username FROM utente WHERE Username=?");
	mysqli_stmt_bind_param($stmt_name,"s",$name);
	mysqli_stmt_execute($stmt_name);
	$result_name=mysqli_stmt_get_result($stmt_name);

	while($row = mysqli_fetch_assoc($result_name)){
		if($row['Username']){
			echo("Il nome utente è già in uso.");
			$i=$i+1;
			mysqli_stmt_close($stmt_name);
			break;
		}
	}
}


if($i==0){
	$stmt_new_account=mysqli_prepare($link, "INSERT INTO utente(IdUtente, Username, Passw, Email, Premium) VALUES (?,?,?,?,?)");
	$id=NULL;
	$premium=0;
	mysqli_stmt_bind_param($stmt_new_account,"isssi", $id, $name, $pass, $email, $premium);
	mysqli_stmt_execute($stmt_new_account);
	mysqli_stmt_close($stmt_new_account);
	echo "OK";
}
?>
