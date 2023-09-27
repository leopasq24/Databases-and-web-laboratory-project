<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $id_utente = $_SESSION["session_utente"];
    $stmt = mysqli_prepare($link, "SELECT Titolo, Descrizione, Immagine FROM blog WHERE IdUtente=?");
    mysqli_stmt_bind_param($stmt, "i", $id_utente);
    mysqli_stmt_execute($stmt);
    $query_blog = mysqli_stmt_get_result($stmt);
    $html = "<div class='griglia_blog_creati'>";
    if (mysqli_num_rows($query_blog) === 0) {
        $html .= "<p id='nessun_blog'>Non hai ancora nessun blog :( Creane uno!</p>";
    }
    else{
        while ($row = mysqli_fetch_assoc($query_blog)) {
            $imageData = $row['Immagine'];
            $descrizione = $row['Descrizione'];
            $Title = $row['Titolo'];
            if ($imageData==null) {
                $src_img = "foto/blog.png";
            } else {
                $base64Image = base64_encode($imageData);
                $src_img = "data:image/png;base64," . $base64Image;
            }
            $html .= "<div class='blog'>";
            $html .= "<img src='{$src_img}' alt='{$Title}'></img>";
            $html .= "<p>{$Title}</p>";
            $html .= "<p>{$descrizione}</p>";
            $html .= "<a href='#''><input type='button' value='Modifica Blog'></a><a href='#'><input type='button' value='Elimina Blog'></a>";
            $html .= "</div>";
        }
    }
    $html .= "</div>";
    echo $html;
}
?>
