<?php
require_once '../../Database.php';

$database = new Database();

// Établir la connexion
$conn = $database->connect();

$tagContent = ""; // Variable pour stocker les détails du tag
$quizContent = ""; // Variable pour stocker les quizs

if (isset($_GET['tag_id'])) {
    $tag_id = intval($_GET['tag_id']);

    // Requête pour récupérer le nom du tag
    $tagSql = "SELECT * FROM tag WHERE id = :tag_id";
    $tagStmt = $conn->prepare($tagSql);
    $tagStmt->bindParam(':tag_id', $tag_id, PDO::PARAM_INT);
    $tagStmt->execute();
  
    if ($tagStmt->rowCount() > 0) {
        $tagRow = $tagStmt->fetch(PDO::FETCH_ASSOC);
        $tagContent .= "<div class='tag-name'>" . htmlspecialchars($tagRow['name']) . "</div>";
    } else {
        $tagContent = "<div class='tag-name'>Tag non trouvé.</div>";
    }

    // Requête pour récupérer les quizs associés au tag avec le nom du créateur et de la difficulté
    $sql = "SELECT quizz.name, quizz.created_at, user.name as creator_name, difficulty.name as difficulty_name, difficulty.id as difficulty_id 
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
            $name = htmlspecialchars($row['name']);
            $created_at = htmlspecialchars($row['created_at']);
            $creator_name = htmlspecialchars($row['creator_name']);
            $difficulty_name = htmlspecialchars($row['difficulty_name']);
            $difficulty_id = htmlspecialchars($row['difficulty_id']);

            // Créer une chaîne formatée
            $quizContent .= "<div class='quiz-item'>
                                <div class='quiz-name'>$name</div>
                                <div class='quiz-created-at'>Créé le: $created_at</div>
                                <div class='quiz-creator-name'>Par: $creator_name</div>
                                <div class='quiz-difficulty-name'>$difficulty_name</div>
                                <div class='quiz-difficulty-img'>
                                    <img src='serve_img.php?id=$difficulty_id' alt='$difficulty_name'>
                                </div>
                             </div>";
        }
    } else {
        $quizContent .= "<div class='quiz-item'><p id='error-msg'>Aucun quizz n'a été trouvé...</p></div>";
    }
} else {
    $tagContent = "<div class='tag-item'>Aucun ID de tag spécifié.</div>";
    $quizContent = "<div class='quiz-item'>Aucun ID de tag spécifié.</div>";
}

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
    <link rel="stylesheet" href="../styles/listpage.css">
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
                <div id="listpage_section">
                    <?php echo $tagContent; ?>
                    <div id="list_quiz">
                        <?php echo $quizContent; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer>

    </footer>
</body>

</html>
