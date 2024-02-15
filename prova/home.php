<?php
session_start();
include_once("connect.php");
$id_utente = $_SESSION["session_utente"];

    //Blog popolari
    $html_blog_pop = "<div class='blog-pop-container'>";
    $stmt_blog_pop = mysqli_prepare($link, "SELECT Immagine, Titolo FROM blog WHERE IdBlog IN (SELECT IdBlog FROM post WHERE IdPost IN (SELECT codice FROM post_popolari ORDER BY conta DESC)) LIMIT 7");
    
    mysqli_stmt_execute($stmt_blog_pop);
    $result_blog_pop = mysqli_stmt_get_result($stmt_blog_pop);
    
    while ($row = mysqli_fetch_assoc($result_blog_pop)) {
        $blog_pop_img = $row['Immagine'];
        if ($blog_pop_img === null) {
            $src_img = "foto/blog.png";
        }else{
            $src_img = $blog_pop_img;
        }
        $blog_pop_title = $row['Titolo'];
        
        $html_blog_pop .= "<div class='blog-pop'>";
        $html_blog_pop .= "<img src='{$src_img}' alt='{$blog_pop_title}'></img>";
        $html_blog_pop .= "<p>{$blog_pop_title}</p>";
        $html_blog_pop .= "</div>";
    }
    
    $html_blog_pop .= "</div>";
    mysqli_stmt_close($stmt_blog_pop);


    //Post recenti
    $html_post = "";
    $stmt_posts = mysqli_prepare($link, "SELECT IdPost, post.Titolo AS Titolo_post, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username, blog.IdBlog, Modificato FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente ORDER BY Data DESC LIMIT 7");
    
    if(mysqli_stmt_execute($stmt_posts)){
      $result_posts = mysqli_stmt_get_result($stmt_posts);

      if (mysqli_num_rows($result_posts) == 0) {
          $html_post .= "<p class='nessun_post'>Nessun post</p>";
      }else{
        while ($row = mysqli_fetch_assoc($result_posts)) {
            $id_blog = $row['IdBlog'];
            $id_post = $row['IdPost'];
            $img_blog = $row['immagine_blog'];
            $img_post = $row['immagine_post'];
            $title = $row['Titolo_post'];
            $testo = $row['Testo'];
            $autore_post = $row['Username'];
            $blog = $row['Argomento'];
            $data = $row['Data'];
            $ora = $row['Ora'];
            $modificato = $row['Modificato'];
            if ($img_blog == null) {
                $src_img = "foto/blog.png";
            } else {
                $src_img = $img_blog;
            }
            
            $stmt_feedback_positivi = mysqli_prepare($link, "SELECT numerofeedbackpositivi FROM numero_feedback_positivi WHERE IdPost=?");
            mysqli_stmt_bind_param($stmt_feedback_positivi,"i", $id_post);
            mysqli_stmt_execute($stmt_feedback_positivi);
            $results_feedback_positivi = mysqli_stmt_get_result($stmt_feedback_positivi);
            if (mysqli_num_rows($results_feedback_positivi) == 0) {
              $num_feedback_positivi=0;
            }else{
              while ($row = mysqli_fetch_assoc($results_feedback_positivi)){
                $num_feedback_positivi=$row['numerofeedbackpositivi'];
              }
            }
            mysqli_stmt_close($stmt_feedback_positivi);

            $stmt_feedback_negativi = mysqli_prepare($link, "SELECT numerofeedbacknegativi FROM numero_feedback_negativi WHERE IdPost=?");
            mysqli_stmt_bind_param($stmt_feedback_negativi,"i", $id_post);
            mysqli_stmt_execute($stmt_feedback_negativi);
            $results_feedback_negativi = mysqli_stmt_get_result($stmt_feedback_negativi);
            if (mysqli_num_rows($results_feedback_negativi) == 0) {
              $num_feedback_negativi=0;
            }else{
              while ($row = mysqli_fetch_assoc($results_feedback_negativi)){
                $num_feedback_negativi=$row['numerofeedbacknegativi'];
              }
            }
            mysqli_stmt_close($stmt_feedback_negativi);

            $stmt_numero_commenti = mysqli_prepare($link, "SELECT numerocommenti FROM numero_commenti WHERE IdPost=?");
            mysqli_stmt_bind_param($stmt_numero_commenti,"i", $id_post);
            mysqli_stmt_execute($stmt_numero_commenti);
            $results_numero_commenti = mysqli_stmt_get_result($stmt_numero_commenti);
            if (mysqli_num_rows($results_numero_commenti) == 0) {
              $numero_commenti=0;
            }else{
              while ($row = mysqli_fetch_assoc($results_numero_commenti)){
                $numero_commenti=$row['numerocommenti'];
              }
            }
            mysqli_stmt_close($stmt_numero_commenti);

            $verifica_autore_post = false;
            $stmt_autore_post = mysqli_prepare($link, "SELECT IdUtente FROM post WHERE IdPost=?");
            mysqli_stmt_bind_param($stmt_autore_post,"i", $id_post);
            mysqli_stmt_execute($stmt_autore_post);
            $results_autore_post = mysqli_stmt_get_result($stmt_autore_post);
            while ($row = mysqli_fetch_assoc($results_autore_post)){
              if($row['IdUtente']==$id_utente){
                $verifica_autore_post = true;
              }
            }
            mysqli_stmt_close($stmt_autore_post);

            $verifica_autore_blog = false;
            $stmt_autore_blog = mysqli_prepare($link, "SELECT IdUtente FROM blog WHERE IdBlog=?");
            mysqli_stmt_bind_param($stmt_autore_blog,"i", $id_blog);
            mysqli_stmt_execute($stmt_autore_blog);
            $results_autore_blog = mysqli_stmt_get_result($stmt_autore_blog);
            while ($row = mysqli_fetch_assoc($results_autore_blog)){
              if($row['IdUtente']==$id_utente){
                $verifica_autore_blog = true;
              }
            }
            mysqli_stmt_close($stmt_autore_blog);

            
            if ($img_post === null) {
                $html_post .= "<div class='nuovo-post' data-post-id ='{$id_post}'>";
                $html_post .= "<img src='{$src_img}' alt='{$blog}'></img>";
                if($verifica_autore_blog==true){
                  if($verifica_autore_post==true){
                    $html_post .= "<p>{$blog} <button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
                  }else{
                    $html_post .= "<p>{$blog} <button class='elimina_post'>Elimina</button></p>";
                  }
                }else{
                  $html_post .= "<p>{$blog}</p>";
                }
                $html_post .= "<h4 class = 'titolo_post'>{$title}</h4>";
                $html_post .= "<p class='contenuto_post'>{$testo}</p>";
                if($modificato==1){
                  $html_post .= "<p class ='post_modificato'>(modificato)</p>";
                }
                $html_post .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
                $html_post .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'><div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div><div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
                $html_post .= "</div>";
            } else {
                $html_post .= "<div class='nuovo-post' data-post-id ='{$id_post}'>";
                $html_post .= "<img src='{$src_img}' alt='{$blog}'></img>";
                if($verifica_autore_blog==true){
                  if($verifica_autore_post==true){
                    $html_post .= "<p>{$blog} <button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
                  }else{
                    $html_post .= "<p>{$blog} <button class='elimina_post'>Elimina</button></p>";
                  }
                }else{
                  $html_post .= "<p>{$blog}</p>";
                }
                $html_post .= "<h4>{$title}</h4>";
                $html_post .= "<img class = 'immagine_post' src='{$img_post}' alt='{$title}'></img>";
                $html_post .= "<p  class='contenuto_post'>{$testo}</p>";
                if($modificato==1){
                  $html_post .= "<p class ='post_modificato'>(modificato)</p>";
                }
                $html_post .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
                $html_post .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'><div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div><div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
                $html_post .= "</div>";
            } 
        }
      }
      
    }else{
      $html_post .="Error: " . mysqli_error($link);
    }
    
    mysqli_stmt_close($stmt_posts);

    //Post personali
    $html_post_personali = "";
    $stmt_post_personali = mysqli_prepare($link, "SELECT IdPost, post.Titolo AS Titolo_post, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username, blog.IdBlog, Modificato FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente AND blog.IdUtente = ? ORDER BY Data DESC LIMIT 7");
    
    mysqli_stmt_bind_param($stmt_post_personali, "i", $id_utente);
    if(mysqli_stmt_execute($stmt_post_personali)){
      $result_post_personali = mysqli_stmt_get_result($stmt_post_personali);
    
      if (mysqli_num_rows($result_post_personali) == 0) {
          $html_post_personali .="<img src='foto/vuoto.png' alt='vuoto'></img>";
          $html_post_personali .= "<p id='nessun_post'>Non hai ancora nessun post. <a href='i_tuoi_blog.php'>Creane uno!</a></p>";
      } else {
        while ($row = mysqli_fetch_assoc($result_post_personali)) {
          $id_blog = $row['IdBlog'];
          $id_post = $row['IdPost'];
          $img_blog = $row['immagine_blog'];
          $img_post = $row['immagine_post'];
          $title = $row['Titolo_post'];
          $testo = $row['Testo'];
          $autore_post = $row['Username'];
          $blog = $row['Argomento'];
          $data = $row['Data'];
          $ora = $row['Ora'];
          $modificato = $row['Modificato'];
          if ($img_blog == null) {
            $src_img = "foto/blog.png";
          } else {
            $src_img = $img_blog;
          }

          $stmt_feedback_positivi = mysqli_prepare($link, "SELECT numerofeedbackpositivi FROM numero_feedback_positivi WHERE IdPost=?");
          mysqli_stmt_bind_param($stmt_feedback_positivi,"i", $id_post);
          mysqli_stmt_execute($stmt_feedback_positivi);
          $results_feedback_positivi = mysqli_stmt_get_result($stmt_feedback_positivi);
          if (mysqli_num_rows($results_feedback_positivi) == 0) {
            $num_feedback_positivi=0;
          }else{
            while ($row = mysqli_fetch_assoc($results_feedback_positivi)){
              $num_feedback_positivi=$row['numerofeedbackpositivi'];
            }
          }
          mysqli_stmt_close($stmt_feedback_positivi);

          $stmt_feedback_negativi = mysqli_prepare($link, "SELECT numerofeedbacknegativi FROM numero_feedback_negativi WHERE IdPost=?");
          mysqli_stmt_bind_param($stmt_feedback_negativi,"i", $id_post);
          mysqli_stmt_execute($stmt_feedback_negativi);
          $results_feedback_negativi = mysqli_stmt_get_result($stmt_feedback_negativi);
          if (mysqli_num_rows($results_feedback_negativi) == 0) {
            $num_feedback_negativi=0;
          }else{
            while ($row = mysqli_fetch_assoc($results_feedback_negativi)){
              $num_feedback_negativi=$row['numerofeedbacknegativi'];
            }
          }
          mysqli_stmt_close($stmt_feedback_negativi);

          $stmt_numero_commenti = mysqli_prepare($link, "SELECT numerocommenti FROM numero_commenti WHERE IdPost=?");
          mysqli_stmt_bind_param($stmt_numero_commenti,"i", $id_post);
          mysqli_stmt_execute($stmt_numero_commenti);
          $results_numero_commenti = mysqli_stmt_get_result($stmt_numero_commenti);
          if (mysqli_num_rows($results_numero_commenti) == 0) {
            $numero_commenti=0;
          }else{
            while ($row = mysqli_fetch_assoc($results_numero_commenti)){
              $numero_commenti=$row['numerocommenti'];
            }
          }
          mysqli_stmt_close($stmt_numero_commenti);

          $verifica_autore_post = false;
          $stmt_autore_post = mysqli_prepare($link, "SELECT IdUtente FROM post WHERE IdPost=?");
          mysqli_stmt_bind_param($stmt_autore_post,"i", $id_post);
          mysqli_stmt_execute($stmt_autore_post);
          $results_autore_post = mysqli_stmt_get_result($stmt_autore_post);
          while ($row = mysqli_fetch_assoc($results_autore_post)){
            if($row['IdUtente']==$id_utente){
              $verifica_autore_post = true;
            }
          }
          mysqli_stmt_close($stmt_autore_post);

          $verifica_autore_blog = false;
          $stmt_autore_blog = mysqli_prepare($link, "SELECT IdUtente FROM blog WHERE IdBlog=?");
          mysqli_stmt_bind_param($stmt_autore_blog,"i", $id_blog);
          mysqli_stmt_execute($stmt_autore_blog);
          $results_autore_blog = mysqli_stmt_get_result($stmt_autore_blog);
          while ($row = mysqli_fetch_assoc($results_autore_blog)){
            if($row['IdUtente']==$id_utente){
              $verifica_autore_blog = true;
            }
          }
          mysqli_stmt_close($stmt_autore_blog);

          if ($img_post === null) {
            $html_post_personali .= "<div class='nuovo-post' data-post-id ='{$id_post}'>";
            $html_post_personali .= "<img src='{$src_img}' alt='{$blog}'></img>";
            if($verifica_autore_blog==true){
              if($verifica_autore_post==true){
                $html_post_personali .= "<p>{$blog} <button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
              }else{
                $html_post_personali .= "<p>{$blog} <button class='elimina_post'>Elimina</button></p>";
              }
            }else{
              $html_post_personali .= "<p>{$blog}</p>";
            }
            $html_post_personali .= "<h4>{$title}</h4>";
            $html_post_personali .= "<p  class='contenuto_post'>{$testo}</p>";
            if($modificato==1){
              $html_post_personali .= "<p class ='post_modificato'>(modificato)</p>";
            }
            $html_post_personali .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
            $html_post_personali .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'><div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div><div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
            $html_post_personali .= "</div>";
          } else {
            $html_post_personali .= "<div class='nuovo-post' data-post-id ='{$id_post}'>";
            $html_post_personali .= "<img src='{$src_img}' alt='{$blog}'></img>";
            if($verifica_autore_blog==true){
              if($verifica_autore_post==true){
                $html_post_personali .= "<p>{$blog} <button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
              }else{
                $html_post_personali .= "<p>{$blog} <button class='elimina_post'>Elimina</button></p>";
              }
            }else{
              $html_post_personali .= "<p>{$blog}</p>";
            }
            $html_post_personali .= "<h4>{$title}</h4>";
            $html_post_personali .= "<img  class = 'immagine_post' src='{$img_post}' alt='{$title}'></img>";
            $html_post_personali .= "<p  class='contenuto_post'>{$testo}</p>";
            if($modificato==1){
              $html_post_personali .= "<p class ='post_modificato'>(modificato)</p>";
            }
            $html_post_personali .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
            $html_post_personali .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'><div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div><div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
            $html_post_personali .= "</div>";
          }
        } 
      }
  }else{
    $html_post_personali .="Error: " . mysqli_error($link);
  } 
  mysqli_stmt_close($stmt_post_personali);
?>
<html lang="it" dir="ltr">
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
        $(".macro-categorie").on("click", ".macrocat", function(){
          var macrocat = $(this);
          var macrocat_nome = macrocat.find("#macrocat_nome").text();
          var freccia = macrocat.find("#freccia");
          var microCategoria = macrocat.find(".micro-categoria");
          var requestData = {
            key1: macrocat_nome
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
        $(".macro-categorie").on("click", "#macrocat_nome", function() {
          var categoria = $(this).closest(".macrocat").data("cat-id");
          $.ajax({
            url: "categorie.php",
            type: "GET",
            success: function() {
              location.replace("categoria.php?id=" + categoria);
            },
            error: function(xhr, status, error) {
              console.error(error);
            }
          });
        });
        $(".macro-categorie").on("click", ".microcat", function() { 
          var categoria = $(this).closest(".microcat").data("cat-id");
          $.ajax({
            url: "sottocategorie.php",
            type: "GET",
            success: function() {
              location.replace("categoria.php?id=" + categoria);
            },
            error: function(xhr, status, error) {
              console.error(error);
            }
          });
        });
        $("#blog_personali").click(function(){
          $(".ultimi-post").hide();
          $(".ultimi-post-personali").show();
          $("#label_personali").addClass("selected");
          $("#label_generali").removeClass("selected");
          var Data={
            operazione : "personali",
          };
          $.get("post_home.php", Data, function(data){
            $(".ultimi-post-personali").empty();
            $(".ultimi-post-personali").append(data);
          });
        });
        $("#blog_generali").click(function(){
          $(".ultimi-post-personali").hide();
          $(".ultimi-post").show();
          $("#label_generali").addClass("selected");
          $("#label_personali").removeClass("selected");
          var Data={
            operazione : "generali",
          };
          $.get("post_home.php",Data, function(data){
            $(".ultimi-post").empty();
            $(".ultimi-post").append(data);
          });
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
                    $.get("post_home.php",{operazione:"generali"}, function(data){
                      $(".ultimi-post").empty();
                      $(".ultimi-post").append(data);
                    });
                  }else{
                    $.get("post_home.php",{operazione:"personali"}, function(data){
                      $(".ultimi-post-personali").empty();
                      $(".ultimi-post-personali").append(data);
                    });
                  }
                }else{
                  alert(response);
                }
              });
            })
            $(".conferma_eliminazione_post").on("click", ".annulla", Data, function(){
              if($("#label_generali").hasClass("selected")){
                    $.get("post_home.php",{operazione:"generali"}, function(data){
                      $(".ultimi-post").empty();
                      $(".ultimi-post").append(data);
                    });
              }else{
                    $.get("post_home.php",{operazione:"personali"}, function(data){
                      $(".ultimi-post-personali").empty();
                      $(".ultimi-post-personali").append(data);
                    });
              }
            })  
          }else{
            if($("#label_generali").hasClass("selected")){
              $.get("post_home.php",{operazione:"generali"}, function(data){
                $(".ultimi-post").empty();
                $(".ultimi-post").append(data);
              });
            }else{
              $.get("post_home.php",{operazione:"personali"}, function(data){
                $(".ultimi-post-personali").empty();
                $(".ultimi-post-personali").append(data);
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

            pulsante.text("Conferma");
            pulsante.css("background","linear-gradient(135deg, #33cc33, #1f7a1f)");
            pulsante.siblings(".elimina_post").text("Annulla");
          }else{
            var Post_id = pulsante.closest(".nuovo-post").data('post-id');
            var new_contenuto = pulsante.closest(".nuovo-post").find(".campo_nuovo_contenuto").val();
            var new_titolo = pulsante.closest(".nuovo-post").find(".campo_nuovo_titolo").val();
            var Data = {
              titolo: new_titolo,
              contenuto: new_contenuto,
              idPost: Post_id
            }
            $.post("manipola_post.php", Data, function(response){
              if(response=="OK"){
                var messaggio_feedback = "<div class='feedback_modifica_post'><div class='contenuto'>Modifica avvenuta correttamente!<div><button class='ok'>Ok</button><div></div></div>";
                pulsante.closest(".nuovo-post").append(messaggio_feedback);
                $(".feedback_modifica_post").css("display","block");
                $(".feedback_modifica_post").on("click", ".ok", function(){
                  if($("#label_generali").hasClass("selected")){
                    $.get("post_home.php",{operazione:"generali"}, function(data){
                      $(".ultimi-post").empty();
                      $(".ultimi-post").append(data);
                    });
                  }else{
                    $.get("post_home.php",{operazione:"personali"}, function(data){
                      $(".ultimi-post-personali").empty();
                      $(".ultimi-post-personali").append(data);
                    });
                  }
                })
                
              }else{
                var messaggio_feedback = "<div class='feedback_modifica_post'><div class='contenuto'>Errore nella modifica<div><button class='ok'>Ok</button><div></div></div>";
                pulsante.closest(".nuovo-post").append(messaggio_feedback);
                $(".feedback_modifica_post").css("display","block");
                $(".feedback_modifica_post").on("click", ".ok", function(){
                  if($("#label_generali").hasClass("selected")){
                    $.get("post_home.php",{operazione:"generali"}, function(data){
                      $(".ultimi-post").empty();
                      $(".ultimi-post").append(data);
                    });
                  }else{
                    $.get("post_home.php",{operazione:"personali"}, function(data){
                      $(".ultimi-post-personali").empty();
                      $(".ultimi-post-personali").append(data);
                    });
                  }
                })
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
            }else if(data=="CAMBIATO"){
              var old_value = parseInt(pulsante.find("span").html());
              var new_value = old_value+1;
              pulsante.find("span").html(new_value);
              var old_value_non_mi_piace = parseInt( pulsante.siblings(".non_mi_piace").find("span").html());
              var new_value_non_mi_piace = old_value_non_mi_piace-1;
              pulsante.siblings(".non_mi_piace").find("span").html(new_value_non_mi_piace);
            }else if(data=="ELIMINATO"){
              var old_value = parseInt(pulsante.find("span").html());
              var new_value = old_value-1;
              pulsante.find("span").html(new_value);
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
            }else if(data=="CAMBIATO"){
              var old_value = parseInt(pulsante.find("span").html());
              var new_value = old_value+1;
              pulsante.find("span").html(new_value);
              var old_value_mi_piace = parseInt( pulsante.siblings(".mi_piace").find("span").html());
              var new_value_mi_piace = old_value_mi_piace-1;
              pulsante.siblings(".mi_piace").find("span").html(new_value_mi_piace);
            }else if(data=="ELIMINATO"){
              var old_value = parseInt(pulsante.find("span").html());
              var new_value = old_value-1;
              pulsante.find("span").html(new_value);
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
            pulsante.parent(".commenti").append(data);
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

        }else{
          var pulsante = $(this);
          var PostId = pulsante.closest(".feedback_e_commenti").data("post-id");
          var Commento_Id = pulsante.closest(".commento").data("commento-id");
          var contenuto_campo = $(this).parent(".autore").siblings(".nuovo_contenuto").val();
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
    });
    </script>
   </head>
<body>
  <header>
    <nav class="navbar">
      <div class="logo"><a href="home.php">Bluggle</a></div>
      <ul class="menu">
        <li><a href="home.php">Home</a></li>
        <li><a href="tutti_i_blog.php"> Tutti i Blog</a></li>
        <li><a href="i_tuoi_blog.php"> I tuoi Blog</a></li>
        <li><a href="account.php">Account</a></li>
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
        <input type="radio" id="blog_generali" name="blog_generali" value="Tutti i blog"><label for="blog_generali" id="label_generali" class="selected">Tutti i Blog</label>
        <input type="radio" id="blog_personali" name="blog_personali" value="I tuoi blog"><label for="blog_personali" id="label_personali">I tuoi Blog</label></p>
        <div class="ultimi-post"><?php echo $html_post?></div>
        <div class="ultimi-post-personali" hidden><?php echo $html_post_personali ?></div>
      </div>
      <div class="popolari">
        <p class="titolo">Blog popolari</p>
        <div class="blog-popolari"><?php echo $html_blog_pop ?></div>
      </div>
    </div>
  </header>
</body>
</html>
