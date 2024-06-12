<?php

require_once "../../Database.php";

class User{
    //Définition des variables
    private $conn;

    //Définition du constructeur
    public function __construct(Database $db){
        $this->conn = $db->connect();
    }

    public function read(){
        $sql = 'SELECT * FROM user';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>