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
      $(document).ready(function() {
        $.get("blog.php", function(data) {
          if(data=="Sessione annullata"){
            location.replace("registrazione.php");
          }
          $(".griglia_blog").html(data);
        });
        $("#crea_blog").click(function(){
          $('#form_crea_blog').toggle();
          if($(this).val()=="Nascondi"){
            $(this).val("Crea un nuovo Blog");
          }else{
            $(this).val("Nascondi");
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
      <form id="form_crea_blog" action="crea_blog.php" method="post" hidden>
        <div class="field">
          <input type="text" name="titolo_blog" id="titolo_blog">
          <label>Titolo</label>
        </div>
        <div class="field">
          <input type="text" name="descrizione_blog" id="descrizione_blog">
          <label>Descrizione</label>
        </div>
        <div class="field">
          <input type="file" id="immagine_blog" name="immagine_blog">
          <label>Immagine</label>
        </div>
        <div class="field">
          <select id="categoria_blog" name="categoria_blog"></select>
            <label>Categoria</label>
        </div>
        <div class="field">
          <select id="sottocategoria_blog" name="sottocategoria_blog"></select>
            <label>Sottocategoria</label>
        </div>
        </br>
        <p id="error_message"></p>
        <div class="field">
            <input type="submit" value="Crea">
        </div>
      </form>
      <p class="presentazione">I Blog creati da te:</p>
      <div class="griglia_blog"></div>
    </div>
  </header>
</body>
</html>
  </header>
</body>
</html>
