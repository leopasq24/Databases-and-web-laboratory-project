<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $query_blog_popolari = mysqli_query($link,"SELECT Immagine, Titolo FROM blog WHERE IdBlog IN (SELECT IdBlog FROM post WHERE IdPost IN (SELECT codice FROM post_popolari ORDER BY conta DESC)) LIMIT 7");
    $html = "<div class='blog-pop'>";
    while($row = mysqli_fetch_assoc($query_blog_popolari)){
        $imageData = $row['Immagine'];
        if($imageData='NULL'){
            $src_img="foto/blog.png";
        }
        else{
            $base64Image = base64_encode($imageData);
            $src_img="data:image/png;base64".$base64Image;
        }
        $base64Image = base64_encode($imageData);
        $Title = $row['Titolo'];
        $html .= "<div class='blog-pop'>";
        $html .= "<img src={$src_img} alt='$Title'></img>";
        $html .= "<p>$Title</p>";
        $html .= "</div>";
    }
    $html .= "</div>";
    echo $html;
}
?>