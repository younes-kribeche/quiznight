<?php
require_once '../../Database.php';

if (isset($_GET['id'])) {
    $database = new Database();
    $conn = $database->connect();
    
    $id = intval($_GET['id']);

    // Requête pour récupérer l'image BLOB depuis la base de données
    $sql = "SELECT img FROM difficulty WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $imgData = $row['img'];

        // Définir l'en-tête pour indiquer qu'il s'agit d'une image
        header("Content-Type: image/png"); // Vous pouvez changer le type de contenu selon le type d'image (image/png, etc.)
        echo $imgData;
    } else {
        echo "Image not found.";
    }

    // Fermer la connexion
    $database->disconnect();
}
?>
