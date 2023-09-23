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
        $.get("categorie.php", function(data) {
          if(data=="Sessione annullata"){
            location.replace("registrazione.php");
          }
          $(".macro-categorie").html(data);
        });
      $.get("blog_popolari.php", function(data) {
          if(data=="Sessione annullata"){
            location.replace("registrazione.php");
          }
          $(".blog-popolari").html(data);
        });
        $("#blog_personali").click(function(){
          $(".ultimi-post").hide();
          $(".ultimi-post-personali").show();
          $("#blog_generali").removeAttr("checked");
        });
        $("#blog_generali").click(function(){
          $(".ultimi-post-personali").hide();
          $(".ultimi-post").show();
        });  
      $.get("post.php", function(data) {
          if(data=="Sessione annullata"){
            location.replace("registrazione.php");
          }
          $(".ultimi-post").html(data);
        });
      $.get("post_personali.php", function(data) {
          if(data=="Sessione annullata"){
            location.replace("registrazione.php");
          }
          $(".ultimi-post-personali").html(data);
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
        <li><a href="#"> I tuoi Blog</a></li>
        <li><a href="#">Account</a></li>
        <li><a href="#">Info</a></li>
      </ul>
      <div class="buttons">
        <input type="button" value="Premium">
        <a href="logout.php"><input type="button" value="Logout"></a>
      </div>
    </nav>
    <div class="grid-post">
      <div class="categorie">
        <p class="titolo">Categorie</p>
        <div class="macro-categorie"></div>
      </div>
      <div class="post">
        <p class="titolo">Nuovi post</br>
        <input type="radio" id="blog_generali" name="blog_generali" value="Tutti i blog" checked><label for="blog_generali">Tutti i Blog</label>
        <input type="radio" id="blog_personali" name="blog_personali" value="I tuoi blog"><label for="blog_personali">I tuoi Blog</label></p>
        <div class="ultimi-post"></div>
        <div class="ultimi-post-personali" hidden></div>
      </div>
      <div class="popolari">
        <p class="titolo">Blog popolari</p>
        <div class="blog-popolari"></div>
      </div>
    </div>
  </header>
</body>
</html>
