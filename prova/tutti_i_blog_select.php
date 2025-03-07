<?php
session_start();
include_once("connect.php");

if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $id_utente = $_SESSION["session_utente"];
    if (isset($_GET['numero'])) {
        $numeroBlog = (int)$_GET['numero'];
    } else {
        $numeroBlog = 0;
    }

    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : "Recenti";

    if ($tipo === "Recenti") {
        $stmt_tutti_i_blog = mysqli_prepare($link, "SELECT IdBlog, Titolo, Descrizione, Immagine, Username FROM blog, utente WHERE blog.IdUtente=utente.IdUtente ORDER BY IdBlog DESC LIMIT ?, 9");
    } elseif ($tipo === "Popolari") {
        $stmt_tutti_i_blog = mysqli_prepare($link, "SELECT IdBlog, Titolo, Descrizione, Immagine, Username FROM blog, utente WHERE blog.IdUtente=utente.IdUtente AND IdBlog IN (SELECT IdBlog FROM post WHERE IdPost IN (SELECT IdPost FROM numero_feedback_positivi ORDER BY numerofeedbackpositivi DESC)) LIMIT ?, 9");
    } elseif ($tipo === "Autore") {
        $stmt_tutti_i_blog = mysqli_prepare($link, "SELECT IdBlog, Titolo, Descrizione, Immagine, Username FROM blog, utente WHERE blog.IdUtente=utente.IdUtente ORDER BY utente.Username ASC LIMIT ?, 9");
    }
    mysqli_stmt_bind_param($stmt_tutti_i_blog, "i", $numeroBlog);
    mysqli_stmt_execute($stmt_tutti_i_blog);
    $query_tutti_i_blog = mysqli_stmt_get_result($stmt_tutti_i_blog);
    $html = "<div class='griglia_blog_creati'>";

    $nessunBlog = (mysqli_num_rows($query_tutti_i_blog) === 0);
    if ($nessunBlog) {
        echo "Nessun Blog";
        exit;
    } else {
        while ($row = mysqli_fetch_assoc($query_tutti_i_blog)) {
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
            $html .= "<p class='autore_tuoi_blog'>di <span>{$autore}</span></p>";
            $html .= "</div>";
        }
        mysqli_stmt_close($stmt_tutti_i_blog);
    }

    $html .= "</div>";
    echo $html;
}
?>
