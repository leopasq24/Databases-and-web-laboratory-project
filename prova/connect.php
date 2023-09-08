<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "servizio_blog";

$link = mysqli_connect($servername, $username, $password, $dbname) or die("Connessione al DataBase fallita: " . mysqli_connect_error());
if (mysqli_connect_errno()) {
	printf("Connessione fallita: %s\n", mysqli_connect_error());
	exit();
}
?>