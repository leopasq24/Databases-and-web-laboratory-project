<?php
session_start();
?>
<html lang="it" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title> Tutti i Blog </title>
    <link rel="stylesheet" href="stile_index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
      $(document).ready(function() {
        $(".griglia_blog").on("click", ".blog", function(){
          var idBlog = $(this).data("blog-id");

          location.replace("singolo_blog.php?id=" + idBlog);
        });
        var numeroBlogCaricati;
        var tipoSelezionato = "Recenti";
        var isMessageAppended = false;
        
        function caricaBlog(tipo) {
          $.ajax({
            url: "tutti_i_blog_select.php",
            method: "GET",
            data: { numero: numeroBlogCaricati, tipo: tipo },
            success: function(data) {
              if (data.trim() === "Nessun Blog") {
                $("#caricablog").hide();
                if (!isMessageAppended) {
                  $(".griglia_blog").append("<p class='messaggio_fine'>Ops, sei arrivato in fondo!</p>");
                  isMessageAppended = true;
                }
              } else {
                $(".messaggio_fine").remove(); 
                $(".griglia_blog").append(data);
                numeroBlogCaricati += 9;
                isMessageAppended = false;
                $("#caricablog").show(); 

              }
            },
            error: function(xhr, status, error) {
              console.error(error);
            }
          });
        }
        function resetAndLoadBlog(tipo) {
          $(".griglia_blog").empty();
          numeroBlogCaricati = 0;
          messaggioPresente = false; 
          caricaBlog(tipo);
        }
        $("#blog_recenti").on("click", function() {
          $(".blog_rec").show();
          $(".blog_pop").hide();
          $(".blog_per_u").hide();
          $("label#blog_recenti").addClass("selected");
          $("label#blog_popolari").removeClass("selected");
          $("label#blog_per_utente").removeClass("selected");
          tipoSelezionato = "Recenti";
          resetAndLoadBlog(tipoSelezionato);
        });
        $("#blog_popolari").on("click", function() {
          $(".blog_rec").hide();
          $(".blog_pop").show();
          $(".blog_per_u").hide();
          $("label#blog_popolari").addClass("selected");
          $("label#blog_recenti").removeClass("selected");
          $("label#blog_per_utente").removeClass("selected");
          tipoSelezionato = "Popolari";
          resetAndLoadBlog(tipoSelezionato);
        });
        $("#blog_per_utente").on("click", function() {
          $(".blog_rec").hide();
          $(".blog_pop").hide();
          $(".blog_per_u").show();
          $("label#blog_per_utente").addClass("selected");
          $("label#blog_recenti").removeClass("selected");
          $("label#blog_popolari").removeClass("selected");
          tipoSelezionato = "Autore";
          resetAndLoadBlog(tipoSelezionato);
        });
        $("#caricablog").on("click", function() {
          caricaBlog(tipoSelezionato);
        });
        resetAndLoadBlog(tipoSelezionato);
      });
    </script>
  </head>
<body id="body_tutti_i_blog">
  <header id="header_tutti_i_blog">
    <nav class="navbar">
      <div class="logo"><a href="home.php">Bluggle</a></div>
      <ul class="menu">
        <li><a href="home.php">Home</a></li>
        <li><a href="tutti_i_blog.php">Tutti i Blog</a></li>
        <li><a href="i_tuoi_blog.php">I tuoi Blog</a></li>
        <li><a href="account.php">Account</a></li>
        <li><a href="#">Info</a></li>
      </ul>
      <div class="buttons">
        <input type="button" value="Premium">
        <a href="logout.php"><input type="button" value="Logout"></a>
      </div>
    </nav>
    <div class="tutti_i_blog">
    <div class="ordina_blog">
      <p>Ordina per: &#160 
      <input type="radio" id="blog_recenti" name="blog_recenti" value="Recenti"><label for="blog_recenti" id="blog_recenti" class="selected">Recenti</label>
      <input type="radio" id="blog_popolari" name="blog_popolari" value="Popolari"><label for="blog_popolari" id="blog_popolari">Popolari</label>
      <input type="radio" id="blog_per_utente" name="blog_per_utente" value="Per Utente"><label for="blog_per_utente" id="blog_per_utente">Autore</label>
      </p>
    </div>
      <div class="griglia_blog">
        <div class="blog_rec"></div>
        <div class="blog_pop" hidden></div>
        <div class="blog_per_u" hidden></div>
      </div>
      <input type="button" id="caricablog" value="Mostra di più">
    </div>
  </header>
</body>
</html> 
