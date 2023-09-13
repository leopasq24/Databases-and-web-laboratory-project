<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
	echo"Sessione non settata";
	exit;
} else {
	$id_utente = $_SESSION["session_utente"];
	$query_nome = mysqli_query($link,"SELECT Username FROM utente WHERE IdUtente=$id_utente");
	$risultato = mysqli_fetch_assoc($query_nome);
	echo $risultato['Username'];
}
?>