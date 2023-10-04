<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $id_utente = $_SESSION["session_utente"];
    $stmt_tuoi_blog = mysqli_prepare($link, "SELECT Titolo, Descrizione, Immagine FROM blog WHERE IdUtente=?");
    mysqli_stmt_bind_param($stmt_tuoi_blog, "i", $id_utente);
    mysqli_stmt_execute($stmt_tuoi_blog);
    $query_tuoi_blog = mysqli_stmt_get_result($stmt_tuoi_blog);
    $html = "";
    $html .="<p class='presentazione'>I Blog creati da te:</p>";
    $html .= "<div class='griglia_blog_creati'>";
    if (mysqli_num_rows($query_tuoi_blog) === 0) {
        $html .= "<p id='nessun_blog'>Ops! Nessun blog nei paraggi... </br> Che ne dici di creane uno? ;)</p>";
    }
    else{
        while ($row = mysqli_fetch_assoc($query_tuoi_blog)) {
            $imageData = $row['Immagine'];
            $descrizione = $row['Descrizione'];
            $Title = $row['Titolo'];
            if ($imageData == null) {
                $src_img = "foto/blog.png";
            } else {
                $base64Image = base64_encode($imageData);
                $src_img = "data:image/png;base64," . $base64Image;
            }
            $html .= "<div class='blog'>";
            $html .= "<img src='{$src_img}' alt='{$Title}'></img>";
            $html .= "<p>{$Title}</p>";
            $html .= "<p>{$descrizione}</p>";
            $html .= "<input type='button' value='Modifica'><input type='button' value='Elimina'>";
            $html .= "</div>";
        }
        mysqli_stmt_close($stmt_tuoi_blog);
    }
    $html .= "</div>";
    $stmt_blog_coaut = mysqli_prepare($link, "SELECT Titolo, Descrizione, Immagine FROM blog WHERE IdBlog IN (SELECT IdBlog FROM coautore WHERE IdUtente=?)");
    mysqli_stmt_bind_param($stmt_blog_coaut, "i", $id_utente);
    mysqli_stmt_execute($stmt_blog_coaut);
    $query_blog_coaut = mysqli_stmt_get_result($stmt_blog_coaut);
    $html .="<p class='presentazione_coautore'>I Blog di cui sei coautore:</p>";
    $html .= "<div class='griglia_blog_coautore'>";
    if (mysqli_num_rows($query_blog_coaut) === 0) {
        $html .= "<p id='nessun_blog'>Ops! Nessun blog nei paraggi... </br> Aspetta che qualcuno ti renda coautore del suo blog! </p>";
    }
    else{
        while ($row = mysqli_fetch_assoc($query_blog_coaut)) {
            $imageData = $row['Immagine'];
            $descrizione = $row['Descrizione'];
            $Title = $row['Titolo'];
            if ($imageData == null) {
                $src_img = "foto/blog.png";
            } else {
                $base64Image = base64_encode($imageData);
                $src_img = "data:image/png;base64," . $base64Image;
            }
            $html .= "<div class='blog'>";
            $html .= "<img src='{$src_img}' alt='{$Title}'></img>";
            $html .= "<p>{$Title}</p>";
            $html .= "<p>{$descrizione}</p>";
            $html .= "</div>";
        }
        mysqli_stmt_close($stmt_blog_coaut);
    }
    $html .= "</div>";
    echo $html;
}
?>
