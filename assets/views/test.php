<?php
    session_start();

    require_once('../Class/Quiz.php');

    $quiz = new Quiz();

    $quiz->addResponse('Bonjour', 1, 12);
?>