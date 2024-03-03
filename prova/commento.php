<?php
include_once("connect.php");
session_start();

$operazione = $_GET['key2'];

if($operazione=="carica"){

    $id_utente = $_SESSION["session_utente"];
    $id_post = $_GET['key1'];
    $output = "<div class='commenti'>";

    $stmt_comments= mysqli_prepare($link, "SELECT IdCommento, Username, Contenuto, Data, Ora, Modificato FROM commenta, utente WHERE commenta.IdUtente=utente.IdUtente AND commenta.IdPost=? ORDER BY Data DESC");
    mysqli_stmt_bind_param($stmt_comments, "i", $id_post);
    mysqli_stmt_execute($stmt_comments);
    $results_comments = mysqli_stmt_get_result($stmt_comments);
  	
    if(mysqli_num_rows($results_comments)==0){
        $output .= "<p class='nessun_commento'>Nessun commento per questo post :(</p>";
        $output .= "<input class='campo_commento' type='text' placeholder=' Lascia un commento'>";
        $output .= "<button class='pubblica_commento'>Pubblica</button>";
        $output .= "</div>";
        echo $output;
    }else{
        $output .= "<div class='elenco_commenti'>";
        while ($row = mysqli_fetch_assoc($results_comments)){
            $id_commento = $row["IdCommento"];
            $autore = $row["Username"];
            $contenuto = $row["Contenuto"];
            $data = $row["Data"];
            $ora = $row["Ora"];
            $modificato = $row["Modificato"];

            
            $output .= "<div class='commento' data-commento-id='{$id_commento}'>";
            $stmt_autore_post= mysqli_prepare($link, "SELECT IdUtente FROM post WHERE IdPost=?");
            mysqli_stmt_bind_param($stmt_autore_post, "i", $id_post);
            mysqli_stmt_execute($stmt_autore_post);
            $results_autore_post = mysqli_stmt_get_result($stmt_autore_post);
            while ($row = mysqli_fetch_assoc($results_autore_post)){
                if ($row["IdUtente"]!=$id_utente){
                    $stmt_autore_commento = mysqli_prepare($link, "SELECT IdUtente FROM commenta WHERE IdCommento=?");
                    mysqli_stmt_bind_param($stmt_autore_commento, "i", $id_commento);
                    mysqli_stmt_execute($stmt_autore_commento);
                    $results_autore_commento = mysqli_stmt_get_result($stmt_autore_commento);
                    while ($record = mysqli_fetch_assoc($results_autore_commento)){
                        if ($record["IdUtente"]==$id_utente){
                            if($modificato==0){
                                $output .= "<p class='autore'>{$autore}<button class='elimina_commento'>Elimina</button><button class='modifica_commento'>Modifica</button></p>";
                            }else{
                                $output .= "<p class='autore'>{$autore}<button class='elimina_commento'>Elimina</button></p>";
                            }
                        }else{
                            $output .= "<p class='autore'>{$autore}</p>";
                        }
                    }
                }else{
                    $stmt_autore_commento = mysqli_prepare($link, "SELECT IdUtente FROM commenta WHERE IdCommento=?");
                    mysqli_stmt_bind_param($stmt_autore_commento, "i", $id_commento);
                    mysqli_stmt_execute($stmt_autore_commento);
                    $results_autore_commento = mysqli_stmt_get_result($stmt_autore_commento);
                    while ($record = mysqli_fetch_assoc($results_autore_commento)){
                        if ($record["IdUtente"]==$id_utente and $modificato==0){
                            $output .= "<p class='autore'>{$autore}<button class='elimina_commento'>Elimina</button><button class='modifica_commento'>Modifica</button></p>";
                        }else{
                            $output .= "<p class='autore'>{$autore}<button class='elimina_commento'>Elimina</button></p>";
                        }
                    }
                };
            }
        
            $output .= "<p class ='contenuto'>{$contenuto}</p>";
            if($modificato==1){
                $output .= "<p class ='modificato'>(modificato)</p>";
            }
            $output .= "<p class='data'>{$data}, {$ora}</p>";
            $output .= "</div>";
        

        }
        $output .= "<input class='altri_commenti' type='button' value='Altro'>";
        $output .= "</div>";
        $output .= "<input class='campo_commento' type='text' placeholder=' Lascia un commento'>";
        $output .= "<button class='pubblica_commento'>Pubblica</button>";
        $output .= "</div>";
        echo $output;
    }
}else if($operazione =="elimina"){
    $id_commento = $_GET['key1'];

    $stmt_elimina_commento= mysqli_prepare($link, "DELETE FROM commenta WHERE IdCommento=?");
    mysqli_stmt_bind_param($stmt_elimina_commento, "i", $id_commento);
    if(mysqli_stmt_execute($stmt_elimina_commento)){
        echo "OK";
    }else{
        echo "Errore nell'eliminazione";
    }
}else if($operazione =="carica_nuovi"){

    $conta = 0;
    $nuovi = $_GET['numero'];
    $id_utente = $_SESSION["session_utente"];
    $id_post = $_GET['key1'];
    $output = "";

    $stmt_comments= mysqli_prepare($link, "SELECT IdCommento, Username, Contenuto, Data, Ora, Modificato FROM commenta, utente WHERE commenta.IdUtente=utente.IdUtente AND commenta.IdPost=? ORDER BY Data DESC LIMIT ?,5");
    mysqli_stmt_bind_param($stmt_comments, "ii", $id_post, $nuovi);
    mysqli_stmt_execute($stmt_comments);
    $results_comments = mysqli_stmt_get_result($stmt_comments);
  	
    if(mysqli_num_rows($results_comments)!=0){
        while ($row = mysqli_fetch_assoc($results_comments)){
            $conta = $conta + 1;
            $id_commento = $row["IdCommento"];
            $autore = $row["Username"];
            $contenuto = $row["Contenuto"];
            $data = $row["Data"];
            $ora = $row["Ora"];
            $modificato = $row["Modificato"];

        
            $output .= "<div class='commento' data-commento-id='{$id_commento}'>";
            $stmt_autore_post= mysqli_prepare($link, "SELECT IdUtente FROM post WHERE IdPost=?");
            mysqli_stmt_bind_param($stmt_autore_post, "i", $id_post);
            mysqli_stmt_execute($stmt_autore_post);
            $results_autore_post = mysqli_stmt_get_result($stmt_autore_post);
            while ($row = mysqli_fetch_assoc($results_autore_post)){
                if ($row["IdUtente"]!=$id_utente){
                    $stmt_autore_commento = mysqli_prepare($link, "SELECT IdUtente FROM commenta WHERE IdCommento=?");
                    mysqli_stmt_bind_param($stmt_autore_commento, "i", $id_commento);
                    mysqli_stmt_execute($stmt_autore_commento);
                    $results_autore_commento = mysqli_stmt_get_result($stmt_autore_commento);
                    while ($record = mysqli_fetch_assoc($results_autore_commento)){
                        if ($record["IdUtente"]==$id_utente){
                            if($modificato==0){
                                $output .= "<p class='autore'>{$autore}<button class='elimina_commento'>Elimina</button><button class='modifica_commento'>Modifica</button></p>";
                            }else{
                                $output .= "<p class='autore'>{$autore}<button class='elimina_commento'>Elimina</button></p>";
                            }
                        }else{
                            $output .= "<p class='autore'>{$autore}</p>";
                        }
                    }
                }else{
                    $stmt_autore_commento = mysqli_prepare($link, "SELECT IdUtente FROM commenta WHERE IdCommento=?");
                    mysqli_stmt_bind_param($stmt_autore_commento, "i", $id_commento);
                    mysqli_stmt_execute($stmt_autore_commento);
                    $results_autore_commento = mysqli_stmt_get_result($stmt_autore_commento);
                    while ($record = mysqli_fetch_assoc($results_autore_commento)){
                        if ($record["IdUtente"]==$id_utente and $modificato==0){
                        $output .= "<p class='autore'>{$autore}<button class='elimina_commento'>Elimina</button><button class='modifica_commento'>Modifica</button></p>";
                        }else{
                        $output .= "<p class='autore'>{$autore}<button class='elimina_commento'>Elimina</button></p>";
                        }
                    }
                };
            }
    
            $output .= "<p class ='contenuto'>{$contenuto}</p>";
            if($modificato==1){
                $output .= "<p class ='modificato'>(modificato)</p>";
            }
            $output .= "<p class='data'>{$data}, {$ora}</p>";
            $output .= "</div>";
        }
    }
    $res = array("data"=> $output, "conta" => $conta);
    echo json_encode($res);
}

