<?php
session_start();
include_once("connect.php");

if (!isset($_SESSION["session_utente"]) || empty($_SESSION["session_utente"])) {
  session_unset();
  session_destroy();
  header("Location: registrazione.php");
  exit;
}

$id_utente = $_SESSION["session_utente"];
$stmt_premium = mysqli_prepare($link, "SELECT Premium FROM utente WHERE IdUtente=?");
mysqli_stmt_bind_param($stmt_premium, "i", $id_utente);
mysqli_stmt_execute($stmt_premium);
$results_premium = mysqli_stmt_get_result($stmt_premium);
if(mysqli_fetch_assoc($results_premium)["Premium"]==0){
  $button = "<a href='premium.php'><input type='button' value='Premium'></a>";
}else{
    $button = "<a href='insight.php'><input type='button' value='Insight'></a>";
}
mysqli_stmt_close($stmt_premium);
?>
<html lang="it" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title> I Tuoi Blog </title>
    <link rel="stylesheet" href="stile_index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src = "js/i_tuoi_blog.js"></script>
   </head>
<body id="body_tuoi_blog">
  <header id="header_tuoi_blog">
    <nav class="navbar">
      <div class="logo"><a href="home.php">Bluggle</a></div>
      <ul class="menu">
        <li><a href="home.php">Home</a></li>
        <li><a href="tutti_i_blog.php"> Tutti i Blog</a></li>
        <li><a href="i_tuoi_blog.php"> I tuoi Blog</a></li>
        <li><a href="account.php">Account</a></li>
        <li><a href="info.php">Info</a></li>
      </ul>
      <div class="buttons">
        <?php echo $button ?>
        <a href="logout.php"><input type="button" value="Logout"></a>
      </div>
    </nav>
    <div class="tuoi_blog">
      <input type="button" id="crea_blog" value="Crea un nuovo Blog">
      <form id="form_crea_blog" action="crea_blog.php" method="post" enctype="multipart/form-data" hidden>
        <div class="field">
          <label>Titolo</label></br>
          <input type="text" name="titolo_blog" id="titolo_blog">
        </div>
        <div class="field">
          <label>Descrizione</label></br>
          <input type="text" name="descrizione_blog" id="descrizione_blog">
        </div>
        <div class="field">
          <label>Immagine</label></br>
          <input type="file" id="immagine_blog" name="immagine_blog">
        </div>
        <div class="field">
          <label>Categoria</label></br>
          <select id="categoria_blog" name="categoria_blog"></select>
        </div>
        <div class="field">
          <label>Seleziona uno o più coautori </br>(separandoli con uno spazio)</label></br>
          <input type="text" name="coautori" id="coautori">
          <div id="searchResults"></div>
        </div>
        <div class="field">
          <input type="submit" value="Crea">
        </div>
        </br>
        <p id="error_message"></p>
      </form>
      <div class="griglia_blog"></div>
  </div>
  </header>
</body>
</html>
