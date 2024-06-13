<?php
require_once '../../Database.php';

// Démarrer la session
session_start();

$database = new Database();

// Établir la connexion
$conn = $database->connect();

// Requête pour obtenir tous les tags
$tags = [];
$sql = "SELECT id, name FROM tag";
$stmt = $conn->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $tags[] = $row;
}

$database->disconnect();

// Tableau de correspondance des tags et des images
$imageMap = [
    'Géographie' => 'img_geography.jpg',
    'Divertissement' => 'img_entertainment.jpg',
    'Histoire' => 'img_history.jpg',
    'Art et Littérature' => 'img_art_and_literature.jpg',
    'Science et Nature' => 'img_science_and_nature.jpg',
    'Sports et Loisirs' => 'img_sports_and_leisures.jpg'
];
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
                <?php foreach ($tags as $tag): ?>
                    <div class="div_tag">
                      <h4><?php echo htmlspecialchars($tag['name']); ?></h4>
                      <a href="http://localhost/quiznight/assets/views/listpage.php?tag_id=<?php echo htmlspecialchars($tag['id']); ?>">
                        <img src="../img/<?php echo $imageMap[$tag['name']]; ?>" alt="img_tag_<?php echo strtolower(str_replace(' ', '_', $tag['name'])); ?>" class="img_tag">
                      </a>
                    </div>
                <?php endforeach; ?>
                </div>
              </div>
            </div>
        </section>
    </main>
    <footer>

    </footer>
</body>
</html>
