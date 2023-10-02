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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
      $(document).ready(function() {
        $.get("blog.php", function(data) {
          if(data=="Sessione annullata"){
            location.replace("registrazione.php");
          }
          $(".griglia_blog").html(data);
        });
        $("#crea_blog").click(function(){
          $('#form_crea_blog').toggle();
          if($(this).val()=="Annulla"){
            $(this).val("Crea un nuovo Blog");
            $(".tuoi_blog").prepend($(this));
            $(this).css("margin-left","");
            $(".content-container").css("display", "");
            $(".griglia_blog").css("flex", "");
             $(".presentazione").css("margin-left", "");
            $(".griglia_blog").css("margin-left", "");
          }else{
            $(this).val("Annulla");
            $(this).css({"margin-left":"38%"});
            $("#form_crea_blog").prepend($(this));
            $(".content-container").css("display", "flex");
            $(".griglia_blog").css("flex", "1");
            $(".presentazione").css("margin-left", "40%");
            $(".griglia_blog").css("margin-left", "40%");
          }
        });
        $.get("categorie_select.php", function(data) {
          if(data=="Sessione annullata"){
            location.replace("registrazione.php");
          }
          $("#categoria_blog").html(data);
        });
        $("#categoria_blog").change(function() {
          var categoria = $(this).val();
          $.get("sottocategorie_select.php", { nome_cat: categoria }, function(data) {
            if(data=="Sessione annullata"){
              location.replace("registrazione.php");
            }
            $("#sottocategoria_blog").html(data);
          });
        });
        $("#form_crea_blog").validate({
          rules : {
            titolo_blog: {
              required : true,
              maxlength:15
              },
            descrizione_blog : {
              required : true,
              maxlength: 30
                },
            categoria_blog : {
              required : true
              } 
            },
          messages : {
            titolo_blog: {
              required: "Inserire un titolo",
              maxlength: "Inserire massimo 15 caratteri"
            },
            descrizione_blog : {
              required : "Inserire una descrizione",
              maxlength: "Inserire massimo 30 caratteri"
            },
            categoria_blog: {
              required: "Inserire una categoria"
            }
          }
        });
        $("#form_crea_blog").on("submit", function(event){
            if($(this).valid()){
            $("#error_message").hide();
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
              type: "POST",
              url: $("#form_crea_blog").attr("action"),
              processData: false,
              contentType: false,
              data: formData,
              success: function(data){
                if(data == "OK"){
                   location.reload();
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
  <header>
    <nav class="navbar">
      <div class="logo"><a href="home.php">Bluggle</a></div>
      <ul class="menu">
        <li><a href="home.php">Home</a></li>
        <li><a href="i_tuoi_blog.php"> I tuoi Blog</a></li>
        <li><a href="#">Account</a></li>
        <li><a href="#">Info</a></li>
      </ul>
      <div class="buttons">
        <input type="button" value="Premium">
        <a href="logout.php"><input type="button" value="Logout"></a>
      </div>
    </nav>
    <div class="tuoi_blog">
      <input type="button" id="crea_blog" value="Crea un nuovo Blog">
      <div class="content-container">
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
          <label>Seleziona uno o più coautori</label></br>
          <input type="text" name="coautori" id="coautori">
        </div>
        <div class="field">
          <input type="submit" value="Crea">
        </div>
        </br>
        <p id="error_message"></p>
      </form>
      <div class="griglia_blog"></div>
      </div>
  </div>
  </header>
</body>
</html>
