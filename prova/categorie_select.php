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
        $categoryName = htmlspecialchars($row_cat['Nome'], ENT_QUOTES, 'UTF-8');
        $html .= "<option value='{$categoryName}'>{$categoryName}</option>";
    }
    mysqli_stmt_close($stmt_categories);
    $html .= "</optgroup>";
    $html .= "<optgroup label='Sottocategorie'>";
    $stmt_subcategories = mysqli_prepare($link, "SELECT Icona, Nome FROM categoria WHERE IdCategoria IN (SELECT IdSottocategoria FROM contiene)");
    mysqli_stmt_execute($stmt_subcategories);
    $result_subcategories = mysqli_stmt_get_result($stmt_subcategories);

    while ($row_subcat = mysqli_fetch_assoc($result_subcategories)) {
        $subcategoryName = htmlspecialchars($row_subcat['Nome'], ENT_QUOTES, 'UTF-8');
        $html .= "<option value='{$subcategoryName}'>{$subcategoryName}</option>";
    }
    mysqli_stmt_close($stmt_subcategories);
    $html .= "</optgroup>";
    echo $html;
}
?>
