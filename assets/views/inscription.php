<?php
    session_start();

    require_once '../../Database.php';

    $error_message = null;

    $database = new Database();

    // Établir la connexion
    $conn = $database->connect();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $username = $_POST['username'];

        // Vérifier si la connexion est établie
        if (isset($email, $password, $username) && $conn) {
            // Vérifier si le mot de passe répond aux exigences
            if (strlen($password) < 8 || !preg_match("#[0-9]+#", $password) || !preg_match("#[A-Z]+#", $password) || !preg_match("#[a-z]+#", $password) || !preg_match("/[!@#$%^&*()\-_=+]/", $password)) {
                $error_message = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
            } else {
                // Hachage du mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Préparer la requête d'insertion avec des paramètres de substitution
                $query = "INSERT INTO user (email, password, name) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);

                // Exécuter la requête en fournissant les valeurs directement
                $stmt->execute([$email, $hashed_password, $username]);

                // Rediriger l'utilisateur vers une page de confirmation
                header("Location: connexion.php");
                exit;
            }
        } else {
            echo 'Échec de la connexion.';
        }
    }

    // Fermer la connexion
    $database->disconnect();
?>


<!-----------------------------HTML------------------------------------>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/global.css">
    <link rel="stylesheet" href="../styles/inscription.css">
    <script src="../script/script.js" defer></script>
    <title>Quiz Night</title>
</head>

<body>
    <?php
        include('header.php');
    ?>
    <main>
        <section id="inscription">
            <div class="section">
                <form action="inscription.php" method="POST">
                    <h1>Inscrivez Vous!</h1>
                    <input type="email" placeholder="Adresse e-mail" name="email" required>
                    <input type="password" placeholder="Mot de passe" name="password" maxlength="30" required>
                    <?php if($error_message != null){echo ('<p style="color: red; width: 80%">'.$error_message.'</p>');} ?>
                    <input type="text" placeholder="Nom d'utilisateur" name="username" minlength="6" maxlength="15" required>
                    <button type="submit">M'inscrire</button>
                </form>
            </div>
        </section>
    </main>
    <footer>

    </footer>
</body>

</html>
