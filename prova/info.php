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
$username = "";
$email = "";

$stmt_info = mysqli_prepare($link, "SELECT Username, Email, Premium FROM utente WHERE IdUtente = ?");
mysqli_stmt_bind_param($stmt_info, "i", $id_utente);
mysqli_stmt_execute($stmt_info);
mysqli_stmt_bind_result($stmt_info, $username, $email, $premium);
$query_info = mysqli_stmt_get_result($stmt_info);
while ($row = mysqli_fetch_assoc($query_info)) {
    $username = $row['Username'];
    $email = $row['Email'];
    $premium = $row['Premium'];
    if($premium==0){
        $button = "<a href='premium.php'><input type='button' value='Premium'></a>";
    }else{
        $button = "<a href='insight.php'><input type='button' value='Insight'></a>";
    }
}
mysqli_stmt_close($stmt_info);
?>
<html lang="it" dir="ltr">
    <head>
        <meta charset="UTF-8">
        <title> Informazioni sito </title>
        <link rel="stylesheet" href="stile_index.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    
    <body>
        <header>
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

<div class="info_container">
    <div class="info_sito">
        <p>Benvenuto su <span class="speciale">Bluggle</span>, dove le idee prendono forma e fluttuano come bolle di sapone!</p>
        <p>Immagina di immergerti in un mondo dove la tua creatività è libera di esplodere in colori vivaci e forme inaspettate. <i>Bluggle</i> ti offre un palcoscenico digitale per condividere le tue passioni e le tue storie con il mondo.</p>
        <p>Che tu sia un appassionato di cucina, un viaggiatore incallito, un esperto di tecnologia o un'anima poetica, qui troverai il tuo spazio per brillare e prendere il volo. Divertiti a fluttuare tra i blog degli altri utenti o crea i tuoi, dando vita alle tue idee più audaci e alle tue visioni più creative.</p>
        <p>Pronto a volare con noi? 🫧</p>
    </div>
</div>

<div class="info_container">
    <div class="credits">
        <p>Il presente sito è stato realizzato da <b>Leonardo Pasquale</b> e <b>Matilde Campanardi</b> per il corso di <i>Basi di dati e Laboratorio Web</i>
        <br>[CdL in Informatica Umanistica - Università di Pisa]</p>
    </div>
</div>

        </header>
    </body>
</html>
