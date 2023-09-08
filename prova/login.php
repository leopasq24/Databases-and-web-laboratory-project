<?php
session_start();
session_unset();
?>
<html lang="it">
   <head>
      <meta charset="utf-8">
      <title>Form di login</title>
      <link rel="stylesheet" href="stile_login.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
      <script>
            $(document).ready(function(){
               $("#login").validate({
                  rules : {
                     username : {
                        required: true
                     },
                     password : {
                        required: true
                     }
                  },
                  messages : {
                     username: {
                        required : "Inserire il nome utente"
                  },
                     password: {
                        required: "Inserire una password"
                     }
                  }
                  });
            });
      </script>
   </head>
   <body>
      <div class="wrapper">
         <div class="title">
            Accedi
         </div>
         <form id="login" action="#">
            <div class="field">
               <input type="text" name="username">
               <label>Username</label>
            </div>
            <div class="field">
               <input type="password" name="password">
               <label>Password</label>
            </div>
			</br>
            <div class="field">
               <input type="submit" value="Login">
            </div>
            <div class="signup-link">
               Non hai un account? <a href="registrazione.php">Registrati</a>
            </div>
         </form>
      </div>
   </body>
</html>