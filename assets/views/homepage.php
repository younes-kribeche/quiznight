<?php
    session_start();

    require_once '../../Database.php';

    $database = new Database();

    // Établir la connexion
    $conn = $database->connect();

    // Définir la base de l'URL et les paramètres
    $listPage = 'http://localhost/quiznight/assets/views/listpage.php';
    $param = 'tag_id';

    // Définir la valeur de l'url
    $tagGeography = 1;
    $tagEntertainment = 2;
    $tagHistory = 3;
    $tagArtAndLiterature = 4;
    $tagScienceAndNature = 5;
    $tagSportsAndLeisures = 6;

    // Construire l'URL complète
    $url = $listPage . '?' . $param;

    $database->disconnect();
?>


<!-----------------------------HTML------------------------------------>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Roboto Condensed' rel='stylesheet'>
    <link rel="stylesheet" href="../styles/global.css">
    <link rel="stylesheet" href="../styles/homepage.css">
    <script src="../script/script.js" defer></script>
    <title>Quiz Night</title>
</head>

<body>
    <?php
        include('header.php');
    ?>
    <main>
        <section id="">
            <div class="section">
              <div id="homepage_section">
                <h1 id="welcome_text">Bienvenue dans Quiz Night !</h1>
                <h3 id="funny_text">Prêts à buzzer jusqu'au bout de la nuit ?</h3>
                <div id="name_div_tag">
                <div class = "div_tag">
                  <h4>Géographie</h4>
                  <a href="<?php echo htmlspecialchars($url . '=' . $tagGeography); ?>">
                    <img src="../img/img_geography.jpg" alt="img_tag_geography" class = "img_tag">
                  </a>
                </div>
                <div class = "div_tag">
                  <h4>Divertissement</h4>
                  <a href="<?php echo htmlspecialchars($url . '=' . $tagEntertainment); ?>">
                    <img src="../img/img_entertainment.jpg" alt="img_tag_entertainment" class = "img_tag">
                  </a>
                </div>
                <div class = "div_tag">
                  <h4>Histoire</h4>
                  <a href="<?php echo htmlspecialchars($url . '=' . $tagHistory); ?>">
                  <img src="../img/img_history.jpg" alt="img_tag_history" class = "img_tag">
                  </a>
                </div>
                <div class = "div_tag">
                  <h4>Art et Littérature</h4>
                  <a href="<?php echo htmlspecialchars($url . '=' . $tagArtAndLiterature); ?>">
                  <img src="../img/img_art_and_literature.jpg" alt="img_tag_art_and_literature" class = "img_tag">
                  </a>
                </div>
                <div class = "div_tag">
                  <h4>Science et Nature</h4>
                  <a href="<?php echo htmlspecialchars($url . '=' . $tagScienceAndNature); ?>">
                    <img src="../img/img_science_and_nature.jpg" alt="img_tag_science_and_nature" class = "img_tag">
                  </a>
                </div>
                <div class = "div_tag">
                  <h4>Sports et Loisirs</h4>
                  <a href="<?php echo htmlspecialchars($url . '=' . $tagSportsAndLeisures); ?>">
                    <img src="../img/img_sports_and_leisures.jpg" alt="img_tag_sports_and_leisures" class = "img_tag">
                  </a>
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