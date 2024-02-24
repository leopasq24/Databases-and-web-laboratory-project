<?php
session_start();
include_once("connect.php");

if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
  
  $categoria = "inesistente";
  $html = "<div class='griglia_blog_creati'>";
    
  if (isset($_GET['id']) and is_numeric($_GET['id'])) {
    $id_categoria = $_GET['id'];
    $stmt_esiste_cat = mysqli_prepare($link, "SELECT IdCategoria FROM categoria Where IdCategoria = ?");
    mysqli_stmt_bind_param($stmt_esiste_cat, "i", $id_categoria);
    mysqli_stmt_execute($stmt_esiste_cat);
    $results_esiste_cat = mysqli_stmt_get_result($stmt_esiste_cat);
    if (mysqli_num_rows($results_esiste_cat) === 0) {
      $html .= "<p id='nessuna_cat'>Non esiste alcuna categoria con questo Id!</p> </div>";
      mysqli_stmt_close($stmt_esiste_cat);
    } else {
          $stmt_cat = mysqli_prepare($link, "SELECT Nome FROM categoria Where IdCategoria = ?");
          mysqli_stmt_bind_param($stmt_cat, "i", $id_categoria);
          mysqli_stmt_execute($stmt_cat);
          $results_cat = mysqli_stmt_get_result($stmt_cat);

          $stmt_macrocat = mysqli_prepare($link, "SELECT IdCategoria, Nome FROM categoria WHERE IdCategoria = ? AND IdCategoria NOT IN (SELECT IdSottocategoria FROM contiene) LIMIT 7");
          mysqli_stmt_bind_param($stmt_macrocat, "i", $id_categoria);
          mysqli_stmt_execute($stmt_macrocat);
          $results_macrocat = mysqli_stmt_get_result($stmt_macrocat);
          $isMacrocat = (mysqli_num_rows($results_macrocat) === 1);
          if ($isMacrocat) {
            $categoria_nome = mysqli_fetch_assoc($results_cat)["Nome"];
            $categoria = $categoria_nome . "<p id='freccia_cat'> &nbsp;&#8250; &nbsp; </p>";
          }
          else {
            $categoria = mysqli_fetch_assoc($results_cat)["Nome"];
          }
          mysqli_stmt_close($stmt_cat);
          mysqli_stmt_close($stmt_macrocat);

          $stmt_blog_per_categoria = mysqli_prepare($link, "SELECT IdBlog, Titolo, Descrizione, Immagine, Username FROM blog, utente WHERE blog.IdUtente = utente.IdUtente AND blog.IdCategoria = ? ORDER BY IdBlog DESC LIMIT ?, 9");
          mysqli_stmt_bind_param($stmt_blog_per_categoria, "ii", $id_categoria, $numeroBlog);
          mysqli_stmt_execute($stmt_blog_per_categoria);
          $query_blog_per_categoria = mysqli_stmt_get_result($stmt_blog_per_categoria);
        
          $nessunBlog = (mysqli_num_rows($query_blog_per_categoria) === 0);
          if ($nessunBlog) {
            $html .= "<img src='foto/bolle.png' alt='bolle'>";
            $html .= "<p id='nessun_blog'>Non ci sono ancora Blog per questa categoria. <a href='i_tuoi_blog.php'>Creane uno!</a> </p>";
          } else {
              while ($row = mysqli_fetch_assoc($query_blog_per_categoria)) {
                $idblog = $row['IdBlog'];
                $src_img = $row['Immagine'];
                $descrizione = $row['Descrizione'];
                $Title = $row['Titolo'];
                $autore = $row['Username'];
                if ($src_img == null) {
                    $src_img = "foto/blog.png";
                }
                $html .= "<div class='blog' data-blog-title='{$Title}' data-blog-id='{$idblog}'>";
                $html .= "<img src='{$src_img}' alt='{$Title}'></img>";
                $html .= "<p class='titolo_tuoi_blog'>{$Title}</p>";
                $html .= "<p class='descrizione_tuoi_blog'>{$descrizione}</p>";
                $html .= "<p class='autore_tuoi_blog'>di <span>{$autore}</span></p>";
                $html .= "</div>";
            }
            mysqli_stmt_close($stmt_blog_per_categoria);
            $html .= "</div>";
            $html .= "<input type='button' id='caricablog' value='Mostra di più'>";
        }
        }
    } else {
        $html .= "<p id='nessun_id_cat'>Id della categoria non specificato!</p> </div>";
    }
}
?>
<html lang="it" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title> Tutti i Blog per categoria </title>
    <link rel="stylesheet" href="stile_index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
    
    $(document).ready(function() {
      
      $(".griglia_blog").on("click", ".blog", function(){
        var idBlog = $(this).data("blog-id");
        $.ajax({
            url: "singolo_blog.php",
            type: "GET",
            success: function() {
              location.replace("singolo_blog.php?id=" + idBlog);
            },
            error: function(xhr) {
              $(".blog").after("<p class='eliminazione_error'>" + xhr + "</p>");
            }
        });
      });
      
      var numeroBlogCaricati;
      var isMessageAppended = false;
      function caricaBlog() {
        var idCategoria = <?php echo $_GET["id"]?>;
        $.ajax({
            url: "tutte_categorie_select.php",
            method: "GET",
            data: { numero: numeroBlogCaricati, idCategoria: idCategoria },
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
            error: function(xhr) {
              $("#caricablog").after("<p class='eliminazione_error'>" + xhr + "</p>");
            }
        });
      }
      $("#caricablog").on("click", function() {
        caricaBlog();
      });

      $("p#freccia_cat").on("click", function() {
        var freccia = $(this);
        var nomeCategoria = "<?php echo $categoria_nome ?>";
        var titolo = freccia.closest("h2");
        function appendiMicrocat() {
          $.ajax({
            url: "sottocategorie.php",
            method: "GET",
            data: { key1: nomeCategoria },
            success: function(data) {
                titolo.append("<div class='ajax-data'>" + data + "</div>");
                $(".ajax-data p").on("click", function() {
                  var idcat = $(this).closest(".microcat").data("cat-id");
                  location.replace("categoria.php?id=" + idcat);
                });
            },
            error: function(xhr) {
                $("p#freccia_cat").after("<p class='eliminazione_error'>" + xhr + "</p>");
            }
          });
        }
        function rimuoviMicrocat() {
          $(".ajax-data").remove();
        }
        freccia.toggleClass("rotated"); 
        if (freccia.hasClass("rotated")) {
          freccia.css("transform", "rotate(90deg)");
          appendiMicrocat()
        } else {
          freccia.css("transform", "rotate(0deg)");
          rimuoviMicrocat();
        }
      });

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
      <h2>Categoria: <p id="nome_categoria"><?php echo $categoria ?></p></h2>
      <div class="griglia_blog"><?php echo $html; ?></div>
    </div>
  </header>
</body>
</html>
