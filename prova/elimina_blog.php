<?php
include_once("connect.php");
session_start();

if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
}else{
	if (isset($_POST["blogId"])) {
    	$blogId = $_POST["blogId"];
    	$stmt = mysqli_prepare($link, "DELETE FROM blog WHERE IdBlog = ?");
    	mysqli_stmt_bind_param($stmt, "i", $blogId);
		if (mysqli_stmt_execute($stmt)) {
       		echo "OK";
    	} else {
        	echo "errore";
    	}

    	mysqli_stmt_close($stmt);
	} else {
    	echo "richiesta fallita";
	}
}
?>