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

$stmt_tuoi_blog = mysqli_prepare($link, "SELECT IdBlog, Titolo, Descrizione, Immagine FROM blog WHERE IdUtente=?");
mysqli_stmt_bind_param($stmt_tuoi_blog, "i", $id_utente);
mysqli_stmt_execute($stmt_tuoi_blog);
$query_tuoi_blog = mysqli_stmt_get_result($stmt_tuoi_blog);
$html = "";
$html .="<p class='presentazione'>I Blog creati da te:</p>";
$html .= "<div class='griglia_blog_creati'>";
if (mysqli_num_rows($query_tuoi_blog) === 0) {
    $html .= "<p id='nessun_blog'><img src='foto/bolle.png' alt='bolle'></img></br>Ops! Nessun blog nei paraggi... </br> Aspetta che qualcuno ti renda coautore del suo blog! </p>";
}
else{
   while ($row = mysqli_fetch_assoc($query_tuoi_blog)) {
      $idblog = $row['IdBlog'];
      $src_img = $row['Immagine'];
      $descrizione = $row['Descrizione'];
      $Title = $row['Titolo'];
      if ($src_img == null) {
        $src_img = "foto/blog.png";
      }
      $html .= "<div class='blog' data-blog-title='{$Title}' data-blog-id='{$idblog}'>";
      $html .= "<img src='{$src_img}' alt='{$Title}'></img>";
      $html .= "<p class='titolo_tuoi_blog'>{$Title}</p>";
      $html .= "<p class='descrizione_tuoi_blog'>{$descrizione}</p>";
      $html .= "</div>";
      }
    mysqli_stmt_close($stmt_tuoi_blog);
}
$html .= "</div>";
$stmt_blog_coaut = mysqli_prepare($link, "SELECT IdBlog, Titolo, Descrizione, Immagine FROM blog WHERE IdBlog IN (SELECT IdBlog FROM coautore WHERE IdUtente=?)");
mysqli_stmt_bind_param($stmt_blog_coaut, "i", $id_utente);
mysqli_stmt_execute($stmt_blog_coaut);
$query_blog_coaut = mysqli_stmt_get_result($stmt_blog_coaut);
$html .="<p class='presentazione_coautore'>I Blog di cui sei coautore:</p>";
$html .= "<div class='griglia_blog_coautore'>";
if (mysqli_num_rows($query_blog_coaut) === 0) {
  $html .= "<p id='nessun_blog'><img src='foto/bolle.png' alt='bolle'></img></br>Ops! Nessun blog nei paraggi... </br> Aspetta che qualcuno ti renda coautore del suo blog! </p>";
}
else{
  while ($row = mysqli_fetch_assoc($query_blog_coaut)) {
            $idblog = $row['IdBlog'];
            $src_img = $row['Immagine'];
            $descrizione = $row['Descrizione'];
            $Title = $row['Titolo'];
            if ($src_img == null) {
                $src_img = "foto/blog.png";
            }
            $html .= "<div class='blog' data-blog-title='{$Title}' data-blog-id='{$idblog}'>";
            $html .= "<img src='{$src_img}' alt='{$Title}'></img>";
            $html .= "<p class='titolo_tuoi_blog'>{$Title}</p>";
            $html .= "<p class='descrizione_tuoi_blog'>{$descrizione}</p>";
            $html .= "</div>";
        }
        mysqli_stmt_close($stmt_blog_coaut);
    }
    $html .= "</div>";
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
      <div class="griglia_blog"><?php echo $html ?></div>
  </div>
  </header>
</body>
</html>
