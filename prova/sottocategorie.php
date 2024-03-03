<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    if(isset( $_GET['key1'])){
        $id_cat = $_GET['key1'];
        $html = "";
        $stmt_sottocategorie = mysqli_prepare($link, "SELECT IdCategoria, Nome FROM categoria, contiene WHERE categoria.IdCategoria = contiene.IdSottocategoria AND contiene.IdSopracategoria = ?");
    
        mysqli_stmt_bind_param($stmt_sottocategorie, "i", $id_cat);
        mysqli_stmt_execute($stmt_sottocategorie);
        $results_sottocategorie = mysqli_stmt_get_result($stmt_sottocategorie);
    
        while ($row = mysqli_fetch_assoc($results_sottocategorie)) {
            $nome_cat = $row['Nome'];
            $id_categoria =  $row['IdCategoria'];
            $html .= "<div class='microcat' data-cat-id='$id_categoria'>";
            $html .= "<p>$nome_cat</p>";
            $html .= "</div>";
        }
    
        $html .= "</div>";
        mysqli_stmt_close($stmt_sottocategorie);
        echo $html; 
    }else{
        $html .= "<p>Categoria non specificata</p>";
        echo $html;
    }
}
?>
