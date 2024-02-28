<?php
session_start();
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
                    <input type="button" value="Premium">
                    <a href="logout.php"><input type="button" value="Logout"></a>
                </div>
            </nav>

<div class="info_container">
    <div class="info_sito">
        <p>Benvenuto su <span class="speciale">Bluggle</span>, dove le idee prendono forma e fluttuano come bolle di sapone!</p>
        <p>Immagina di immergerti in un mondo dove la tua creatività è libera di esplodere in colori vivaci e forme inaspettate. <i>Bluggle</i> ti offre un palcoscenico digitale per condividere le tue passioni e le tue storie con il mondo.</p>
        <p>Che tu sia un appassionato di cucina, un viaggiatore incallito, un esperto di tecnologia o un'anima poetica, qui troverai il tuo spazio per brillare e prendere il volo. Divertiti a fluttuare tra i blog degli altri utenti o crea i tuoi, dando vita alle tue idee più audaci e alle tue visioni più creative.</p>
        <p>Con <i>Bluggle</i>, il cielo non è il limite, ma solo il punto di partenza! 🫧</p>
    </div>
</div>

<div class="info_container">
    <div class="credits">
        <p>Il presente sito è stato realizzato da <b>Leonardo Pasquale</b> e <b>Matilde Campanardi</b> per il corso di <i>Basi di dati</i>
        <br>[CdL in Informatica Umanistica - Università di Pisa]</p>
    </div>
</div>

        </header>
    </body>
</html>