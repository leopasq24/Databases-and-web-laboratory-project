<?php
session_start();
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
    <!-- <script>
    $(document).ready(function(){
    </script> -->
    
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
                    <h3>Nell'ultima settimana, hai pubblicato X post con X like e X commenti</h3>
                </div>
                <div class="wrapper-insight">
                    <h3>I post che ti sono piaciuti nell'ultima settimana:</h3>
                    <p>Lista post</p>
                </div>
                <div class="wrapper-insight">
                    <h3>Gli utenti che hanno interagito di più con i tuoi post sono:</h3>
                    <p>Lista utenti</p>
                </div>
                <div class="wrapper-insight">
                    <h3>Nella classifica dei blog più popolari, il tuo blog è alla Xesima posizione</h3>
                </div>
            </div>

        </header>
    </body>
</html>