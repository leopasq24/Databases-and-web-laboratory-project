<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $id_utente = $_SESSION["session_utente"];
    $stmt_tuoi_blog = mysqli_prepare($link, "SELECT IdBlog, Titolo, Descrizione, Immagine FROM blog WHERE IdUtente=?");
    mysqli_stmt_bind_param($stmt_tuoi_blog, "i", $id_utente);
    mysqli_stmt_execute($stmt_tuoi_blog);
    $query_tuoi_blog = mysqli_stmt_get_result($stmt_tuoi_blog);
    $html = "";
    $html .="<p class='presentazione'>I Blog creati da te:</p>";
    $html .= "<div class='griglia_blog_creati'>";
    if (mysqli_num_rows($query_tuoi_blog) === 0) {
        $html .= "<p id='nessun_blog'><img src='foto/bolle.png' alt='bolle'></img></br>Ops! Nessun blog nei paraggi... </br> Aspetta che qualcuno ti renda coautore del suo blog! </p>";
    }
    else{
        while ($row = mysqli_fetch_assoc($query_tuoi_blog)) {
            $idblog = $row['IdBlog'];
            $src_img = $row['Immagine'];
            $descrizione = $row['Descrizione'];
            $Title = $row['Titolo'];
            if ($src_img == null) {
                $src_img = "foto/blog.png";
            }
            $html .= "<div class='blog' data-blog-title='{$Title}' data-blog-id='{$idblog}'>";
            $html .= "<img src='{$src_img}' alt='{$Title}'></img>";
            $html .= "<p class='titolo_tuoi_blog'>{$Title}</p>";
            $html .= "<p class='descrizione_tuoi_blog'>{$descrizione}</p>";
            $html .= "</div>";
        }
        mysqli_stmt_close($stmt_tuoi_blog);
    }
    $html .= "</div>";
    $stmt_blog_coaut = mysqli_prepare($link, "SELECT IdBlog, Titolo, Descrizione, Immagine FROM blog WHERE IdBlog IN (SELECT IdBlog FROM coautore WHERE IdUtente=?)");
    mysqli_stmt_bind_param($stmt_blog_coaut, "i", $id_utente);
    mysqli_stmt_execute($stmt_blog_coaut);
    $query_blog_coaut = mysqli_stmt_get_result($stmt_blog_coaut);
    $html .="<p class='presentazione_coautore'>I Blog di cui sei coautore:</p>";
    $html .= "<div class='griglia_blog_coautore'>";
    if (mysqli_num_rows($query_blog_coaut) === 0) {
        $html .= "<p id='nessun_blog'><img src='foto/bolle.png' alt='bolle'></img></br>Ops! Nessun blog nei paraggi... </br> Aspetta che qualcuno ti renda coautore del suo blog! </p>";
    }
    else{
        while ($row = mysqli_fetch_assoc($query_blog_coaut)) {
            $idblog = $row['IdBlog'];
            $src_img = $row['Immagine'];
            $descrizione = $row['Descrizione'];
            $Title = $row['Titolo'];
            if ($src_img == null) {
                $src_img = "foto/blog.png";
            }
            $html .= "<div class='blog' data-blog-title='{$Title}' data-blog-id='{$idblog}'>";
            $html .= "<img src='{$src_img}' alt='{$Title}'></img>";
            $html .= "<p class='titolo_tuoi_blog'>{$Title}</p>";
            $html .= "<p class='descrizione_tuoi_blog'>{$descrizione}</p>";
            $html .= "</div>";
        }
        mysqli_stmt_close($stmt_blog_coaut);
    }
    $html .= "</div>";
    echo $html;
}
?>
