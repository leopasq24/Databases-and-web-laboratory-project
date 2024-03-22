<?php
session_start();
include_once("connect.php");

if (!isset($_SESSION["session_utente"]) || empty($_SESSION["session_utente"])) {
  session_unset();
  session_destroy();
  header("Location: registrazione.php");
  exit;
}
    $id_utente = $_SESSION["session_utente"];
    $stmt_premium = mysqli_prepare($link, "SELECT Premium FROM utente WHERE IdUtente=?");
    mysqli_stmt_bind_param($stmt_premium, "i", $id_utente);
    mysqli_stmt_execute($stmt_premium);
    $results_premium = mysqli_stmt_get_result($stmt_premium);
    if(mysqli_fetch_assoc($results_premium)["Premium"]==0){
      $button = "<a href='premium.php'><input type='button' value='Premium'></a>";
    }else{
      $button = "<a href='insight.php'><input type='button' value='Insight'></a>";
    }
    mysqli_stmt_close($stmt_premium);


    //Blog popolari
    $html_blog_pop = "<div class='blog-pop-container'>";
    $stmt_blog_pop = mysqli_prepare($link, "SELECT IdBlog, Immagine, Titolo FROM blog WHERE IdBlog IN (SELECT IdBlog FROM post WHERE IdPost IN (SELECT idPost FROM numero_feedback_positivi ORDER BY numerofeedbackpositivi DESC)) LIMIT 8");
    
    mysqli_stmt_execute($stmt_blog_pop);
    $result_blog_pop = mysqli_stmt_get_result($stmt_blog_pop);

    if(mysqli_num_rows($result_blog_pop)==0){
      $html_blog_pop .= "<p>Nessun blog popolare...per ora</p>";
    }else{
      while ($row = mysqli_fetch_assoc($result_blog_pop)) {
        $blog_pop_img = $row['Immagine'];
        if ($blog_pop_img === null) {
            $src_img = "foto/blog.png";
        }else{
            $src_img = $blog_pop_img;
        }
        $blog_pop_titolo = $row['Titolo'];
        $IdBlog =  $row['IdBlog'];
        
        $html_blog_pop .= "<div class='blog-pop' data-blog-id='{$IdBlog}'>";
        $html_blog_pop .= "<img src='{$src_img}' alt='{$blog_pop_titolo}'></img>";
        $html_blog_pop .= "<p>{$blog_pop_titolo}</p>";
        $html_blog_pop .= "</div>";
      } 
    }
    $html_blog_pop .= "</div>";
    mysqli_stmt_close($stmt_blog_pop);


    //Post recenti
    $html_post = "";
    $conta_post = 0;
    $stmt_posts = mysqli_prepare($link, "SELECT IdPost, post.Titolo AS Titolo_post, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username, blog.IdBlog, Modificato FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente ORDER BY Data DESC LIMIT 7");
    
    if(mysqli_stmt_execute($stmt_posts)){
      $result_posts = mysqli_stmt_get_result($stmt_posts);

      if (mysqli_num_rows($result_posts) == 0) {
          $html_post .= "<p class='nessun_post'>Nessun post</p>";
      }else{
        while ($row = mysqli_fetch_assoc($result_posts)) {
            $conta_post = $conta_post + 1;
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
            
            $html_post .= "<div class='nuovo-post' data-post-id ='{$id_post}' data-blog-id ='{$id_blog}'>";
            $html_post .= "<div class='immagine_blog'><img src='{$src_img}' alt='{$blog}' class='img_blog'></div>";
            if($verifica_autore_blog==true){
              if($verifica_autore_post==true and $modificato==0){
                $html_post .= "<p class='titolo_blog'><span>{$blog}</span> <button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
              }else{
                $html_post .= "<p class='titolo_blog'><span>{$blog}</span> <button class='elimina_post'>Elimina</button></p>";
              }
            }else{
              $html_post .= "<p class='titolo_blog'><span>{$blog}</span></p>";
            }
            $html_post .= "<h4 class = 'titolo_post'>{$title}</h4>";
            if($img_post != null) {
              $html_post .= "<img class = 'immagine_post' src='{$img_post}' alt='{$title}'></img> ";
            }
            $html_post .= "<p class='contenuto_post'>{$testo}</p>";
            if($modificato==1){
              $html_post .= "<p class ='post_modificato'>(modificato)</p>";
            }
            $html_post .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
            $html_post .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'>";
            if($piaciuto==true){
              $html_post .="<div class='mi_piace piaciuto'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
            }else{
              $html_post .="<div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
            }
            if($non_piaciuto==true){
              $html_post .="<div class='non_mi_piace non_piaciuto'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
            }else{
              $html_post .="<div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
            }
            $html_post .= "</div>";
        }
      }
      
    }else{
      $html_post .="Error: " . mysqli_error($link);
    }
    
    mysqli_stmt_close($stmt_posts);

    //Post personali
    $html_post_personali = "";
    $conta_post_personali = 0;
    $stmt_post_personali = mysqli_prepare($link, "SELECT IdPost, post.Titolo AS Titolo_post, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username, blog.IdBlog, Modificato FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente AND blog.IdUtente = ? ORDER BY Data DESC LIMIT 7");
    
    mysqli_stmt_bind_param($stmt_post_personali, "i", $id_utente);
    if(mysqli_stmt_execute($stmt_post_personali)){
      $result_post_personali = mysqli_stmt_get_result($stmt_post_personali);
    
      if (mysqli_num_rows($result_post_personali) == 0) {
          $html_post_personali .="<img src='foto/vuoto.png' alt='vuoto'></img>";
          $html_post_personali .= "<p id='nessun_post'>Non hai ancora nessun post. <a href='i_tuoi_blog.php'>Creane uno!</a></p>";
      } else {
        while ($row = mysqli_fetch_assoc($result_post_personali)) {
          $conta_post_personali = $conta_post_personali + 1;
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

          $html_post_personali .= "<div class='nuovo-post' data-post-id ='{$id_post}' data-blog-id ='{$id_blog}'>";
          $html_post_personali .= "<div class='immagine_blog'><img src='{$src_img}' alt='{$blog}' class='img_blog'></div>";
          if($verifica_autore_blog==true){
            if($verifica_autore_post==true and $modificato==0){
              $html_post_personali .= "<p class='titolo_blog'><span>{$blog}</span> <button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
            }else{
              $html_post_personali .= "<p class='titolo_blog'><span>{$blog}</span> <button class='elimina_post'>Elimina</button></p>";
            }
          }else{
            $html_post_personali .= "<p class='titolo_blog'><span>{$blog}</span></p>";
          }
          $html_post_personali .= "<h4 class = 'titolo_post'>{$title}</h4>";
          if($img_post != null){
            $html_post_personali .= "<img  class = 'immagine_post' src='{$img_post}' alt='{$title}'></img>";
          }
          $html_post_personali .= "<p  class='contenuto_post'>{$testo}</p>";
          if($modificato==1){
            $html_post_personali .= "<p class ='post_modificato'>(modificato)</p>";
          }
          $html_post_personali .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
          $html_post_personali .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'>";
          if($piaciuto==true){
            $html_post_personali .="<div class='mi_piace piaciuto'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
          }else{
            $html_post_personali .="<div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
          }
          if($non_piaciuto==true){
            $html_post_personali .="<div class='non_mi_piace non_piaciuto'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
          }else{
            $html_post_personali .="<div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
          }
          $html_post_personali .= "</div>";
        } 
      }
  }else{
    $html_post_personali .="Error: " . mysqli_error($link);
  } 
  mysqli_stmt_close($stmt_post_personali);

  //categorie
  $html_cat = "";
  $stmt_categorie = mysqli_prepare($link, "SELECT IdCategoria, Icona, Nome FROM categoria WHERE IdCategoria NOT IN (SELECT IdSottocategoria FROM contiene) LIMIT 7");
    
  if (mysqli_stmt_execute($stmt_categorie)){
    $result_categorie = mysqli_stmt_get_result($stmt_categorie);
    while ($row = mysqli_fetch_assoc($result_categorie)) {
      $icona = $row['Icona'];
      $nome_categoria = $row['Nome'];
      $id_categoria =  $row['IdCategoria'];

      $html_cat .= "<div class='macrocat' data-cat-id='$id_categoria'>";
      $html_cat .= "<img src='{$icona}' alt='$nome_categoria'></img>";
      $html_cat .= "<p id='macrocat_nome'>{$nome_categoria}</p><p id='freccia'>&#8250;</p>";
      $html_cat .= "<div class='micro-categoria'></div>";
      $html_cat .= "</div>";
    }
    mysqli_stmt_close($stmt_categorie);
  }else{
    $html_cat = "Error: " . mysqli_error($link);
  }
    
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
      var n_post_generali = <?php echo $conta_post ?>;
      var n_post_personali = <?php echo $conta_post_personali ?>;
    </script>
    <script src = "js/home.js" ></script>
   </head>
<body id="home">
  <header id ="home">
    <nav class="navbar">
      <div class="logo"><a href="home.php">Bluggle</a></div>
      <ul class="menu">
        <li><a href="home.php">Home</a></li>
        <li><a href="tutti_i_blog.php"> Tutti i Blog</a></li>
        <li><a href="i_tuoi_blog.php"> I tuoi Blog</a></li>
        <li><a href="account.php">Account</a></li>
        <li><a href="info.php">Info</a></li>
      </ul>
      <div class="buttons">
        <?php echo $button ?>
        <a href="logout.php"><input type="button" value="Logout"></a>
      </div>
    </nav>
    <div class="search-container">
      <input type="text" placeholder="Cerca..." class="search-input">
      <button type="submit" class="search-btn">
        <i id='lente' class="fas fa-search"></i>      
      </button>
    </div>
    <div class="filtri_ricerca" hidden>
      <p>Filtri: &#160 
      <input type="radio" id="cerca_blog" name="cerca_blog" value="Cerca Blog"><label for="cerca_blog" id="cerca_blog" class="selected">Blog</label>
      <input type="radio" id="cerca_post" name="cerca_post" value="Cerca Post"><label for="cerca_post" id="cerca_post">Post</label>
      </p>
    </div>
    <div class='results_ricerca' hidden></div>
    <div class="grid-post">
      <div class="categorie">
        <p class="titolo">Categorie</p>
        <div class="macro-categorie"><?php echo $html_cat ?></div>
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
