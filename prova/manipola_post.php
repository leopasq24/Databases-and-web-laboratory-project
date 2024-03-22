<?php
include_once("connect.php");
session_start();
$idutente = $_SESSION
["session_utente"];
if(isset($_GET["idPost"])){
    $id_post = $_GET['idPost'];

    $stmt_elimina_post= mysqli_prepare($link, "DELETE FROM post WHERE IdPost=? AND (IdUtente = ? OR IdUtente = (SELECT IdUtente FROM blog WHERE blog.IdUtente = post.IdUtente AND blog.IdUtente = ?))");
    mysqli_stmt_bind_param($stmt_elimina_post, "iii", $id_post, $idutente, $idutente);
    if(mysqli_stmt_execute($stmt_elimina_post)){
        echo "OK";
    }else{
        echo "Errore nell'eliminazione";
    }
    mysqli_stmt_close($stmt_elimina_post);
}else if(isset($_POST["idPost"]) and isset($_POST["contenuto"]) and isset($_POST["titolo"])){
    $titolo = $_POST["titolo"];
    $id_post= $_POST["idPost"];
    $contenuto = trim($_POST["contenuto"]);
    $modificato = 1;
    $uploadDir = "foto_utenti/";
    $target_file = null;

    if(strlen($titolo)==0){
        echo "Inserire un titolo!";
        exit;
    }else if(strlen($contenuto)==0){
        echo "Inserire un contenuto!";
        exit;
    }else if(strlen($titolo)>50){
        echo "Max 50 caratteri per il titolo";
        exit;
    }else if(strlen($contenuto)>200){
        echo "Max 200 caratteri per il contenuto";
        exit;
    }else if(isset($_FILES["campo_img_post"]) && $_FILES["campo_img_post"]["size"] > 0) {
        $check = getimagesize($_FILES["campo_img_post"]["tmp_name"]);
        if ($check == false) {
            $output="Il file caricato non è un'immagine";
            echo $output;
            exit;
        }

        $target_file = $uploadDir . basename($_FILES["campo_img_post"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        

        if (file_exists($target_file)) {
            $num = rand();
              $newname = $num.".".$imageFileType;
              $target_file = $uploadDir.basename($newname);
        }
        if ($_FILES["campo_img_post"]["size"] > 5000000) {
            $output="Il file caricato è troppo pesante";
              echo $output;
              exit;
        }
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            $output="Le immagini devono essere in formato JPG, PNG, JPEG o GIF";
              echo $output;
              exit;
        }
        if (!move_uploaded_file($_FILES["campo_img_post"]["tmp_name"], $target_file)){
            $output = "Errore nel caricamento del file: " . $_FILES["campo_img_post"]["error"];
            echo $output;
            exit;
        }
        $stmt = mysqli_prepare($link, "UPDATE post SET Immagine=?,Titolo=?,Testo=?,Modificato=? WHERE IdPost = ? AND IdUtente=?");
        mysqli_stmt_bind_param($stmt, "sssiii",$target_file, $titolo, $contenuto, $modificato, $id_post, $idutente);
        if (mysqli_stmt_execute($stmt)) {
            echo "OK";
        } else {
            echo "Errore durante la modifica del post";
        }
        mysqli_stmt_close($stmt);
    }else{
        $stmt = mysqli_prepare($link, "UPDATE post SET Titolo = ?, Testo = ?, Modificato=? WHERE IdPost = ? AND IdUtente = ?");
        mysqli_stmt_bind_param($stmt, "ssiii",$titolo, $contenuto,$modificato, $id_post, $idutente);
        if (mysqli_stmt_execute($stmt)) {
            echo "OK";
        }else {
            echo "Errore durante la modifica del post";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
