<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $html = "<optgroup label='Categorie'>";
    $stmt_categories = mysqli_prepare($link, "SELECT Icona, Nome FROM categoria WHERE IdCategoria NOT IN (SELECT IdSottocategoria FROM contiene)");
    mysqli_stmt_execute($stmt_categories);
    $result_categories = mysqli_stmt_get_result($stmt_categories);

    while ($row_cat = mysqli_fetch_assoc($result_categories)) {
        $nome_cat = $row_cat['Nome'];

        $html .= "<option value='{$nome_cat}'>{$nome_cat}</option>";
    }
    mysqli_stmt_close($stmt_categories);
    $html .= "</optgroup>";
    $html .= "<optgroup label='Sottocategorie'>";
    $stmt_subcategories = mysqli_prepare($link, "SELECT Icona, Nome FROM categoria WHERE IdCategoria IN (SELECT IdSottocategoria FROM contiene)");
    mysqli_stmt_execute($stmt_subcategories);
    $result_subcategories = mysqli_stmt_get_result($stmt_subcategories);

    while ($row_cat = mysqli_fetch_assoc($result_subcategories)) {
        $nome_cat = $row_cat['Nome'];
        $html .= "<option value='{$nome_cat}'>{$nome_cat}</option>";
    }
    mysqli_stmt_close($stmt_subcategories);
    $html .= "</optgroup>";
    echo $html;
}
?>
