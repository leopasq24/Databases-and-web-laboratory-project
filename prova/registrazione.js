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