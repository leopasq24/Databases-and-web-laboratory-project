      function PrimaVisita() {
        var cookies = document.cookie.split(';');
        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i].trim();
            if (cookie.indexOf('visited=') === 0) {
                return false;
            }
        }
        document.cookie = 'visited=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
        return true;
      }

      $(document).ready(function() {
        if (PrimaVisita()) {
          location.replace("intro.php");
        }

        $(".macro-categorie").on("click", ".macrocat", function(){
          var macrocat = $(this);
          var macrocat_id = macrocat.data("cat-id");
          var freccia = macrocat.find("#freccia");
          var microCategoria = macrocat.find(".micro-categoria");
          var requestData = {
            key1: macrocat_id
          };

          microCategoria.toggleClass("visible");

          if (microCategoria.hasClass("visible")) {
            freccia.css({'transform': 'rotate(90deg)'});
            $.get("sottocategorie.php", requestData, function(data) {
              if(data=="Sessione annullata"){
                location.replace("registrazione.php");
              }
              microCategoria.html(data);
              microCategoria.show();
            });
          } else {
            freccia.css({'transform': 'rotate(0deg)'});
            microCategoria.hide();
          }
        });

        $(".griglia_blog").on("click", ".blog", function(){
          var idBlog = $(this).data("blog-id");

          window.location.href = "singolo_blog.php?id=" + idBlog;
        });

        $(".macro-categorie").on("click", "#macrocat_nome", function(){
          var categoria = $(this).closest(".macrocat").data("cat-id");

          window.location.href = "categoria.php?id=" + categoria;
        });

        $(".macro-categorie").on("click", ".microcat", function(){
          var categoria = $(this).data("cat-id");

          window.location.href = "categoria.php?id=" + categoria;
        });

        $(document).on("click", ".nuovo-post .titolo_blog span" , function(){
          var idBlog = $(this).closest(".nuovo-post").data("blog-id");

          window.location.href = "singolo_blog.php?id=" + idBlog;
        });

        $(document).on("click", ".popolari .blog-pop" , function(){
          var idBlog = $(this).data("blog-id");

          window.location.href = "singolo_blog.php?id=" + idBlog;
        });

      

        $("#blog_personali").click(function(){
          $(".ultimi-post").hide();
          $(".ultimi-post-personali").show();
          $("#label_personali").addClass("selected");
          $("#label_generali").removeClass("selected");
          var Data={
            operazione : "personali",
          };
          $.ajax({
                      url: "post_home.php",
                      type: "GET",
                      data: Data,
                      dataType: "json",
                      success: function(res) {
                        $(".ultimi-post-personali").empty();
                        $(".ultimi-post-personali").append(res.data);
                        n_post_personali = res.conta;
                      },
                      error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                      }
                    });
          $(".ultimi-post-personali").scrollTop(0);
        });
        $("#blog_generali").click(function(){
          $(".ultimi-post-personali").hide();
          $(".ultimi-post").show();
          $("#label_generali").addClass("selected");
          $("#label_personali").removeClass("selected");
          var Data={
            operazione : "generali",
          };
          $.ajax({
                      url: "post_home.php",
                      type: "GET",
                      data: Data,
                      dataType: "json",
                      success: function(res) {
                        $(".ultimi-post").empty();
                        $(".ultimi-post").append(res.data);
                        n_post_generali = res.conta;
                      },
                      error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                      }
                    });
          $(".ultimi-post").scrollTop(0);
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
                  if($("#label_generali").hasClass("selected")){
                    $.ajax({
                      url: "post_home.php",
                      type: "GET",
                      data: { operazione: "generali"},
                      dataType: "json",
                      success: function(res) {
                        $(".ultimi-post").empty();
                        $(".ultimi-post").append(res.data);
                        n_post_generali = res.conta;
                      },
                      error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                      }
                    });
                  }else{
                    $.ajax({
                      url: "post_home.php",
                      type: "GET",
                      data: { operazione: "personali"},
                      dataType: "json",
                      success: function(res) {
                        $(".ultimi-post-personali").empty();
                        $(".ultimi-post-personali").append(res.data);
                        n_post_personali = res.conta;
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
              if($("#label_generali").hasClass("selected")){
                $.ajax({
                      url: "post_home.php",
                      type: "GET",
                      data: { operazione: "generali"},
                      dataType: "json",
                      success: function(res) {
                        $(".ultimi-post").empty();
                        $(".ultimi-post").append(res.data);
                        n_post_generali = res.conta;
                      },
                      error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                      }
                    });
              }else{
                $.ajax({
                      url: "post_home.php",
                      type: "GET",
                      data: { operazione: "personali"},
                      dataType: "json",
                      success: function(res) {
                        $(".ultimi-post-personali").empty();
                        $(".ultimi-post-personali").append(res.data);
                        n_post_personali = res.conta;
                      },
                      error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                      }
                    });
              }
            })  
          }else{
            if($("#label_generali").hasClass("selected")){
              $.ajax({
                      url: "post_home.php",
                      type: "GET",
                      data: { operazione: "generali"},
                      dataType: "json",
                      success: function(res) {
                        $(".ultimi-post").empty();
                        $(".ultimi-post").append(res.data);
                        n_post_generali = res.conta;
                      },
                      error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                      }
                    });
            }else{
              $.ajax({
                      url: "post_home.php",
                      type: "GET",
                      data: { operazione: "personali"},
                      dataType: "json",
                      success: function(res) {
                        $(".ultimi-post-personali").empty();
                        $(".ultimi-post-personali").append(res.data);
                        n_post_personali = res.conta;
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
            var input_contenuto = "<input class='campo_nuovo_contenuto' required maxlength='200'></input>";
            var input_titolo = "<input class='campo_nuovo_titolo' required maxlength='50'></input>";
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
            }else if(new_titolo.length>50 && new_contenuto.length>200){
              pulsante.closest(".nuovo-post").find("#errore_nuovo_titolo").text("Max 50 caratteri per il titolo");
              pulsante.closest(".nuovo-post").find("#errore_nuovo_cont").text("Max 200 caratteri per il contenuto");
              return;
            }else if(new_titolo.length>50){
              pulsante.closest(".nuovo-post").find("#errore_nuovo_titolo").text("Max 50 caratteri per il titolo");
              pulsante.closest(".nuovo-post").find("#errore_nuovo_cont").text("");
              return;
            }
            else if(new_contenuto.length>200){
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
                  if($("#label_generali").hasClass("selected")){
                    $.ajax({
                      url: "post_home.php",
                      type: "GET",
                      data: { operazione: "generali"},
                      dataType: "json",
                      success: function(res) {
                        $(".ultimi-post").empty();
                        $(".ultimi-post").append(res.data);
                        n_post_generali = res.conta;
                      },
                      error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                      }
                    });
                  }else{
                    $.ajax({
                      url: "post_home.php",
                      type: "GET",
                      data: { operazione: "personali"},
                      dataType: "json",
                      success: function(res) {
                        $(".ultimi-post-personali").empty();
                        $(".ultimi-post-personali").append(res.data);
                        n_post_personali = res.conta;
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
              messaggio_errore=true;
              pulsante.parent(".commenti").append(data);
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



      var loading_post = false;
      $(document).find(".ultimi-post, .ultimi-post-personali").scroll(function() {
        var bottomDistance = $(this)[0].scrollHeight - $(this).scrollTop() - $(this).outerHeight();
        if (bottomDistance <= 1 && loading_post==false) {
          loading_post = true;
          $(this).append("<img class='caricamento' src='foto/caricamento.gif'>");

          setTimeout(function() {
            if($("#label_generali").hasClass("selected")){
              $.ajax({
                url: "post_home.php",
                type: "GET",
                data: { operazione: "nuovi_generali", numero: n_post_generali },
                dataType: "json",
                success: function(res) {
                  $(".ultimi-post").find(".caricamento").remove();
                  $(".ultimi-post").append(res.data);
                  n_post_generali += res.conta;
                },
                error: function(xhr, status, error) {
                  console.error("AJAX error:", status, error);
                }
              });
            }else{
              $.ajax({
                url: "post_home.php",
                type: "GET",
                data: { operazione: "nuovi_personali", numero: n_post_personali },
                dataType: "json",
                success: function(res) {
                  $(".ultimi-post-personali").find(".caricamento").remove();
                  $(".ultimi-post-personali").append(res.data);
                  n_post_personali += res.conta;
                },
                error: function(xhr, status, error) {
                  console.error("AJAX error:", status, error);
                }
              });
          }
          loading_post = false;
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

          // Barra di ricerca (per blog e post)
      $(document).on("click", ".search-container", function() {
        $(".results_ricerca").show();
        $(this).css({"background": "white", "width": "35%", "margin-left": "32%", "box-shadow": "0px 15px 20px rgba(0,0,0,0.1)", "transform": "scale(1)"});
        $(".search-input").css("width", "20%").show();
        $("i#lente").removeClass();
        $("i#lente").addClass("fas fa-eraser canc");
        $("i#lente").css("color", "#3aa6ff");
        $(".close").off("click").remove();
        $("i#lente").after("<i style='color:red' class='fas fa-times close'></i>");
        $(".close").css({"font-size":"15px", "margin-left": "50px", "margin-right": "-250px"});

        $(".filtri_ricerca").show();

        $(".close").on("click", function() {
          $(".results_ricerca").hide();
          $(".search-container").removeAttr("style");
          $(this).remove();
          $("i#lente").removeClass();
          $("i#lente").addClass("fas fa-search");
          $("i#lente").css("color", "white");
          $(".search-input").hide();
          $(".filtri_ricerca").hide();
        });

        $(".canc").on("click", function() {
          $(".search-input").val("");
          $(".results_ricerca").text("");
        });
      });

    // Filtri barra di ricerca
      $("#cerca_blog").on("click", function() {
        $("label#cerca_blog").addClass("selected");
        $("label#cerca_post").removeClass("selected");
        var stringa = $(".search-input").val();
        if(stringa!=""){
          $.ajax({
                url: "ricerca.php",
                type: "GET",
                data: { selezione:"blog", str: stringa},
                success: function(data) {
                  $(".results_ricerca").empty();
                  $(".results_ricerca").append(data);
                },
                error: function(xhr, status, error) {
                  console.error("AJAX error:", status, error);
                }
              });
        }
      });

      $("#cerca_post").on("click", function() {
        $("label#cerca_post").addClass("selected");
        $("label#cerca_blog").removeClass("selected");
        var stringa = $(".search-input").val();
        if(stringa!=""){
          $.ajax({
                url: "ricerca.php",
                type: "GET",
                data: { selezione:"post", str: stringa},
                success: function(data) {
                  $(".results_ricerca").empty();
                  $(".results_ricerca").append(data);
                },
                error: function(xhr, status, error) {
                  console.error("AJAX error:", status, error);
                }
              });
        }
      });

      $(document).find(".search-input").on("input", function(){
        var stringa = $(this).val();
        $(".results_ricerca").html("<img class = 'caricamento_risultati' src='foto/caricamento_2.gif'>");
        setTimeout(function(){if($(".filtri_ricerca #cerca_blog").hasClass("selected")){
          $.ajax({
                url: "ricerca.php",
                type: "GET",
                data: { selezione:"blog", str: stringa},
                success: function(data) {
                  $(".results_ricerca").empty();
                  $(".results_ricerca").append(data);
                },
                error: function(xhr, status, error) {
                  console.error("AJAX error:", status, error);
                }
              });
        }else{
          $.ajax({
                url: "ricerca.php",
                type: "GET",
                data: { selezione:"post", str: stringa},
                success: function(data) {
                  $(".results_ricerca").empty();
                  $(".results_ricerca").append(data);
                },
                error: function(xhr, status, error) {
                  console.error("AJAX error:", status, error);
                }
              });
        }}, 1000);
      });

      $(document).on("click", ".results_ricerca #risultati", function(){
        var idBlog = $(this).data("blog-id");
        
        window.location.href = "singolo_blog.php?id=" + idBlog;
      });

      var loading_risultati = false;

      $(document).find(".results_ricerca").scroll(function() {
        var stringa = $(".search-input").val();
        var bottomDistance = $(this)[0].scrollHeight - $(this).scrollTop() - $(this).outerHeight();
        var n_risultati = 0;
        $(this).find("tr#risultati").each(function(){
          n_risultati++;
        });
        if (bottomDistance <= 1 && loading_risultati==false) {
          loading_risultati = true;
          $(this).find("table").append("<img class='caricamento_nuovi_risultati' src='foto/caricamento_punti.gif'>");

          setTimeout(function() {
            if($(".filtri_ricerca #cerca_blog").hasClass("selected")){
              $.ajax({
                url: "ricerca.php",
                type: "GET",
                data: { selezione: "nuovi_risultati_blog", str: stringa, numero: n_risultati },
                dataType: "json",
                success: function(res) {
                  $(".results_ricerca").find(".caricamento_nuovi_risultati").remove();
                  $(".results_ricerca table tbody").append(res.data);
                  n_risultati += res.conta;
                },
                error: function(xhr, status, error) {
                  console.error("AJAX error:", status, error);
                }
              });
            }else{
              $.ajax({
                url: "ricerca.php",
                type: "GET",
                data: { selezione: "nuovi_risultati_post", str: stringa, numero: n_risultati },
                dataType: "json",
                success: function(res) {
                  $(".results_ricerca").find(".caricamento_nuovi_risultati").remove();
                  $(".results_ricerca table tbody").append(res.data);
                  n_risultati += res.conta;
                },
                error: function(xhr, status, error) {
                  console.error("AJAX error:", status, error);
                }
              });
          }
          loading_risultati = false;
          }, 1000);
        }
      });
    });