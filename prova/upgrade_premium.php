<?php
session_start();
include_once("connect.php");

$id_utente = $_SESSION["session_utente"];

if (isset($id_utente)) {
    $upgrade_premium_query = mysqli_prepare($link, "UPDATE utente SET Premium = 1 WHERE IdUtente = ?");
    mysqli_stmt_bind_param($upgrade_premium_query, "i", $id_utente);
    mysqli_stmt_execute($upgrade_premium_query);
    if (mysqli_stmt_affected_rows($upgrade_premium_query) > 0) {
        echo json_encode(["status" => "OK", "message" => "Congratulazioni, ora sei un utente Premium! Cliccando su OK, sarai reindirizzato alla pagina di login."]);
    } else {
        echo json_encode(["status" => "Errore", "message" => "Errore nell'aggiornamento dello status dell'account"]);
    }
    mysqli_stmt_close($upgrade_premium_query);
}
?>
