<?php
include_once("connect.php");
session_start();

if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
}else{
	if (isset($_POST["idblog"]) && isset($_POST["campo_titolo"]) && isset($_POST ["campo_descrizione"])&& isset ($_POST["campo_categoria"])) {
    	$blogId = $_POST["idblog"];
    	$nuovo_titolo = trim($_POST["campo_titolo"]);
    	$nuova_desc = trim($_POST["campo_descrizione"]);
    	$nuova_cat = $_POST["campo_categoria"];
    	$uploadDir = "foto_utenti/";
    	$target_file = null;


    	$stmt_id_cat= mysqli_prepare($link, "SELECT IdCategoria FROM categoria WHERE Nome=?");
  		mysqli_stmt_bind_param($stmt_id_cat, "s", $nuova_cat);
  		mysqli_stmt_execute($stmt_id_cat);
  		$query_id_cat = mysqli_stmt_get_result($stmt_id_cat);
  		$array_id_cat = mysqli_fetch_assoc($query_id_cat);
  		$id_nuova_cat = $array_id_cat["IdCategoria"];

    	if(strlen($nuovo_titolo)>30){
    		$output="<p class='errore_modifica'>Il titolo è troppo lungo(max 15 caratteri)</p>";
    		echo $output;
    		exit;
    	}else if(strlen($nuova_desc)>40){
    		$output="<p class='errore_modifica'>La descrizione è troppo lunga(max 40 caratteri)</p>";
    		echo $output;
    		exit;
    	}else if(isset($_FILES["campo_img"]) && $_FILES["campo_img"]["size"] > 0) {
    		$check = getimagesize($_FILES["campo_img"]["tmp_name"]);
    		if ($check == false) {
        		$output="<p class='errore_modifica'>Il file caricato non è un'immagine</p>";
        		echo $output;
    			exit;
    		}

    		$target_file = $uploadDir . basename($_FILES["campo_img"]["name"]);
    		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    		

			if (file_exists($target_file)) {
				$num = rand();
  				$newname = $num.".".$imageFileType;
  				$target_file = $uploadDir.basename($newname);
			}
			if ($_FILES["campo_img"]["size"] > 5000000) {
				$output="<p class='errore_modifica'>Il file caricato è troppo pesante</p>";
  				echo $output;
  				exit;
			}
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
			&& $imageFileType != "gif" ) {
				$output="<p class='errore_modifica'>Le immagini devono essere in formato JPG, PNG, JPEG o GIF</p>";
  				echo $output;
  				exit;
			}
        	if (!move_uploaded_file($_FILES["campo_img"]["tmp_name"], $target_file)){
    			$output = "<p class='errore_modifica'>Errore nel caricamento del file: " . $_FILES["campo_img"]["error"] . "</p>";
    			echo $output;
    			exit;
			}
			$stmt = mysqli_prepare($link, "UPDATE blog SET Immagine=?,Titolo=?,Descrizione=?,IdCategoria=? WHERE IdBlog = ?");
    		mysqli_stmt_bind_param($stmt, "sssii",$target_file, $nuovo_titolo, $nuova_desc, $id_nuova_cat, $blogId);
			if (mysqli_stmt_execute($stmt)) {
				echo "OK";
    		} else {
        		echo "Errore";
    		}
    		mysqli_stmt_close($stmt);
    	}else{
    		$stmt = mysqli_prepare($link, "UPDATE blog SET Titolo=?,Descrizione=?,IdCategoria=? WHERE IdBlog = ?");
    		mysqli_stmt_bind_param($stmt, "ssii", $nuovo_titolo, $nuova_desc, $id_nuova_cat, $blogId);
			if (mysqli_stmt_execute($stmt)) {
				echo "OK";
    		} else {
        		echo "Errore";
    		}
    		mysqli_stmt_close($stmt);
    	}
    }else {
    	echo "Richiesta fallita"; 
	}
}		
?>
