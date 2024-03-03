<?php
include_once("connect.php");
session_start();

$blogname = trim($_POST["titolo_blog"]);
$desc = trim($_POST["descrizione_blog"]);
$uploadDir = "foto_utenti/";
$target_file = $uploadDir.basename($_FILES["immagine_blog"]["name"]);
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$cat = trim($_POST["categoria_blog"]);
$coautori = explode(" ", trim($_POST["coautori"]));
$idutente = $_SESSION["session_utente"];
$i=0;
$a = array();

foreach ($coautori as $key => $value){
	$stmt_idcoautori=mysqli_prepare($link, "SELECT IdUtente FROM utente WHERE username=?");
	mysqli_stmt_bind_param($stmt_idcoautori,"s",$value);
	mysqli_stmt_execute($stmt_idcoautori);
	$result_idcoautori=mysqli_stmt_get_result($stmt_idcoautori);
	while($row = mysqli_fetch_assoc($result_idcoautori)){
		array_push($a, $row["IdUtente"]);
	}
	mysqli_stmt_close($stmt_idcoautori);
}

if (isset($_POST["categoria_blog"])) {
    $stmt_cat=mysqli_prepare($link, "SELECT IdCategoria FROM categoria WHERE Nome=?");
	mysqli_stmt_bind_param($stmt_cat,"s",$cat);
	mysqli_stmt_execute($stmt_cat);
	$result_cat=mysqli_stmt_get_result($stmt_cat);
	$row = mysqli_fetch_assoc($result_cat);
	$idcat = $row["IdCategoria"];
} else {
    echo "Scegliere la sottocategoria";
    $i = $i + 1;
}
if(strlen($blogname)==0){
	echo("Inserire un titolo");
	$i=$i+1;
}
if(strlen($desc) ==0){
	echo("Inserire una descrizione");
	$i=$i+1;
}
if(strlen($cat) ==0) {
	echo("Scegliere la categoria");
	$i=$i+1;
}

if (isset($_FILES["immagine_blog"]) && $_FILES["immagine_blog"]["size"] > 0) {
    $check = getimagesize($_FILES["immagine_blog"]["tmp_name"]);
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
		if ($_FILES["immagine_blog"]["size"] > 5000000) {
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
        if (move_uploaded_file($_FILES["immagine_blog"]["tmp_name"], $target_file)) {
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
    $stmt_blogname = mysqli_prepare($link, "INSERT INTO blog(IdBlog, Titolo, Descrizione, Immagine, IdCategoria, IdUtente) VALUES (NULL, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_blogname, "sssii", $blogname, $desc, $target_file, $idcat, $idutente);
    if (mysqli_stmt_execute($stmt_blogname)) {
        $idblog = mysqli_insert_id($link);
        mysqli_stmt_close($stmt_blogname);

        foreach ($a as $key => $value) {
            $stmt_coautori = mysqli_prepare($link, "INSERT INTO coautore(IdUtente, IdBlog) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt_coautori, "ii", $value, $idblog);
            mysqli_stmt_execute($stmt_coautori);
            mysqli_stmt_close($stmt_coautori);
        }

        echo "OK";
    } else {
        echo "Errore durante l'inserimento del blog.";
    }
}
?>
