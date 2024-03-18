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
            $.ajax({
                type: "POST",
                url: $("form#premium").attr("action"),
                data: {utente: id_utente, operazione: "Premium"},
                success: function(response){
                    var responseObject = JSON.parse(response);
                    if(responseObject.status === "OK"){
                        $("form#premium").html("</br> <div class='contenuto'>" + responseObject.message + "</br> <div><button class='ok'>Ok</button>");
                        $("form#premium .contenuto").on("click", ".ok", function(){
                            location.replace("home.php");
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