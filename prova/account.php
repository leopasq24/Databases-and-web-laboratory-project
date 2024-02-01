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
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            
            var old_username;
            var old_mail;
            var old_passw;
            var annulla = "<input type='button' id='annulla' value='Annulla' style='background:linear-gradient(135deg, #ff1414, #6b0c0c)'>";
            
            $("#cambia_username").on("click", function() {
                if($(this).val() == "✎"){
                    $(this).val("Conferma");
                    $(this).css({"background":"linear-gradient(135deg, #33cc33, #1f7a1f)", "margin-top":"1%"});
                    old_username = $("#current-username");
                    old_username.hide();
                    new_input_username = "<input type='text' name='campo_username' class='campo_username'>";
                    $("#change-username-form").append(new_input_username);
                    $("#change-username-form").append("<br class='br_provvisorio'>");
                    $("#change-username-form").find(".campo_username").val(old_username.text());
                    $("#change-username-form").append($(this));
                    $("#change-username-form").append(annulla);
                    $("#change-username-form").find("#annulla").on("click", function(){
                        old_username.show();
                        $("#cambia_username").val("✎");
                        $("#cambia_username").css("background", "linear-gradient(135deg, #83c1ff, #2972af)");
                        $("#cambia_username").css("margin-top", "0");
                        $("#change-username-form").find(".br_provvisorio").remove();
                        $("#change-username-form").find(".campo_username").remove();
                        $(this).remove();
                        $("label#campo_username-error.error").remove();
                    });
                } else {
                    var form = $("#change-username-form");
                    var formData = new FormData(form[0]);
                    if (confirm("Sicuro di voler modificare il tuo username?")) {
                        $.ajax({
                            type: "POST",
                            url: "modifica_info_utente.php", 
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                var responseObject = JSON.parse(response);
                                if (responseObject.status === "OK") {
                                    var updatedData = responseObject.data;
                                    old_username.text(updatedData.campo_username);
                                    old_username.show();
                                    $(".campo_username").remove();
                                    $("#cambia_username").val("✎");
                                    $("#cambia_username").css("background", "linear-gradient(135deg, #83c1ff, #2972af)");
                                    $("#cambia_username").css("margin-left", "5%");
                                    $("input#annulla").remove();
                                } else if(responseObject.status === "Errore"){
                                    if (responseObject.message === "Sessione annullata"){
                                        location.replace("registrazione.php");
                                    } else {
                                        $("#campo_username-error.error").text(responseObject.message);
                                    }
                                }
                            },
                            error: function() {
                                alert("Errore di comunicazione con il server");
                            }
                        });
                    }
                }
            });

            $("#cambia_mail").on("click", function() {
                if($(this).val() == "✎"){
                    $(this).val("Conferma");
                    $(this).css({"background":"linear-gradient(135deg, #33cc33, #1f7a1f)", "margin-top":"1%"});
                    old_mail = $("#current-email");
                    old_mail.hide();
                    new_input_email = "<input type='text' name='campo_email' class='campo_email'>";
                    $("#change-email-form").append(new_input_email);
                    $("#change-email-form").append("<br class='br_provvisorio'>");
                    $("#change-email-form").find(".campo_email").val(old_mail.text());
                    $("#change-email-form").append($(this));
                    $("#change-email-form").append(annulla);
                    $("#change-email-form").find("#annulla").on("click", function(){
                        old_mail.show();
                        $("#cambia_mail").val("✎");
                        $("#cambia_mail").css("background", "linear-gradient(135deg, #83c1ff, #2972af)");
                        $("#cambia_mail").css("margin-top", "0");
                        $("#change-email-form").find(".br_provvisorio").remove();
                        $("#change-email-form").find(".campo_email").remove();
                        $(this).remove();
                        $("label#campo_email-error.error").remove();
                    });
                } else {
                    var form = $("#change-email-form");
                    var formData = new FormData(form[0]);
                    if (confirm("Sicuro di voler modificare la tua email?")) {
                        $.ajax({
                            type: "POST",
                            url: "modifica_info_utente.php", 
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                var responseObject = JSON.parse(response);
                                if (responseObject.status === "OK") {
                                    var updatedData = responseObject.data;
                                    old_mail.text(updatedData.campo_email);
                                    old_mail.show();
                                    $(".campo_email").remove();
                                    $("#cambia_mail").val("✎");
                                    $("#cambia_mail").css("background", "linear-gradient(135deg, #83c1ff, #2972af)");
                                    $("#cambia_mail").css("margin-left", "5%");
                                    $("input#annulla").remove();
                                } else if(responseObject.status === "Errore"){
                                    if (responseObject.message === "Sessione annullata"){
                                        location.replace("registrazione.php");
                                    } else {
                                    $("#campo_email-error.error").text(responseObject.message);
                                    }
                                }
                            },
                            error: function() {
                                alert("Errore di comunicazione con il server");
                            }
                        });
                    }
                }
            });

            $("#cambia_password").on("click", function() {
                if($(this).val() == "✎"){
                    $(this).val("Conferma");
                    $(this).css({"background":"linear-gradient(135deg, #33cc33, #1f7a1f)", "margin-top":"2%"});
                    old_passw = $("#current-passw");
                    old_passw.hide();
                    input_old_passw = "<input type='password' name='passw_corrente' class='campo_passw_corrente' id='campo_passw_corrente' placeholder='Digita la tua password corrente'>";
                    new_input_passw = "<input type='password' name='campo_passw' class='campo_passw' id='campo_passw' placeholder='Digita una nuova password'>";
                    new_input_conf_passw = "<input type='password' name='campo_conf_passw' class='campo_conf_passw' id='campo_conf_passw' placeholder='Conferma la nuova password'>";
                    $("#change-password-form").append(input_old_passw);
                    $("#change-password-form").append("<br class='br_provvisorio'>");
                    $("#change-password-form").append("<br class='br_provvisorio'>");
                    $("#change-password-form").append(new_input_passw);
                    $("#change-password-form").append("<br class='br_provvisorio'>");
                    $("#change-password-form").append("<br class='br_provvisorio'>");
                    $("#change-password-form").append(new_input_conf_passw);
                    $("#change-password-form").append("<br class='br_provvisorio'>");
                    $("#change-password-form").append($(this));
                    $("#change-password-form").append(annulla);
                    $("#change-password-form").find("#annulla").on("click", function(){
                        old_passw.show();
                        $("#cambia_password").val("✎");
                        $("#cambia_password").css("background", "linear-gradient(135deg, #83c1ff, #2972af)");
                        $("#cambia_password").css("margin-top", "0");
                        $("#change-password-form").find(".br_provvisorio").remove();
                        $("#change-password-form").find(".campo_passw_corrente").remove();
                        $("#change-password-form").find(".campo_passw").remove();
                        $("#change-password-form").find(".campo_conf_passw").remove();
                        $(this).remove();
                        $("#campo_passw-error.error").remove();
                        $("#campo_conf_passw-error.error").remove();
                    });
                } else {
                    var form = $("#change-password-form");
                    var formData = new FormData(form[0]);
                    if (confirm("Sicuro di voler modificare la tua password?")) {
                        $.ajax({
                            type: "POST",
                            url: "modifica_info_utente.php", 
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                var responseObject = JSON.parse(response);
                                if (responseObject.status === "OK") {
                                    var risposta = $("<span>").text(responseObject.message).css({
                                        "color": "#33cc33",
                                        "font-size": "15px",
                                        "font-weight": "500",
                                        "margin-left": "2%"
                                    });
                                    old_passw.show().append(risposta);
                                    $(".campo_passw_corrente").remove();
                                    $(".campo_passw").remove();
                                    $(".campo_conf_passw").remove();
                                    $("#cambia_password").val("✎");
                                    $("#cambia_password").css("background", "linear-gradient(135deg, #83c1ff, #2972af)");
                                    $("input#annulla").remove();
                                } else if(responseObject.status === "Errore"){
                                    if (responseObject.message === "Sessione annullata"){
                                        location.replace("registrazione.php");
                                    } else {
                                        $("#change-password-form").append("<label id='campo_passw-error' class='error'></div>");
                                        $("#campo_passw-error.error").text(responseObject.message);
                                        $("#campo_passw-error.error").css({"display":"block", "margin-top":"-0.5%"});
                                        $("#campo_passw_corrente, #campo_passw, #campo_conf_passw").on("input", function() {
                                            $("#campo_passw-error.error").hide();
                                        });
                                    } 
                                }
                            },
                            error: function() {
                                alert("Errore di comunicazione con il server");
                            }
                        });
                    }
                }
            });

            $("#elimina_account").on("click", function() {
                if (confirm("Sicuro di voler eliminare definitivamente il tuo account? Proseguendo con l'operazione, tutti i dati e i blog associati all'account andranno persi.")) {
                    var passwordConferma = "";
                    var passwordDialog = $("#passw_elimina").html("Inserisci la tua password corrente per confermare l'eliminazione del tuo account. Cliccando su OK, sarai reindirizzato alla pagina di login.");
                    var passwordInput = $("<input>").attr({ type: "password", id: "passwordInput" });
                    passwordDialog.append(passwordInput);
                    passwordDialog.dialog({
                        resizable: false,
                        modal: true,
                        buttons: {
                            "OK": function() {
                                passwordConferma = $("#passwordInput").val();
                                if (passwordConferma !== "") {
                                    $(this).dialog("close");
                                    $.ajax({
                                        type: "POST",
                                        url: "modifica_info_utente.php",
                                        data: { elimina_account: true, passw_corrente: passwordConferma },
                                        success: function(response) {
                                            var responseObject = JSON.parse(response);
                                            if (responseObject.status === "OK") {
                                                location.replace("login.php");
                                            }
                                        },
                                        error: function() {
                                            alert("Errore nella richiesta AJAX: " + error);
                                        }
                                    });
                                }
                            },
                            "Annulla": function() {
                                $(this).dialog("close");
                            }
                        }
                    });
                }
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
                }
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
          <span id="current-username"><?php echo $username ?></span>
          <input type="button" id="cambia_username" value="✎">
        </form>

        <form id="change-email-form">
          <label for="new-email">Email:</label></br>
          <span id="current-email"><?php echo $email ?></span>
          <input type="button" id="cambia_mail" value="✎">
        </form>

        <form id="change-password-form">
          <label for="new-password">Password: </label></br>
          <span id="current-passw">********</span>
          <input type="button" id="cambia_password" value="✎">
        </form>
            
        <input type="button" id="elimina_account" value="Elimina account">
        <div id="passw_elimina"></div>

        </div>
    </header>
</body>
</html>