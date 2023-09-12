<?php
session_start();
unset($_SESSION['session_utente']);
if(session_destroy()) {
	header("Location: registrazione.php");
}
?>