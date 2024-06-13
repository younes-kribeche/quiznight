<?php
require_once '../../Database.php';

// Démarrer la session
session_start();

$database = new Database();

// Établir la connexion
$conn = $database->connect();   

$quizContent = ""; // Variable pour stocker les quiz

// Définir la base de l'URL et les paramètres
if (isset($_GET['quizz_id'])) {
    $tag_id = intval($_GET['quizz_id']);
    $sql = "SELECT quizz.id, quizz.name, quizz.created_at, user.name as creator_name, difficulty.name as difficulty_name, difficulty.id as difficulty_id 
    FROM quizz 
    JOIN user ON quizz.id_user = user.id 
    JOIN difficulty ON quizz.id_difficulty = difficulty.id
    WHERE quizz.id_tag = :tag_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':tag_id', $tag_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Récupérer chaque colonne séparément
            $quiz_id = htmlspecialchars($row['id']);
            $name = htmlspecialchars($row['name']);
            $created_at = htmlspecialchars($row['created_at']);
            $creator_name = htmlspecialchars($row['creator_name']);
            $difficulty_name = htmlspecialchars($row['difficulty_name']);
            $difficulty_id = htmlspecialchars($row['difficulty_id']);

            // Créer une chaîne formatée avec le lien vers le quiz, en incluant l'ID du quiz
            $quizContent .= "<a href='listpage.php?quiz_id=$quiz_id'>
                                <div class='quiz-item'>
                                    <div class='quiz-name'>$name</div>
                                    <div class='quiz-created-at'>Créé le: &nbsp;<strong>$created_at</strong></div>
                                    <div class='quiz-creator-name'>Par: &nbsp;<strong>$creator_name</strong></div>
                                    <div class='quiz-difficulty-name'><strong>$difficulty_name</strong></div>
                                    <div class='quiz-difficulty-img'>
                                        <img src='serve_img.php?id=$difficulty_id' alt='$difficulty_name'>
                                    </div>
                                </div>
                            </a>";
        }
    } else {
        $quizContent .= "<p>Aucun quiz trouvé avec l'ID spécifié.</p>";
    }

} else {
    $quizContent .= "<p>Aucun ID de tag spécifié.</p>";
}

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
                <div id="section_quizzpage">
                    <?php echo $quizContent; ?>
                </div>
            </div>
        </section>
    </main>
    <footer>

    </footer>
</body>

</html>
