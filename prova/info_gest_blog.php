<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    if (isset($_GET["idBlog"])){
        echo "ciao";
        /*$id_utente = $_SESSION["session_utente"];
        $id_blog = $_GET["idBlog"];
        $stmt_info_blog = mysqli_prepare($link, "SELECT blog.Immagine, blog.Titolo, categoria.Nome, blog.Descrizione FROM blog, categoria WHERE blog.IdUtente=? AND blog.IdBlog=? AND blog.IdCategoria = categoria.IdCategoria");
    mysqli_stmt_bind_param($stmt_info_blog, "ii", $id_utente, $id_blog);
    mysqli_stmt_execute($stmt_info_blog);
    $query_info_blog = mysqli_stmt_get_result($stmt_info_blog);
    $html = "";
    $html .= "<div class='info-blog'>";
    while ($row = mysqli_fetch_assoc($query_info_blog)) {
        $imageData = $row['Immagine'];
        $titolo = $row['Titolo'];
        $categoria = $row['Nome'];
        $descrizione = $row['Descrizione'];
        if ($imageData == null) {
            $src_img = "foto/blog.png";
        } else {
            $base64Image = base64_encode($imageData);
            $src_img = "data:image/png;base64," . $base64Image;
        }
        $html .= "<img src='{$src_img}' alt='{$titolo}'></img>";
        $html .= "<p class='titolo_blog'>{$titolo}</p>";
        $html .= "<p class='cat_blog'>{$categoria}</p>";
        $html .= "<p class='descrizione_blog'>{$descrizione}</p>";
        $html .= "</div>";*/
    }
    else {
        echo "ciaone";
    }
    /*mysqli_stmt_close($stmt_info_blog);
    }
$html .= "</div>";
echo $html;*/
?>