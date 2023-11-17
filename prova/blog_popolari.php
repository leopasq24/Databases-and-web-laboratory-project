<?php
session_start();
include_once("connect.php");

if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $html = "<div class='blog-pop'>";
    $stmt_popular_blogs = mysqli_prepare($link, "SELECT Immagine, Titolo FROM blog WHERE IdBlog IN (SELECT IdBlog FROM post WHERE IdPost IN (SELECT codice FROM post_popolari ORDER BY conta DESC)) LIMIT 7");
    
    mysqli_stmt_execute($stmt_popular_blogs);
    $result_popular_blogs = mysqli_stmt_get_result($stmt_popular_blogs);
    
    while ($row = mysqli_fetch_assoc($result_popular_blogs)) {
        $imageData = $row['Immagine'];
        if ($imageData === null) {
            $src_img = "foto/blog.png";
        }else{
            $src_img = $imageData;
        }
        $Title = $row['Titolo'];
        
        $html .= "<div class='blog-pop'>";
        $html .= "<img src='{$src_img}' alt='{$Title}'></img>";
        $html .= "<p>{$Title}</p>";
        $html .= "</div>";
    }
    
    $html .= "</div>";
    mysqli_stmt_close($stmt_popular_blogs);
    echo $html;
}
?>
