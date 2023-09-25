<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $query_categorie = mysqli_query($link,"SELECT Icona, Nome FROM categoria WHERE IdCategoria NOT IN (SELECT IdSottocategoria FROM contiene) LIMIT 7");
    $html = "";
    while($row = mysqli_fetch_assoc($query_categorie)){
        $iconData = $row['Icona'];
        $base64Icon = base64_encode($iconData);
        $categoryName = $row['Nome'];
        $html .= "<div class='macrocat'>";
        $html .= "<img src='data:image/png;base64, $base64Icon' alt='$categoryName'></img>";
        $html .= "<p id='macrocat_nome'>$categoryName</p>";
        $html .= "<div class='micro-categoria'></div>";
        $html .= "</div>";
    }
    $html .= "</div>";
    echo $html;
}
?>
