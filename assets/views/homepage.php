<?php
require_once '../../Database.php';

$database = new Database();

// Établir la connexion
$conn = $database->connect();

// Fermer la connexion
$database->disconnect();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="../styles/global.css">
    <link rel="stylesheet" href="../styles/homepage.css">
    <title>Quiz Night</title>
</head>

<body>
    <header>
        <nav>
            <img class="logo" src="../img/logo.png" alt="logo">
        </nav>
    </header>
    <main>
        <section id="">
            <div class="section">
              <div id="homepage_section">
                <h1 id="welcome_text">Bienvenue dans Quiz Night !</h1>
                <h3 id="funny_text">Prêts à buzzer jusqu'au bout de la nuit ?</h3>
                <div id="name_div_tag">
                <div class = "div_tag">
                  <h4>Géographie</h4>
                  <img src="../img/img_geography.jpg" alt="img_tag_geography" class = "img_tag">
                </div>
                <div class = "div_tag">
                  <h4>Divertissement</h4>
                  <img src="../img/img_entertainment.jpg" alt="img_tag_entertainment" class = "img_tag">
                </div>
                <div class = "div_tag">
                  <h4>Histoire</h4>
                  <img src="../img/img_history.jpg" alt="img_tag_history" class = "img_tag">
                </div>
                <div class = "div_tag">
                  <h4>Art et Littérature</h4>
                  <img src="../img/img_art_and_literature.jpg" alt="img_tag_art_and_literature" class = "img_tag">
                </div>
                <div class = "div_tag">
                  <h4>Science et Nature</h4>
                  <img src="../img/img_science_and_nature.jpg" alt="img_tag_science_and_nature" class = "img_tag">
                </div>
                <div class = "div_tag">
                  <h4>Sports et Loisirs</h4>
                  <img src="../img/img_sports_and_leisures.jpg" alt="img_tag_sports_and_leisures" class = "img_tag">
                </div>
                </div>
              </div>
            </div>
        </section>
    </main>
    <footer>

    </footer>
</body>

</html>