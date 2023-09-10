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
	$resultset_utente_passw = mysqli_query($link, "SELECT IdUtente, Username, Passw FROM utente WHERE Username='$name'") or die("database error:". mysqli_error($link));

	while($row = mysqli_fetch_assoc($resultset_utente_passw)){
		if($row['Passw']!= $pass ){
			echo("Username o Password non validi");
			$i=$i+1;
			break;
		}
		else{
			$_SESSION['user_session'] = $row['IdUtente'];
		}
	}
}

if($i==0){
	echo "OK";
}

?>
