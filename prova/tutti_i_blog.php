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
    <title> Tutti i Blog </title>
    <link rel="stylesheet" href="stile_index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src = "js/tutti_i_blog.js"></script>
  </head>
<body id="body_tutti_i_blog">
  <header id="header_tutti_i_blog">
    <nav class="navbar">
      <div class="logo"><a href="home.php">Bluggle</a></div>
      <ul class="menu">
        <li><a href="home.php">Home</a></li>
        <li><a href="tutti_i_blog.php">Tutti i Blog</a></li>
        <li><a href="i_tuoi_blog.php">I tuoi Blog</a></li>
        <li><a href="account.php">Account</a></li>
        <li><a href="info.php">Info</a></li>
      </ul>
      <div class="buttons">
        <?php echo $button ?>
        <a href="logout.php"><input type="button" value="Logout"></a>
      </div>
    </nav>
    <div class="tutti_i_blog">
    <div class="ordina_blog">
      <p>Ordina per: &#160 
      <input type="radio" id="blog_recenti" name="blog_recenti" value="Recenti"><label for="blog_recenti" id="blog_recenti" class="selected">Recenti</label>
      <input type="radio" id="blog_popolari" name="blog_popolari" value="Popolari"><label for="blog_popolari" id="blog_popolari">Popolari</label>
      <input type="radio" id="blog_per_utente" name="blog_per_utente" value="Per Utente"><label for="blog_per_utente" id="blog_per_utente">Autore</label>
      </p>
    </div>
      <div class="griglia_blog">
        <div class="blog_rec"></div>
        <div class="blog_pop" hidden></div>
        <div class="blog_per_u" hidden></div>
      </div>
      <input type="button" id="caricablog" value="Mostra di più">
    </div>
  </header>
</body>
</html> 
