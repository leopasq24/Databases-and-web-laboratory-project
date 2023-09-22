<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $query_post = mysqli_query($link,"SELECT post.Titolo AS Titolo_post, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, Username FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente ORDER BY Data DESC LIMIT 7");
    $html = "";
    while($row = mysqli_fetch_assoc($query_post)){
    	$imageData = $row['immagine_post'];
    	$title = $row['Titolo_post'];
        $testo = $row['Testo'];
        $autore_post = $row['Username'];
        $blog = $row['Argomento'];
    	if($imageData='NULL'){
    		$html .= "<div class='nuovo-post'>";
    		$html .= "<p>$blog</p>";
        	$html .= "<h3>$title</h3>";
        	$html .= "<p>$testo</p>";
        	$html .= "</div>";
    	}
    	else{
    		$base64Image = base64_encode($iconData);
        	$html .= "<div class='nuovo-post'>";
        	$html .= "<h3>$title</h3>";
        	$html .= "<img src='data:image/png;base64, $base64Image' alt='$title'></img>";
        	$html .= "<p>$testo</p>";
        	$html .= "</div>";
    	}
    }
    $html .= "</div>";
    echo $html;
}
?>