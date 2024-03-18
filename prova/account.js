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
                            $('#msg_passw_agg').text(responseObject.message);
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
            var messaggio_conferma_passw = "<div class='mess_conf_passw'><div class='contenuto'>Inserisci la tua <span style='font-weight:bold'>password corrente</span> per confermare l'eliminazione del tuo account. Cliccando su <b style='color:#33cc33;'>Conferma</b>, sarai reindirizzato alla pagina di login.<br><input type='password' placeholder='Password' id='passwordInput'><br><div><button class='conferma' style='margin-left:-8%;'>Conferma</button><button class='annulla'>Annulla</button></div></div></div>";
            $(this).closest($(".conferma_eliminazione_account")).html(messaggio_conferma_passw);

            $(".mess_conf_passw").on("click", ".conferma", function(){
                var passwordConferma = $("#passwordInput").val();
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
                            $("#passwordInput").after("<p class='eliminazione_error'>"+ xhr.statusText + "</p>");
                        }
                    })
            });
            $(".mess_conf_passw").on("click", ".annulla", function(){
                $(this).closest($(".conferma_eliminazione_account")).remove();
            });
        });

        $(".conferma_eliminazione_account").on("click", ".annulla", function(){
            $(this).closest(".conferma_eliminazione_account").remove();
        });
    });

    $(".disdetta_premium").on("click", function() {
        var messaggio_conferma = "<div class='conferma_eliminazione_account'><div class='contenuto'>Sicuro di voler disdire l'abbonamento?<div><button class='conferma'>Prosegui</button><button class='annulla'>Annulla</button></div></div></div>";
        $(this).closest("#account-settings").append(messaggio_conferma);
        $(".conferma_eliminazione_account").css("display", "block");

        $(".conferma_eliminazione_account").on("click", ".conferma", function(){
            $.ajax({
                type: "POST",
                url: "upgrade_premium.php", 
                data: {utente: idutente, operazione: "Disdici"},
                success: function(response) {
                    var responseObject = JSON.parse(response);
                    if (responseObject.status === "OK") {
                        $(".conferma_eliminazione_account .contenuto").html("<p>" + responseObject.message + "</p><input class='ok'type='button' value='Ok'>");
                        $(".conferma_eliminazione_account .ok").on("click", function(){
                            location.reload();
                        });
                    } else {
                        $(".conferma_eliminazione_account .contenuto").html("<p class='eliminazione_error'>" + responseObject.message + "</p><br><input class='ok'type='button' value='Ok'>");
                        $(".conferma_eliminazione_account .ok").on("click", function(){
                            $(".conferma_eliminazione_account").remove();
                        });
                    }
                },
                error: function(xhr) {
                    $("#passwordInput").after("<p class='eliminazione_error'>"+ xhr.statusText + "</p>");
                }
            })
        });

        $(".conferma_eliminazione_account").on("click", ".annulla", function(){
            $(this).closest(".conferma_eliminazione_account").remove();
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