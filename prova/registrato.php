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

$cerca_mail=mysql_query("SELECT Email FROM utente WHERE Email='$email'");
$resultset = mysqli_query($link, $cerca_mail) or die("database error:". mysqli_error($link));

while($row = mysql_fetch_row($resultset)){
	if($row['email']){
		echo("L'indirizzo email risulta già registrato!");
		$i=$i+1;
	}
}

if($i==0){
	$new_account="INSERT INTO utente('IdUtente', 'Username', 'Passw', 'Email', 'Premium') VALUES (NULL, '$name', '$pass', '$email', 0)";
	mysqli_query($link, $new_account) or die("database error:".mysqli_error($link)."qqq".$new_account);
	echo "OK";
}
?>
