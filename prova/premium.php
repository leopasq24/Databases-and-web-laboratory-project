<?php
session_start();
if (!isset($_SESSION["session_utente"]) || empty($_SESSION["session_utente"])) {
    session_unset();
    session_destroy();
    header("Location: registrazione.php");
    exit;
  }
  $id_utente = $_SESSION["session_utente"];
?>

<html lang="it" dir="ltr">
    <head>
        <meta charset="UTF-8">
        <title> Passa a Premium </title>
        <link rel="stylesheet" href="stile_index.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    </head>
    <script> var id_utente = <?php echo $id_utente ?>; </script>
    <script src = "js/premium.js"></script>
    
    <body id="premium">
        <header id="premium">
            <nav class="navbar">
                <div class="logo"><a href="home.php">Bluggle</a></div>
                <ul class="menu" id= "menu_premium">
                    
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
            <div class="grid-premium">
                
                <div class="piano_premium">
                    <h2>Scopri i vantaggi del piano Premium!</h2>
                    <h3>a $5.99 al mese</h3>
                    <p>Porta la tua esperienza su <i>Bluggle</i> ad un livello superiore! Con una vasta gamma di vantaggi e funzionalità esclusive, il piano Premium ti offre un accesso privilegiato e un'esperienza senza interruzioni.
                    <p>
                        <ul>
                            <li><b>More blogs, more party.</b> Nessun limite al numero di blog, creane quanti ne vuoi!</li>
                            <li><b>More people, more party.</b> Nessun limite al numero di coautori dei tuoi blog, nominane quanti preferisci!</li>
                            <li><b>Tieni d'occhio le tue statistiche.</b> Avrai a disposizione una pagina tutta tua per controllare gli insights del tuo account, come il successo dei tuoi blog o l'interazione degli altri utenti con i tuoi post.</li>
                            <li><b>Disdici quando vuoi.</b> Puoi annullare il piano in qualsiasi momento senza costi aggiuntivi dalla pagina di gestone dell'account!</li>
                        </ul>
                    </p>
                    </p>
                    <p class="disclaimer">⚠️: La procedura di upgrade non comporterà la perdita di blog e dati a te già associati come utente Standard.</p>
                </div>
                
                <div class="form-premium" id="form-premium">
                    <div class="wrapper">
                        <div class="title">Passa a Premium</div>
                        <form id="premium" action="upgrade_premium.php" method="post">
                            <div class="field">
                                <label>Numero carta</label>
                                <input type="text" name="num_carta" id="num_carta" placeholder="XXXX-YYYY-ZZZZ-TTTT">
                            </div>
                            <div class="field">
                                <label>Data di scadenza</label>
                                <input type="text" name="data_scad" id="data_scad" placeholder="MM/AA">
                            </div>
                            <div class="field">
                                <label>CVV</label>
                                <input type="password" name="cvv" id="cvv" placeholder="XYZ">            
                            </div>
                            <div class="field">
                                <label>Nome sulla carta</label>
                                <input type="text" name="nome_carta" id="nome_carta">
                            </div>
                            </br>
                            <p id="error_message"></p>
                            <div class="field" id="paga">
                                <input type="submit" value="Vai al pagamento 🛒">
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            
            </div>

        </header>
    </body>
</html>
