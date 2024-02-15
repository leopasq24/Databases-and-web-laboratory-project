<?php
session_start();
include_once("connect.php");

if (isset($_GET['numero'])) {
    $numeroBlog = (int)$_GET['numero'];
} else {
    $numeroBlog = 0;
}

if (isset($_GET['idCategoria'])) {
    $stmt_tutte_categorie = mysqli_prepare($link, "SELECT IdBlog, Titolo, Descrizione, Immagine, Username FROM blog, utente WHERE IdCategoria = ? ORDER BY IdBlog DESC LIMIT ?, 9");
    mysqli_stmt_bind_param($stmt_tutte_categorie, "ii", $idCategoria, $numeroBlog);
    mysqli_stmt_execute($stmt_tutte_categorie);
    $query_tutte_categorie = mysqli_stmt_get_result($stmt_tutte_categorie);
    $html = "<div class='griglia_blog_creati'>";

    $nessunBlog = (mysqli_num_rows($query_tutte_categorie) === 0);
    if ($nessunBlog) {
        echo "Nessun Blog";
        exit;
    } else {
        while ($row = mysqli_fetch_assoc($query_tutte_categorie)) {
            $idblog = $row['IdBlog'];
            $src_img = $row['Immagine'];
            $descrizione = $row['Descrizione'];
            $Title = $row['Titolo'];
            $autore = $row['Username'];
            if ($src_img == null) {
                $src_img = "foto/blog.png";
            }
            $html .= "<div class='blog' data-blog-title='{$Title}' data-blog-id='{$idblog}'>";
            $html .= "<img src='{$src_img}' alt='{$Title}'></img>";
            $html .= "<p class='titolo_tuoi_blog'>{$Title}</p>";
            $html .= "<p class='descrizione_tuoi_blog'>{$descrizione}</p>";
            $html .= "<p class='autore_tuoi_blog'>{$autore}</p>";
            $html .= "</div>";
        }
        mysqli_stmt_close($stmt_tutte_categorie);
    }
    $html .= "</div>";
    echo $html;
}
?>
