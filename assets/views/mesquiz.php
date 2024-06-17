<?php
    session_start();

    require_once '../../Database.php';
    require_once('../Class/Quiz.php');
    require_once('../Class/User.php');

    $quiz = new Quiz();
    $user = new User();
    $userId = null;
    $userQuiz = null;

    if (isset($_SESSION['user'])){
        $username = $user->findByName($_SESSION['user']);
        $userId = $username;
        $userQuiz = $quiz->getQuiz($userId);
    }
?>

<!-----------------------------HTML------------------------------------>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../img/favicon.png">
    <script src="../script/script.js" defer></script>
    <link rel="stylesheet" type="text/css" href="../styles/global.css">
    <link rel="stylesheet" type="text/css" href="../styles/mesquiz.css">
    <title>Mes Quiz | QuizNight</title>
</head>
<body>
    <?php
        include('./header.php');
    ?>
    <main>
        <section id="mesquiz">
            <div class="section">
                <div class="mesquiz">
                    <?php if(!isset($_SESSION['user'])): ?>
                        <h1>Vous devez être connecté pour voir vos Quiz!</h1>
                        <button class="connexionButton"><a href="./connexion.php">Me connecter</a></button>
                    <?php elseif(isset($_SESSION['user']) && $userQuiz == null): ?>
                        <h2>Vous n’avez aucun quiz pour l’instant! <br>Qu’attendez vous pour en créer un? <br>👇</h2>
                        <button class="addButton"><a href="newQuiz.php"><img src="../img/addButton.png" alt="addButton"></a></button>
                    <?php else: ?>
                        <?php
                        
                            foreach($userQuiz as $newquiz){
                                $quizId = $quiz->getQuizIdByName($newquiz['name']);
                                $category = $quiz->getCategory($newquiz['id_tag']);
                                $difficulty = $quiz->getDifficultyImg($newquiz['id_difficulty']);
                                if ($difficulty) {
                                    $base64Image = base64_encode($difficulty);
                                    $difficultyName = $quiz->getDifficultyName($newquiz['id_difficulty']);
                                    $imageSrc = 'data:image/jpeg;base64,' . $base64Image; 
                                } else {
                                    $imageSrc = null;
                                }
                                echo '
                                <a href="quizzpage.php?quizz_id='.$quizId.'">
                                <div class="quiz-item">
                                    <div class="quiz-name">'.$newquiz['name'].'</div>
                                    <div class="quiz-created-at">Créé le: &nbsp;<strong>'.$newquiz['created_at'].'</strong></div>
                                    <div class="quiz-creator-name">Catégorie: &nbsp;<strong>'.$category.'</strong></div>
                                    <div class="quiz-difficulty-name"><strong>'.$difficultyName.'</strong></div>
                                    <div class="quiz-difficulty-img">
                                        <img src="'.$imageSrc.'" alt="$difficulty_name">
                                    </div>
                                </div>
                                </a>
                                ';
                            }
                        ?>
                        <div>
                            <button class="addButton_green"><a href="./newQuiz.php"><img src="../img/addButton_green.png" alt="addButton"></a></button>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
