<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"]) || empty($_SESSION["session_utente"])) {
  session_unset();
  session_destroy();
  header("Location: registrazione.php");
  exit;
} else {
	$id_utente = $_SESSION["session_utente"];

	$stmt = mysqli_prepare($link, "SELECT Username FROM utente WHERE IdUtente = ?");
	mysqli_stmt_bind_param($stmt, "i", $id_utente);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_bind_result($stmt, $username);
}
?>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title> Pagina iniziale </title>
    <link rel="stylesheet" href="stile_index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
      function PrimaVisita() {
        var cookies = document.cookie.split(';');
        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i].trim();
            if (cookie.indexOf('intro=') === 0) {
                return false;
            }
        }
        document.cookie = 'intro=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
        return true;
      }
      if (!PrimaVisita()) {
          location.replace("home.php");
      }
      </script>
   </head>
<body>
  <header>
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
        <input type="button" value="Premium">
        <a href="logout.php"><input type="button" value="Logout"></a>
      </div>
    </nav>
    <div class="text-content">
      <h2>Ti diamo il benvenuto,<br><span id="nome_utente">
        <?php if (mysqli_stmt_fetch($stmt)) {
    	    echo $username;
          mysqli_stmt_close($stmt);
	      } ?></span></h2>
      <p> <span>Crea</span> il tuo blog personale, <span>posta</span> quello che più ti piace, <span>condividi</span> un pensiero...
      o divertiti a <span>galleggiare</span> tra i post degli altri utenti! 🫧 </br></p>
      <a href="home.php"><input type="button" value="Iniziamo!"></a>
    </div>
  </header>
</body>
</html>
