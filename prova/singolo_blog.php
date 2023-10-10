<?php
session_start();
$idBlog = $_GET['idBlog'];
$Blog_title = $_GET['Blog_title'];
?>
    <div class="grid-post">
      <div class="categorie">
        <p class="titolo">Blog</p>
        <div class="macro-categorie"></div>
      </div>
      <div class="post">
        <p class="titolo"><?php echo $Blog_title ?></br>
        <input type="radio" id="blog_generali" name="blog_generali" value="Tutti i blog"><label for="blog_generali" id="label_generali" class="selected">Recenti</label>
        <input type="radio" id="blog_personali" name="blog_personali" value="I tuoi blog"><label for="blog_personali" id="label_personali">Popolari</label></p>
        <div class="ultimi-post"></div>
        <div class="ultimi-post-personali" hidden></div>
      </div>
      <div class="popolari">
        <p class="titolo">Pannello di controllo</p>
        <div class="blog-popolari"></div>
      </div>
    </div>