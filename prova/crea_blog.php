<?php
include_once("connect.php");
session_start();

$blogname = trim($_POST["titolo_blog"]);
$desc = trim($_POST["descrizione_blog"]);
$img = $_FILES["immagine_blog"]["tmp_name"];
$cat = trim($_POST["categoria_blog"]);
$coautori = trim($_POST["coautori"]);
$idutente = $_SESSION["session_utente"];
$i=0;

if (isset($_POST["sottocategoria_blog"])) {
    $subcat = trim($_POST["sottocategoria_blog"]);
    $stmt_subcat=mysqli_prepare($link, "SELECT IdCategoria FROM categoria WHERE Nome=?");
	mysqli_stmt_bind_param($stmt_subcat,"s",$subcat);
	mysqli_stmt_execute($stmt_subcat);
	$result_subcat=mysqli_stmt_get_result($stmt_subcat);
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
if (!move_uploaded_file($_FILES["immagine_blog"]["tmp_name"], $img)) {
	if( $_FILES["immagine_blog"]["error"]==4){
		$img=NULL;
	}
    else{
    	echo "File upload failed.Error". $_FILES["immagine_blog"]["error"];
    	$i=$i+1;
    }
}
if(strlen($cat) ==0) {
	echo("Scegliere la categoria");
	$i=$i+1;
}




if($i==0){
	if($img != NULL){
		$stmt_blogname=mysqli_prepare($link, "INSERT INTO blog(IdBlog,Titolo, Descrizione, Immagine, IdCategoria,IdUtente) VALUES (NULL,?,?,?,?,?)");
		$row = mysqli_fetch_assoc($result_subcat);
    	$idsubcat = $row['IdCategoria'];
		mysqli_stmt_bind_param($stmt_blogname,"ssbii", $blogname, $desc, $img, $idsubcat, $idutente);
		mysqli_stmt_execute($stmt_blogname);
		mysqli_stmt_close($stmt_blogname);
		echo "OK";
	}else{
		$stmt_blogname=mysqli_prepare($link, "INSERT INTO blog(IdBlog,Titolo, Descrizione, Immagine, IdCategoria,IdUtente) VALUES (NULL,?,?,NULL,?,?)");
		$row = mysqli_fetch_assoc($result_subcat);
    	$idsubcat = $row['IdCategoria'];
		mysqli_stmt_bind_param($stmt_blogname,"ssii", $blogname, $desc, $idsubcat, $idutente);
		mysqli_stmt_execute($stmt_blogname);
		mysqli_stmt_close($stmt_blogname);
		echo "OK";
	}
	
}
?>
