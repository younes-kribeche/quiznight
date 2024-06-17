<?php

session_start();

require_once('../Class/User.php');
require_once('../Class/Quiz.php');

// Déclaration globale
global $user, $quiz;
$user = new User();
$quiz = new Quiz();

function setTitle(){
    if(!empty($_POST['title'])){
        $title = htmlspecialchars($_POST['title']);
        $_SESSION['title'] = $title;
    }
}

function setQuestion() {
    if (!isset($_SESSION['qid'])) {
        $_SESSION['qid'] = 0;
    }

    if (!isset($_SESSION['allQuestions'])) {
        $_SESSION['allQuestions'] = [];
    }

    if (!empty($_POST['question'])) {
        $qid = $_SESSION['qid'];
        $question = [
            'value' => htmlspecialchars($_POST['question']),
            'questionId' => $qid
        ];

        $_SESSION['qid']++; // Incrémente qid pour la prochaine question

        $_SESSION['allQuestions'][] = $question; // Ajoute la question à la liste des questions

        $_SESSION['question'] = $_SESSION['allQuestions']; // Optionnel : met à jour 'question' avec la liste complète
    }
}


function setAnswers() {
    if (!empty($_POST['reponse1']) && !empty($_POST['reponse2']) && !empty($_POST['reponse3']) && !empty($_POST['reponse4'])) {
        $reponse1 = htmlspecialchars($_POST['reponse1']);
        $reponse2 = htmlspecialchars($_POST['reponse2']);
        $reponse3 = htmlspecialchars($_POST['reponse3']);
        $reponse4 = htmlspecialchars($_POST['reponse4']);

        $qid = $_SESSION['qid'] - 1;
        
        $reponsesTab = [
            ['value' => $reponse1, 'is_correct' => 1, 'qid' => $qid],  // Première réponse vraie
            ['value' => $reponse2, 'is_correct' => 0, 'qid' => $qid],  // Deuxième réponse fausse
            ['value' => $reponse3, 'is_correct' => 0, 'qid' => $qid],  // Troisième réponse fausse
            ['value' => $reponse4, 'is_correct' => 0, 'qid' => $qid]   // Quatrième réponse fausse
        ];

        $_SESSION['reponses'] = $reponsesTab;

        if (!isset($_SESSION['allReponses'])) {
            $_SESSION['allReponses'] = [];
        }

        // Ajout des réponses pour la question courante au tableau des réponses
        $_SESSION['allReponses'][] = $reponsesTab;
    }
}


//Fonctionnement de l'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['title'])) {
        setTitle();
    } elseif (isset($_POST['question'])) {
        setQuestion();
    } elseif (!empty($_POST['reponse1']) && !empty($_POST['reponse2']) && !empty($_POST['reponse3']) && !empty($_POST['reponse4'])) {
        setAnswers();
    } elseif (isset($_POST['new_question'])) {
        unset($_SESSION['question']);
        unset($_SESSION['reponses']);
    }
}

function setQuiz(){
    if (isset($_POST["finishedQuiz"])){
        global $user;
        global $quiz;
        //---------------------
        //----------------------Envoyoyer le Quiz en bdd
        //---------------------
    
        //Variables
        $title = $_SESSION['title'];
        $userId = $user->findByName($_SESSION['user']);
        $category = $_POST['category'];
        $difficulty = $_POST['difficulty'];
        
        //Exécution
        $quiz->createQuiz($title, $userId, $category, $difficulty);

        //---------------------
        //----------------------Envoyoyer les questions en bdd
        //---------------------

        //Variables
        $questions = $_SESSION['allQuestions'];
        $quizId = null;

        //On doit d'abord récupérer les quiz et extraire le quizId avec le nom
        $allQuiz = $quiz->getQuiz($userId);
        foreach($allQuiz as $thisone){
            if ($thisone['name'] == $title){
                $quizId = $thisone['id'];
            }
        }

        //Exécution
        foreach($questions as $thisone){
            $quiz->addQuestion($thisone['value'], $quizId);
        }

        //---------------------
        //----------------------Envoyoyer les réponses en bdd
        //---------------------

        $questionsTab = $_SESSION['allQuestions'];
        $questionsBDD = $quiz->getQuestions($quizId);
        $i = 0;
        
        $responsesTab = $_SESSION['allReponses'];
        foreach($responsesTab as $ensemble){
            foreach($ensemble as $response){
                foreach($questionsTab as $question){
                    if ($question['questionId'] == $response['qid']){
                        //Variables
                        $reponse = $response['value'];
                        $solution = $response['is_correct'];
                        $questionId = $quiz->getQuestionByName($question['value']);

                        $quiz->addResponse($reponse, $solution, $questionId);
                    }
                }
            }
        }

        //Enfin on se dirige vers le end
        $_SESSION['finalQuiz'] = 'test';
        echo '<script type="text/javascript">
        window.location.reload();
        </script>';
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../script/script.js" async></script>
    <link rel="stylesheet" type="text/css" href="../styles/global.css">
    <link rel="stylesheet" type="text/css" href="../styles/newQuiz.css">
    <title>Créer un Quiz | QuizNight</title>
</head>
<body>
    <?php include('./header.php'); ?>

    <main>
        <section id="newQuiz">
            <div class="section">
                <!------------------------SELECTION DU TITRE----------------------------->
                <?php if(!isset($_SESSION['title'])): ?>
                    <h1>Choisissez un titre pour votre Quiz!</h1>
                    <form action="newQuiz.php" method="POST">
                        <input type="text" name="title" required>
                        <button id="nextButton" type="submit"><img src="../img/next.png" alt="flèche_suivant"></button>
                    </form>
                <!------------------------SELECTION DE LA QUESTION----------------------------->
                <?php elseif(!isset($_SESSION['question'])): ?>
                    <h1>Saisissez une question</h1>
                    <form action="newQuiz.php" method="POST">
                        <input type="text" name="question" required>
                        <button id="nextButton" type="submit"><img src="../img/next.png" alt="flèche_suivant"></button>
                    </form>
                <!------------------------SELECTION DES REPONSES----------------------------->
                <?php elseif(!isset($_SESSION['reponses'])): ?>
                    <h1>A présent créez 4 réponses: <br>- 1 vraie<br>- 3 fausses</h1>
                    <form action="newQuiz.php" method="POST">
                        <input type="text" name="reponse1" class="trueAnswer" required>
                        <input type="text" name="reponse2" class="falseAnswer" required>
                        <input type="text" name="reponse3" class="falseAnswer" required>
                        <input type="text" name="reponse4" class="falseAnswer" required>
                        <button id="nextButton" type="submit"><img src="../img/next.png" alt="flèche_suivant"></button>
                    </form>
                <!------------------------FINALISATION DU QUIZ----------------------------->
                <?php elseif(!isset($_SESSION['finalQuiz'])): ?>
                    <h1>Finalisez votre Quiz!</h1>
                    <form action="newQuiz.php" method="POST">
                        <div class="quizHeader">
                            <h1><?php echo $_SESSION['title']; ?></h1>
                                <select name="difficulty" id="difficulty">
                                    <option value="1">Facile</option>
                                    <option value="2">Normal</option>
                                    <option value="3">Difficile</option>
                                </select>

                                <select name="category" id="category">
                                    <option value="1">Géographie</option>
                                    <option value="2">Divertissement</option>
                                    <option value="3">Histoire</option>
                                    <option value="4">Art et Littérature</option>
                                    <option value="5">Science et Nature</option>
                                    <option value="6">Sports et Loisirs</option>
                                </select>
                        </div>
                        <div class="quizBody">
                            <h1>Questions</h1>
                                <?php
                                    $questions = $_SESSION['allQuestions'];
                                    $i = 0;
                                    foreach($questions as $question){
                                        $i++;
                                        $value = $question['value'];
                                        echo '<div class="questionRow"><h2>'.$i.'.</h2><h2 class="question">'.$value.'</h2></div>';
                                    } 
                                ?>
                        </div>
                        <?php setQuiz(); ?>
                        <input type="hidden" name="finishedQuiz" value="1">
                        <button type="submit" class="finishButton">Terminer</button>
                    </form>
                    <form action="newQuiz.php" method="POST">
                        <input type="hidden" name="new_question" value="1">
                        <button type="submit" class="addQuestionButton">Ajouter une question</button>
                    </form>
                <?php else: ?>
                    <h1>Félicitations! Votre Quiz a été créé avec succès!</h1>
                    <h2>Vous allez être redirigés vers l'accueil dans un instant.</h2>
                    <script>
                        function delayedRedirection(link){
                            setTimeout(function(){
                                window.location.href = link;
                            }, 3000);
                        }
                        delayedRedirection('./mesquiz.php');
                    </script>
                    <?php 
                        unset($_SESSION['title']);
                        unset($_SESSION['question']);
                        unset($_SESSION['allQuestions']);
                        unset($_SESSION['qid']);
                        unset($_SESSION['reponses']);
                        unset($_SESSION['allReponses']);
                        unset($_SESSION['finalQuiz']);
                    ?>
                <?php endif ?>
            </div>
        </section>
    </main>
</body>
</html>
