<?php
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
	echo "OK";
}
?>