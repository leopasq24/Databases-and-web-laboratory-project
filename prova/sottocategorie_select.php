<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $nome_cat = $_GET['nome_cat'];
    $query = "SELECT Nome FROM categoria, contiene WHERE categoria.IdCategoria = contiene.IdSottocategoria AND contiene.IdSopracategoria = (SELECT IdCategoria FROM categoria WHERE Nome = ?)";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "s", $nome_cat);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $html = "";
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $subcategoryName = $row['Nome'];
            $html .= "<option value='{$subcategoryName}'>{$subcategoryName}</option>";
    }
        echo $html;
    } else {
        echo "<option value=''>nessuna sottocategoria trovata</option>";
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
}
?>