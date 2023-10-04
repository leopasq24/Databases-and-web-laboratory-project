<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
  $query = $_GET["query"];
  $nomi = explode(" ", $query);
  $idutente = $_SESSION["session_utente"];
  foreach ($nomi as $key => $value) {
    $stmt_sql = mysqli_prepare($link, "SELECT Username FROM utente WHERE Username LIKE ? AND idUtente != $idutente");
    $searchQuery = "%" . $value . "%";
    mysqli_stmt_bind_param($stmt_sql,"s", $searchQuery);
    mysqli_stmt_execute($stmt_sql);
    $result_set=mysqli_stmt_get_result($stmt_sql);
    $html = "<ul>";
    if (mysqli_num_rows($result_set) > 0) {
      while ($row = mysqli_fetch_assoc($result_set)) {
        $html .= "<li>" . $row["Username"] . "</li>";
      }
    } else {
      $html .= "<li>Nessun risultato</li>";
    }
    mysqli_stmt_close($stmt_sql);
    $html .= "</ul>";
  }
  echo $html;
}
?>
