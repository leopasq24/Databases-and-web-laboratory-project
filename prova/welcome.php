<?php
session_start();
session_unset();
?>
<html lang="it">
   <head>
      <meta charset="utf-8">
      <title>Schermata di benvenuto</title>
      <link rel="stylesheet" href="stile_login.css">
   </head>
   <body>
      <div class="wrapper">
         <div class="title">
            Benvenuto!
         </div>
         <div class="msg">
            La tua registrazione è avvenuta con successo!
         </br> Ora puoi accedere effettuando il login.
         </div>
        <form action="login.php">
         <div class="field">
               <input type="submit" value="Login">
         </div>
        </form>
      </div>
   </body>
</html>