<?php
session_start();
?>
<html lang="it" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title> I Tuoi Blog </title>
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
        $(".griglia_blog").on("click", ".blog", function(){
          var idBlog = $(this).data("blog-id");
          var Blog_title = $(this).data("blog-title");
          $.ajax({
            url: "singolo_blog.php",
            type: "GET",
            data:{ idBlog : idBlog, Blog_title: Blog_title },
            success: function(data) {
              $(".header_tuoi_blog").css({"height":"85vh", "margin":"auto"});
              $(".body_tuoi_blog").css("height","100%");
              $(".tuoi_blog").html(data);
              },
            error: function(xhr, status, error) {
              console.error(error);
              }
            })
        });
        $("#crea_blog").click(function(){
          $('#form_crea_blog').toggle();
          if($(this).val()=="Annulla"){
            $(this).val("Crea un nuovo Blog");
            $(".tuoi_blog").prepend($(this));
            $(this).css("margin-left","");
            $("input[value='Crea']").css({"margin-top":""});
            $(".tuoi_blog .presentazione").css({"margin-top":""});
          }else{
            $(this).val("Annulla");
            $(this).css({"margin-left":"25%"});
            $("#form_crea_blog").prepend($(this));
            $("input[value='Crea']").css({"margin-top":"2%"});
            $(".tuoi_blog .presentazione").css({"margin-top":"5%"});
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
        /*var $old_p_titolo;
        var $new_input_titolo;
        var $old_p_desc;
        var $new_input_desc;
        $(".griglia_blog").on("click", ".modifica_blog", function() {
          if ($(this).hasClass("disabled")) {
            return;
          }
          if($(this).val()=="Modifica" && !$(this).hasClass("disabled")){
            $(this).siblings(".elimina_blog").val("Indietro");
            $(this).addClass("active");
            $(".griglia_blog .modifica_blog").not(this).addClass("disabled");
            $(".griglia_blog .elimina_blog").not(this).addClass("disabled");
            $old_p_titolo = $(this).siblings(".titolo_tuoi_blog");
            $new_input_titolo = $("<input class='campo_titolo_tuoi_blog'>");
            $new_input_titolo.val($old_p_titolo.text());
            $old_p_titolo.replaceWith($new_input_titolo);
            $old_p_desc = $(this).siblings(".descrizione_tuoi_blog");
            $new_input_desc = $("<input class='campo_descrizione_tuoi_blog'>");
            $new_input_desc.val($old_p_desc.text());
            $old_p_desc.replaceWith($new_input_desc);
            $new_input_desc.focus();
          }else{
            var blogId = $(this).data("blog-id");
            var nuovo_titolo = $(this).siblings(".campo_titolo_tuoi_blog").val();
            var nuova_desc = $(this).siblings(".campo_descrizione_tuoi_blog").val();
            if (confirm("Sicuro di voler modificare il seguente Blog?")) {
              console.log("Reached this point");
              $.ajax({
                type: "POST",
                url: "modifica_blog.php", 
                data: { blogId: blogId, nuovo_titolo: nuovo_titolo, nuova_desc: nuova_desc},
                success: function(response) {
                  if (response === "OK") {
                    location.reload();
                  } else if(response === "Sessione annullata"){
                    location.replace("registrazione.php");
                  } else if(response === "richiesta fallita"){
                    alert("errore nella modifica del blog");
                  }else if(response === "errore"){
                    alert("Errore nel DataBase");
                  }
                },
                error: function() {
                  alert("Errore nel comunicare col server");
                }
              });
            }
          }         
        });        
        $(".griglia_blog").on("click", ".elimina_blog", function() {
           if ($(this).hasClass("disabled")) {
            return;
          }
          if ($(this).val()=="Elimina"){
            var blogId = $(this).data("blog-id");
            if (confirm("Sicuro di voler eliminare il seguente Blog?")) {
              $.ajax({
                type: "POST",
                url: "elimina_blog.php", 
                data: { blogId: blogId },
                success: function(response) {
                  if (response === "OK") {
                  $(this).closest(".blog").remove();
                  location.reload();
                  } else if(response === "Sessione annullata"){
                    location.replace("registrazione.php");
                  } else if(response === "richiesta fallita"){
                    alert("errore nell'eliminazione del blog");
                  }else if(response === "errore"){
                    alert("Errore nel DataBase");
                  }
                },
                error: function() {
                  alert("Errore nel comunicare col server");
                }
              });
            }
          }else{
            $new_input_titolo.replaceWith($old_p_titolo);
            $new_input_desc.replaceWith($old_p_desc);
            $(".griglia_blog .modifica_blog").not(this).removeClass("disabled");
            $(".griglia_blog .elimina_blog").not(this).removeClass("disabled");
            $(this).val("Elimina");
            $(this).siblings(".modifica_blog").val("Modifica");
          }
        });*/        
        $("#form_crea_blog").validate({
          rules : {
            titolo_blog: {
              required : true,
              maxlength:15
              },
            descrizione_blog : {
              required : true,
              maxlength: 40
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
              maxlength: "Inserire massimo 40 caratteri"
            },
            categoria_blog: {
              required: "Inserire una categoria"
            }
          }
        });
        var searchInput = $("#coautori");
        var searchResults = $("#searchResults");
        searchInput.on("input", function() {
        var query = $(this).val();
          $.ajax({
            type: "GET",
            url: "search_coautori.php",
            data: { query: query },
            dataType: "html",
            success: function(data) {
              searchResults.html(data);
            }
          });
        });
        searchResults.on("click", "li", function() {
          var selezione = $(this).text();
          var valore = searchInput.val();
          if (valore) {
            var elenco = valore.split(' ');
            if (elenco.indexOf(selezione) === -1) {
              searchInput.val(valore + ' ' + selezione);
            }
          } else {
            searchInput.val(selezione);
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
<body id="body_tuoi_blog">
  <header id="header_tuoi_blog">
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
          <label>Seleziona uno o più coautori </br>(separandoli con uno spazio)</label></br>
          <input type="text" name="coautori" id="coautori">
          <div id="searchResults"></div>
        </div>
        <div class="field">
          <input type="submit" value="Crea">
        </div>
        </br>
        <p id="error_message"></p>
      </form>
      <div class="griglia_blog"></div>
  </div>
  </header>
</body>
</html>
