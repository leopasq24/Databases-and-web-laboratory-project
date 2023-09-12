<?php
session_start();
include_once("connect.php");
	
$name = trim($_POST["username"]);
$pass = trim($_POST["password"]);
$i=0;

if(strlen($name)==0){
	echo("Inserire il nome utente!");
	$i=$i+1;
}

if(strlen($pass)==0){
	echo("Inserire la password!");
	$i=$i+1;
}

if($i==0){
	$stmt_utente_passw=mysqli_prepare($link, "SELECT IdUtente, Username, Passw FROM utente WHERE Username=?");
	mysqli_stmt_bind_param($stmt_utente_passw,"s",$name);
	mysqli_stmt_execute($stmt_utente_passw);
	$result_utente_passw=mysqli_stmt_get_result($stmt_utente_passw);
	$result_array = mysqli_fetch_row($result_utente_passw);
	if(empty($result_array)){
		echo("Username non valido");
		$i=$i+1;
		mysqli_stmt_close($stmt_utente_passw);
	}
	elseif($result_array[2]!=$pass){
		echo("Password errata");
		$i=$i+1;
		mysqli_stmt_close($stmt_utente_passw);
	}
	else{
		$_SESSION['session_utente']=$result_array[0];
	}
}

if($i==0){
	echo "OK";
}

?>
