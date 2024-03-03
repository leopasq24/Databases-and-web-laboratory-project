<?php
session_start();
include_once("connect.php");

$operazione = $_POST["operazione"];
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
}else if($operazione=="creazione"){
    if(isset($_POST["id_post"]) and isset($_POST["data"]) and isset($_POST["ora"]) and isset($_POST["contenuto"])){
        $idutente=$_SESSION["session_utente"];
        $idpost= $_POST["id_post"];
        $data = $_POST["data"];
        $ora = $_POST["ora"];
        $contenuto = trim($_POST["contenuto"]);

        if(strlen($contenuto)==0){
            echo "<p class=inserire_commento>Inserire un commento!</p>";
            exit;
        }else{
            $stmt = mysqli_prepare($link, "INSERT INTO commenta(IdPost, Contenuto, Data, Ora, IdUtente) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "isssi", $idpost, $contenuto, $data, $ora, $idutente);
            if (mysqli_stmt_execute($stmt)) {
                echo "OK";
            }else {
                echo "Errore durante l'inserimento del post";
            }   
        }
    }
}else{
    if(isset($_POST["Commento_Id"]) and isset($_POST["contenuto"])){
        $idcommento= $_POST["Commento_Id"];
        $contenuto = trim($_POST["contenuto"]);
        $modificato = 1;

        if(strlen($contenuto)==0){
            echo "<p class='inserire_commento'>Inserire un commento!</p>";
            exit;
        }else{
            $stmt = mysqli_prepare($link, "UPDATE commenta SET Contenuto = ?, Modificato=? WHERE IdCommento = ?");
            mysqli_stmt_bind_param($stmt, "sii", $contenuto,$modificato, $idcommento);
            if (mysqli_stmt_execute($stmt)) {
                echo "OK";
            }else {
                echo "Errore durante l'inserimento del post";
            }   
        }
    }
}
?>
