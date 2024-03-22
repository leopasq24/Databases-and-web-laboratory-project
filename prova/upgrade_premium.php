<?php
include_once("connect.php");
session_start();

if (isset($_POST['utente']) and $_POST['utente'] == $_SESSION['session_utente']){
    $id_utente = $_POST['utente'];
    if($_POST['operazione']=='Premium'){
        $upgrade_premium_query = mysqli_prepare($link, "UPDATE utente SET Premium = 1 WHERE IdUtente = ?");
        mysqli_stmt_bind_param($upgrade_premium_query, "i", $id_utente);
        if (mysqli_stmt_execute($upgrade_premium_query)){
            echo json_encode(["status" => "OK", "message" => "Congratulazioni, ora sei un utente Premium! Cliccando su OK, sarai reindirizzato alla pagina di login."]);
        } else {
            echo json_encode(["status" => "Errore", "message" => "Errore nell'aggiornamento dello status dell'account"]);
        }
        mysqli_stmt_close($upgrade_premium_query);
    }else if($_POST['operazione']=='Disdici'){
        $upgrade_premium_query = mysqli_prepare($link, "UPDATE utente SET Premium = 0 WHERE IdUtente = ?");
        mysqli_stmt_bind_param($upgrade_premium_query, "i", $id_utente);
        if (mysqli_stmt_execute($upgrade_premium_query)){
            echo json_encode(["status" => "OK", "message" => "Disdetta avvenuta con successo. Puoi riattivare l'abbonamento in qualsiasi momento!"]);
        } else {
            echo json_encode(["status" => "Errore", "message" => "Errore nell'aggiornamento dello status dell'account"]);
        }
        mysqli_stmt_close($upgrade_premium_query);
    }
    
}else{
    echo json_encode(["status" => "Errore", "message" => "Errore nella sessione"]);
}
?>
