<?php
require_once '../../Database.php';

session_start();
$database = new Database();

// Établir la connexion
$conn = $database->connect();

// Définir la base de l'URL et les paramètres
$listPage = 'http://localhost/quiznight/assets/views/quizzpage.php';
$param = 'quizz_id';

$tagContent = ""; // Variable pour stocker les détails du tag
$quizContent = ""; // Variable pour stocker les quiz

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

    // Récupérer les valeurs de filtre et de tri depuis le formulaire
    $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
    $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

    // Requête pour récupérer les quiz associés au tag avec le nom du créateur et de la difficulté
    $sql = "SELECT quizz.id, quizz.name, quizz.created_at, user.name as creator_name, difficulty.name as difficulty_name, difficulty.id as difficulty_id 
            FROM quizz 
            JOIN user ON quizz.id_user = user.id 
            JOIN difficulty ON quizz.id_difficulty = difficulty.id
            WHERE quizz.id_tag = :tag_id";

    // Ajout de la clause de filtrage si nécessaire
    if (!empty($filter)) {
        $sql .= " AND (quizz.name LIKE :filter OR user.name LIKE :filter)";
    }

    // Ajout de la clause de tri
    $sql .= " ORDER BY ";
    switch ($sort) {
        case 'name':
            $sql .= "quizz.name";
            break;
        case 'created_at':
            $sql .= "quizz.created_at";
            break;
        case 'creator_name':
            $sql .= "user.name";
            break;
        case 'difficulty_id':
            $sql .= "difficulty.id";
            break;
        default:
            $sql .= "quizz.name";
            break;
    }
    $sql .= " $order";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':tag_id', $tag_id, PDO::PARAM_INT);

    if (!empty($filter)) {
        $filterParam = '%' . $filter . '%';
        $stmt->bindParam(':filter', $filterParam, PDO::PARAM_STR);
    }

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
            $quizContent .= "<a href='$listPage?$param=$quiz_id'>
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
        $quizContent .= "<div class='quiz-item'><p id='error-msg'>Aucun quizz n'a été trouvé...</p></div>";
    }
} else {
    $tagContent = "<div class='tag-item'>Aucun ID de tag spécifié.</div>";
    $quizContent = "<div class='quiz-item'>Aucun ID de tag spécifié.</div>";
}

// Gestion de l'affichage des détails du quiz sélectionné
if (isset($_GET['quizz_id'])) {
    $quiz_id = intval($_GET['quizz_id']);

    // Requête pour récupérer les détails spécifiques du quiz
    $quizDetailsSql = "SELECT * FROM quizz WHERE id = :quiz_id";
    $quizDetailsStmt = $conn->prepare($quizDetailsSql);
    $quizDetailsStmt->bindParam(':quiz_id', $quiz_id, PDO::PARAM_INT);
    $quizDetailsStmt->execute();

    if ($quizDetailsStmt->rowCount() > 0) {
        $quizRow = $quizDetailsStmt->fetch(PDO::FETCH_ASSOC);
        $quizName = htmlspecialchars($quizRow['name']);
        $quizCreatedAt = htmlspecialchars($quizRow['created_at']);
        $quizCreatorId = htmlspecialchars($quizRow['id_user']); 
        $quizDifficultyId = htmlspecialchars($quizRow['id_difficulty']); 



    } else {
        echo "Aucun quiz trouvé avec l'ID spécifié.";
    }
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
    <script src="../script/script.js" defer></script>
    <title>Quiz Night</title>
</head>

<body>
    <?php
        include('header.php');
    ?>
    <main>
        <section>
            <div class="section">
                <div id="listpage_section">
                    <?php echo $tagContent; ?>
                    <div id="filter-sort-section">
                        <form method="GET" action="">
                            <input type="hidden" name="tag_id" value="<?php echo htmlspecialchars($tag_id); ?>">
                            <label for="filter">Rechercher :</label>
                            <input class="filters" type="text" id="filter" name="filter"
                                placeholder="Nom du quiz ou créateur"
                                value="<?php echo isset($_GET['filter']) ? htmlspecialchars($_GET['filter']) : ''; ?>">
                            &nbsp;&nbsp;&nbsp;
                            <label for="sort">Trier par :</label>
                            <select id="sort" name="sort">
                                <option value="name"
                                    <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name') ? 'selected' : ''; ?>>Nom
                                </option>
                                <option value="created_at"
                                    <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'created_at') ? 'selected' : ''; ?>>
                                    Date de création</option>
                                <option value="creator_name"
                                    <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'creator_name') ? 'selected' : ''; ?>>
                                    Créateur</option>
                                <option value="difficulty_id"
                                    <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'difficulty_id') ? 'selected' : ''; ?>>
                                    Difficulté</option>
                            </select>
                            &nbsp;&nbsp;&nbsp;
                            <label for="order">Ordre :</label>
                            <select id="order" name="order">
                                <option value="ASC"
                                    <?php echo (isset($_GET['order']) && $_GET['order'] == 'ASC') ? 'selected' : ''; ?>>
                                    Ascendant</option>
                                <option value="DESC"
                                    <?php echo (isset($_GET['order']) && $_GET['order'] == 'DESC') ? 'selected' : ''; ?>>
                                    Descendant</option>
                            </select>
                            &nbsp;&nbsp;&nbsp;
                            <button id="button_filter" type="submit">Appliquer</button>
                        </form>
                    </div>
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
