$(document).ready(function() {
    $.get("blog.php", function(data) {
      if(data=="Sessione annullata"){
        location.replace("registrazione.php");
      }
      $(".griglia_blog").html(data);
    });
    $(".griglia_blog").on("click", ".blog", function(){
      var idBlog = $(this).data("blog-id");

      window.location.href = "singolo_blog.php?id=" + idBlog;
      
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
      if($(this)!="Nessun risultato"){
        var selezione = $(this).text();
        var valore = searchInput.val();
        if (valore) {
          var elenco = valore.split(' ');
          if (elenco.indexOf(selezione) === -1) {
            searchInput.val(valore + selezione);
          }
        } else {
          searchInput.val(selezione);
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