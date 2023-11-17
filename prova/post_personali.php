<?php
session_start();
include_once("connect.php");
if (!isset($_SESSION["session_utente"])) {
    echo "Sessione annullata";
    exit;
} else {
    $id_utente = $_SESSION["session_utente"];
    $html = "";
    $stmt_posts = mysqli_prepare($link, "SELECT IdPost, post.Titolo AS Titolo_post, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente AND blog.IdUtente = ? ORDER BY Data DESC LIMIT 7");
    
    mysqli_stmt_bind_param($stmt_posts, "i", $id_utente);
    mysqli_stmt_execute($stmt_posts);
    $result_posts = mysqli_stmt_get_result($stmt_posts);
    
    $query_results = mysqli_fetch_assoc($result_posts);
    
    if (empty($query_results)) {
        $html .="<img src='foto/vuoto.png' alt='vuoto'></img>";
        $html .= "<p id='nessun_post'>Non hai ancora nessun post.<a href='i_tuoi_blog.php'>Creane uno!</a></p>";
    } else {
            while ($row = mysqli_fetch_assoc($result_posts)) {
                $id_post = $row['IdPost'];
                $img_blog = $row['immagine_blog'];
                $img_post = $row['immagine_post'];
                $title = $row['Titolo_post'];
                $testo = $row['Testo'];
                $autore_post = $row['Username'];
                $blog = $row['Argomento'];
                $data = $row['Data'];
                $ora = $row['Ora'];
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

            $stmt_numero_commenti = mysqli_prepare($link, "SELECT numerocommenti FROM numero_commenti WHERE IdPost=?");
            mysqli_stmt_bind_param($stmt_numero_commenti,"i", $id_post);
            mysqli_stmt_execute($stmt_numero_commenti);
            $stmt_numero_commenti = mysqli_stmt_get_result($stmt_numero_commenti);
            if (mysqli_num_rows($stmt_numero_commenti) == 0) {
                $numero_commenti=0;
            }else{
                while ($row = mysqli_fetch_assoc($stmt_numero_commenti)){
                    $numero_commenti=$row['numerocommenti'];
                }
            }

            if ($img_post === null) {
                $html .= "<div class='nuovo-post'>";
                $html .= "<img src='{$src_img}' alt='{$blog}'></img>";
                $html .= "<p>{$blog}</p>";
                $html .= "<h4>{$title}</h4>";
                $html .= "<p>{$testo}</p>";
                $html .= "<p class='dataeora'><span>{$autore_post}</span> {$data} {$ora}</p>";
                $html .= "<p class='feedback_e_commenti'>{$num_feedback_positivi} {$num_feedback_negativi} {$numero_commenti}</p>";
                $html .= "</div>";
            } else {
                $html .= "<div class='nuovo-post'>";
                $html .= "<img src='{$src_img}' alt='{$blog}'></img>";
                $html .= "<p>{$blog}</p>";
                $html .= "<h4>{$title}</h4>";
                $html .= "<img src='{$img_post}' alt='{$title}'></img>";
                $html .= "<p>{$testo}</p>";
                $html .= "<p class='dataeora'><span>{$autore_post}</span> {$data} {$ora}</p>";
                $html .= "<p class='feedback_e_commenti'>{$num_feedback_positivi} {$num_feedback_negativi} {$numero_commenti}</p>";
                $html .= "</div>";
            }
        }
    }
    
    $html .= "</div>";
    mysqli_stmt_close($stmt_posts);
    echo $html;
}
?>
