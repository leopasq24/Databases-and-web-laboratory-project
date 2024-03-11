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

// Dati dell'ultimo mese

$data = date("Y-m-d");
$data_un_mese_fa = date("Y-m-d", strtotime("-1 month"));
$stmt_ultimo_mese = mysqli_prepare($link, "SELECT IdPost FROM post WHERE IdUtente=? AND post.Data BETWEEN ? AND ? GROUP BY post.IdPost");
mysqli_stmt_bind_param($stmt_ultimo_mese, "iss", $id_utente, $data_un_mese_fa, $data);
mysqli_stmt_execute($stmt_ultimo_mese);
$results_ultimo_mese = mysqli_stmt_get_result($stmt_ultimo_mese);
if(mysqli_num_rows($results_ultimo_mese)!=0){
    $conta_post = 0;
    $numerocommenti = 0;
    $numero_positivi = 0;
    $numero_negativi = 0;
    while($row=mysqli_fetch_assoc($results_ultimo_mese)){
        $id_post = $row['IdPost'];
        $conta_post = $conta_post + 1;
        $stmt_feedback_positivi = mysqli_prepare($link, "SELECT numerofeedbackpositivi FROM numero_feedback_positivi WHERE IdPost=?");
        mysqli_stmt_bind_param($stmt_feedback_positivi,"i", $id_post);
        mysqli_stmt_execute($stmt_feedback_positivi);
         $results_feedback_positivi = mysqli_stmt_get_result($stmt_feedback_positivi);
        if (mysqli_num_rows($results_feedback_positivi) != 0) {
            while ($row = mysqli_fetch_assoc($results_feedback_positivi)){
            $num_feedback_positivi=$row['numerofeedbackpositivi'];
            $numero_positivi = $numero_positivi + $num_feedback_positivi;
            }
        }
        mysqli_stmt_close($stmt_feedback_positivi);

        $stmt_feedback_negativi = mysqli_prepare($link, "SELECT numerofeedbacknegativi FROM numero_feedback_negativi WHERE IdPost=?");
        mysqli_stmt_bind_param($stmt_feedback_negativi,"i", $id_post);
        mysqli_stmt_execute($stmt_feedback_negativi);
        $results_feedback_negativi = mysqli_stmt_get_result($stmt_feedback_negativi);
        if (mysqli_num_rows($results_feedback_negativi) != 0) {
            while ($row = mysqli_fetch_assoc($results_feedback_negativi)){
            $num_feedback_negativi=$row['numerofeedbacknegativi'];
            $numero_negativi = $numero_negativi + $num_feedback_negativi;
            }
        }
        mysqli_stmt_close($stmt_feedback_negativi);

        $stmt_numero_commenti = mysqli_prepare($link, "SELECT numerocommenti FROM numero_commenti WHERE IdPost=?");
        mysqli_stmt_bind_param($stmt_numero_commenti,"i", $id_post);
        mysqli_stmt_execute($stmt_numero_commenti);
        $results_numero_commenti = mysqli_stmt_get_result($stmt_numero_commenti);
        if (mysqli_num_rows($results_numero_commenti) != 0) {
            while ($row = mysqli_fetch_assoc($results_numero_commenti)){
            $commenti=$row['numerocommenti'];
            $numerocommenti =  $numerocommenti + $commenti;
            }
        }
        mysqli_stmt_close($stmt_numero_commenti);
    }
}else{
    $conta_post = 0;
    $numerocommenti = 0;
    $numero_positivi = 0;
    $numero_negativi = 0;
}
mysqli_stmt_close($stmt_ultimo_mese);

// Post piaciuti nell'ultimo mese

$data = date("Y-m-d");
$data_un_mese_fa = date("Y-m-d", strtotime("-1 month"));
$html_post_mese = "";

$stmt_post_ultimo_mese = mysqli_prepare($link, "SELECT blog.IdBlog, blog.Titolo AS nomeblog, blog.Immagine AS imgblog, post.Titolo AS titolopost, post.Testo, post.Immagine FROM post, feedback, blog WHERE post.IdBlog = blog.IdBlog AND post.IdPost = feedback.IdPost AND feedback.IdUtente = ? AND feedback.Tipo = 1 AND post.Data BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmt_post_ultimo_mese, "iss", $id_utente, $data_un_mese_fa, $data);
mysqli_stmt_execute($stmt_post_ultimo_mese);
$results_post_ultimo_mese = mysqli_stmt_get_result($stmt_post_ultimo_mese);
$post_ultimo_mese = [];
$i = 0;
while ($row = mysqli_fetch_assoc($results_post_ultimo_mese)) {
    $imgBlog = $row["imgblog"];
    if($imgBlog == NULL){
        $imgBlog = "foto/blog.png";
    }
    $nomeBlog = $row["nomeblog"];
    $titolo = $row["titolopost"];
    $testo = $row["Testo"];
    $immagine = $row["Immagine"];
    $idblog = $row["IdBlog"];
    $post_ultimo_mese[$i] = [$imgBlog, $nomeBlog, $titolo, $testo, $immagine, $idblog];
    $i = $i + 1;
}
foreach ($post_ultimo_mese as $value){
    if (empty($post_ultimo_mese)) {
        $html_post_mese = "</br><p>Non hai ancora messo like a nessun post! :(</p>";
    } else {
        $html_post_mese .= "<div class='singolo_post_mese'>
        <img class='img_blog' src='".$value[0]."'></img>
        <p class='nome_blog' data-blog-id='".$value[5]."'>".$value[1]."</p>
        <h4 class='titolo_post'>".$value[2]."</h4>";
        if (!empty($value[4])) {
            $html_post_mese .= "<img class='img_post' src='".$value[4]."'></img>";
            $html_post_mese .= "<p class='testo_post'>".$value[3]."</p>
            </div> </br></br>";
        } else {
            $html_post_mese .= "<p class='testo_post'>".$value[3]."</p>
            </div> </br></br>";
        }
    }
}
mysqli_stmt_close($stmt_post_ultimo_mese);

// Classifica blog

$stmt_blog_pop = mysqli_prepare($link, "SELECT IdBlog FROM blog WHERE IdBlog IN (SELECT IdBlog FROM post WHERE IdPost IN (SELECT codice FROM post_popolari ORDER BY conta DESC))");
mysqli_stmt_execute($stmt_blog_pop);
$result_blog_pop = mysqli_stmt_get_result($stmt_blog_pop);

$stmt_blog_personali = mysqli_prepare($link, "SELECT IdBlog, Immagine, Titolo FROM blog WHERE IdUtente = ?");
mysqli_stmt_bind_param($stmt_blog_personali, "i", $id_utente);
mysqli_stmt_execute($stmt_blog_personali);
$result_blog_personali = mysqli_stmt_get_result($stmt_blog_personali);

$pos = 0;
$tuoi_blog_pop = array();
$html_blog_pop = "";

while ($row = mysqli_fetch_assoc($result_blog_pop)) {
    $blog_pop = $row["IdBlog"];
    $pos = $pos + 1;
    mysqli_data_seek($result_blog_personali, 0);
    while($rec = mysqli_fetch_assoc($result_blog_personali)){
        if($rec['IdBlog'] == $blog_pop){
            $imgpath = $rec["Immagine"];
            if($rec["Immagine"]==NULL){
                $imgpath = "foto/blog.png";
            }
            array_push($tuoi_blog_pop, [$pos, $rec["IdBlog"], $imgpath, $rec["Titolo"]]);
        };
    }
}
mysqli_stmt_close($stmt_blog_pop);
if(empty($tuoi_blog_pop)){
    $html_blog_pop = "<p>Nessuno dei tuoi blog è ancora in classifica :(</p>";
}else{
    foreach ($tuoi_blog_pop as $value) {
        $html_blog_pop.="</br><div class='tuo_blog_pop' data-blog-id='".$value[1]."'><span id = 'valori-insight'>".$value[0]."°</span> | <img src='".$value[2]."'> ".$value[3]."</div>";
    }
}
?>

<html lang="it" dir="ltr">
    <head>
        <meta charset="UTF-8">
        <title> Insight </title>
        <link rel="stylesheet" href="stile_index.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    </head>
    <script>
    
    $(document).ready(function(){
        $("p.nome_blog").on("click", function(){
            var idBlog = $(this).data("blog-id");
            window.location.href = "singolo_blog.php?id=" + idBlog;
        });
        $("img.img_blog").on("click", function(){
            var idBlog = $(this).data("blog-id");
            windows.location.href = "singolo_blog.php?id=" + idBlog;
        });
    });
    
    </script>
    
    <body id="insight">
        <header id="insight">
            <nav class="navbar">
                <div class="logo"><a href="home.php">Bluggle</a></div>
                <ul class="menu" id="menu_insight">
                    
                    <li><a href="home.php">Home</a></li>
                    <li><a href="tutti_i_blog.php"> Tutti i Blog</a></li>
                    <li><a href="i_tuoi_blog.php"> I tuoi Blog</a></li>
                    <li><a href="account.php">Account</a></li>
                    <li><a href="info.php">Info</a></li>
                </ul>
                <div class="buttons">
                    <a href="logout.php"><input type="button" value="Logout"></a>
                </div>
            </nav>

            <div class="grid-insight">
                <div class="wrapper-insight">
                    <h3>Nell'ultimo mese hai pubblicato <span id="valori-insight"><?php echo $conta_post ?></span> post, per un totale di <span id="valori-insight"><?php echo $numero_positivi ?></span> like,
                    <span id="valori-insight"><?php echo $numero_negativi ?></span> dislike e <span id="valori-insight"><?php echo $numerocommenti ?></span> comment<?php if ($numerocommenti == 1){echo "o";}else{echo "i";}?></h3>
                </div>
                <div class="wrapper-insight">
                    <h3>I post che ti sono piaciuti nell'ultimo mese:</h3>
                    <div class='post_mese'>
                        <?php echo $html_post_mese ?>
                    </div>
                </div>
                <div class="wrapper-insight">
                    <h3>Nella classifica dei blog più popolari, i tuoi blog sono nelle seguenti posizioni:</h3>
                        <div class='tuoi_blog_pop'>
                            <?php echo $html_blog_pop ?>
                        </div>
                </div>
                <div class="wrapper-insight">
                    <h3>Gli utenti che hanno interagito di più con i tuoi post sono:</h3></br>
                    <p>Lista utenti</p>
                </div>
            </div>

        </header>
    </body>
</html>
