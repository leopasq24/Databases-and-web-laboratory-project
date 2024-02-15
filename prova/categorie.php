<?php
session_start();
include_once("connect.php");

if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $html = "";
    $stmt_categorie = mysqli_prepare($link, "SELECT IdCategoria, Icona, Nome FROM categoria WHERE IdCategoria NOT IN (SELECT IdSottocategoria FROM contiene) LIMIT 7");
    
    mysqli_stmt_execute($stmt_categorie);
    $result_categorie = mysqli_stmt_get_result($stmt_categorie);
    
    while ($row = mysqli_fetch_assoc($result_categorie)) {
        $iconData = $row['Icona'];
        $base64Icon = base64_encode($iconData);
        $nome_categoria = $row['Nome'];
        $id_categoria =  $row['IdCategoria'];

        $html .= "<div class='macrocat' data-cat-id='$id_categoria'>";
        $html .= "<img src='data:image/png;base64, $base64Icon' alt='$nome_categoria'></img>";
        $html .= "<p id='macrocat_nome'>{$nome_categoria}</p><p id='freccia'>&#8250;</p>";
        $html .= "<div class='micro-categoria'></div>";
        $html .= "</div>";
    }
    
    $html .= "</div>";
    mysqli_stmt_close($stmt_categorie);
    echo $html;   
}
?>
