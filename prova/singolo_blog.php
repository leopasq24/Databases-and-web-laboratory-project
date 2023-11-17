<?php
session_start();
include_once("connect.php");

$idBlog = $_GET['idBlog'];
$Blog_title = $_GET['Blog_title'];
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
}else if (isset($_GET["idBlog"])){
  $id_utente = $_SESSION["session_utente"];
  $stmt_info_blog = mysqli_prepare($link, "SELECT blog.Immagine, blog.Titolo, categoria.Nome, blog.Descrizione FROM blog, categoria WHERE blog.IdBlog=? AND blog.IdCategoria = categoria.IdCategoria");
  mysqli_stmt_bind_param($stmt_info_blog, "i", $idBlog);
  mysqli_stmt_execute($stmt_info_blog);
  $query_info_blog = mysqli_stmt_get_result($stmt_info_blog);
  $post_rec = "";
  $post_pop = "";
  $html = "";
  $crea = "";
  $html .= "<div class='dati-blog'>";
  while ($row = mysqli_fetch_assoc($query_info_blog)) {
    $imagePath = $row['Immagine'];
    $titolo = $row['Titolo'];
    $categoria = $row['Nome'];
    $descrizione = $row['Descrizione'];
    if ($imagePath == null) {
      $imagePath = "foto/blog.png";
    }
    $html .= "<img class='img_blog' src='{$imagePath}' alt='{$titolo}'></img>";
    $html .= "<p class='titolo_blog'>{$titolo}</p>";
    $html .= "<p class='descrizione_blog'>{$descrizione}</p>";
    $html .= "<p class='cat_blog'>{$categoria}</p>";
  }
  mysqli_stmt_close($stmt_info_blog);
  $stmt_proprietario = mysqli_prepare($link, "SELECT IdUtente FROM blog WHERE IdBlog=?");
  mysqli_stmt_bind_param($stmt_proprietario, "i", $idBlog);
  mysqli_stmt_execute($stmt_proprietario);
  $query_proprietario = mysqli_stmt_get_result($stmt_proprietario);
  while ($row = mysqli_fetch_assoc($query_proprietario)){
    if ($row["IdUtente"]==$id_utente){
      $creatore = $id_utente;
      $html .= "<input type='button' data-blog-id='{$idBlog}' class='modifica_blog' value='Modifica'>
      <input type='button' data-blog-id='{$idBlog}' class='elimina_blog' value='Elimina'>";
      $crea = "<input type='button' id='crea_post' value='Crea un nuovo post'>";
    }else{
      $creatore = $row["IdUtente"];
    }
  }

  mysqli_stmt_close($stmt_proprietario);
  $stmt_nome_creatore= mysqli_prepare($link, "SELECT Username FROM utente WHERE IdUtente=?");
  mysqli_stmt_bind_param($stmt_nome_creatore, "i", $creatore);
  mysqli_stmt_execute($stmt_nome_creatore);
  $query_nome_creatore = mysqli_stmt_get_result($stmt_nome_creatore);
  $array_nome_creatore = mysqli_fetch_assoc($query_nome_creatore);
  $nome_creatore = $array_nome_creatore["Username"];

  $stmt_id_coautori = mysqli_prepare($link, "SELECT IdUtente FROM coautore WHERE IdBlog=?");
  mysqli_stmt_bind_param($stmt_id_coautori, "i", $idBlog);
  mysqli_stmt_execute($stmt_id_coautori);
  $query_id_coautori = mysqli_stmt_get_result($stmt_id_coautori);
  $nome_coautori = array();

  while ($row = mysqli_fetch_assoc($query_id_coautori)) {
    $stmt_nome_coautore = mysqli_prepare($link, "SELECT Username FROM utente WHERE IdUtente=?");
    mysqli_stmt_bind_param($stmt_nome_coautore, "i", $row['IdUtente']);
    mysqli_stmt_execute($stmt_nome_coautore);
    $query_nome_coautore = mysqli_stmt_get_result($stmt_nome_coautore);
    $coautore = mysqli_fetch_assoc($query_nome_coautore);
    $nome_coautori[] = $coautore["Username"];
  }
  $nome_creatore = $array_nome_creatore["Username"];
  mysqli_stmt_close($stmt_nome_creatore);
  $html .= "<p class='autori'>Creatore:</p>";
  $html .= "<p class='nomi_autori'>$nome_creatore</p>";
  $html .= "<p class='autori'>Coautori:</p>";
  if(empty($nome_coautori)){
    $html .= "<p class='nomi_autori'>Nessun coautore</p>";
  }else{
    $html .= "<p class='nomi_autori'>".implode(", ", $nome_coautori)."</p>";
  }

  $new_stmt_proprietario = mysqli_prepare($link, "SELECT IdUtente FROM blog WHERE IdBlog=?");
  mysqli_stmt_bind_param($new_stmt_proprietario, "i", $idBlog);
  mysqli_stmt_execute($new_stmt_proprietario);
  $new_query_proprietario = mysqli_stmt_get_result($new_stmt_proprietario);
  while ($row = mysqli_fetch_assoc($new_query_proprietario)){
    if ($row["IdUtente"]==$id_utente){
      $html .= "<input type='button' data-blog-id='{$idBlog}' class='modifica_coautore' value='Modifica'>";
    }
  }
  $html .= "</div>";

  mysqli_stmt_close($new_stmt_proprietario);
  $stmt_coautore = mysqli_prepare($link, "SELECT IdUtente FROM coautore WHERE IdBlog=?");
  mysqli_stmt_bind_param($stmt_coautore, "i", $idBlog);
  mysqli_stmt_execute($stmt_coautore);
  $query_coautore = mysqli_stmt_get_result($stmt_coautore);
  while ($row = mysqli_fetch_assoc($query_coautore)){
    if ($row["IdUtente"]==$id_utente){
      $crea = "<input type='button' id='crea_post' value='Crea un nuovo post'>";
    }
  }
  mysqli_stmt_close($stmt_coautore);

  $stmt_post_rec = mysqli_prepare($link, "SELECT post.Titolo AS Titolo_post, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente AND blog.IdBlog=? ORDER BY Data DESC LIMIT 7");
  mysqli_stmt_bind_param($stmt_post_rec, "i", $idBlog);
  mysqli_stmt_execute($stmt_post_rec);
  $result_post_rec = mysqli_stmt_get_result($stmt_post_rec);
  
  if  (empty(mysqli_fetch_assoc($result_post_rec))){
      $post_rec  .="<img src='foto/vuoto.png' alt='vuoto'></img>";
      $post_rec  .= "<p id='nessun_post'>Non ci sono ancora post</p>";
  }else{
    while ($row = mysqli_fetch_assoc($result_post_rec)) {
      $img_blog = $row['immagine_blog'];
      $img_post = $row['immagine_post'];
      $title = $row['Titolo_post'];
      $testo = $row['Testo'];
      $autore_post = $row['Username'];
      $blog = $row['Argomento'];
      $data = $row['Data'];
      $ora = $row['Ora'];
      if ($img_blog == null) {
          $src_img = "foto/blog.png";
        } else {
          $src_img = $img_blog;
        }
        
      if ($img_post === null) {
        $post_rec .= "<div class='nuovo-post'>";
        $post_rec .= "<img src='{$src_img}' alt='{$blog}'></img>";
        $post_rec .= "<p>{$blog}</p>";
        $post_rec .= "<h4>{$title}</h4>";
        $post_rec .= "<p>{$testo}</p>";
        $post_rec .= "<p class='dataeora'><span>{$autore_post}</span> {$data} {$ora}</p>";
        $post_rec .= "</div>";
      } else {
        $post_rec .= "<div class='nuovo-post'>";
        $post_rec .= "<img src='{$src_img}' alt='{$blog}'></img>";
        $post_rec .= "<p>{$blog}</p>";
        $post_rec .= "<h4>{$title}</h4>";
        $post_rec .= "<img src='{$img_post}' alt='{$title}'></img>";
        $post_rec .= "<p>{$testo}</p>";
        $post_rec .= "<p class='dataeora'><span>{$autore_post}</span> {$data} {$ora}</p>";
        $post_rec .= "</div>";
      }
    }
  }
  
  mysqli_stmt_close($stmt_post_rec);

  $stmt_post_pop = mysqli_prepare($link, "SELECT post.Titolo AS Titolo_post, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username FROM post, blog, utente, post_popolari WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente AND post.IdPost=post_popolari.codice AND blog.IdBlog=? ORDER BY post_popolari.conta DESC");
  mysqli_stmt_bind_param($stmt_post_pop, "i", $idBlog);
  mysqli_stmt_execute($stmt_post_pop);
  $result_post_pop = mysqli_stmt_get_result($stmt_post_pop);
  
  if  (empty(mysqli_fetch_assoc($result_post_pop))){
      $post_pop  .="<img src='foto/solitario.png' alt='solitario'></img>";
      $post_pop  .= "<p id='nessun_post'>Non ci sono ancora post popolari</p>";
  }else{
    while ($row = mysqli_fetch_assoc($result_post_pop)) {
      $img_blog = $row['immagine_blog'];
      $img_post = $row['immagine_post'];
      $title = $row['Titolo_post'];
      $testo = $row['Testo'];
      $autore_post = $row['Username'];
      $blog = $row['Argomento'];
      $data = $row['Data'];
      $ora = $row['Ora'];
      if ($img_blog == null) {
          $src_img = "foto/blog.png";
        } else {
          $src_img = $img_blog;
        }
        
      if ($img_post === null) {
        $post_pop .= "<div class='nuovo-post'>";
        $post_pop .= "<img src='{$src_img}' alt='{$blog}'></img>";
        $post_pop .= "<p>{$blog}</p>";
        $post_pop .= "<h4>{$title}</h4>";
        $post_pop .= "<p>{$testo}</p>";
        $post_pop .= "<p class='dataeora'><span>{$autore_post}</span> {$data} {$ora}</p>";
        $post_pop .= "</div>";
      } else {
        $post_pop .= "<div class='nuovo-post'>";
        $post_pop .= "<img src='{$src_img}' alt='{$blog}'></img>";
        $post_pop .= "<p>{$blog}</p>";
        $post_pop .= "<h4>{$title}</h4>";
        $post_pop .= "<img src='{$img_post}' alt='{$title}'></img>";
        $post_pop .= "<p>{$testo}</p>";
        $post_pop .= "<p class='dataeora'><span>{$autore_post}</span> {$data} {$ora}</p>";
        $post_pop .= "</div>";
      }
    }
  }

  mysqli_stmt_close($stmt_post_pop);
}
?>
<script>  
var old_p_titolo;
var new_input_titolo;
var old_p_desc;
var new_input_desc;
var old_p_cat;
var new_select_cat;
var old_img;
var new_image;

$(".info-blog").on("click", ".modifica_blog", function() {
  if($(this).val()=="Modifica"){
    $(this).val("Conferma");
    $(this).siblings(".elimina_blog").val("Indietro");

    old_img = $(this).siblings(".img_blog");
    new_image = "<input type='file' name='campo_img' class='campo_img' >";
    old_p_titolo = $(this).siblings(".titolo_blog");
    new_input_titolo = "<input type='text' name='campo_titolo' class='campo_titolo' >";
    old_p_cat = $(this).siblings(".cat_blog");
    new_select_cat = "<select name='campo_categoria' class='campo_categoria'></select>";
    old_p_desc = $(this).siblings(".descrizione_blog");
    new_input_desc = $("<input type='text' name='campo_descrizione' class='campo_descrizione' >");

    old_img.hide();
    old_p_titolo.hide();
    old_p_cat.hide();
    old_p_desc.hide();

    var form = $("<form class='form_modifica_blog' method='post' action='modifica_blog.php' enctype='multipart/form-data'></form>");
    form.append("<div class='titolo_campo'>Immagine:</div>");
    form.append(new_image);
    form.append("<div class='titolo_campo'>Titolo:</div>");
    form.append(new_input_titolo);
    form.append("<div class='titolo_campo'>Descrizione:</div>");
    form.append(new_input_desc);
    form.append("<div class='titolo_campo'>Categoria:</div>");
    form.append(new_select_cat);

    form.find(".campo_titolo").val(old_p_titolo.text());
    form.find(".campo_descrizione").val(old_p_desc.text());
    $.get("categorie_select.php", function(data) {
      if(data=="Sessione annullata"){
        location.replace("registrazione.php");
      }
      form.find(".campo_categoria").html(`<option value='${old_p_cat.text()}' selected>${old_p_cat.text()}</option>${data}`);
    });

    $(this).closest(".info-blog").prepend(form);
  }else{
    var idblog = $(this).data("blog-id");
    var form = $(".form_modifica_blog");
    var formData = new FormData(form[0]);
    formData.append('idblog', idblog);

    if (confirm("Sicuro di voler modificare il seguente Blog?")) {
      $.ajax({
        type: form.attr("method"),
        url: form.attr("action"), 
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          var responseObject = JSON.parse(response);
          if (responseObject.status === "OK") {
            var updatedData = responseObject.data;

            form.remove();
            old_img.show();
            old_p_titolo.show();
            old_p_cat.show();
            old_p_desc.show();

            old_img.attr('src', updatedData.img_blog);
            old_p_titolo.text(updatedData.titolo_blog);
            old_p_desc.text(updatedData.descrizione_blog);
            old_p_cat.text(updatedData.cat_blog);

            $(".modifica_blog").val("Modifica");
            $(".modifica_blog").siblings(".elimina_blog").val("Elimina");
            old_p_cat.after("<p class='modifica_avvenuta'>Modifiche avvenute con successo!</p>");

          } else if(response === "Sessione annullata"){
            location.replace("registrazione.php");
          } else if(response === "richiesta fallita"){
            form.append("<p class='errore_modifica'>Errore nella modifica del blog</p>");
          }else if(response === "errore"){
            form.append("<p class='errore_modifica'>Errore nel DataBase</p>");
          }else{
            form.append(response);
          }
        },
        error: function() {
          alert("Errore nel comunicare col server");
        }
      });
    }
  }         
});        
$(".info-blog").on("click", ".elimina_blog", function() {
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
            $(".cat_blog").after("<p class='errore_modifica'>Errore nell'eliminazione del blog</p>");
          }else if(response === "errore"){
            $(".cat_blog").after("<p class='errore_modifica'>Errore nel DataBase</p>");
          }
        },
        error: function() {
          alert("Errore nel comunicare col server");
        }
      });
    }
  }else{
    old_img.show();
    old_p_titolo.show();
    old_p_desc.show();
    old_p_cat.show();
    $(".form_modifica_blog").remove();
    $(".errore_modifica").remove();
    $(this).val("Elimina");
    $(this).siblings(".modifica_blog").val("Modifica");
  }
});

var old_p_coautori;
var searchInput;
var searchResults;
var form;
$(".info-blog").on("click", ".modifica_coautore", function() {
  if ($(this).val() == "Modifica") {
    $(this).val("Conferma");
    var old_p_coautori = $(this).siblings("p.nomi_autori:eq(1)");
    var input_indietro = "<input type='button' class='indietro_coautori' value='Indietro'>";
    $(this).after(input_indietro);

    old_p_coautori.hide();
    form = $("<form class='form_modifica_coautori' method='post' action='modifica_coautori.php'></form>");
    var searchInput = $("<input type='text' name='coautori' id='coautori'>");
    var searchResults = $("<div id='searchResults'></div>");
    
    if (old_p_coautori.text() == "Nessun coautore") {
      searchInput.val("");
    } else {
      searchInput.val(old_p_coautori.text());
    }

    form.append(searchInput);
    form.append(searchResults);
    $(this).before(form);

    form.on("input", "#coautori", function() {
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

    form.on("click", "#searchResults li", function() {
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
  }else{
      if (confirm("Sicuro di voler modificare i coautori?")) {

      var formData = form.serialize();
      var idblog = $(".modifica_blog").data("blog-id");
      formData +="&idblog=" + idblog;

      $.ajax({
        type: form.attr("method"),
        url: form.attr("action"),
        data: formData,
        success: function(data) {
          if(data=="OK"){
            alert("modifiche avvenute con successo");
          }else{
            alert("errore nella modifica");
          }
        }
      });  
    }
  }
});

$(".info-blog").on("click", ".indietro_coautori", function(){
  $(this).siblings("p.nomi_autori:eq(1)").show();
  $(this).siblings(".form_modifica_coautori").remove();
  $(this).siblings(".modifica_coautore").val("Modifica");
  $(this).remove();
});

$("#crea_post").click(function(){
          $('#form_crea_post').toggle();
          if($(this).val()=="Annulla"){
            $(this).val("Crea un nuovo post");
            $(this).css("margin-left","");
          }else{
            $(this).val("Annulla");
            $(this).css("margin-left","25%");
          }
        });
$("#form_crea_post").validate({
          rules : {
            titolo_post: {
              required : true,
              maxlength:30
              },
            testo_post : {
              required : true
                } 
            },
          messages : {
            titolo_post: {
              required: "Inserire un titolo",
              maxlength: "Inserire massimo 30 caratteri"
            },
            testo_post: {
              required : "Inserire un contenuto"
            },
          }
        });
const now = new Date();
const formattedDate = now.toISOString().split('T')[0];
const formattedTime = now.toTimeString().split(' ')[0];
$("#data_post").val(formattedDate);
$("#ora_post").val(formattedTime);

$("#form_crea_post").on("submit", function(event){
  var blogId = $(this).find("#crea_post").data("blog-id");
  var form = $(this);
  var formData = new FormData(form[0]);
  formData.append('blogId', blogId);
  
  if($(this).valid()){
    $("#error_message").hide();
    event.preventDefault();
    var formData = formData;
    $.ajax({
      type: "POST",
      url: $("#form_crea_post").attr("action"),
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

$("#post_recenti").click(function(){
  $(".post_pop").hide();
  $(".post_rec").show();
  $("#label_recenti").addClass("selected");
  $("#label_popolari").removeClass("selected");
});
$("#post_popolari").click(function(){
  $(".post_rec").hide();
  $(".post_pop").show();
  $("#label_popolari").addClass("selected");
  $("#label_recenti").removeClass("selected");
}); 
  </script>
    <?php echo $crea ?>
    <form id="form_crea_post" action="crea_post.php" method="post" enctype="multipart/form-data" hidden>
        <div class="field">
          <label>Titolo</label></br>
          <input type="text" name="titolo_post" id="titolo_post">
        </div>
        <div class="field">
          <label>Immagine</label></br>
          <input type="file" id="immagine_post" name="immagine_post">
        </div>
        <div class="field">
          <label>Testo </label></br>
          <input type="text" name="testo_post" id="testo_post">
        </div>
        <div class="field">
        <label for="eventDate">Data:</label>
        <input type="date" id="data_post" name="data_post" readonly>
        </div>
        <div class="field">
        <label for="eventTime">Ora:</label>
        <input type="time" id="ora_post" name="ora_post" readonly>
        </div>
        <div class="field">
          <input type="submit" id="crea_post" value="Crea" data-blog-id="<?php echo $idBlog; ?>">
        </div>
        </br>
        <p id="error_message"></p>
      </form>
    <div class="grid-post">
      <div class="colonna-blog">
        <p class="titolo">Info e gestione blog</p>
        <div class="info-blog"><?php echo $html ?></div>
      </div>
      <div class="post">
        <p class="titolo">I post di <?php echo $Blog_title ?></br>
         <input type="radio" id="post_recenti" name="post_recenti" value="Recenti"><label for="post_recenti" id="label_recenti" class="selected">Recenti</label>
         <input type="radio" id="post_popolari" name="post_popolari" value="Popolari"><label for="post_popolari" id="label_popolari">Popolari</label>
        </p>
        <div class="post_rec"><?php echo $post_rec ?></div>
        <div class="post_pop" hidden><?php echo $post_pop ?></div>
      </div>
      <div class="p_controllo">
        <p class="titolo">Pannello di controllo</p>
        <div class="comandi_controllo"></div>
      </div>
    </div>
