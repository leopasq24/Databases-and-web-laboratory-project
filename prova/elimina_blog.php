<?php
include_once("connect.php");
session_start();

if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
}else{
	$id_utente = $_SESSION["session_utente"];
	if (isset($_POST["blogId"])) {
    	$blogId = $_POST["blogId"];
    	$stmt = mysqli_prepare($link, "DELETE FROM blog WHERE IdBlog = ? AND IdUtente=?");
    	mysqli_stmt_bind_param($stmt, "ii", $blogId, $id_utente);
		if (mysqli_stmt_execute($stmt)) {
       		echo "OK";
    	} else {
        	echo "Errore nell'eliminazione";
    	}

    	mysqli_stmt_close($stmt);
	} else {
    	echo "richiesta fallita";
	}
}
?>
