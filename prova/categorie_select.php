<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $query_categorie = mysqli_query($link,"SELECT Icona, Nome FROM categoria WHERE IdCategoria NOT IN (SELECT IdSottocategoria FROM contiene)");
    $query_sottocategorie = mysqli_query($link,"SELECT Icona, Nome FROM categoria WHERE IdCategoria IN (SELECT IdSottocategoria FROM contiene)");
    $html = "<optgroup label='Categorie'>";
    while($row_cat = mysqli_fetch_assoc($query_categorie)){
        $categoryName = $row_cat['Nome'];
        $html .= "<option value='{$categoryName}'>{$categoryName}</option>";
    }
    $html .="</optgroup>";
    $html .= "<optgroup label='Sottocategorie'>";
    while($row_subcat = mysqli_fetch_assoc($query_sottocategorie)){
        $subcategoryName = $row_subcat['Nome'];
        $html .= "<option value='{$subcategoryName}'>{$subcategoryName}</option>";
    }
    $html .="</optgroup>";
    echo $html;
}
?>
