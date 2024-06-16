<?php
require_once '../../Database.php';

// Démarrer la session
session_start();

$database = new Database();

// Établir la connexion
$conn = $database->connect();   

$quizContent = ""; // Variable pour stocker les quiz
$questionContent = ""; // Variable pour stocker les questions

$totalQuestions = 0; // Variable pour compter le nombre total de questions

// Vérifier si quizz_id est présent dans l'URL
if (isset($_GET['quizz_id'])) {
    $quiz_id = intval($_GET['quizz_id']);

    // Requête pour récupérer les détails du quiz
    $sql_quiz = "SELECT quizz.*, 
                        user.name as creator_name, 
                        difficulty.name as difficulty_name, 
                        tag.name as tag_name
                 FROM quizz
                 JOIN user ON quizz.id_user = user.id
                 JOIN difficulty ON quizz.id_difficulty = difficulty.id
                 JOIN tag ON quizz.id_tag = tag.id
                 WHERE quizz.id = :quiz_id";

    $stmt_quiz = $conn->prepare($sql_quiz);
    $stmt_quiz->bindParam(':quiz_id', $quiz_id, PDO::PARAM_INT);
    $stmt_quiz->execute();

    if ($stmt_quiz->rowCount() > 0) {
        $quizRow = $stmt_quiz->fetch(PDO::FETCH_ASSOC);

        $quizName = htmlspecialchars($quizRow['name']);
        $quizCreatedAt = htmlspecialchars($quizRow['created_at']);
        $creatorName = htmlspecialchars($quizRow['creator_name']); 
        $difficultyName = htmlspecialchars($quizRow['difficulty_name']); 
        $tagName = htmlspecialchars($quizRow['tag_name']);
    
        // Requête pour récupérer les questions du quiz
        $sql_questions = "SELECT * FROM question WHERE id_quizz = :quiz_id";
        $stmt_questions = $conn->prepare($sql_questions);
        $stmt_questions->bindParam(':quiz_id', $quiz_id, PDO::PARAM_INT);
        $stmt_questions->execute();

        $totalQuestions = $stmt_questions->rowCount(); // Compter le nombre total de questions

        if ($totalQuestions > 0) {
            $questionIndex = 1;

            while ($questionRow = $stmt_questions->fetch(PDO::FETCH_ASSOC)) {
                $questionName = htmlspecialchars($questionRow['name']);
                $questionId = $questionRow['id'];
                $questionContent .= "<div class='question' id='question-$questionIndex' style='display: " . ($questionIndex === 1 ? 'block' : 'none') . ";'>";
                $questionContent .= "<div class='question-name'>$questionName</div>";

                // Requête pour récupérer les réponses à la question courante
                $sql_responses = "SELECT * FROM response WHERE id_question = :question_id";
                $stmt_responses = $conn->prepare($sql_responses);
                $stmt_responses->bindParam(':question_id', $questionId, PDO::PARAM_INT);
                $stmt_responses->execute();

                // Construction du contenu HTML pour les réponses
                $responseContent = "<div class='responses'>";
                while ($responseRow = $stmt_responses->fetch(PDO::FETCH_ASSOC)) {
                    $responseName = htmlspecialchars($responseRow['name']);
                    $isSolution = intval($responseRow['solution']) === 1 ? 'correct' : 'incorrect';
                    $responseContent .= "<div class='response $isSolution'>$responseName</div>";
                }
                $responseContent .= "</div>";

                // Ajouter le contenu des réponses à la question courante
                $questionContent .= $responseContent;
                $questionContent .= "</div>";

                $questionIndex++;
            }
        } else {
            $questionContent .= "<p>Aucune question trouvée pour ce quiz.</p>";
        }   

        // Construction du contenu HTML pour afficher le quiz
        $quizContent .= "
            <div id='quiz-item1'>
                <div class='div-quiz-item1'>
                    <div class='quiz-created-at'>Créé le: &nbsp;<strong>$quizCreatedAt</strong></div>
                    <div class='quiz-creator-name'>Par: &nbsp;<strong>$creatorName</strong></div>
                </div>
                <div class='div-quiz-item2'>
                    <div class='quiz-difficulty-img'>
                        <img src='serve_img.php?id={$quizRow['id_difficulty']}' alt='$difficultyName'>
                    </div>
                </div>
            </div>
            <div id='quiz-item2'>
                <h1 class='quiz-name'>$quizName</h1>
                <h4 class='quiz-tag-name'><strong>$tagName</strong></h4>
            </div>";
    } else {
        $quizContent .= "<p>Aucun quiz trouvé avec l'ID spécifié.</p>";
    }

} else {
    $quizContent .= "<p>Aucun ID de quiz spécifié.</p>";
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
    <link rel="stylesheet" href="../styles/quizzpage.css">
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
                    <div id="questions-container">
                        <div class="question-counter">
                            <p>Questions:&nbsp;</p>
                            <?php if ($totalQuestions > 0): ?>
                                <span id="current-question-number">1</span> / <span id="total-questions"><?php echo $totalQuestions; ?></span>
                            <?php endif; ?>
                        </div>
                        <?php echo $questionContent; ?>
                        <?php if ($totalQuestions > 0): ?>
                            <button id="next-question-btn" onclick="handleButtonClick()">Réponse</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer>

    </footer>
    <script>
    var currentQuestionIndex = 0;
    var totalQuestions = <?php echo $totalQuestions; ?>;
    var questions = document.querySelectorAll('.question');
    var nextQuestionBtn = document.getElementById('next-question-btn');
    var currentQuestionNumberSpan = document.getElementById('current-question-number');
    var showCorrectAnswer = false; // Suivre si on doit montrer la réponse correcte ou la question suivante

    function handleButtonClick() {
        if (showCorrectAnswer) {
            // Afficher la question suivante
            if (currentQuestionIndex < questions.length - 1) {
                questions[currentQuestionIndex].style.display = 'none'; // Masquer la question actuelle
                currentQuestionIndex++; // Passer à la question suivante
                questions[currentQuestionIndex].style.display = 'block'; // Afficher la question suivante

                // Mettre à jour le compteur de questions
                currentQuestionNumberSpan.textContent = currentQuestionIndex + 1;

                // Réinitialiser pour afficher toutes les réponses d'abord
                var responsesContainer = questions[currentQuestionIndex].querySelector('.responses');
                var responses = questions[currentQuestionIndex].querySelectorAll('.response');
                responses.forEach(function(response) {
                    response.style.display = 'block'; // Afficher toutes les réponses
                });
                responsesContainer.classList.remove('flex-center'); // Supprimer la classe flex-center

                showCorrectAnswer = false; // Le prochain clic montrera la réponse correcte
                nextQuestionBtn.textContent = 'Réponse'; // Mettre à jour le texte du bouton en "Réponse"
            } else {
                // Toutes les questions sont terminées
                nextQuestionBtn.style.display = 'none'; // Masquer le bouton
                var returnLink = document.createElement('a');
                returnLink.setAttribute('href', 'javascript:history.back()');
                returnLink.textContent = 'Retourner à la page précédente';
                returnLink.classList.add('link-page-quizs'); // Ajouter la classe link-page-quizs
                document.getElementById('questions-container').appendChild(returnLink);
            }
        } else {
            // Afficher uniquement la réponse correcte
            var responsesContainer = questions[currentQuestionIndex].querySelector('.responses');
            var responses = questions[currentQuestionIndex].querySelectorAll('.response');
            responses.forEach(function(response) {
                if (!response.classList.contains('correct')) {
                    response.style.display = 'none'; // Masquer les réponses incorrectes
                }
            });
            responsesContainer.classList.add('flex-center'); // Ajouter la classe flex-center pour centrer la réponse correcte

            showCorrectAnswer = true; // Le prochain clic montrera la question suivante
            nextQuestionBtn.textContent = 'Question suivante'; // Mettre à jour le texte du bouton en "Question suivante"

            // Vérifier si c'est la dernière question et masquer le bouton après la réponse
            if (currentQuestionIndex === questions.length - 1) {
                nextQuestionBtn.style.display = 'none'; // Masquer le bouton
                var returnLink = document.createElement('a');
                returnLink.setAttribute('href', 'javascript:history.back()');
                returnLink.textContent = 'Revenir aux quizs';
                returnLink.classList.add('link-page-quizs'); // Ajouter la classe link-page-quizs
                document.getElementById('questions-container').appendChild(returnLink);
            }
        }
    }
</script>


</body>

</html>
