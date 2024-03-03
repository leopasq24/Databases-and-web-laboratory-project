<?php
session_start();
?>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title> Pagina iniziale </title>
    <link rel="stylesheet" href="stile_index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>
      $(document).ready(function(){
        $("#nome_utente").load("nome_utente.php", function(response) {
          if (response == "Sessione annullata") {
            location.replace("registrazione.php");
            }
        });
      });
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
      <h2>Ti diamo il benvenuto,<br><span id="nome_utente"></span></h2>
      <p> <span>Crea</span> il tuo blog personale, <span>posta</span> quello che più ti piace, <span>condividi</span> un pensiero...
      o divertiti a <span>galleggiare</span> tra i post degli altri utenti! 🫧 </br></p>
      <a href="home.php"><input type="button" value="Iniziamo!"></a>
    </div>
  </header>
</body>
</html>
