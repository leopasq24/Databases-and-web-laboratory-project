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
             $("#error_message").hide();
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
                         location.replace("home.php");                                
                   } else{
                         $("#error_message").show();
                         $("#error_message").text(data);                                  
                      }         
                   }
             });
          }
       });
 });