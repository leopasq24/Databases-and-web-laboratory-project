<?php
session_start();
session_unset();
?>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <title> Pagina iniziale </title>
    <link rel="stylesheet" href="stile_index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
   </head>
<body>
  <header>
    <nav class="navbar">
      <div class="logo"><a href="#">BLUGGLE></a></div>
      <ul class="menu">
        <li><a href="#">Home</a></li>
        <li><a href="#">Latest</a></li>
        <li><a href="#">Offers</a></li>
        <li><a href="#">Services</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
      <div class="buttons">
        <input type="button" value="Premium">
        <a href="logout.php"><input type="button" value="Logout"></a>
      </div>
    </nav>
    <div class="text-content">
      <h2>Learn To Enjoy,<br>Every Moment Of Your Life</h2>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum facere in nam, officiis aspernatur consectetur aliquid sequi possimus et. Sint.</p>
    </div>
  </header>
</body>
</html>
