<?php
session_start();
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
    <script>
    $(document).ready(function(){
        
        $("form#premium").validate({
            rules : {
                num_carta : {
                    required : true,
                    formatocarta: true
                },
                data_scad : {
                    required : true,
                    datascad: true
                },
                cvv : {
                    required : true,
                    cvv: true
                },
                nome_carta : {
                    required: true,
                    nomecarta: true
                }
            },
            messages : {
                num_carta: {
                    required: "Inserisci un numero di carta"
                },
                data_scad: {
                    required: "Inserisci la data di scadenza della carta"
                },
                cvv: {
                    required: "Inserisci il numero di sicurezza della carta"
                },
                nome_carta : {
                    required: "Inserisci il nome del titolare della carta"
                }
            }
        });

        $.validator.addMethod("formatocarta", function(value, element) {
            return /^([0-9][0-9][0-9][0-9]){4}$/.test(value);
        }, "Formato non valido");

        $.validator.addMethod("datascad", function(value, element) {
            return /^((0[1-9])|(1[0-2]))\/(\d{2})$/.test(value);
        }, "Formato non valido");
        
        $.validator.addMethod("cvv", function(value, element) {
            return /^[0-9]{3}$/.test(value);
        }, "Formato non valido");
        
        $.validator.addMethod("nomecarta", function(value, element) {
            return /^[a-zA-Z\s]+$/.test(value);
        }, "Inserisci solo caratteri alfabetici");
                  
        $("form#premium").on("submit", function(event){
            if($(this).valid()){
                $("#error_message").hide();
                event.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    type: "POST",
                    url: $("form#premium").attr("action"),
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function(response){
                        var responseObject = JSON.parse(response);
                        if(responseObject.status === "OK"){
                            $("form#premium").html("</br> <div class='contenuto'>" + responseObject.message + "</br> <div><button class='ok'>Ok</button>");
                            $("form#premium .contenuto").on("click", ".ok", function(){
                                location.replace("login.php");
                            });
                        } else {
                            $("#error_message").show();
                            $("#error_message").text(responseObject.message);
                            $("form#premium").on("input", function(){
                                $("#error_message").remove();
                            });
                        }         
                    },
                    error: function(xhr) {
                        $("#error_message").after("<p class='eliminazione_error'>"+ xhr + "</p>");
                    }
                });
            }
        });

        $(".disdetta_premium").on("click", function(){
            alert("Sicuro di voler rinunciare alla tua sottoscrizione Premium? Confermando l'operazione, perderai l'accesso ai blog creati come utente Premium e tutti i dati correlati ad essi, insieme a tutti i vantaggi esclusivi inclusi nel piano.");
        });

    });
    </script>
    
    <body id="premium">
        <header id="premium">
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
                            <li><b>Disdici quando vuoi.</b> Puoi annullare il piano in qualsiasi momento senza costi aggiuntivi!" Puoi riformulare il tutto in modo che sia più verosimile rispetto a un sito vero?</li>
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
                                <input type="text" name="cvv" id="cvv" placeholder="342">            
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
