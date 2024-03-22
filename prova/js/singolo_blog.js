$(document).ready(function() {

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

  var form = $("<form class='form_modifica_blog' id = 'form_modifica_blog' method='post' action='modifica_blog.php' enctype='multipart/form-data'></form>");
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

  form.validate({
      rules : {
        campo_titolo: {
          required : true,
          maxlength: 15
          },
        campo_descrizione: {
          required : true,
          maxlength: 40
            }
        },
      messages : {
        campo_titolo: {
          required: "Inserire un titolo",
          maxlength: "Inserire massimo 15 caratteri"
        },
        campo_descrizione: {
          required : "Inserire una descrizione",
          maxlength: "Inserire massimo 40 caratteri"
        }
      }
  });
}else{
  var idblog = $(this).data("blog-id");
  var form = $(".form_modifica_blog");
  var formData = new FormData(form[0]);
  formData.append('idblog', idblog);

  $("#messaggio_conferma").find("span").text("modificare");
  $("#messaggio_conferma").css("display","block");
  $("#messaggio_conferma").find(".domanda").show();
  $("#messaggio_conferma").find(".conferma_modifica").show();
  $("#messaggio_conferma").find(".annulla_modifica").show();
  $("#messaggio_conferma").find(".modifica_avvenuta").remove();
  $("#messaggio_conferma").find(".ok_modifica").remove();

  $("#messaggio_conferma").on("click", ".annulla_modifica", function(){
    $("#messaggio_conferma").css("display","none");
  });
  $("#messaggio_conferma").off("click", ".conferma_modifica");
  $("#messaggio_conferma").on("click", ".conferma_modifica", function(event){
      event.stopPropagation();
      var risposta;

      $.ajax({
        type: form.attr("method"),
        url: form.attr("action"), 
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          if(response === "Sessione annullata"){
            location.replace("registrazione.php");
          }else{
            if (response == "OK") {
              risposta = "Modifiche avvenute con successo!";
              $("#messaggio_conferma").on("click",".ok_modifica", function(){
                location.reload();
              })
            } else {
              risposta = response;
            }

            $("#messaggio_conferma").find(".domanda").hide();
            $("#messaggio_conferma").find(".conferma_modifica").hide();
            $("#messaggio_conferma").find(".annulla_modifica").hide();
            $("#messaggio_conferma").find(".contenuto_messaggio").append("<div class='modifica_avvenuta'>"+risposta+"</div><button class='ok_modifica'>Ok</button>");
            $("#messaggio_conferma").on("click",".ok_modifica", function(){
              $("#messaggio_conferma").css("display","none");
            })
          }
          
        },
        error: function() {
          alert("Errore nel comunicare col server");
        }
      });
  });
}         
});        
$(".info-blog").on("click", ".elimina_blog", function() {
if ($(this).val()=="Elimina"){
  var blogId = $(this).data("blog-id");

  $("#messaggio_conferma").find("span").text("eliminare");
  $("#messaggio_conferma").css("display","block");
  $("#messaggio_conferma").find(".domanda").show();
  $("#messaggio_conferma").find(".conferma_modifica").show();
  $("#messaggio_conferma").find(".annulla_modifica").show();
  $("#messaggio_conferma").find(".modifica_avvenuta").remove();
  $("#messaggio_conferma").find(".ok_modifica").remove();

  $("#messaggio_conferma").on("click", ".annulla_modifica", function(){
    $("#messaggio_conferma").css("display","none");
  });
  $("#messaggio_conferma").off("click", ".conferma_modifica");
  $("#messaggio_conferma").on("click", ".conferma_modifica", function() {
    var risposta;
    $.ajax({
      type: "POST",
      url: "elimina_blog.php", 
      data: { blogId: blogId },
      success: function(response) {
        if (response == "OK") {
          risposta="Blog eliminato correttamente";
        } else if(response === "Sessione annullata"){
          location.replace("registrazione.php");
        } else if(response === "richiesta fallita"){
          risposta="Errore nell'eliminazione del blog";
        }else if(response === "errore"){
         risposta= "Errore nel DataBase";
        }
        
        $("#messaggio_conferma").find(".domanda").hide();
        $("#messaggio_conferma").find(".conferma_modifica").hide();
        $("#messaggio_conferma").find(".annulla_modifica").hide();
        $("#messaggio_conferma").find(".contenuto_messaggio").append("<div class='modifica_avvenuta'>"+risposta+"</div><button class='ok_modifica'>Ok</button>");
        $("#messaggio_conferma").on("click",".ok_modifica", function(){
          $("#messaggio_conferma").css("display","none");
          location.replace("i_tuoi_blog.php");
        })
      },
      error: function() {
        alert("Errore nel comunicare col server");
      }
    });
  });
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
  old_p_coautori = $(this).siblings("p.nomi_autori:eq(1)");
  old_p_coautori.before("<p class='direttive_coautori'>Separare i coautori con una virgola</p>");
  var input_indietro = "<input type='button' class='indietro_coautori' value='Indietro'>";
  $(this).after(input_indietro);

  old_p_coautori.hide();
  form = $("<form class='form_modifica_coautori' method='post' action='modifica_coautori.php'></form>");
  searchInput = $("<input type='text' name='coautori' id='coautori'>");
  searchResults = $("<div id='searchResults'></div>");

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
    if($(this).text()!="Nessun risultato"){
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
    }
  });
}else{

  function messaggio_conferma() {
    $("#messaggio_conferma").find("span").text("modificare");
    $("#messaggio_conferma").css("display", "block");
    $("#messaggio_conferma").find(".domanda").show();
    $("#messaggio_conferma").find(".conferma_modifica").show();
    $("#messaggio_conferma").find(".annulla_modifica").show();
    $("#messaggio_conferma").find(".modifica_avvenuta").remove();
    $("#messaggio_conferma").find(".ok_modifica").remove();
  }

  messaggio_conferma();

  $("#messaggio_conferma").off("click", ".annulla_modifica").on("click", ".annulla_modifica", function() {
    $("#messaggio_conferma").css("display", "none");
  });


  $(document).off("click", "#messaggio_conferma .conferma_modifica").on("click", "#messaggio_conferma .conferma_modifica", function() {
    var risposta;
    var formData = form.serialize();
    var idblog = $(".modifica_blog").data("blog-id");
    formData += "&idblog=" + idblog;

    $.ajax({
        type: form.attr("method"),
        url: form.attr("action"),
        data: formData,
        success: function(response) {
            var responseObject = JSON.parse(response);
            if (responseObject.status == "OK") {
              risposta = "Modifiche avvenute con successo";
              var updatedData = responseObject.data;

              form.remove();
              old_p_coautori.show();
              old_p_coautori.text(updatedData);

              $(".modifica_coautore").val("Modifica");
              $(".modifica_coautore").siblings(".indietro_coautori").hide();
              $(".direttive_coautori").remove();
            } else if (responseObject.status == "Errore") {
              risposta = responseObject.data;
          }
          $("#messaggio_conferma").find(".domanda").hide();
          $("#messaggio_conferma").find(".conferma_modifica").hide();
          $("#messaggio_conferma").find(".annulla_modifica").hide();
          $("#messaggio_conferma").find(".contenuto_messaggio").append("<div class='modifica_avvenuta'>" + risposta + "</div><button class='ok_modifica'>Ok</button>");
          $("#messaggio_conferma").off("click", ".ok_modifica").on("click", ".ok_modifica", function() {
            $(this).closest("#messaggio_conferma").css("display", "none");
          })
    },
    error: function(xhr, status, error) {
        alert("Errore nel comunicare col server:".error);
    },
  });
});
}
});

$(".info-blog").on("click", ".indietro_coautori", function(){
$(this).siblings("p.nomi_autori:eq(1)").show();
$(this).siblings(".direttive_coautori").remove();
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
          maxlength:50
          },
        testo_post : {
          required : true,
          maxlength: 1800
            } 
        },
      messages : {
        titolo_post: {
          required: "Inserire un titolo",
          maxlength: "Inserire massimo 50 caratteri"
        },
        testo_post: {
          required : "Inserire un contenuto",
          maxlength: "Inserire massimo 1800 caratteri"
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
        $("#crea_post").val("Crea un nuovo post");
        $("#crea_post").css("margin-left","");
        $("#form_crea_post").hide();
        var feedback = "<div class='feedback_creazione_post'><div class='contenuto'>Post creato correttamente<div><button type='button' class='ok'>Ok</button><div></div></div>";
        $("#crea_post").after(feedback);
        $(".feedback_creazione_post").css("display","block");
        $(".feedback_creazione_post").on("click",".ok",function(){
          location.reload();
        });
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
var Data ={
  idBlog : id_Blog,
  operazione : "recenti",
};
$.ajax({
  url: "post_singolo_blog.php",
  type: "GET",
  data: Data,
  dataType: "json",
  success: function(res) {
    $(".post_rec").empty();
    $(".post_rec").append(res.data);
    n_post_recenti = res.conta;
  },
  error: function(xhr, status, error) {
    console.error("AJAX error:", status, error);
  }
});
$(".post_rec").scrollTop(0);
});

$("#post_popolari").click(function(){
$(".post_rec").hide();
$(".post_pop").show();
$("#label_popolari").addClass("selected");
$("#label_recenti").removeClass("selected");
var Data ={
  idBlog : id_Blog,
  operazione : "popolari",
};
$.ajax({
  url: "post_singolo_blog.php",
  type: "GET",
  data: Data,
  dataType: "json",
  success: function(res) {
    $(".post_pop").empty();
    $(".post_pop").append(res.data);
    n_post_popolari = res.conta;
  },
  error: function(xhr, status, error) {
    console.error("AJAX error:", status, error);
  }
});
$(".post_pop").scrollTop(0);
});

$(document).on("click", ".dati-blog .cat_blog", function(){
var idCat = $(this).data("cat-id");

window.location.href = "categoria.php?id=" + idCat;
});

$(document).on("click", ".altri-blog .altro_blog", function(){
var idBlog = $(this).data("blog-id");

window.location.href = "singolo_blog.php?id=" + idBlog;
});

$(document).on("click", ".blog-simili .blog_simile", function(){
var idBlog = $(this).data("blog-id");

window.location.href = "singolo_blog.php?id=" + idBlog;
});


$(document).on("click", ".nuovo-post .elimina_post", function(){
if($(this).text()=="Elimina"){
var messaggio_conferma = "<div class='conferma_eliminazione_post'><div class='contenuto'>Sicuro di voler eliminare il post selezionato?<div><button class='conferma'>Conferma</button><button class='annulla'>Annulla</button><div></div></div>";
$(this).closest(".nuovo-post").append(messaggio_conferma);
$(".conferma_eliminazione_post").css("display","block");
var Post_id = $(this).closest(".nuovo-post").data('post-id');
var Data = {
  idPost: Post_id
}
$(".conferma_eliminazione_post").on("click", ".conferma", Data, function(){
  $.get("manipola_post.php", Data, function(response){
    if(response=="OK"){
      if($("#label_recenti").hasClass("selected")){
        $.ajax({
          url: "post_singolo_blog.php",
          type: "GET",
          data: {operazione:"recenti", idBlog: id_Blog },
          dataType: "json",
          success: function(res) {
            $(".post_rec").empty();
            $(".post_rec").append(res.data);
            n_post_recenti = res.conta;
          },
          error: function(xhr, status, error) {
            console.error("AJAX error:", status, error);
          }
        });
      }else{
        $.ajax({
          url: "post_singolo_blog.php",
          type: "GET",
          data: {operazione:"popolari", idBlog: id_Blog},
          dataType: "json",
          success: function(res) {
            $(".post_pop").empty();
            $(".post_pop").append(res.data);
            n_post_popolari = res.conta;
          },
          error: function(xhr, status, error) {
            console.error("AJAX error:", status, error);
          }
        });
      }
    }else{
      alert(response);
    }
  });
})
$(".conferma_eliminazione_post").on("click", ".annulla", Data, function(){
  if($("#label_recenti").hasClass("selected")){
        $.ajax({
          url: "post_singolo_blog.php",
          type: "GET",
          data: {operazione:"recenti", idBlog: id_Blog},
          dataType: "json",
          success: function(res) {
            $(".post_rec").empty();
            $(".post_rec").append(res.data);
            n_post_recenti = res.conta;
          },
          error: function(xhr, status, error) {
            console.error("AJAX error:", status, error);
          }
        });
      }else{
        $.ajax({
          url: "post_singolo_blog.php",
          type: "GET",
          data: {operazione:"popolari", idBlog: id_Blog},
          dataType: "json",
          success: function(res) {
            $(".post_pop").empty();
            $(".post_pop").append(res.data);
            n_post_popolari = res.conta;
          },
          error: function(xhr, status, error) {
            console.error("AJAX error:", status, error);
          }
        });
      }
  })  
  }else{
    if($("#label_recenti").hasClass("selected")){
        $.ajax({
          url: "post_singolo_blog.php",
          type: "GET",
          data: {operazione:"recenti", idBlog: id_Blog},
          dataType: "json",
          success: function(res) {
            $(".post_rec").empty();
            $(".post_rec").append(res.data);
            n_post_recenti = res.conta;
          },
          error: function(xhr, status, error) {
            console.error("AJAX error:", status, error);
          }
        });
      }else{
        $.ajax({
          url: "post_singolo_blog.php",
          type: "GET",
          data: {operazione:"popolari", idBlog: id_Blog},
          dataType: "json",
          success: function(res) {
            $(".post_pop").empty();
            $(".post_pop").append(res.data);
            n_post_popolari = res.conta;
          },
          error: function(xhr, status, error) {
            console.error("AJAX error:", status, error);
          }
        });
      }
  }
});

$(document).on("click", ".nuovo-post .modifica_post", function(){
var pulsante = $(this);
if(pulsante.text()=="Modifica"){
  var input_contenuto = "<input class='campo_nuovo_contenuto'></input>";
  var input_titolo = "<input class='campo_nuovo_titolo'></input>";
  var old_contenuto = pulsante.closest(".nuovo-post").find(".contenuto_post").text();
  var old_titolo = pulsante.closest(".nuovo-post").find(".titolo_post").text();
  pulsante.closest(".nuovo-post").find(".contenuto_post").replaceWith(input_contenuto);
  pulsante.closest(".nuovo-post").find(".titolo_post").replaceWith(input_titolo);
  pulsante.closest(".nuovo-post").find(".campo_nuovo_contenuto").val(old_contenuto);
  pulsante.closest(".nuovo-post").find(".campo_nuovo_titolo").val(old_titolo);
  var new_image = "<input type='file' name='campo_img_post' class='campo_img_post' >";
  if(pulsante.closest(".nuovo-post").find(".immagine_post").length){
    pulsante.closest(".nuovo-post").find(".immagine_post").replaceWith(new_image);
  }else{
    pulsante.closest(".nuovo-post").find(".campo_nuovo_titolo").after(new_image);
  }

  pulsante.closest(".nuovo-post").find(".campo_nuovo_titolo").after("<div style='color:red' id='errore_nuovo_titolo'></div>");
  pulsante.closest(".nuovo-post").find(".campo_nuovo_contenuto").after("<div style='color:red' id='errore_nuovo_cont'></div>");
  pulsante.text("Conferma");
  pulsante.css("background","linear-gradient(135deg, #33cc33, #1f7a1f)");
  pulsante.siblings(".elimina_post").text("Annulla");
}else{
  var Post_id = pulsante.closest(".nuovo-post").data('post-id');
  var new_contenuto = pulsante.closest(".nuovo-post").find(".campo_nuovo_contenuto").val();
  var new_titolo = pulsante.closest(".nuovo-post").find(".campo_nuovo_titolo").val();
  if($.trim(new_contenuto).length==0 && $.trim(new_titolo).length==0){
    pulsante.closest(".nuovo-post").find("#errore_nuovo_cont").text("Inserire un contenuto");
    pulsante.closest(".nuovo-post").find("#errore_nuovo_titolo").text("Inserire un titolo");
    return;
  }else if($.trim(new_contenuto).length==0){
    pulsante.closest(".nuovo-post").find("#errore_nuovo_cont").text("Inserire un contenuto");
    pulsante.closest(".nuovo-post").find("#errore_nuovo_titolo").text("");
    return;
  }else if($.trim(new_titolo).length==0){
    pulsante.closest(".nuovo-post").find("#errore_nuovo_titolo").text("Inserire un titolo");
    pulsante.closest(".nuovo-post").find("#errore_nuovo_cont").text("");
    return;
  }else if(new_titolo.length>50 && new_contenuto.length>1800){
    pulsante.closest(".nuovo-post").find("#errore_nuovo_titolo").text("Max 50 caratteri per il titolo");
    pulsante.closest(".nuovo-post").find("#errore_nuovo_cont").text("Max 200 caratteri per il contenuto");
    return;
  }else if(new_titolo.length>50){
    pulsante.closest(".nuovo-post").find("#errore_nuovo_titolo").text("Max 50 caratteri per il titolo");
    pulsante.closest(".nuovo-post").find("#errore_nuovo_cont").text("");
    return;
  }else if(new_contenuto.length>200){
    pulsante.closest(".nuovo-post").find("#errore_nuovo_cont").text("Max 200 caratteri per il contenuto");
    pulsante.closest(".nuovo-post").find("#errore_nuovo_titolo").text("");
    return;
  }
    var formDataPost = new FormData();
    var fileInput = pulsante.closest(".nuovo-post").find('.campo_img_post')[0].files[0];
    if (fileInput) {
      formDataPost.append('campo_img_post', fileInput);
    }
    formDataPost.append('idPost', Post_id);
    formDataPost.append('titolo', new_titolo);
    formDataPost.append('contenuto', new_contenuto);
    $.ajax({
      url: "manipola_post.php",
      type: "POST",
      data: formDataPost,
      contentType: false,
      processData: false,
      success: function(response) {
        if(response=="OK"){
      var messaggio_feedback = "<div class='feedback_modifica_post'><div class='contenuto'>Modifica avvenuta correttamente!<div><button class='ok'>Ok</button><div></div></div>";
      pulsante.closest(".nuovo-post").append(messaggio_feedback);
      $(".feedback_modifica_post").css("display","block");
      $(".feedback_modifica_post").on("click", ".ok", function(){
        if($("#label_recenti").hasClass("selected")){
          $.ajax({
          url: "post_singolo_blog.php",
          type: "GET",
          data: {operazione:"recenti", idBlog: id_Blog},
          dataType: "json",
          success: function(res) {
            $(".post_rec").empty();
            $(".post_rec").append(res.data);
            n_post_recenti = res.conta;
          },
          error: function(xhr, status, error) {
            console.error("AJAX error:", status, error);
          }
        });
        }else{
          $.ajax({
          url: "post_singolo_blog.php",
          type: "GET",
          data: {operazione:"popolari", idBlog: id_Blog},
          dataType: "json",
          success: function(res) {
            $(".post_pop").empty();
            $(".post_pop").append(res.data);
            n_post_popolari = res.conta;
          },
          error: function(xhr, status, error) {
            console.error("AJAX error:", status, error);
          }
        });
        }
      })
            
    }else{
      var messaggio_feedback = "<div class='feedback_modifica_post'><div class='contenuto'>"+response+"<div><button class='ok'>Ok</button><div></div></div>";
      pulsante.closest(".nuovo-post").append(messaggio_feedback);
      $(".feedback_modifica_post").css("display","block");
      $(".feedback_modifica_post").on("click", ".ok", function(){
        $(".feedback_modifica_post").remove();
      })
    }
      },
      error: function(xhr, status, error) {
        console.error("AJAX error:", status, error);
      }
    });
}
});

$(document).on("click", ".nuovo-post .mi_piace", function(){
    if ($(this).hasClass("disabled")){
      //
    }else{
      var PostId = $(this).parent(".feedback_e_commenti").data("post-id");
      var requestData = {
        key1: "mi piace",
        key2: PostId
      };
      var pulsante = $(this);
      $.get("feedback.php",requestData, function(data){
        if(data=="Sessione annullata"){
          location.replace("registrazione.php");
        }else if(data=="AGGIUNTO"){
          var old_value = parseInt(pulsante.find("span").html());
          var new_value = old_value+1;
          pulsante.find("span").html(new_value);
          pulsante.addClass('piaciuto');
        }else if(data=="CAMBIATO"){
          var old_value = parseInt(pulsante.find("span").html());
          var new_value = old_value+1;
          pulsante.find("span").html(new_value);
          var old_value_non_mi_piace = parseInt( pulsante.siblings(".non_mi_piace").find("span").html());
          var new_value_non_mi_piace = old_value_non_mi_piace-1;
          pulsante.siblings(".non_mi_piace").find("span").html(new_value_non_mi_piace);
          pulsante.addClass('piaciuto');
          pulsante.siblings(".non_mi_piace").removeClass("non_piaciuto");
        }else if(data=="ELIMINATO"){
          var old_value = parseInt(pulsante.find("span").html());
          var new_value = old_value-1;
          pulsante.find("span").html(new_value);
          pulsante.removeClass('piaciuto');
        }
      }).fail(function() {
        alert("Errore nella richesta");
    });
      var mi_piace =$(this).find("img");
      var animazione = "foto/mi-piace.gif";
      mi_piace.attr("src", animazione);
      $(".non_mi_piace").addClass('disabled');
      setTimeout(function() {
        mi_piace.attr("src", "foto/mi-piace.png");
        $(".non_mi_piace").removeClass('disabled');
      }, 1000);
    }
});
$(document).on("click", ".nuovo-post .non_mi_piace", function(){
    if ($(this).hasClass("disabled")){
      //
    }else{
      var PostId = $(this).parent(".feedback_e_commenti").data("post-id");
      var requestData = {
        key1: "non mi piace",
        key2: PostId
      };
      var pulsante = $(this);
      $.get("feedback.php",requestData, function(data){
        if(data=="Sessione annullata"){
          location.replace("registrazione.php");
        }else if(data=="AGGIUNTO"){
          var old_value = parseInt(pulsante.find("span").html());
          var new_value = old_value+1;
          pulsante.find("span").html(new_value);
          pulsante.addClass('non_piaciuto');
        }else if(data=="CAMBIATO"){
          var old_value = parseInt(pulsante.find("span").html());
          var new_value = old_value+1;
          pulsante.find("span").html(new_value);
          var old_value_mi_piace = parseInt( pulsante.siblings(".mi_piace").find("span").html());
          var new_value_mi_piace = old_value_mi_piace-1;
          pulsante.siblings(".mi_piace").find("span").html(new_value_mi_piace);
          pulsante.addClass('non_piaciuto');
          pulsante.siblings(".mi_piace").removeClass("piaciuto");
        }else if(data=="ELIMINATO"){
          var old_value = parseInt(pulsante.find("span").html());
          var new_value = old_value-1;
          pulsante.find("span").html(new_value);
          pulsante.removeClass('non_piaciuto');
        }
      }).fail(function() {
        alert("Errore nella richesta");
    });
      var non_mi_piace =$(this).find("img");
      var animazione = "foto/non-mi-piace.gif";
      non_mi_piace.attr("src", animazione);
      $(".mi_piace").addClass('disabled');
      setTimeout(function() {
        non_mi_piace.attr("src", "foto/non-mi-piace.png");
        $(".mi_piace").removeClass('disabled');
      }, 1000);
    }
});
  $(document).on("click", ".nuovo-post .commenta", function(){
    if ($(this).hasClass("selected")){
      $(this).css("background-color", "#f2f2f2");
      $(this).siblings(".commenti").remove();
      $(this).removeClass("selected");
    }else{
      $(this).css("background-color", "#c7c7c7");
      $(this).addClass("selected");
      var PostId = $(this).parent(".feedback_e_commenti").data("post-id");
      var requestData = {
        key1: PostId,
        key2: "carica"
      };
      var pulsante = $(this);
      $.get("commento.php",requestData, function(data){
        pulsante.parent(".feedback_e_commenti").append(data);
      })
  }
});
var messaggio_errore = false;
$(document).on("click", ".nuovo-post .pubblica_commento", function(){
    
    var pulsante = $(this);
    var contenuto_campo = $(this).siblings(".campo_commento").val();
    var PostId = $(this).parent(".commenti").parent(".feedback_e_commenti").data("post-id");
    const now = new Date();
    const formattedDate = now.toISOString().split('T')[0];
    const formattedTime = now.toTimeString().split(' ')[0];
    var requestData1 = {
      data: formattedDate,
      ora: formattedTime,
      id_post: PostId,
      contenuto: contenuto_campo,
      operazione: "creazione"
    };
    var requestData2 = {
        key1: PostId,
        key2: "carica"
    };
    $.post("crea_commento.php",requestData1, function(data){
      if(data=="Sessione annullata"){
        location.replace("registrazione.php");
      }else if(data=="OK"){
        $.get("commento.php",requestData2, function(response){
            pulsante.closest(".commenti").replaceWith(response);
        });
        let num_commenti =  parseInt(pulsante.closest(".commenti").siblings(".commenta").find("span").html());
        let new_value = num_commenti+1;
        pulsante.closest(".commenti").siblings(".commenta").find("span").html(new_value);
      }else{
        if(!messaggio_errore){
          pulsante.parent(".commenti").append(data);
          messaggio_errore = true;
        }
      }
      })
  });
  $(document).on("click", ".nuovo-post .elimina_commento", function(){
    if($(this).text()=="Elimina"){
      var bottone =$(this);
      var Commento_Id = bottone.closest(".commento").data("commento-id");
      var PostId = bottone.closest(".feedback_e_commenti").data("post-id");
      var requestData = {
        key1: Commento_Id,
        key2: "elimina"
      };
      var requestData2 = {
        key1: PostId,
        key2: "carica"
      };  
      $.get("commento.php",requestData, function(data){
        if(data=="OK"){
          $.get("commento.php",requestData2, function(response){
            bottone.closest(".commenti").replaceWith(response);
          });
          let num_commenti =  parseInt(bottone.closest(".commenti").siblings(".commenta").find("span").html());
          let new_value = num_commenti-1;
          bottone.closest(".commenti").siblings(".commenta").find("span").html(new_value);
        }
      });
    }else{
      var bottone =$(this);
      var PostId = bottone.closest(".feedback_e_commenti").data("post-id");
      var requestData2 = {
        key1: PostId,
        key2: "carica"
      };
      $.get("commento.php",requestData2, function(response){
        bottone.closest(".commenti").replaceWith(response);
      });
    }
   
  })

  $(document).on("click", ".nuovo-post .modifica_commento", function(){
    
    if ($(this).text()=="Modifica"){
      var pulsante = $(this);
      pulsante.closest(".commento").siblings(".commento").find(".modifica_commento, .elimina_commento").hide();
      pulsante.closest(".commenti").find(".altri_commenti").hide();
      $(this).text("Conferma");
      $(this).css("background","linear-gradient(135deg, #33cc33, #1f7a1f)");
      $(this).siblings(".elimina_commento").text("Annulla");

      var contenuto_commento = $(this).parent(".autore").siblings(".contenuto");
      var nuovo_contenuto = "<input class='nuovo_contenuto'></input>";
      contenuto_commento.replaceWith(nuovo_contenuto);
      $(this).parent(".autore").siblings(".nuovo_contenuto").val(contenuto_commento.text());
      $(this).parent(".autore").siblings(".nuovo_contenuto").after("</br><div style='margin-left:2%; color:red; display:inline; border:none' id='errore_nuovo_commento'></div>");

    }else{
      var pulsante = $(this);
      var PostId = pulsante.closest(".feedback_e_commenti").data("post-id");
      var Commento_Id = pulsante.closest(".commento").data("commento-id");
      var contenuto_campo = $(this).parent(".autore").siblings(".nuovo_contenuto").val();
      if($.trim(contenuto_campo).length==0){
        pulsante.closest(".nuovo-post").find("#errore_nuovo_commento").text("Inserire un commento");
        return;
      }else if($.trim(contenuto_campo).length>150){
        pulsante.closest(".nuovo-post").find("#errore_nuovo_commento").text("max 150 caratteri per il commento");
        return;
      }
      var requestData1 = {
        Commento_Id: Commento_Id,
        contenuto: contenuto_campo,
        operazione: "modifica"
      };
      var requestData2 = {
        key1: PostId,
        key2: "carica"
      };
      $.post("crea_commento.php",requestData1, function(data){
        if(data=="Sessione annullata"){
          location.replace("registrazione.php");
        }else if(data=="OK"){
          $.get("commento.php",requestData2, function(response){
            pulsante.closest(".commenti").replaceWith(response);
          });
        }else{
          pulsante.parent(".commenti").append(data);
        }
      })
    } 
  });

  var loading = false;
  $(document).find(".post_rec, .post_pop").scroll(function() {
    var bottomDistance = $(this)[0].scrollHeight - $(this).scrollTop() - $(this).outerHeight();
    if (bottomDistance <= 1 && loading==false) {
      loading = true;
      $(this).append("<img style ='width:8%' class='caricamento' src='foto/caricamento.gif'>");

      setTimeout(function() {
        if($("#label_recenti").hasClass("selected")){
          $.ajax({
            url: "post_singolo_blog.php",
            type: "GET",
            data: { operazione: "nuovi_recenti", numero: n_post_recenti, idBlog: id_Blog},
            dataType: "json",
            success: function(res) {
              $(".post_rec").find(".caricamento").remove();
              $(".post_rec").append(res.data);
              n_post_recenti += res.conta;
            },
            error: function(xhr, status, error) {
              console.error("AJAX error:", status, error);
            }
          });
      }else{
          $.ajax({
            url: "post_singolo_blog.php",
            type: "GET",
            data: { operazione: "nuovi_popolari", numero: n_post_popolari, idBlog: id_Blog},
            dataType: "json",
            success: function(res) {
              $(".post_pop").find(".caricamento").remove();
              $(".post_pop").append(res.data);
              n_post_popolari += res.conta;
            },
            error: function(xhr, status, error) {
              console.error("AJAX error:", status, error);
            }
          });
      }
      loading = false;
      }, 1000);
    }
  });

  var loading_commenti = false;
  $(document).on("click", ".nuovo-post .feedback_e_commenti .commenti .elenco_commenti .altri_commenti", function(){
      loading_commenti = true;
      var elenco = $(this).closest(".elenco_commenti");
      var PostId = $(this).closest(".feedback_e_commenti").data("post-id");
      var n_commenti = 0;
      elenco.find(".commento").each(function(){
        n_commenti++;
      });
      $(this).replaceWith("<img class='caricamento' src='foto/caricamento_2.gif'>"); 
      setTimeout(function() {
        $.ajax({
            url: "commento.php",
            type: "GET",
            data: { key1: PostId, key2:"carica_nuovi", numero: n_commenti },
            dataType: "json",
            success: function(res) {
              elenco.find(".caricamento").remove();
              elenco.append(res.data);
              n_commenti += res.conta;
            },
            error: function(xhr, status, error) {
              console.error("AJAX error:", status, error);
            }
          });
        lading_commenti = false;
      }, 1000);
  });


});