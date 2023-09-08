<?php
session_start();
session_unset();
?>
<html lang="it">
   <head>
      <meta charset="utf-8">
      <title>Registrati</title>
      <link rel="stylesheet" href="stile_login.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
      <script>
            $(document).ready(function(){
               $("#registrati").validate({
                  rules : {
                     mail: {
                        required : true,
                        email: true
                     },
                     username : {
                        required : true,
                        minlength : 5,
                        maxlength : 20
                     },
                     password : {
                        required : true,
                        minlength: 4
                     },
                     password_2 : {
                        equalTo: "#password"
                     }
                  },
                  messages : {
                     mail: {
			               required: "La mail è obbligatoria!",
			               email: "La mail non ha il formato corretto!"
			         },
                     username : {
                        required : "Il nome utente è obbligatorio!",
                        minlength : "Il nome utente deve essere di almeno 5 caratteri!",
                        maxlength: "Il nome utente non può contenere più di 20 caratteri!"
                  },
                     password: {
                        required: "La password è obbligatoria!",
                        minlength: "La password deve essere di almeno 4 caratteri!"
                     },
                     password_2: {
                        equalTo: "Le password devono coincidere!"
                     }
                  }
                  });
                  $("#registrati").on("submit", function(event){
                     if($(this).valid()){
                        $("#return_message").hide();
                        event.preventDefault();
                        var formData = new FormData(this);
                        $.ajax({
                           type: "POST",
                           url: $("#registrati").attr("action"),
                           processData: false,
                           contentType: false,
                           data: formData,
                           success: function(data){
                              if(data == "OK"){
                                 location.replace("welcome.php");
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
      <div class="wrapper">
         <div class="title">
            Registrati
         </div>
         <form id="registrati" action="registrato.php" method="post">
            <div class="field">
               <input type="text" name="mail" id="mail">
               <label>Indirizzo Email</label>
            </div>
            <div class="field">
               <input type="text" name="username" id="username">
               <label>Username</label>
            </div>
            <div class="field">
               <input type="password" name="password" id="password">
               <label>Password</label>
            </div>
            <div class="field">
               <input type="password" name="password_2" id="password_2">
               <label>Conferma Password</label>
            </div>
         </br>
         <p id="error_message"></p>
            <div class="field">
               <input type="submit" value="Salva">
            </div>
            <div class="signup-link">
               Hai già un account? <a href="login.php">Accedi</a>
            </div>
         </form>
      </div>
   </body>
</html>
