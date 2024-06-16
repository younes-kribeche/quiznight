<?php

require_once "../../Database.php";

class User{
    //Définition des variables
    private $conn;

    //Définition du constructeur
    public function __construct(){
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function read(){
        $sql = 'SELECT * FROM user';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByName($username){
        $sql = 'SELECT * FROM user WHERE name = :username';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':username' => $username]);
        return $stmt->fetchColumn();
    }

}

?>