<?php
session_start();
include_once("connect.php");
$id_utente = $_SESSION["session_utente"];

if ($_GET["operazione"]=="generali"){
    $html = "";
    $conta = 0;
    $stmt_posts = mysqli_prepare($link, "SELECT IdPost, post.Titolo AS Titolo_post, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username, blog.IdBlog, Modificato FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente ORDER BY Data DESC LIMIT 7");
    
    if(mysqli_stmt_execute($stmt_posts)){
    	$result_posts = mysqli_stmt_get_result($stmt_posts);

    	if (mysqli_num_rows($result_posts) == 0) {
        	$html .= "<p class='nessun_post'>Nessun post</p>";
    	}else{
    		while ($row = mysqli_fetch_assoc($result_posts)) {
                $conta = $conta + 1;
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
            
            $html .= "<div class='nuovo-post' data-post-id ='{$id_post}' data-blog-id ='{$id_blog}'>";
            $html .= "<div class='immagine_blog'><img src='{$src_img}' alt='{$blog}' class='img_blog'></div>";
            if($verifica_autore_blog==true){
              if($verifica_autore_post==true and $modificato==0){
                $html .= "<p class = 'titolo_blog'><span>{$blog}</span><button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
              }else{
                $html .= "<p class = 'titolo_blog'><span>{$blog}</span> <button class='elimina_post'>Elimina</button></p>";
              }
            }else{
              $html .= "<p class = 'titolo_blog'><span>{$blog}</span></p>";
            }
            $html .= "<h4 class = 'titolo_post'>{$title}</h4>";
            if($img_post != null) {
              $html .= "<img class = 'immagine_post' src='{$img_post}' alt='{$title}'></img>";
            }
            $html .= "<p class='contenuto_post'>{$testo}</p>";
            if($modificato==1){
              $html .= "<p class ='post_modificato'>(modificato)</p>";
            }
            $html .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
            $html .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'>";
            if($piaciuto==true){
              $html .="<div class='mi_piace piaciuto'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
            }else{
              $html .="<div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
            }
            if($non_piaciuto==true){
              $html .="<div class='non_mi_piace non_piaciuto'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
            }else{
              $html .="<div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
            }
            $html .= "</div>";
    		}
    	}
    	
    }else{
    	$html .="<p>Errore nel DataBase</p>";
    }
    
    mysqli_stmt_close($stmt_posts);
    $res = array("data"=>$html, "conta"=> $conta);
    echo json_encode($res);
}else if($_GET['operazione']=="personali"){
    $html = "";
    $conta = 0;
    $stmt_posts = mysqli_prepare($link, "SELECT IdPost, post.Titolo AS Titolo_post, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username, blog.IdBlog, Modificato FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente AND blog.IdUtente = ? ORDER BY Data DESC LIMIT 7");
    
    mysqli_stmt_bind_param($stmt_posts, "i", $id_utente);
    mysqli_stmt_execute($stmt_posts);
    $result_posts = mysqli_stmt_get_result($stmt_posts);
    
    if (mysqli_num_rows($result_posts)==0) {
        $html .="<img src='foto/vuoto.png' alt='vuoto'></img>";
        $html .= "<p id='nessun_post'>Non hai ancora nessun post. <a href='i_tuoi_blog.php'>Creane uno!</a></p>";
    } else {
            while ($row = mysqli_fetch_assoc($result_posts)) {
                $conta = $conta + 1;
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

            $html .= "<div class='nuovo-post' data-post-id ='{$id_post}' data-blog-id ='{$id_blog}'>";
            $html .= "<div class='immagine_blog'><img src='{$src_img}' alt='{$blog}' class='img_blog'></div>";
            if($verifica_autore_blog==true){
                if($verifica_autore_post==true and $modificato==0){
                    $html .= "<p class = 'titolo_blog'><span>{$blog}</span><button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
                }else{
                    $html .= "<p class = 'titolo_blog'><span>{$blog}</span> <button class='elimina_post'>Elimina</button></p>";
                }
            }else{
                $html .= "<p class = 'titolo_blog'><span>{$blog}</span></p>";
            }
            $html .= "<h4 class = 'titolo_post'>{$title}</h4>";
            if($img_post != null){
                $html .= "<img  class = 'immagine_post' src='{$img_post}' alt='{$title}'></img>";
            }
            $html .= "<p  class='contenuto_post'>{$testo}</p>";
            if($modificato==1){
                $html .= "<p class ='post_modificato'>(modificato)</p>";
            }
            $html .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
            $html .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'>";
            if($piaciuto==true){
                $html .="<div class='mi_piace piaciuto'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
            }else{
                $html .="<div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
            }
            if($non_piaciuto==true){
                $html .="<div class='non_mi_piace non_piaciuto'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
            }else{
                $html .="<div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
            }
            $html .= "</div>";
        }
    }
    
    $html .= "</div>";
    mysqli_stmt_close($stmt_posts);
    $res = array("data"=>$html, "conta"=> $conta);
    echo json_encode($res);
}else if($_GET['operazione']=="nuovi_generali"){
    $n_post = $_GET['numero'];
    $html = "";
    $conta = 0;
    $stmt_posts = mysqli_prepare($link, "SELECT IdPost, post.Titolo AS Titolo_post, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username, blog.IdBlog, Modificato FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente ORDER BY Data DESC LIMIT ?,7");
    mysqli_stmt_bind_param($stmt_posts,"i", $n_post);
    if(mysqli_stmt_execute($stmt_posts)){
    	$result_posts = mysqli_stmt_get_result($stmt_posts);
    		while ($row = mysqli_fetch_assoc($result_posts)) {
                $conta = $conta + 1;
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
            
            $html .= "<div class='nuovo-post' data-post-id ='{$id_post}' data-blog-id ='{$id_blog}'>";
            $html .= "<div class='immagine_blog'><img src='{$src_img}' alt='{$blog}' class='img_blog'></div>";
            if($verifica_autore_blog==true){
              if($verifica_autore_post==true and $modificato==0){
                $html .= "<p class = 'titolo_blog'><span>{$blog}</span><button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
              }else{
                $html .= "<p class = 'titolo_blog'><span>{$blog}</span> <button class='elimina_post'>Elimina</button></p>";
              }
            }else{
              $html .= "<p class = 'titolo_blog'><span>{$blog}</span></p>";
            }
            $html .= "<h4 class = 'titolo_post'>{$title}</h4>";
            if($img_post != null) {
              $html .= "<img class = 'immagine_post' src='{$img_post}' alt='{$title}'></img>";
            }
            $html .= "<p class='contenuto_post'>{$testo}</p>";
            if($modificato==1){
              $html .= "<p class ='post_modificato'>(modificato)</p>";
            }
            $html .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
            $html .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'>";
            if($piaciuto==true){
              $html .="<div class='mi_piace piaciuto'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
            }else{
              $html .="<div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
            }
            if($non_piaciuto==true){
              $html .="<div class='non_mi_piace non_piaciuto'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
            }else{
              $html .="<div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
            }
            $html .= "</div>";
    		}
    	
    }else{
    	$html .="<p>Errore nel DataBase</p>";
    }
    
    mysqli_stmt_close($stmt_posts);
    $res = array("data"=>$html, "conta"=>$conta);
    echo json_encode($res);
}else if($_GET['operazione']=="nuovi_personali"){
    $html = "";
    $n_post = $_GET['numero'];
    $conta = 0;
    $stmt_posts = mysqli_prepare($link, "SELECT IdPost, post.Titolo AS Titolo_post, Testo, Data, Ora, post.Immagine AS immagine_post, blog.Titolo AS Argomento, blog.Immagine AS immagine_blog, Username, blog.IdBlog, Modificato FROM post, blog, utente WHERE post.IdBlog=blog.IdBlog AND post.IdUtente=utente.IdUtente AND blog.IdUtente = ? ORDER BY Data DESC LIMIT ?,7");
    
    mysqli_stmt_bind_param($stmt_posts, "ii", $id_utente, $n_post);
    mysqli_stmt_execute($stmt_posts);
    $result_posts = mysqli_stmt_get_result($stmt_posts);
            while ($row = mysqli_fetch_assoc($result_posts)) {
                $conta = $conta + 1;
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

            $html .= "<div class='nuovo-post' data-post-id ='{$id_post}' data-blog-id ='{$id_blog}'>";
            $html .= "<div class='immagine_blog'><img src='{$src_img}' alt='{$blog}' class='img_blog'></div>";
            if($verifica_autore_blog==true){
                if($verifica_autore_post==true and $modificato==0){
                    $html .= "<p class = 'titolo_blog'><span>{$blog}</span><button class='elimina_post'>Elimina</button><button class='modifica_post'>Modifica</button></p>";
                }else{
                    $html .= "<p class = 'titolo_blog'><span>{$blog}</span> <button class='elimina_post'>Elimina</button></p>";
                }
            }else{
                $html .= "<p class = 'titolo_blog'><span>{$blog}</span></p>";
            }
            $html .= "<h4 class = 'titolo_post'>{$title}</h4>";
            if($img_post != null){
                $html .= "<img  class = 'immagine_post' src='{$img_post}' alt='{$title}'></img>";
            }
            $html .= "<p  class='contenuto_post'>{$testo}</p>";
            if($modificato==1){
                $html .= "<p class ='post_modificato'>(modificato)</p>";
            }
            $html .= "<p class='dataeora'><span>~ {$autore_post}</span> il {$data} alle {$ora}</p>";
            $html .= "<div class='feedback_e_commenti' data-post-id='{$id_post}'>";
            if($piaciuto==true){
                $html .="<div class='mi_piace piaciuto'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
            }else{
                $html .="<div class='mi_piace'><img src='foto/mi-piace.png' alt='mi_piace'><span>{$num_feedback_positivi}</span></div>";
            }
            if($non_piaciuto==true){
                $html .="<div class='non_mi_piace non_piaciuto'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
            }else{
                $html .="<div class='non_mi_piace'><img src='foto/non-mi-piace.png' alt='non_mi_piace'><span>{$num_feedback_negativi}</span></div><div class='commenta'><img src='foto/commenti.png' alt='commenti_icona'><span>{$numero_commenti}</span></div></div>";
            }
            $html .= "</div>";
        }
    
    $html .= "</div>";
    mysqli_stmt_close($stmt_posts);
    $res = array("data"=>$html, "conta"=>$conta);
    echo json_encode($res);
}
?>
