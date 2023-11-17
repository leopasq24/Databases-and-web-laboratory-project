<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
	echo "Sessione annullata";
	exit;
} else {
	$id_utente = $_SESSION["session_utente"];

	$stmt = mysqli_prepare($link, "SELECT Username FROM utente WHERE IdUtente = ?");
	mysqli_stmt_bind_param($stmt, "i", $id_utente);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $username);
	if (mysqli_stmt_fetch($stmt)) {
    	echo $username;
	} else {
    	echo "Utente non trovato";
	}
	mysqli_stmt_close($stmt);
}
?>
