<?php
session_start();
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
               $.validator.addMethod("regex_username",
                  function(value, element, regexp) {
                     var re = new RegExp(regexp);
                     return this.optional(element) || re.test(value);
                  },

                  "Non ammessi caratteri speciali tranne: _ , - , .");

               $.validator.addMethod("regex_password",
                  function(value, element, regexp) {
                     var re = new RegExp(regexp);
                     return this.optional(element) || re.test(value);
                  },

                  "Obbligatori: lettere e numeri. Ammessi: ?, !, #, $, *");

               $("#registrati").validate({
                  rules : {
                     mail: {
                        required : true,
                        email: true
                     },
                     username : {
                        required : true,
                        minlength : 5,
                        maxlength : 20,
                        regex_username: /^[\w\.-]{5,20}$/
                     },
                     password : {
                        required : true,
                        minlength: 8,
                        regex_password: /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d?!#$*]{8,}$/
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
                        minlength: "La password deve essere di almeno 8 caratteri!"
                     },
                     password_2: {
                        equalTo: "Le password devono coincidere!"
                     }
                  }
                  });
                  $("#registrati").on("submit", function(event){
                     if($(this).valid()){
                        $("#error_message").hide();
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
      <div class="grid-container">
         <div class="testo">
            <h2>Bluggle</h2>
            <h3><span>crea,</span></h3>
            <h3><span>commenta,</span></h3>
            <h3><span>condividi.</span></h3>
         </div>
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
         <div class="images">
            <div class="img_1">
               <img src="foto/selfie.jpg" alt="selfie">
            </div>
            <div class="img_2">
               <img src="foto/social_7.jpg" alt="social">
            </div>
         </div>
      </div>
   </body>
</html>

