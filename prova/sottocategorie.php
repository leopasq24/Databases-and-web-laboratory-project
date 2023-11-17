<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $nome_cat = $_GET['key1'];
    $html = "";
    $stmt_subcategories = mysqli_prepare($link, "SELECT Nome FROM categoria, contiene WHERE categoria.IdCategoria = contiene.IdSottocategoria AND contiene.IdSopracategoria = (SELECT IdCategoria FROM categoria WHERE Nome = ?)");
    
    mysqli_stmt_bind_param($stmt_subcategories, "s", $nome_cat);
    mysqli_stmt_execute($stmt_subcategories);
    $result_subcategories = mysqli_stmt_get_result($stmt_subcategories);
    
    while ($row = mysqli_fetch_assoc($result_subcategories)) {
        $subcategoryName = $row['Nome'];
        $html .= "<div class='microcat'>";
        $html .= "<p>$subcategoryName</p>";
        $html .= "</div>";
    }
    
    $html .= "</div>";
    mysqli_stmt_close($stmt_subcategories);
    echo $html;  
}
?>
