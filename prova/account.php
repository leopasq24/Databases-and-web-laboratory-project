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
        $disdici = "<input type='button' class='disdetta_premium' value='Disdici'>";
    }
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
    <script>var idutente = <?php echo $id_utente ?></script>
    <script src = "js/account.js"></script>
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
                <li><a href="info.php">Info</a></li>
            </ul>
            <div class="buttons">
                <?php echo $button ?>
                <a href="logout.php"><input type="button" value="Logout"></a>
            </div>
        </nav>
        <div id="account-settings">

            <h2>Impostazioni account &nbsp; <i class='fas fa-cog'></i></h2>

        <form>
            <label for="status">Stato dell'account:</label></br>
            <span id="current-status">
            <?php if ($premium == 1) {
                echo "Premium 🎖️";
            } else {
                echo "Standard";
            }?></span><br>
            <?php if ($premium == 1) {
                echo $disdici;
            }?>
        </form>    
            
        <form id="change-username-form" action ="modifica_info_utente.php" method="post">
          <label for="new-username">Username:</label></br>
          <input type='text' name='campo_username' class='campo_username' value='<?php echo $username ?>'></br>
          <input type='submit' id='conferma' value='Conferma' style='background:linear-gradient(135deg, #33cc33, #1f7a1f)'>
          <input type='button' id='annulla' value='Annulla' style='background:linear-gradient(135deg, #ff1414, #6b0c0c)'>
        </form>

        <form id="change-email-form" action ="modifica_info_utente.php" method="post">
          <label for="new-email">Email:</label></br>
          <input type='text' name='campo_email' class='campo_email' value='<?php echo $email ?>'></br>
          <input type='submit' id='conferma' value='Conferma' style='background:linear-gradient(135deg, #33cc33, #1f7a1f)'>
          <input type='button' id='annulla' value='Annulla' style='background:linear-gradient(135deg, #ff1414, #6b0c0c)'>
        </form>

        <form id="change-password-form" action ="modifica_info_utente.php" method="post">
          <label for="new-password">Password: </label></br>
          <input type='password' name='passw_corrente' class='campo_passw_corrente' id='campo_passw_corrente' placeholder='Digita la tua password corrente'></br></br>
          <input type='password' name='campo_passw' class='campo_passw' id='campo_passw' placeholder='Digita una nuova password'></br></br>
          <input type='password' name='campo_conf_passw' class='campo_conf_passw' id='campo_conf_passw' placeholder='Conferma la nuova password'></br>
          <p id='msg_passw_agg' hidden></p>
          <p id='msg_passw_agg_error' hidden></p>
          <input type='submit' id='conferma' value='Conferma' style='background:linear-gradient(135deg, #33cc33, #1f7a1f)'>
          <input type='button' id='annulla' value='Annulla' style='background:linear-gradient(135deg, #ff1414, #6b0c0c)'>
        </form>
            
        <input type="button" id="elimina_account" value="Elimina account">
        <div id="passw_elimina"></div>

        </div>
    </header>
</body>
</html>
