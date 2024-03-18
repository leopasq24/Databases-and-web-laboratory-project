$(document).ready(function() {
    $(".griglia_blog").on("click", ".blog", function(){
      var idBlog = $(this).data("blog-id");

      window.location.href = "singolo_blog.php?id=" + idBlog;
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
              $(".griglia_blog").append("<p class='messaggio_fine'><img class='ops' src='foto/ops.png'><br>Ops, sei arrivato in fondo!</p>");
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