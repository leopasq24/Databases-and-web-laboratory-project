<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $nome_cat = $_GET['key1'];
    $query_sottocategorie = mysqli_query($link,"SELECT Nome FROM categoria, contiene WHERE categoria.IdCategoria = contiene.IdSottocategoria AND contiene.IdSopracategoria = (SELECT IdCategoria FROM categoria WHERE Nome = '$nome_cat')");
    $html = "";
    while($row = mysqli_fetch_assoc($query_sottocategorie)){
        $subcategoryName = $row['Nome'];
        $html .= "<div class='microcat'>";
        $html .= "<p>$subcategoryName</p>";
        $html .= "</div>";
    }
    $html .= "</div>";
    echo $html;
}
?>