<?php
session_start();
include_once("connect.php");

if (!isset($_SESSION["session_utente"])) {
    echo json_encode(["status" => "Errore", "message" => "Sessione annullata"]);
    exit;
}

$id_utente = $_SESSION["session_utente"];

if (isset($_POST["campo_username"])) {
    $new_username = trim($_POST["campo_username"]);

    if (strlen($new_username) < 5 || strlen($new_username) > 20) {
        echo json_encode(["status" => "Errore", "message" => "Lo username deve essere tra 5 e 20 caratteri!"]);
        exit;
    }

    $check_username_query = mysqli_prepare($link, "SELECT IdUtente FROM utente WHERE Username = ?");
    mysqli_stmt_bind_param($check_username_query, "s", $new_username);
    mysqli_stmt_execute($check_username_query);
    mysqli_stmt_store_result($check_username_query);
    if (mysqli_stmt_num_rows($check_username_query) == 0) {
        $update_username_query = mysqli_prepare($link, "UPDATE utente SET Username = ? WHERE IdUtente = ?");
        mysqli_stmt_bind_param($update_username_query, "si", $new_username, $id_utente);
        if (mysqli_stmt_execute($update_username_query)) {
            echo json_encode(["status" => "OK", "data" => ["campo_username" => $new_username]]);
        } else {
            echo json_encode(["status" => "Errore", "message" => "Errore nell'aggiornamento dello username!"]);
        }
        mysqli_stmt_close($update_username_query);
    } else {
        echo json_encode(["status" => "Errore", "message" => "Lo username che hai inserito esiste già!"]);
    }
    mysqli_stmt_close($check_username_query);
}

if (isset($_POST["campo_email"])) {
    $new_email = trim($_POST["campo_email"]);

    if (strlen($new_email) == 0 || !filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "Errore", "message" => "Formato email non valido o campo obbligatorio!"]);
        exit;
    }

    $check_email_query = mysqli_prepare($link, "SELECT IdUtente FROM utente WHERE Email = ?");
    mysqli_stmt_bind_param($check_email_query, "s", $new_email);
    mysqli_stmt_execute($check_email_query);
    mysqli_stmt_store_result($check_email_query);
    if (mysqli_stmt_num_rows($check_email_query) == 0) {
        $update_email_query = mysqli_prepare($link, "UPDATE utente SET Email = ? WHERE IdUtente = ?");
        mysqli_stmt_bind_param($update_email_query, "si", $new_email, $id_utente);
        if (mysqli_stmt_execute($update_email_query)) {
            echo json_encode(["status" => "OK", "data" => ["campo_email" => $new_email]]);
        } else {
            echo json_encode(["status" => "Errore", "message" => "Errore nell'aggiornamento dell'email!"]);
        }
        mysqli_stmt_close($update_email_query);
    } else {
        echo json_encode(["status" => "Errore", "message" => "L'email che hai inserito esiste già!"]);
    }
    mysqli_stmt_close($check_email_query);
}

if (isset($_POST["campo_passw"]) && isset($_POST["campo_conf_passw"]) && isset($_POST["passw_corrente"])) {
    $current_passw = trim($_POST["passw_corrente"]);
    $hashed_curr_passw = hash('sha3-512', $current_passw); 
    $new_passw = trim($_POST["campo_passw"]);
    $conf_passw = trim($_POST["campo_conf_passw"]);

    if (strlen($new_passw) < 8 || $new_passw != $conf_passw) {
        echo json_encode(["status" => "Errore", "message" => "La password deve essere di almeno 8 caratteri e coincidere con la conferma!"]);
        exit;
    }

    $check_passw_query = mysqli_prepare($link, "SELECT IdUtente, Passw FROM utente WHERE IdUtente = ?");
    mysqli_stmt_bind_param($check_passw_query, "i", $id_utente);
    mysqli_stmt_execute($check_passw_query);
    mysqli_stmt_store_result($check_passw_query);
    if (mysqli_stmt_num_rows($check_passw_query) === 1) {
        mysqli_stmt_bind_result($check_passw_query, $id_utente_db, $hashed_passw_db);
        mysqli_stmt_fetch($check_passw_query);
        if ($hashed_curr_passw === $hashed_passw_db) {
            $update_passw_query = mysqli_prepare($link, "UPDATE utente SET Passw = ? WHERE IdUtente = ?");
            $hashed_passw = hash('sha3-512', $new_passw);
            mysqli_stmt_bind_param($update_passw_query, "si", $hashed_passw, $id_utente);
            if (mysqli_stmt_execute($update_passw_query)) {
                echo json_encode(["status" => "OK", "message" => "Password aggiornata con successo!"]);
                exit;
            } else {
                echo json_encode(["status" => "Errore", "message" => "Errore nell'aggiornamento della password!"]);
            }
            mysqli_stmt_close($update_passw_query);
        } else {
            echo json_encode(["status" => "Errore", "message" => "La password corrente fornita non è corretta!"]);
            exit;
        }
    } else {
        echo json_encode(["status" => "Errore", "message" => "La password corrente fornita non è corretta!"]);
        exit;
    }
    mysqli_stmt_close($check_passw_query);
}

if (isset($_POST["passw_corrente"])) {
    $curr_passw = trim($_POST["passw_corrente"]);
    $hashed_curr_passw = hash('sha3-512', $curr_passw);

    $check_passw_query = mysqli_prepare($link, "SELECT Passw FROM utente WHERE IdUtente = ?");
    mysqli_stmt_bind_param($check_passw_query, "i", $id_utente);
    mysqli_stmt_execute($check_passw_query);
    mysqli_stmt_store_result($check_passw_query);

    if (mysqli_stmt_num_rows($check_passw_query) === 0) {
        echo json_encode(["status" => "Errore", "message" => "La password corrente fornita non è corretta!"]);
        exit;
    } else {
        mysqli_stmt_bind_result($check_passw_query, $hashed_passw_db);
        mysqli_stmt_fetch($check_passw_query);

        if ($hashed_curr_passw === $hashed_passw_db) {
            $delete_account_query = mysqli_prepare($link, "DELETE FROM utente WHERE IdUtente = ?");
            mysqli_stmt_bind_param($delete_account_query, "i", $id_utente);
            mysqli_stmt_execute($delete_account_query);

            if (mysqli_stmt_affected_rows($delete_account_query) > 0) {
                session_unset();
                session_destroy();
                echo json_encode(["status" => "OK", "message" => "Account eliminato con successo!"]);
            } else {
                echo json_encode(["status" => "Errore", "message" => "Errore nell'eliminazione dell'account"]);
            }

            mysqli_stmt_close($delete_account_query);
        } else {
            echo json_encode(["status" => "Errore", "message" => "La password corrente fornita non è corretta!"]);
            exit;
        }
    }
    mysqli_stmt_close($check_passw_query);
}
?>
