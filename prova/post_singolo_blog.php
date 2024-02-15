<?php
session_start();
include_once("connect.php");

$post_rec = "";
$post_pop = "";
$id_utente = $_SESSION["session_utente"];

if (isset($_GET["idBlog"]) and ($_GET["operazione"]=="recenti")){
    $idBlog = $_GET["idBlog"];
    $stmt_post_rec = mysqli_prepare($link, "SELECT post.Titolo AS Titolo_post, IdPost, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username, Modificato FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente AND blog.IdBlog=? ORDER BY Data DESC LIMIT 7");
    mysqli_stmt_bind_param($stmt_post_rec, "i", $idBlog);
    mysqli_stmt_execute($stmt_post_rec);
    $result_post_rec = mysqli_stmt_get_result($stmt_post_rec);
  
  if  (mysqli_num_rows($result_post_rec) == 0){
      $post_rec  .="<img src='foto/vuoto.png' alt='vuoto'></img>";
      $post_rec  .= "<p id='nessun_post'>Non ci sono ancora post</p>";
  }else{
    while ($row = mysqli_fetch_assoc($result_post_rec)) {
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

        $verifica_autore_blog = false;
        $stmt_autore_blog = mysqli_prepare($link, "SELECT IdUtente FROM blog WHERE IdBlog=?");
        mysqli_stmt_bind_param($stmt_autore_blog,"i", $idBlog);
        mysqli_stmt_execute($stmt_autore_blog);
        $results_autore_blog = mysqli_stmt_get_result($stmt_autore_blog);
        while ($row = mysqli_fetch_assoc($results_autore_blog)){
          if($row['IdUtente']==$id_utente){
            $verifica_autore_blog = true;
          }
        }
        mysqli_stmt_close($stmt_autore_blog);


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

      if ($img_post === null) {
        $post_rec .= "<div class='nuovo-post' data-post-id ='{$id_post}'>";
        $post_rec .= "<img src='{$src_img}' alt='{$blog}'></img>";
        if($verifica_autore_blog==true){
          if($verifica_autore_post==true){
            $post_rec .= "<p>{$blog} <button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
          }else{
            $post_rec .= "<p>{$blog} <button class='elimina_post'>Elimina</button></p>";
          }
        }else{
          $post_rec .= "<p>{$blog}</p>";
        }
        $post_rec .= "<h4 class='titolo_post'>{$title}</h4>";
        $post_rec .= "<p class='contenuto_post'>{$testo}</p>";
        if($modificato==1){
          $post_rec .= "<p class ='post_modificato'>(modificato)</p>";
        }
        $post_rec .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
        $post_rec .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'><div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div><div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
        $post_rec .= "</div>";
      } else {
        $post_rec .= "<div class='nuovo-post' data-post-id ='{$id_post}'>";
        $post_rec .= "<img src='{$src_img}' alt='{$blog}'></img>";
        if($verifica_autore_blog==true){
          if($verifica_autore_post==true){
            $post_rec .= "<p>{$blog} <button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
          }else{
            $post_rec .= "<p>{$blog} <button class='elimina_post'>Elimina</button></p>";
          }
        }else{
          $post_rec .= "<p>{$blog}</p>";
        }
        $post_rec .= "<h4>{$title}</h4>";
        $post_rec .= "<img class='immagine_post' src='{$img_post}' alt='{$title}'></img>";
        $post_rec .= "<p class='contenuto_post'>{$testo}</p>";
        if($modificato==1){
          $post_rec .= "<p class ='post_modificato'>(modificato)</p>";
        }
        $post_rec .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
        $post_rec .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'><div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div><div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
        $post_rec .= "</div>";
      }
    }
  }
  
  mysqli_stmt_close($stmt_post_rec);
  echo $post_rec;
}else{
    $idBlog = $_GET["idBlog"];
    $stmt_post_pop = mysqli_prepare($link, "SELECT post.Titolo AS Titolo_post, IdPost, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username, Modificato FROM post, blog, utente, post_popolari WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente AND post.IdPost=post_popolari.codice AND blog.IdBlog=? ORDER BY post_popolari.conta DESC");
    mysqli_stmt_bind_param($stmt_post_pop, "i", $idBlog);
    mysqli_stmt_execute($stmt_post_pop);
    $result_post_pop = mysqli_stmt_get_result($stmt_post_pop);
    
    if  (mysqli_num_rows($result_post_pop)==0){
        $post_pop  .="<img src='foto/solitario.png' alt='solitario'></img>";
        $post_pop  .= "<p id='nessun_post'>Non ci sono ancora post popolari</p>";
    }else{
      while ($row = mysqli_fetch_assoc($result_post_pop)) {
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
  
          $verifica_autore_blog = false;
          $stmt_autore_blog = mysqli_prepare($link, "SELECT IdUtente FROM blog WHERE IdBlog=?");
          mysqli_stmt_bind_param($stmt_autore_blog,"i", $idBlog);
          mysqli_stmt_execute($stmt_autore_blog);
          $results_autore_blog = mysqli_stmt_get_result($stmt_autore_blog);
          while ($row = mysqli_fetch_assoc($results_autore_blog)){
            if($row['IdUtente']==$id_utente){
              $verifica_autore_blog = true;
            }
          }
          mysqli_stmt_close($stmt_autore_blog);


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
  
        if ($img_post === null) {
          $post_pop .= "<div class='nuovo-post' data-post-id ='{$id_post}'>";
          $post_pop .= "<img src='{$src_img}' alt='{$blog}'></img>";
          if($verifica_autore_blog==true){
            if($verifica_autore_post==true){
              $post_pop .= "<p>{$blog} <button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
            }else{
              $post_pop .= "<p>{$blog} <button class='elimina_post'>Elimina</button></p>";
            }
          }else{
            $post_pop .= "<p>{$blog}</p>";
          }
          $post_pop .= "<h4 class='titolo_post'>{$title}</h4>";
          $post_pop .= "<p class='contenuto_post'>{$testo}</p>";
          if($modificato==1){
            $post_pop .= "<p class ='post_modificato'>(modificato)</p>";
          }
          $post_pop .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
          $post_pop .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'><div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div><div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
          $post_pop .= "</div>";
        } else {
          $post_pop .= "<div class='nuovo-post' data-post-id ='{$id_post}'>";
          $post_pop .= "<img src='{$src_img}' alt='{$blog}'></img>";
          if($verifica_autore_blog==true){
            if($verifica_autore_post==true){
              $post_pop .= "<p>{$blog} <button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
            }else{
              $post_pop .= "<p>{$blog} <button class='elimina_post'>Elimina</button></p>";
            }
          }else{
            $post_pop .= "<p>{$blog}</p>";
          }
          $post_pop .= "<h4 class='titolo_post'>{$title}</h4>";
          $post_pop .= "<img class='immagine_post' src='{$img_post}' alt='{$title}'></img>";
          $post_pop .= "<p class='contenuto_post'>{$testo}</p>";
          if($modificato==1){
            $post_pop .= "<p class ='post_modificato'>(modificato)</p>";
          }
          $post_pop .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
          $post_pop .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'><div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div><div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
          $post_pop .= "</div>";
        }
      }
    }
  
    mysqli_stmt_close($stmt_post_pop);
    echo $post_pop;

}
?>