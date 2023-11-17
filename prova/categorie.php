<?php
session_start();
include_once("connect.php");

if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $html = "";
    $stmt_categories = mysqli_prepare($link, "SELECT Icona, Nome FROM categoria WHERE IdCategoria NOT IN (SELECT IdSottocategoria FROM contiene) LIMIT 7");
    
    mysqli_stmt_execute($stmt_categories);
    $result_categories = mysqli_stmt_get_result($stmt_categories);
    
    while ($row = mysqli_fetch_assoc($result_categories)) {
        $iconData = $row['Icona'];
        $base64Icon = base64_encode($iconData);
        $categoryName = $row['Nome'];
        
        $html .= "<div class='macrocat'>";
        $html .= "<img src='data:image/png;base64, $base64Icon' alt='$categoryName'></img>";
        $html .= "<p id='macrocat_nome'>{$categoryName}</p><p id='freccia'>&#8250;</p>";
        $html .= "<div class='micro-categoria'></div>";
        $html .= "</div>";
    }
    
    $html .= "</div>";
    mysqli_stmt_close($stmt_categories);
    echo $html;   
}
?>
