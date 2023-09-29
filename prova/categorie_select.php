<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $query_categorie = mysqli_query($link,"SELECT Icona, Nome FROM categoria WHERE IdCategoria NOT IN (SELECT IdSottocategoria FROM contiene)");
    $html = "";
    while($row = mysqli_fetch_assoc($query_categorie)){
        $categoryName = $row['Nome'];
        $html .= "<option value='{$categoryName}'>{$categoryName}</option>";
    }
    echo $html;
}
?>