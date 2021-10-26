<?php

session_start();

if (isset($_GET['logout'])) {

  $_SESSION = [];
  session_destroy();
}

$errorMsg   = '';
$successMsg = '';

try {

  $db = new PDO(
    'mysql:dbname=myscookl;host=localhost;charset=utf8',
    'root',
    'root',
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
  );
} catch (Exception $e) {
  var_dump($e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MySCookL - Home</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <link rel="stylesheet" href="public/style/style.css">
</head>

<body>

  <header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="index.php">MySCookL</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item active">
            <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="recipe.php">Recettes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="selectingredient.php">+ Recettes</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Lives</a>
          </li>

          <?php if (isset($_SESSION['user'])) { ?>

            <?php if($_SESSION['user']['admin_name'] == 'Kitsunhey') { ?>
              <li class="nav-item">
              <a class="nav-link" href="admin_panel.php">Admin Panel</a>
            </li>
            <?php } ?>

            <li class="nav-item">
              <a class="nav-link" href="#">Mon compte</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="myrecipe.php">Mes recettes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="index.php?logout=true">Deconnection</a>
            </li>

          <?php } else { ?>
            <li class="nav-item">
              <a class="nav-link" href="signin.php">Se connecter</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="signup.php">S'inscrire</a>
            </li>
          <?php } ?>
        </ul>
      </div>
    </nav>
  </header>

  <main>