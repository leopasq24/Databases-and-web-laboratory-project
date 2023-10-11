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
  $stmt_info_blog = mysqli_prepare($link, "SELECT blog.Immagine, blog.Titolo, categoria.Nome, blog.Descrizione FROM blog, categoria WHERE blog.IdUtente=? AND blog.IdBlog=? AND blog.IdCategoria = categoria.IdCategoria");
  mysqli_stmt_bind_param($stmt_info_blog, "ii", $id_utente, $idBlog);
  mysqli_stmt_execute($stmt_info_blog);
  $query_info_blog = mysqli_stmt_get_result($stmt_info_blog);
  $html = "";
  $html .= "<div class='dati-blog'>";
  while ($row = mysqli_fetch_assoc($query_info_blog)) {
    $imagePath = $row['Immagine'];
    $titolo = $row['Titolo'];
    $categoria = $row['Nome'];
    $descrizione = $row['Descrizione'];
    if ($imagePath == null) {
      $imagePath = "foto/blog.png";
    }
    $html .= "<img src='{$imagePath}' alt='{$titolo}'></img>";
    $html .= "<p class='titolo_blog'>{$titolo}</p>";
    $html .= "<p class='cat_blog'>{$categoria}</p>";
    $html .= "<p class='descrizione_blog'>{$descrizione}</p>";
  }
  $stmt_proprietario = mysqli_prepare($link, "SELECT IdUtente FROM blog WHERE IdBlog=?");
  mysqli_stmt_bind_param($stmt_proprietario, "i", $idBlog);
  mysqli_stmt_execute($stmt_proprietario);
  $query_proprietario = mysqli_stmt_get_result($stmt_proprietario);
  while ($row = mysqli_fetch_assoc($query_proprietario)){
    if ($row["IdUtente"]==$id_utente){
      $html .= "<input type='button' class='modifica_blog' value='Modifica'>
      <input type='button' class='elimina_blog' value='Elimina'>";
    }
  }
  mysqli_stmt_close($stmt_info_blog);
  $html .= "</div>";
}
?>
    <input type="button" id="crea_post" value="Crea un nuovo post">
    <div class="grid-post">
      <div class="colonna-blog">
        <p class="titolo">Info e gestione blog</p>
        <div class="info-blog"><?php echo $html ?></div>
      </div>
      <div class="post">
        <p class="titolo">I post di <?php echo $Blog_title ?></br>
         <input type="radio" id="post_recenti" name="post_recenti" value="Recenti"><label for="post_recenti" id="post_recenti" class="selected">Recenti</label>
         <input type="radio" id="post_popolari" name="post_popolari" value="Popolari"><label for="post_popolari" id="post_popolari">Popolari</label>
        </p>
        <div class="post_rec"></div>
        <div class="post_pop" hidden></div>
      </div>
      <div class="p_controllo">
        <p class="titolo">Pannello di controllo</p>
        <div class="comandi_controllo"></div>
      </div>
    </div>
