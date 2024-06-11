<?php


class Database{

    private $host = 'localhost';
    private $db_name  = 'quiznight';
    private $username = 'root';
    private $password = '';
    private $conn;

    // Méthode pour établir la connexion
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Erreur de connexion: ' . $e->getMessage();
        }

        return $this->conn;
    }

    // Méthode pour fermer la connexion
    public function disconnect() {
        $this->conn = null;
    }
}

?>
