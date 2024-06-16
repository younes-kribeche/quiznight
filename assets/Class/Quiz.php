<?php

require_once('../../Database.php');
require_once('User.php');

class Quiz {
    // Définition des variables
    private $conn;

    // Définition du constructeur
    public function __construct() {
        $db = new Database;
        $this->conn = $db->connect();
    } 

    public function createQuiz(string $title, int $userid, int $idTag, int $idDifficulty) {
        $query = "INSERT INTO quizz (name, id_user, id_tag, id_difficulty) VALUES (:title, :userid, :idTag, :idDifficulty)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':title' => $title, ':userid' => $userid, ':idTag' => $idTag, ':idDifficulty' => $idDifficulty]);
    }

    public function getQuiz(int $userId){
        $query = "SELECT * FROM quizz where id_user = :userId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuestions(int $quizId){
        $query = "SELECT * FROM question WHERE id_quizz = :quizId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':quizId' => $quizId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuestionByName(string $question){
        $query = "SELECT id FROM question WHERE name = :question";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':question' => $question]);
        return $stmt->fetchColumn();
    }

    public function addQuestion(string $question, int $quizId){
        $query = "INSERT INTO question (name, id_quizz) VALUES (:question, :quizId)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':question' => $question,':quizId' => $quizId]);
    }

    public function addResponse(string $response, int $solution, int $id_question){
        $query = "INSERT INTO response (name, solution, id_question) VALUES (:response, :solution, :id_question)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':response' => $response, ':solution' => $solution, ':id_question' => $id_question]);
    }

    public function getCategory(int $categoryId){
        $query = "SELECT name FROM tag WHERE id = :categoryId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':categoryId' => $categoryId]);
        return $stmt->fetchColumn();
    }

    public function getDifficulty(int $difficultyId){
        $query = "SELECT img FROM difficulty WHERE id = :difficultyId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':difficultyId' => $difficultyId]);
        return $stmt->fetchColumn();
    }
}

?>
