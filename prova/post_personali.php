<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $id_utente = $_SESSION["session_utente"];
    $query_post = mysqli_query($link,"SELECT post.Titolo AS Titolo_post, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente AND blog.IdUtente = $id_utente ORDER BY Data DESC LIMIT 7");
    $html = "";
    $query_results = mysqli_fetch_assoc($query_post);
    if(empty($query_results)){
        $html .= "<p id='nessun_blog'>Non hai ancora nessun blog :( <a href='i_tuoi_blog.php'>Creane uno!</a></p>";    
    }
    else{
        while($row = mysqli_fetch_assoc($query_post)){
    	   $image_post_Data = $row['immagine_post'];
    	   $title = $row['Titolo_post'];
            $testo = $row['Testo'];
            $autore_post = $row['Username'];
            $blog = $row['Argomento'];
            $image_blog_Data = $row['immagine_blog'];
            $data = $row['Data'];
            $ora = $row['Ora'];
            if($image_blog_Data='NULL'){
                $src_img="foto/blog.png";
            }
            else{
                $base64Image_blog_Data = base64_encode($image_blog_Data);
                $src_img="data:image/png;base64".$base64Image_blog_Data;
            }   
    	    if($imageData='NULL'){
    		   $html .= "<div class='nuovo-post'>";
    		   $html .= "<img src={$src_img} alt='$blog'></img>";
    		   $html .= "<p>$blog</p>";
        	   $html .= "<h4>$title</h4>";
        	   $html .= "<p>$testo</p>";
               $html .= "<p class='dataeora'>$data $ora</p>";
        	   $html .= "</div>";
    	    }
    	    else{
    		   $base64Image_post_Data = base64_encode($image_post_Data);
        	   $html .= "<div class='nuovo-post'>";
        	   $html .= "<p>$blog</p>";
        	   $html .= "<h4>$title</h4>";
        	   $html .= "<img src='data:image/png;base64, $base64Image_post_Data' alt='$title'></img>";
        	   $html .= "<p>$testo</p>";
                $html .= "<p class='dataeora'>$data $ora</p>";
        	   $html .= "</div>";
    	    }
        }
    }
    $html .= "</div>";
    echo $html;
}
?>
