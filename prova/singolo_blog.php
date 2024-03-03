<?php
session_start();
include_once("connect.php");
$titolo_info = "Info Blog";
if (isset($_GET['id']) and is_numeric($_GET['id'])){
  $idBlog = $_GET['id'];
  $stmt_esiste_blog = mysqli_prepare($link, "SELECT IdBlog FROM Blog Where IdBlog=?");
  mysqli_stmt_bind_param($stmt_esiste_blog, "i", $idBlog);
  mysqli_stmt_execute($stmt_esiste_blog);
  $results_esiste_blog = mysqli_stmt_get_result($stmt_esiste_blog);
  if(mysqli_num_rows($results_esiste_blog)==0){
    $crea="<p class='nessun_id_blog'>Errore: non esiste alcun blog con questo id</p>";
    mysqli_stmt_close($stmt_esiste_blog);
  }else{
  mysqli_stmt_close($stmt_esiste_blog);
  $stmt_titolo_blog = mysqli_prepare($link, "SELECT Titolo FROM Blog Where IdBlog=?");
  mysqli_stmt_bind_param($stmt_titolo_blog, "i", $idBlog);
  mysqli_stmt_execute($stmt_titolo_blog);
  $query_titolo_blog = mysqli_stmt_get_result($stmt_titolo_blog);
  while ($row = mysqli_fetch_assoc($query_titolo_blog)){
    $Blog_title = $row['Titolo'];
  };
  mysqli_stmt_close($stmt_titolo_blog);
  $id_utente = $_SESSION["session_utente"];
  $stmt_info_blog = mysqli_prepare($link, "SELECT blog.Immagine, blog.Titolo, categoria.Nome, blog.Descrizione FROM blog, categoria WHERE blog.IdBlog=? AND blog.IdCategoria = categoria.IdCategoria");
  mysqli_stmt_bind_param($stmt_info_blog, "i", $idBlog);
  mysqli_stmt_execute($stmt_info_blog);
  $query_info_blog = mysqli_stmt_get_result($stmt_info_blog);
  $post_rec = "";
  $post_pop = "";
  $html = "";
  $crea = "";
  $altri_blog = "";
  $blog_simili = "";
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
      $html .= "<div id='messaggio_conferma' class='modal'><div class='contenuto_messaggio'><div class='domanda'>Sicuro di voler <span></span> il seguente Blog?</div><button class='conferma_modifica'>Conferma</button> <button class='annulla_modifica'>Annulla</button></div></div>";
      $crea = "<input type='button' id='crea_post' value='Crea un nuovo post'>";
      $titolo_info ="Info e gestione Blog";
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

  $verifica_autore_blog = false;
  $new_stmt_proprietario = mysqli_prepare($link, "SELECT IdUtente FROM blog WHERE IdBlog=?");
  mysqli_stmt_bind_param($new_stmt_proprietario, "i", $idBlog);
  mysqli_stmt_execute($new_stmt_proprietario);
  $new_query_proprietario = mysqli_stmt_get_result($new_stmt_proprietario);
  while ($row = mysqli_fetch_assoc($new_query_proprietario)){
    if ($row["IdUtente"]==$id_utente){
      $html .= "<input type='button' data-blog-id='{$idBlog}' class='modifica_coautore' value='Modifica'>";
      $verifica_autore_blog = true;
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

  //Post recenti
  $stmt_post_rec = mysqli_prepare($link, "SELECT post.Titolo AS Titolo_post, IdPost, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username, Modificato FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente AND blog.IdBlog=? ORDER BY Data DESC LIMIT 7");
  mysqli_stmt_bind_param($stmt_post_rec, "i", $idBlog);
  mysqli_stmt_execute($stmt_post_rec);
  $result_post_rec = mysqli_stmt_get_result($stmt_post_rec);
  
  $conta_post_rec = 0;
  if  (mysqli_num_rows($result_post_rec) == 0){
      $post_rec  .="<img src='foto/vuoto.png' alt='vuoto'></img>";
      $post_rec  .= "<p id='nessun_post'>Non ci sono ancora post</p>";
  }else{
    while ($row = mysqli_fetch_assoc($result_post_rec)) {
      $conta_post_rec = $conta_post_rec + 1;
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

        $piaciuto = false;
        $stmt_piaciuto = mysqli_prepare($link, "SELECT * FROM feedback WHERE IdPost=? AND IdUtente=? AND Tipo=1");
        mysqli_stmt_bind_param($stmt_piaciuto,"ii", $id_post, $id_utente);
        mysqli_stmt_execute($stmt_piaciuto);
        $results_stmt_piaciuto = mysqli_stmt_get_result($stmt_piaciuto);
        if(mysqli_num_rows($results_stmt_piaciuto)!=0){
          $piaciuto = true;
        }
        mysqli_stmt_close($stmt_piaciuto);

        $non_piaciuto = false;
        $stmt_non_piaciuto = mysqli_prepare($link, "SELECT * FROM feedback WHERE IdPost=? AND IdUtente=? AND Tipo=0");
        mysqli_stmt_bind_param($stmt_non_piaciuto,"ii", $id_post, $id_utente);
        mysqli_stmt_execute($stmt_non_piaciuto);
        $results_stmt_non_piaciuto = mysqli_stmt_get_result($stmt_non_piaciuto);
        if(mysqli_num_rows($results_stmt_non_piaciuto)!=0){
          $non_piaciuto = true;
        }
        mysqli_stmt_close($stmt_non_piaciuto);
            
        $post_rec .= "<div class='nuovo-post' data-post-id ='{$id_post}'>";
        $post_rec .= "<div class='immagine_blog'><img src='{$src_img}' alt='{$blog}' class='img_blog'></div>";
        if($verifica_autore_blog==true){
          if($verifica_autore_post==true and $modificato==0){
        $post_rec .= "<p>{$blog} <button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
          }else{
            $post_rec .= "<p>{$blog} <button class='elimina_post'>Elimina</button></p>";
          }
        }else{
          $post_rec .= "<p>{$blog}</p>";
        }
        $post_rec .= "<h4 class = 'titolo_post'>{$title}</h4>";
        if($img_post != null) {
          $post_rec .= "<img class = 'immagine_post' src='{$img_post}' alt='{$title}'></img>";
        }
        $post_rec .= "<p class='contenuto_post'>{$testo}</p>";
        if($modificato==1){
          $post_rec .= "<p class ='post_modificato'>(modificato)</p>";
        }
        $post_rec .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
        $post_rec .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'>";
        if($piaciuto==true){
          $post_rec .="<div class='mi_piace piaciuto'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
        }else{
          $post_rec .="<div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
        }
        if($non_piaciuto==true){
          $post_rec .="<div class='non_mi_piace non_piaciuto'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
        }else{
          $post_rec .="<div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
        }
        $post_rec .= "</div>";
    }
  }
  
  mysqli_stmt_close($stmt_post_rec);
  
  //Post Popolari
  $stmt_post_pop = mysqli_prepare($link, "SELECT post.Titolo AS Titolo_post, IdPost, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username, Modificato FROM post, blog, utente, post_popolari WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente AND post.IdPost=post_popolari.codice AND blog.IdBlog=? ORDER BY post_popolari.conta DESC LIMIT 7");
  mysqli_stmt_bind_param($stmt_post_pop, "i", $idBlog);
  mysqli_stmt_execute($stmt_post_pop);
  $result_post_pop = mysqli_stmt_get_result($stmt_post_pop);
  $conta_post_pop = 0;

  if  (mysqli_num_rows($result_post_pop)==0){
      $post_pop  .="<img src='foto/solitario.png' alt='solitario'></img>";
      $post_pop  .= "<p id='nessun_post'>Non ci sono ancora post popolari</p>";
  }else{
    while ($row = mysqli_fetch_assoc($result_post_pop)) {
      $conta_post_pop = $conta_post_pop + 1;
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
        
        $piaciuto = false;
        $stmt_piaciuto = mysqli_prepare($link, "SELECT * FROM feedback WHERE IdPost=? AND IdUtente=? AND Tipo=1");
        mysqli_stmt_bind_param($stmt_piaciuto,"ii", $id_post, $id_utente);
        mysqli_stmt_execute($stmt_piaciuto);
        $results_stmt_piaciuto = mysqli_stmt_get_result($stmt_piaciuto);
        if(mysqli_num_rows($results_stmt_piaciuto)!=0){
          $piaciuto = true;
        }
        mysqli_stmt_close($stmt_piaciuto);

        $non_piaciuto = false;
        $stmt_non_piaciuto = mysqli_prepare($link, "SELECT * FROM feedback WHERE IdPost=? AND IdUtente=? AND Tipo=0");
        mysqli_stmt_bind_param($stmt_non_piaciuto,"ii", $id_post, $id_utente);
        mysqli_stmt_execute($stmt_non_piaciuto);
        $results_stmt_non_piaciuto = mysqli_stmt_get_result($stmt_non_piaciuto);
        if(mysqli_num_rows($results_stmt_non_piaciuto)!=0){
          $non_piaciuto = true;
        }
        mysqli_stmt_close($stmt_non_piaciuto);
            
        $post_pop .= "<div class='nuovo-post' data-post-id ='{$id_post}'>";
        $post_pop .= "<div class='immagine_blog'><img src='{$src_img}' alt='{$blog}' class='img_blog'></div>";
        if($verifica_autore_blog==true){
          if($verifica_autore_post==true and $modificato==0){
        $post_pop .= "<p>{$blog} <button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
          }else{
            $post_pop .= "<p>{$blog} <button class='elimina_post'>Elimina</button></p>";
          }
        }else{
          $post_pop .= "<p>{$blog}</p>";
        }
        $post_pop .= "<h4 class = 'titolo_post'>{$title}</h4>";
        if($img_post != null) {
          $post_pop .= "<img class = 'immagine_post' src='{$img_post}' alt='{$title}'></img>";
        }
        $post_pop .= "<p class='contenuto_post'>{$testo}</p>";
        if($modificato==1){
          $post_pop .= "<p class ='post_modificato'>(modificato)</p>";
        }
        $post_pop .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
        $post_pop .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'>";
        if($piaciuto==true){
          $post_pop .="<div class='mi_piace piaciuto'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
        }else{
          $post_pop .="<div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
        }
        if($non_piaciuto==true){
          $post_pop .="<div class='non_mi_piace non_piaciuto'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
        }else{
          $post_pop .="<div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
        }
        $post_pop .= "</div>";
    }
  }

  mysqli_stmt_close($stmt_post_pop);


  //Altri blog
  $altri_blog .= "<div class='altri-blog'>";
  $stmt_altri_blog = mysqli_prepare($link, "SELECT IdBlog, Titolo, Immagine FROM blog WHERE IdBlog!=? AND IdUtente=(SELECT IdUtente FROM blog WHERE IdBlog=?)");
  mysqli_stmt_bind_param($stmt_altri_blog, "ii", $idBlog, $idBlog);
  mysqli_stmt_execute($stmt_altri_blog);
  $results_altri_blog = mysqli_stmt_get_result($stmt_altri_blog);
  if  (mysqli_num_rows($results_altri_blog)==0){
      $altri_blog  .= "<p id='nessun_post'>Non ci sono altri blog :(</p>";
  }else{
    while ($row = mysqli_fetch_assoc($results_altri_blog)) {
      $img_blog = $row['Immagine'];
      $blog_title = $row['Titolo'];
      $blog_id = $row['IdBlog'];
      if ($img_blog == null) {
          $src_img = "foto/blog.png";
        } else {
          $src_img = $img_blog;
        }
      $altri_blog  .= "<div class='altro_blog' data-blog-id='{$blog_id}'>";
      $altri_blog  .= "<img src='{$src_img}' alt='{$blog_title}'></img>";
      $altri_blog  .= "<p>{$blog_title}</p>";
      $altri_blog  .= "</div>";
    }
  }
  $altri_blog .="</div>";
  mysqli_stmt_close($stmt_altri_blog);


  //Blog simili
  $blog_simili .= "<div class='blog-simili'>";
  $stmt_blog_simili = mysqli_prepare($link, "SELECT IdBlog, Titolo, Immagine FROM blog WHERE IdBlog!=? AND IdCategoria=(SELECT IdCategoria FROM blog WHERE IdBlog=?) UNION SELECT IdBlog, Titolo, Immagine FROM blog WHERE IdBlog!=? AND IdCategoria IN(SELECT contiene.IdSottocategoria FROM contiene WHERE IdSopracategoria=(SELECT IdCategoria FROM blog WHERE IdBlog=?))LIMIT 7");
  mysqli_stmt_bind_param($stmt_blog_simili, "iiii", $idBlog, $idBlog, $idBlog, $idBlog);
  mysqli_stmt_execute($stmt_blog_simili);
  $results_blog_simili = mysqli_stmt_get_result($stmt_blog_simili);
  if  (mysqli_num_rows($results_blog_simili)==0){
      $blog_simili  .= "<p id='nessun_post'>Non ci sono blog simili :(</p>";
  }else{
    while ($row = mysqli_fetch_assoc($results_blog_simili)) {
      $img_blog = $row['Immagine'];
      $blog_title = $row['Titolo'];
      $blog_id = $row['IdBlog'];
      if ($img_blog == null) {
          $src_img = "foto/blog.png";
        } else {
          $src_img = $img_blog;
        }
      $blog_simili  .= "<div class='blog_simile' data-blog-id='{$blog_id}'>";
      $blog_simili  .= "<img src='{$src_img}' alt='{$blog_title}'></img>";
      $blog_simili  .= "<p class='blog_simile' data-blog-id='{$blog_id}'>{$blog_title}</p>";
      $blog_simili  .= "</div>";
    }
  }
  $blog_simili .="</div>";
  mysqli_stmt_close($stmt_blog_simili);

  }
}else{
  $crea="<p class='nessun_id_blog'>Errore: non è stato specificato alcun id per il blog</p>";
}
?>
<html lang="it" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title> Tutti i Blog </title>
    <link rel="stylesheet" href="stile_index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
      $(document).ready(function() {

        var n_post_recenti = <?php echo $conta_post_rec ?>;
        var n_post_popolari = <?php echo $conta_post_rec ?>;

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
                } else {
                  risposta = response;
                }

                $("#messaggio_conferma").find(".domanda").hide();
                $("#messaggio_conferma").find(".conferma_modifica").hide();
                $("#messaggio_conferma").find(".annulla_modifica").hide();
                $("#messaggio_conferma").find(".contenuto_messaggio").append("<div class='modifica_avvenuta'>"+risposta+"</div><button class='ok_modifica'>Ok</button>");
                $("#messaggio_conferma").on("click",".ok_modifica", function(){
                  $("#messaggio_conferma").css("display","none");
                  location.reload();
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
              location.reload();
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
                  risposta = "modifiche avvenute con successo";
                  var updatedData = responseObject.data;

                  form.remove();
                  old_p_coautori.show();
                  old_p_coautori.text(updatedData);

                  $(".modifica_coautore").val("Modifica");
                  $(".modifica_coautore").siblings(".indietro_coautori").hide();
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
              required : true
                } 
            },
          messages : {
            titolo_post: {
              required: "Inserire un titolo",
              maxlength: "Inserire massimo 50 caratteri"
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
      idBlog : <?php if (isset($idBlog)){echo $idBlog;} ?>,
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
      idBlog : <?php if (isset($idBlog)){echo $idBlog;} ?>,
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

  $(document).on("click", ".altri-blog .altro_blog", function(){
    var idBlog = $(this).data("blog-id");

    location.replace("singolo_blog.php?id=" + idBlog);
  });

  $(document).on("click", ".blog-simili .blog_simile", function(){
    var idBlog = $(this).data("blog-id");

    location.replace("singolo_blog.php?id=" + idBlog);
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
              data: {operazione:"recenti", idBlog: <?php echo $idBlog ?>},
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
              data: {operazione:"popolari", idBlog: <?php echo $idBlog ?>},
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
              data: {operazione:"recenti", idBlog: <?php echo $idBlog ?>},
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
              data: {operazione:"popolari", idBlog: <?php echo $idBlog ?>},
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
              data: {operazione:"recenti", idBlog: <?php echo $idBlog ?>},
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
              data: {operazione:"popolari", idBlog: <?php echo $idBlog ?>},
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
            if($("#label_recenti").hasClass("selected")){
              $.ajax({
              url: "post_singolo_blog.php",
              type: "GET",
              data: {operazione:"recenti", idBlog: <?php echo $idBlog ?>},
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
              data: {operazione:"popolari", idBlog: <?php echo $idBlog ?>},
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
          var messaggio_feedback = "<div class='feedback_modifica_post'><div class='contenuto'>Errore nella modifica<div><button class='ok'>Ok</button><div></div></div>";
          pulsante.closest(".nuovo-post").append(messaggio_feedback);
          $(".feedback_modifica_post").css("display","block");
          $(".feedback_modifica_post").on("click", ".ok", function(){
            if($("#label_recenti").hasClass("selected")){
              $.ajax({
              url: "post_singolo_blog.php",
              type: "GET",
              data: {operazione:"recenti", idBlog: <?php echo $idBlog ?>},
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
              data: {operazione:"popolari", idBlog: <?php echo $idBlog ?>},
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
                data: { operazione: "nuovi_recenti", numero: n_post_recenti, idBlog: <?php echo $idBlog ?>},
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
                data: { operazione: "nuovi_popolari", numero: n_post_popolari, idBlog: <?php echo $idBlog ?>},
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
        <li><a href="info.php">Info</a></li>
      </ul>
      <div class="buttons">
        <input type="button" value="Premium">
        <a href="logout.php"><input type="button" value="Logout"></a>
      </div>
    </nav>
    <div class="tuoi_blog">
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
        <p class="titolo"><?php echo $titolo_info ?></p>
        <div class="info-blog"><?php if (isset($html)){echo $html;} ?></div>
      </div>
      <div class="post">
        <p class="titolo">I post di <?php if (isset($html)){echo $Blog_title;} ?></br>
         <input type="radio" id="post_recenti" name="post_recenti" value="Recenti"><label for="post_recenti" id="label_recenti" class="selected">Recenti</label>
         <input type="radio" id="post_popolari" name="post_popolari" value="Popolari"><label for="post_popolari" id="label_popolari">Popolari</label>
        </p>
        <div class="post_rec"><?php if (isset($html)){echo $post_rec;} ?></div>
        <div class="post_pop" hidden><?php if (isset($html)){echo $post_pop;} ?></div>
      </div>
      <div class="p_controllo">
        <p class="titolo">Suggerimenti</p>
        <div class='sottotitolo'>Altri blog di <span><?php if (isset($html)){echo $nome_creatore;} ?></span></div>
        <div class="comandi_controllo"><?php if (isset($html)){echo $altri_blog;} ?></div>
        <div class='sottotitolo'>Blog simili a <span><?php if (isset($html)){echo $Blog_title;} ?></span></div>
        <div class="comandi_controllo"><?php if (isset($html)){echo $blog_simili;} ?></div>
      </div>
    </div>
</div>
    </body>
  </header>
</html>


