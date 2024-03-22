<?php
session_start();
include_once("connect.php");

if (!isset($_SESSION["session_utente"]) || empty($_SESSION["session_utente"])) {
  session_unset();
  session_destroy();
  header("Location: registrazione.php");
  exit;
}
$idUtente = $_SESSION["session_utente"];
$stmt_premium = mysqli_prepare($link, "SELECT Premium FROM utente WHERE IdUtente=?");
mysqli_stmt_bind_param($stmt_premium, "i", $idUtente);
mysqli_stmt_execute($stmt_premium);
$results_premium = mysqli_stmt_get_result($stmt_premium);
if(mysqli_fetch_assoc($results_premium)["Premium"]==0){
  $button = "<a href='premium.php'><input type='button' value='Premium'></a>";
}else{
  $button = "<a href='insight.php'><input type='button' value='Insight'></a>";
}
mysqli_stmt_close($stmt_premium);

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
  $stmt_info_blog = mysqli_prepare($link, "SELECT blog.Immagine, blog.Titolo, categoria.Nome, categoria.IdCategoria AS CatId, blog.Descrizione FROM blog, categoria WHERE blog.IdBlog=? AND blog.IdCategoria = categoria.IdCategoria");
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
    $html .= "<p class='cat_blog' data-cat-id='{$row['CatId']}'>{$categoria}</p>";
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
  $stmt_post_pop = mysqli_prepare($link, "SELECT post.Titolo AS Titolo_post, post.IdPost, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username, Modificato FROM post, blog, utente, numero_feedback_positivi WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente AND post.IdPost=numero_feedback_positivi.idPost AND blog.IdBlog=? ORDER BY numero_feedback_positivi.numerofeedbackpositivi DESC LIMIT 7");
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
    var n_post_recenti = <?php echo $conta_post_rec ?>;
    var n_post_popolari = <?php echo $conta_post_rec ?>;
    var id_Blog = <?php if (isset($idBlog)){echo $idBlog;} ?>
    </script>
    <script src = "js/singolo_blog.js"></script>
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
        <?php echo $button ?>
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


