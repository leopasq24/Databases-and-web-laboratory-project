<?php
session_start();
include_once("connect.php");

if (!isset($_SESSION["session_utente"]) || empty($_SESSION["session_utente"])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
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
}
mysqli_stmt_close($stmt_info);
?>

<html lang="it" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title> Impostazioni Account </title>
    <link rel="stylesheet" href="stile_index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            
            var old_username = $('.campo_username').val();
            var old_email = $('.campo_email').val();
            
            $(".campo_username").on('input', function() {
                if ($(this).val() !== old_username) {
                    $(this).css("opacity","1");
                    $('#change-username-form').find("#conferma").css({"display":"inline", "margin-top":"1%"});
                    $('#change-username-form').find("#annulla").css({"display":"inline", "margin-top":"1%"});
                } else {
                    $('#change-username-form').find("#conferma").hide();
                    $('#change-username-form').find("#annulla").hide();
                    $(this).css("opacity","0.5");
                }
            });
            $('#change-username-form').on('click', '#annulla', function() {
                $('.campo_username').css("opacity","0.5");
                $('.campo_username').val(old_username);
                $('#change-username-form').find("#conferma").hide();
                $('#change-username-form').find("#annulla").hide();
                $("#campo_username-error.error").remove();
            });
            $('#change-username-form').on('click', '#conferma', function() {
                var form = $("#change-username-form");
                var formData = new FormData(form[0]);
                var messaggio_conferma = "<div class='conferma_modifica_username'><div class='contenuto'>Sicuro di voler modificare il tuo username?<div><button class='conferma'>Conferma</button><button class='annulla'>Annulla</button><div></div></div>";
                $(this).closest("#account-settings").append(messaggio_conferma);
                $(".conferma_modifica_username").css("display","block");
                $(".conferma_modifica_username").on("click", ".conferma", function(){
                    $.ajax({
                        type: "POST",
                        url: "modifica_info_utente.php", 
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            var responseObject = JSON.parse(response);
                            if (responseObject.status === "OK") {
                                $(".conferma_modifica_username .contenuto").html("<div class='contenuto'>Username modificato correttamente<div><button class='ok'>Ok</button>");
                                $(".conferma_modifica_username").on("click", ".ok", function(){
                                    $(this).closest($(".conferma_modifica_username")).remove();
                                    $(".campo_username").css("opacity","0.5");
                                    $('#change-username-form').find("#conferma").hide();
                                    $('#change-username-form').find("#annulla").hide();;
                                    var updatedData = responseObject.data;
                                    old_username = updatedData.campo_username;
                                })
                            } else if(responseObject.status === "Errore"){
                                if (responseObject.message === "Sessione annullata"){
                                    location.replace("login.php");
                                } else {
                                    $(".conferma_modifica_username").html("<div class='contenuto'>"+responseObject.message+"<div><button class='ok'>Ok</button>");
                                    $(".conferma_modifica_username").on("click", ".ok", function(){
                                        $(this).closest($(".conferma_modifica_username")).remove();
                                    })
                                }
                            }
                        },
                        error: function() {
                            alert("Errore di comunicazione con il server");
                        }
                    });
                })
                $(".conferma_modifica_username").on("click", ".annulla", function(){
                    $(this).closest($(".conferma_modifica_username")).remove();
                })
            });

            $(".campo_email").on('input', function() {
                if ($(this).val() !== old_email) {
                    $(this).css("opacity","1");
                    $('#change-email-form').find("#conferma").css({"display":"inline", "margin-top":"1%"});
                    $('#change-email-form').find("#annulla").css({"display":"inline", "margin-top":"1%"});
                } else {
                    $('#change-email-form').find("#conferma").hide();
                    $('#change-email-form').find("#annulla").hide();
                    $(this).css("opacity","0.5");
                }
            });
            $('#change-email-form').on('click', '#annulla', function() {
                $(".campo_email").css("opacity","0.5");
                $('.campo_email').val(old_email);
                $('#change-email-form').find("#conferma").hide();
                $('#change-email-form').find("#annulla").hide();
                $("#campo_email-error.error").remove();
            });
            $('#change-email-form').on('click', '#conferma', function() {
                var form = $("#change-email-form");
                var formData = new FormData(form[0]);
                var messaggio_conferma = "<div class='conferma_modifica_email'><div class='contenuto'>Sicuro di voler modificare la tua email?<div><button class='conferma'>Conferma</button><button class='annulla'>Annulla</button><div></div></div>";
                $(this).closest("#account-settings").append(messaggio_conferma);
                $(".conferma_modifica_email").css("display","block");
                $(".conferma_modifica_email").on("click", ".conferma", function(){
                    $.ajax({
                        type: "POST",
                        url: "modifica_info_utente.php", 
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            var responseObject = JSON.parse(response);
                            if (responseObject.status === "OK") {
                                $(".conferma_modifica_email .contenuto").html("<div class='contenuto'>Email modificata correttamente<div><button class='ok'>Ok</button>");
                                $(".conferma_modifica_email").on("click", ".ok", function(){
                                    $(".campo_email").css("opacity","0.5");
                                    $(this).closest($(".conferma_modifica_email")).remove();
                                    $('#change-email-form').find("#conferma").hide();
                                    $('#change-email-form').find("#annulla").hide();;
                                    var updatedData = responseObject.data;
                                    old_email = updatedData.campo_email;
                                }) 
                            } else if(responseObject.status === "Errore"){
                                if (responseObject.message === "Sessione annullata"){
                                    location.replace("login.php");
                                } else {
                                    $(".conferma_modifica_email").html("<div class='contenuto'>"+responseObject.message+"<div><button class='ok'>Ok</button>");
                                    $(".conferma_modifica_email").on("click", ".ok", function(){
                                        $(this).closest($(".conferma_modifica_email")).remove();
                                    })
                                }
                            }
                        },
                        error: function() {
                            alert("Errore di comunicazione con il server");
                        }
                    });
                })
                $(".conferma_modifica_email").on("click", ".annulla", function(){
                    $(this).closest($(".conferma_modifica_email")).remove();
                })
            });

            $(".campo_passw_corrente, .campo_passw, .campo_conf_passw").on('input', function() {
                $(this).css("opacity","1");
                $('#msg_passw_agg').hide();
                $('#msg_passw_agg_error').hide();
                $('#change-password-form').find("#conferma").css({"display":"inline", "margin-top":"1%"});
                $('#change-password-form').find("#annulla").css({"display":"inline", "margin-top":"1%"});
            });
            $('#change-password-form').on('click', '#annulla', function() {
                $(".campo_passw_corrente, .campo_passw, .campo_conf_passw").css("opacity","0.5");
                $('#msg_passw_agg_error').hide();
                $("#campo_passw-error.passw_error").remove();
                $("#campo_conf_passw-error.passw_error").remove();
                $('#change-password-form').find("#conferma").hide();
                $('#change-password-form').find("#annulla").hide();
                $('.campo_passw_corrente, .campo_passw, .campo_conf_passw').val('');
            });
            
            $('#change-password-form').on('click', '#conferma', function() {
                var form = $("#change-password-form");
                var formData = new FormData(form[0]);

                var messaggio_conferma = "<div class='conferma_modifica_password'><div class='contenuto'>Sicuro di voler modificare la tua password?<div><button class='conferma'>Conferma</button><button class='annulla'>Annulla</button><div></div></div>";
                $(this).closest("#account-settings").append(messaggio_conferma);
                $(".conferma_modifica_password").css("display","block");
                $(".conferma_modifica_password").on("click", ".conferma", function(){
                    $.ajax({
                        type: "POST",
                        url: "modifica_info_utente.php", 
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            var responseObject = JSON.parse(response);
                            if (responseObject.status === "OK") {
                                $(".conferma_modifica_password .contenuto").html("<div class='contenuto'>Password modificata correttamente<div><button class='ok'>Ok</button>");
                                $(".conferma_modifica_password").on("click", ".ok", function(){
                                    $(this).closest($(".conferma_modifica_password")).remove();
                                    $(".campo_passw_corrente, .campo_passw, .campo_conf_passw").css("opacity","0.5");
                                    $('#msg_passw_agg').append(responseObject.message);
                                    $('#msg_passw_agg').show();
                                    $('#change-password-form').find("#conferma, #annulla").hide();
                                    $('.campo_passw_corrente, .campo_passw, .campo_conf_passw').val('');
                                }) 
                            } else if(responseObject.status === "Errore"){
                                if (responseObject.message === "Sessione annullata"){
                                    location.replace("login.php");
                                } else {
                                    $('#campo_passw-error.passw_error').remove();
                                    $('#campo_conf_passw-error.passw_error').remove();
                                    $(".conferma_modifica_password").html("<div class='contenuto'>"+responseObject.message+"<div><button class='ok'>Ok</button>");
                                    $(".conferma_modifica_password").on("click", ".ok", function(){
                                        $(this).closest($(".conferma_modifica_password")).remove();
                                    })
                                }
                            }
                        },
                        error: function() {
                            alert("Errore di comunicazione con il server");
                        }
                    });
                })
                $(".conferma_modifica_password").on("click", ".annulla", function(){
                    $(this).closest($(".conferma_modifica_password")).remove();
                })
            });

            $("#elimina_account").on("click", function() {
                var messaggio_conferma = "<div class='conferma_eliminazione_account'><div class='contenuto'>Sicuro di voler eliminare definitivamente il tuo account? Proseguendo con l'operazione, tutti i dati e i blog associati all'account andranno persi.<div><button class='conferma'>Prosegui</button><button class='annulla'>Annulla</button></div></div></div>";
                $(this).closest("#account-settings").append(messaggio_conferma);
                $(".conferma_eliminazione_account").css("display", "block");
    
                $(".conferma_eliminazione_account").on("click", ".conferma", function(){
                    $(this).closest($(".conferma_eliminazione_account")).remove();
                    var messaggio_conferma_passw = "<div class='mess_conf_passw'><div class='contenuto'>Inserisci la tua password corrente per confermare l'eliminazione del tuo account. Cliccando su <b style='color:#33cc33;'>Conferma</b>, sarai reindirizzato alla pagina di login.";
                    $("#passw_elimina").append(messaggio_conferma_passw);
                    var passwordInput = "<br><input type='password' id='passwordInput'>";
                    $("#passw_elimina").find(".contenuto").append(passwordInput);
                    var pulsanti = "<br><div><button class='conferma' style='margin-left:-8%;'>Conferma</button><button class='annulla'>Annulla</button></div></div></div>";
                    $("#passw_elimina").find(".contenuto").append(pulsanti);
                    $(".mess_conf_passw").css("display", "block");

                    $(".mess_conf_passw").on("click", ".conferma", function(){
                        var passwordConferma = $("#passwordInput").val();
                        if (passwordConferma !== "") {
                            $.ajax({
                                type: "POST",
                                url: "modifica_info_utente.php", 
                                data: { passw_corrente: passwordConferma },
                                success: function(response) {
                                    var responseObject = JSON.parse(response);
                                    if (responseObject.status === "OK") {
                                        location.replace("login.php");
                                    } else {
                                        $("#passwordInput").after("<p class='eliminazione_error'>" + responseObject.message + "</p>");
                                        $("#passwordInput").on("input", function(){
                                            $("p.eliminazione_error").remove();
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    $("#passwordInput").after("<p class='eliminazione_error'>"+ xhr + "</p>");
                                }
                            })
                        }
                    });
                    $(".mess_conf_passw").on("click", ".annulla", function(){
                        $(this).closest($(".mess_conf_passw")).remove();
                    });
                });
    
                $(".conferma_eliminazione_account").on("click", ".annulla", function(){
                    $(this).closest($(".conferma_eliminazione_account")).remove();
                });
            });

            $.validator.addMethod("regex_username",
            function(value, element, regexp) {
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            },
            "Non ammessi caratteri speciali tranne: _ , - , .");
            
            $.validator.addMethod("regex_password",
            function(value, element, regexp) {
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            },
            "Obbligatori: lettere e numeri. Ammessi: ?, !, #, $, *");
            
            $("#change-username-form").validate({
                rules: {
                    campo_username: {
                        required: true,
                        minlength : 5,
                        maxlength : 20,
                        regex_username: /^[\w\.-]{5,20}$/
                    }
                },
                messages: {
                    campo_username: {
                        required: "Username obbligatorio!",
                        minlength : "Il nome utente deve essere di almeno 5 caratteri!",
                        maxlength: "Il nome utente non può contenere più di 20 caratteri!"
                    }
                }
            });

            $("#change-email-form").validate({
                rules: {
                    campo_email: {
                        required : true,
                        email: true
                    }
                },
                messages: {
                    campo_email: {
                        required: "Email obbligatoria!",
                        email: "Formato non valido!"
                    }
                }
            });

            $("#change-password-form").validate({
                rules: {
                    campo_passw: {
                        required : true,
                        minlength: 8,
                        regex_password: /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d?!#$*]{8,}$/
                    },
                    campo_conf_passw: {
                        equalTo: "#campo_passw"
                    }
                },
                messages: {
                    campo_passw: {
                        required: "Password obbligatoria!",
                        minlength: "La password deve essere di almeno 8 caratteri!"
                    },
                    campo_conf_passw: {
                        equalTo: "Le password devono coincidere!"
                    }
                },
                errorClass: 'passw_error'
            });
        });
    </script>
  </head>
<body id="body_tuoi_blog">
    <header id="header_tuoi_blog">
        <nav class="navbar">
            <div class="logo"><a href="home.php">Bluggle</a></div>
            <ul class="menu">
                <li><a href="home.php">Home</a></li>
                <li><a href="tutti_i_blog.php"> Tutti i Blog</a></li>
                <li><a href="i_tuoi_blog.php"> I tuoi Blog</a></li>
                <li><a href="account.php">Account</a></li>
                <li><a href="#">Info</a></li>
            </ul>
            <div class="buttons">
                <input type="button" value="Premium">
                <a href="logout.php"><input type="button" value="Logout"></a>
            </div>
        </nav>
        <div id="account-settings">

            <h2>Impostazioni account &nbsp;
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="18" viewBox="0 0 50 50">
            <path d="M47.16,21.221l-5.91-0.966c-0.346-1.186-0.819-2.326-1.411-3.405l3.45-4.917c0.279-0.397,0.231-0.938-0.112-1.282 l-3.889-3.887c-0.347-0.346-0.893-0.391-1.291-0.104l-4.843,3.481c-1.089-0.602-2.239-1.08-3.432-1.427l-1.031-5.886 C28.607,2.35,28.192,2,27.706,2h-5.5c-0.49,0-0.908,0.355-0.987,0.839l-0.956,5.854c-1.2,0.345-2.352,0.818-3.437,1.412l-4.83-3.45 c-0.399-0.285-0.942-0.239-1.289,0.106L6.82,10.648c-0.343,0.343-0.391,0.883-0.112,1.28l3.399,4.863 c-0.605,1.095-1.087,2.254-1.438,3.46l-5.831,0.971c-0.482,0.08-0.836,0.498-0.836,0.986v5.5c0,0.485,0.348,0.9,0.825,0.985 l5.831,1.034c0.349,1.203,0.831,2.362,1.438,3.46l-3.441,4.813c-0.284,0.397-0.239,0.942,0.106,1.289l3.888,3.891 c0.343,0.343,0.884,0.391,1.281,0.112l4.87-3.411c1.093,0.601,2.248,1.078,3.445,1.424l0.976,5.861C21.3,47.647,21.717,48,22.206,48 h5.5c0.485,0,0.9-0.348,0.984-0.825l1.045-5.89c1.199-0.353,2.348-0.833,3.43-1.435l4.905,3.441 c0.398,0.281,0.938,0.232,1.282-0.111l3.888-3.891c0.346-0.347,0.391-0.894,0.104-1.292l-3.498-4.857 c0.593-1.08,1.064-2.222,1.407-3.408l5.918-1.039c0.479-0.084,0.827-0.5,0.827-0.985v-5.5C47.999,21.718,47.644,21.3,47.16,21.221z M25,32c-3.866,0-7-3.134-7-7c0-3.866,3.134-7,7-7s7,3.134,7,7C32,28.866,28.866,32,25,32z"></path></svg>
            </h2>

        <form>
            <label for="status">Stato dell'account:</label></br>
            <span id="current-status">
            <?php if ($premium == 1) {
                echo "Premium 🎖️";
            } else {
                echo "Standard";
            }?></span>
        </form>    
            
        <form id="change-username-form">
          <label for="new-username">Username:</label></br>
          <input type='text' name='campo_username' class='campo_username' value='<?php echo $username ?>'></br>
          <input type='button' id='conferma' value='Conferma' style='background:linear-gradient(135deg, #33cc33, #1f7a1f)'>
          <input type='button' id='annulla' value='Annulla' style='background:linear-gradient(135deg, #ff1414, #6b0c0c)'>
        </form>

        <form id="change-email-form">
          <label for="new-email">Email:</label></br>
          <input type='text' name='campo_email' class='campo_email' value='<?php echo $email ?>'></br>
          <input type='button' id='conferma' value='Conferma' style='background:linear-gradient(135deg, #33cc33, #1f7a1f)'>
          <input type='button' id='annulla' value='Annulla' style='background:linear-gradient(135deg, #ff1414, #6b0c0c)'>
        </form>

        <form id="change-password-form">
          <label for="new-password">Password: </label></br>
          <input type='password' name='passw_corrente' class='campo_passw_corrente' id='campo_passw_corrente' placeholder='Digita la tua password corrente'></br></br>
          <input type='password' name='campo_passw' class='campo_passw' id='campo_passw' placeholder='Digita una nuova password'></br></br>
          <input type='password' name='campo_conf_passw' class='campo_conf_passw' id='campo_conf_passw' placeholder='Conferma la nuova password'></br>
          <p id='msg_passw_agg' hidden></p>
          <p id='msg_passw_agg_error' hidden></p>
          <input type='button' id='conferma' value='Conferma' style='background:linear-gradient(135deg, #33cc33, #1f7a1f)'>
          <input type='button' id='annulla' value='Annulla' style='background:linear-gradient(135deg, #ff1414, #6b0c0c)'>
        </form>
            
        <input type="button" id="elimina_account" value="Elimina account">
        <div id="passw_elimina"></div>

        </div>
    </header>
</body>
</html>
