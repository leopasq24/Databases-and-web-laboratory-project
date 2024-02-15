<?php
session_start();
include_once("connect.php");

if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
	$id_utente=$_SESSION["session_utente"];
    $valore = $_GET['key1'];
    $post = $_GET['key2'];

    if($valore=="mi piace"){
    	$Tipo=1;
    	$stmt_verifica_feedback = mysqli_prepare($link,"SELECT Tipo FROM feedback WHERE IdUtente=? AND IdPost=?");
    	mysqli_stmt_bind_param($stmt_verifica_feedback, "ii", $id_utente, $post);
    	mysqli_stmt_execute($stmt_verifica_feedback);
    	$results_verifica_feedback = mysqli_stmt_get_result($stmt_verifica_feedback);
        $row=mysqli_fetch_assoc($results_verifica_feedback);
    	if (mysqli_num_rows($results_verifica_feedback) == 0) {
    		$stmt_insert_feedback = mysqli_prepare($link, "INSERT INTO feedback VALUES (?, ?, ?)");
    		mysqli_stmt_bind_param($stmt_insert_feedback, "iii", $id_utente, $post, $Tipo);
    		mysqli_stmt_execute($stmt_insert_feedback);
            echo"AGGIUNTO";
    	}else if($row["Tipo"]==0){
    		$stmt_update_feedback = mysqli_prepare($link, "UPDATE feedback SET Tipo = ? WHERE IdUtente=? AND IdPost=?");
    		mysqli_stmt_bind_param($stmt_update_feedback, "iii", $Tipo, $id_utente, $post);
    		mysqli_stmt_execute($stmt_update_feedback);
            echo "CAMBIATO";
    	}else if($row["Tipo"]==1){
            $stmt_delete_feedback = mysqli_prepare($link, "DELETE FROM feedback WHERE IdUtente = ? AND IdPost = ?");
            mysqli_stmt_bind_param($stmt_delete_feedback, "ii", $id_utente, $post);
            mysqli_stmt_execute($stmt_delete_feedback);
            echo "ELIMINATO";
        }
    	
    }else if($valore=="non mi piace"){
    	$Tipo=0;
    	$stmt_verifica_feedback = mysqli_prepare($link,"SELECT * FROM feedback WHERE IdUtente=? AND IdPost=?");
    	mysqli_stmt_bind_param($stmt_verifica_feedback, "ii", $id_utente, $post);
    	mysqli_stmt_execute($stmt_verifica_feedback);
    	$results_verifica_feedback = mysqli_stmt_get_result($stmt_verifica_feedback);
        $row=mysqli_fetch_assoc($results_verifica_feedback);
    	if (mysqli_num_rows($results_verifica_feedback) == 0) {
    		$stmt_insert_feedback = mysqli_prepare($link, "INSERT INTO feedback VALUES (?, ?, ?)");
    		mysqli_stmt_bind_param($stmt_insert_feedback, "iii", $id_utente, $post, $Tipo);
    		mysqli_stmt_execute($stmt_insert_feedback);
            echo"AGGIUNTO";
    	}else if($row["Tipo"]==1){
    		$stmt_update_feedback = mysqli_prepare($link, "UPDATE feedback SET Tipo = ? WHERE IdUtente=? AND IdPost=?");
    		mysqli_stmt_bind_param($stmt_update_feedback, "iii", $Tipo, $id_utente, $post);
    		mysqli_stmt_execute($stmt_update_feedback);
            echo "CAMBIATO";
    	}else if($row["Tipo"]==0){
            $stmt_delete_feedback = mysqli_prepare($link, "DELETE FROM feedback WHERE IdUtente = ? AND IdPost = ?");
            mysqli_stmt_bind_param($stmt_delete_feedback, "ii", $id_utente, $post);
            mysqli_stmt_execute($stmt_delete_feedback);
            echo "ELIMINATO";
        }
    }
}   
?>