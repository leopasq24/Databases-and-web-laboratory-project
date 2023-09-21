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
                        required: "Inserire la password"
                     }
                  }
                  });
               $("#login").on("submit", function(event){
                     if($(this).valid()){
                        $("#return_message").hide();
                        event.preventDefault();
                        var formData = new FormData(this);
                        $.ajax({
                           type: "POST",
                           url: $("#login").attr("action"),
                           processData: false,
                           contentType: false,
                           data: formData,
                           success: function(data){
                              if(data == "OK"){
                                 location.replace("intro.php");
                                 } else{
                                    $("#error_message").show();
                                    $("#error_message").text(data);
                                    }         
                              }
                        });
                     }
                  });
            });
      </script>
   </head>
   <body>
      <div class="grid-container">
         <div class="testo">
            <h2>Bluggle</h2>
            <h3><span>crea,</span></h3>
            <h3><span>commenta,</span></h3>
            <h3><span>condividi.</span></h3>
         </div>
         <div class="wrapper">
            <div class="title"> Accedi </div>
            <form id="login" action="loggato.php" method="post">
               <div class="field">
                  <input type="text" name="username" id="username">
                  <label>Username</label>
               </div>
               <div class="field">
                  <input type="password" name="password" id="password">
                  <label>Password</label>
               </div>
			      </br>
               <p id="error_message"></p>
               <div class="field">
                  <input type="submit" value="Login">
               </div>
               <div class="signup-link"> Non hai un account? <a href="registrazione.php">Registrati</a></div>
            </form>
         </div>
         <div class="images">
            <div class="img_3">
               <img src="foto/social_5.jpg" alt="pc">
            </div>
         </div>
      </div>
   </body>
</html>
