<?php
include("connect.php");
$selezione = $_GET['selezione'];
$str = trim($_GET['str']);
if($selezione == "blog" and $str!=""){
    $new_str="%".$str."%";
    $stmt= mysqli_prepare($link, "SELECT DISTINCT blog.IdBlog AS idblog, Titolo, Descrizione, Nome, Username FROM blog, categoria, utente WHERE blog.IdCategoria=categoria.IdCategoria AND blog.IdUtente=utente.IdUtente  AND (Titolo LIKE ? OR Descrizione LIKE ? OR Nome LIKE ? OR Username LIKE ?) LIMIT 4");
    mysqli_stmt_bind_param($stmt,"ssss", $new_str, $new_str, $new_str, $new_str);
	mysqli_stmt_execute($stmt);
	$results = mysqli_stmt_get_result($stmt);
	if (mysqli_num_rows($results) == 0) {
        echo "<div class='nessun_risulato_ricerca'>Nessun risultato</div>";
    }else{
        $arr = array($str => "<span>".$str."</span>");
        $res = "";
        $res.="<table>
        <thead>
            <tr id='attributi'>
                <th>Titolo</th>
                <th>Descrizione</th>
                <th>Categoria</th>
                <th>Autore</th>
            </tr>
        </thead>
        <tbody>";
        while($row = mysqli_fetch_assoc($results)){
            $idBlog = $row['idblog'];
            $titolo_blog = str_replace($str, $arr[$str], $row['Titolo']);
            $desc_blog = str_replace($str, $arr[$str], $row['Descrizione']);
            $cat_blog = str_replace($str, $arr[$str], $row['Nome']);
            $autore_blog = str_replace($str, $arr[$str], $row['Username']);
            $res.="<tr id='risultati' data-blog-id='{$idBlog}'>
                <td class='titolo_blog'>".$titolo_blog."</td>
                <td class='descrizione_blog'>".$desc_blog."</td>
                <td class='categoria_blog'>".$cat_blog."</td>
                <td class='autore_blog'>".$autore_blog."</td>
            </tr>";
        }
        $res.="</tbody></table>";
        echo $res;
    }

}else if($selezione == "post" and $str!=""){
    $new_str="%".$str."%";
    $stmt= mysqli_prepare($link, "SELECT DISTINCT Titolo, Testo, Username, IdBlog FROM post, utente WHERE post.IdUtente=utente.IdUtente  AND (Titolo LIKE ? OR Testo LIKE ? OR Username LIKE ?)LIMIT 4");
    mysqli_stmt_bind_param($stmt,"sss", $new_str, $new_str, $new_str);
	mysqli_stmt_execute($stmt);
	$results = mysqli_stmt_get_result($stmt);
	if (mysqli_num_rows($results) == 0) {
        echo "<div class='nessun_risulato_ricerca'>Nessun risultato</div>";
    }else{
        $arr = array($str => "<span>".$str."</span>");
        $res = "";
        $res.="<table>
        <thead>
            <tr id='attributi'>
                <th>Titolo</th>
                <th>Testo</th>
                <th>Autore</th>
            </tr>
        </thead>
        <tbody>";
        while($row = mysqli_fetch_assoc($results)){
            $idBlog = $row['IdBlog'];
            $titolo_post = str_replace($str, $arr[$str], $row['Titolo']);
            $testo_post = str_replace($str, $arr[$str], $row['Testo']);
            $autore_post = str_replace($str, $arr[$str], $row['Username']);
            $res.="<tr id='risultati'  data-blog-id='{$idBlog}'>
                <td class='titolo_post'>".$titolo_post."</td>
                <td class='testo_post'>".$testo_post."</td>
                <td class='autore_post'>".$autore_post."</td>
            </tr>";
        }
        $res.="</tbody></table>";
        echo $res;
    }
}else if($selezione == "nuovi_risultati_blog" and $str!=""){
    $n = $_GET['numero'];
    $conta = 0;
    $res = "";
    $new_str="%".$str."%";
    $stmt= mysqli_prepare($link, "SELECT DISTINCT blog.IdBlog AS idblog, Titolo, Descrizione, Nome, Username FROM blog, categoria, utente WHERE blog.IdCategoria=categoria.IdCategoria AND blog.IdUtente=utente.IdUtente  AND (Titolo LIKE ? OR Descrizione LIKE ? OR Nome LIKE ? OR Username LIKE ?) LIMIT ?,4");
    mysqli_stmt_bind_param($stmt,"ssssi", $new_str, $new_str, $new_str, $new_str, $n);
	mysqli_stmt_execute($stmt);
	$results = mysqli_stmt_get_result($stmt);
	if (mysqli_num_rows($results) != 0) {
        $arr = array($str => "<span>".$str."</span>");
        while($row = mysqli_fetch_assoc($results)){
            $conta = $conta + 1;
            $idBlog = $row['idblog'];
            $titolo_blog = str_replace($str, $arr[$str], $row['Titolo']);
            $desc_blog = str_replace($str, $arr[$str], $row['Descrizione']);
            $cat_blog = str_replace($str, $arr[$str], $row['Nome']);
            $autore_blog = str_replace($str, $arr[$str], $row['Username']);
            $res.="<tr id='risultati' data-blog-id='{$idBlog}'>
                <td class='titolo_blog'>".$titolo_blog."</td>
                <td class='descrizione_blog'>".$desc_blog."</td>
                <td class='categoria_blog'>".$cat_blog."</td>
                <td class='autore_blog'>".$autore_blog."</td>
            </tr>";
        }
    }
    $data = array("data"=>$res, "conta"=>$conta);
    echo json_encode($data);
}else if($selezione == "nuovi_risultati_post" and $str!=""){
    $n = $_GET['numero'];
    $conta = 0;
    $res = "";
    $new_str="%".$str."%";
    $stmt= mysqli_prepare($link, "SELECT DISTINCT Titolo, Testo, Username, IdBlog FROM post, utente WHERE post.IdUtente=utente.IdUtente  AND (Titolo LIKE ? OR Testo LIKE ? OR Username LIKE ?)LIMIT?,4");
    mysqli_stmt_bind_param($stmt,"sssi", $new_str, $new_str, $new_str,$n);
	mysqli_stmt_execute($stmt);
	$results = mysqli_stmt_get_result($stmt);
	if (mysqli_num_rows($results) != 0) {
        $arr = array($str => "<span>".$str."</span>");
        while($row = mysqli_fetch_assoc($results)){
            $conta = $conta + 1;
            $idBlog = $row['IdBlog'];
            $titolo_post = str_replace($str, $arr[$str], $row['Titolo']);
            $testo_post = str_replace($str, $arr[$str], $row['Testo']);
            $autore_post = str_replace($str, $arr[$str], $row['Username']);
            $res.="<tr id='risultati' data-blog-id='{$idBlog}'>
                <td class='titolo_post'>".$titolo_post."</td>
                <td class='testo_post'>".$testo_post."</td>
                <td class='autore_post'>".$autore_post."</td>
            </tr>";
        }
    }
    $data = array("data"=>$res, "conta"=>$conta);
    echo json_encode($data);
}
?>
