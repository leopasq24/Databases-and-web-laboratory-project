<?php
include_once("connect.php");
session_start();


if (!isset($_POST["blogId"]) || !isset($_POST["titolo_post"]) || !isset($_POST["testo_post"])|| !isset($_POST["data_post"])|| !isset($_POST["ora_post"])){
	echo "Rischiesta fallita".var_dump($_POST);
	exit;
}
$idutente=$_SESSION["session_utente"];
$blogId =$_POST["blogId"];
$titolo_post = trim($_POST["titolo_post"]);
$testo_post = trim($_POST["testo_post"]);
$data_post = $_POST["data_post"];
$ora_post = $_POST["ora_post"];
$uploadDir = "foto_utenti/";
$target_file = $uploadDir.basename($_FILES["immagine_post"]["name"]);
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$i=0;
$a = array();

if(strlen($titolo_post)==0){
	echo("Inserire un titolo");
	$i=$i+1;
}
if(strlen($testo_post) ==0){
	echo("Inserire un contenuto");
	$i=$i+1;
}

if (isset($_FILES["immagine_post"]) && $_FILES["immagine_post"]["size"] > 0) {
    $check = getimagesize($_FILES["immagine_post"]["tmp_name"]);
    if ($check == false) {
        echo "Il file caricato non è un'immagine";
        $i = $i + 1;
    }
	if($i==0){
		if (file_exists($target_file)) {
			$num = rand();
  			$newname = $num.".".$imageFileType;
  			$target_file = $uploadDir.basename($newname);
		}
	}
	if($i==0){
		if ($_FILES["immagine_post"]["size"] > 500000) {
  			echo "Il file caricato è troppo pesante";
  			$i=$i+1;
		}
	}
	if($i==0){
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
		&& $imageFileType != "gif" ) {
  			echo "Le immagini devono essere in formato JPG, PNG, JPEG o GIF";
  			$i=$i+1;
		}
	}
    if ($i==0) {
        if (move_uploaded_file($_FILES["immagine_post"]["tmp_name"], $target_file)) {
            $i=0;
        } else {
            echo "Errore nel caricamento del file";
            $i=$i+1;
        }
    }
} else {

    $target_file = null;
}
if ($i == 0) {
    $stmt = mysqli_prepare($link, "INSERT INTO post(IdPost, Titolo, Data, Ora, Testo, Immagine, IdBlog, IdUtente) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssssii", $titolo_post, $data_post, $ora_post, $testo_post, $target_file, $blogId, $idutente);
    if (mysqli_stmt_execute($stmt)) {
        echo "OK";
    }else {
        echo "Errore durante l'inserimento del post";
    }
}
?>
