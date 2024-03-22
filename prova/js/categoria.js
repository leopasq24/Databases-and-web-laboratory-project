$(document).ready(function() {
      
    $(".griglia_blog").on("click", ".blog", function(){
      var idBlog = $(this).data("blog-id");
      $.ajax({
          url: "singolo_blog.php",
          type: "GET",
          success: function() {
            window.location.href = "singolo_blog.php?id=" + idBlog;
          },
          error: function(xhr) {
            $(".blog").after("<p class='eliminazione_error'>" + xhr + "</p>");
          }
      });
    });
    
    var numeroBlogCaricati;
    var isMessageAppended = false;
    function caricaBlog() {
      var idCategoria = id_cat;
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
      var idCategoria = id_cat;
      var titolo = freccia.closest("h2");
      function appendiMicrocat() {
        $.ajax({
          url: "sottocategorie.php",
          method: "GET",
          data: { key1: idCategoria },
          success: function(res) {
              titolo.append("<div class='sottocat'>" + res + "</div>");
              $(".sottocat p").on("click", function() {
                var idcat = $(this).closest(".microcat").data("cat-id");
                window.location.href = "categoria.php?id=" + idcat;
              });
          },
          error: function(xhr) {
              $("p#freccia_cat").after("<p class='eliminazione_error'>" + xhr + "</p>");
          }
        });
      }
      function rimuoviMicrocat() {
        $(".sottocat").remove();
      }
      freccia.toggleClass("rotated"); 
      if (freccia.hasClass("rotated")) {
        freccia.css("transform", "rotate(90deg)");
        appendiMicrocat();
      } else {
        freccia.css("transform", "rotate(0deg)");
        rimuoviMicrocat();
      }
    });

  });