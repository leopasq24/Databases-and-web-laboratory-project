<?php
include_once("connect.php");

if(isset($_GET["idPost"])){
    $id_post = $_GET['idPost'];

    $stmt_elimina_post= mysqli_prepare($link, "DELETE FROM post WHERE IdPost=?");
    mysqli_stmt_bind_param($stmt_elimina_post, "i", $id_post);
    if(mysqli_stmt_execute($stmt_elimina_post)){
        echo "OK";
    }else{
        echo "Errore nell'eliminazione";
    }
    mysqli_stmt_close($stmt_elimina_post);
}else if(isset($_POST["idPost"]) and isset($_POST["contenuto"])and isset($_POST["titolo"])){
    $titolo = $_POST["titolo"];
    $id_post= $_POST["idPost"];
    $contenuto = trim($_POST["contenuto"]);
    $modificato = 1;

    if(strlen($contenuto)==0){
        echo "Inserire un contenuto!";
        exit;
    }else{
        $stmt = mysqli_prepare($link, "UPDATE post SET Titolo = ?, Testo = ?, Modificato=? WHERE IdPost = ?");
        mysqli_stmt_bind_param($stmt, "ssii",$titolo, $contenuto,$modificato, $id_post);
        if (mysqli_stmt_execute($stmt)) {
            echo "OK";
        }else {
            echo "Errore durante l'inserimento del post";
        }   
    }
}
?>