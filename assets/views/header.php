<?php

    function getStatus() {
        if (!isset($_SESSION['user'])) {
            return 'Connexion';
        } else {
            return 'Déconnexion';
        }
    }
    
    function disconnect() {
        session_destroy();
        header("Location: connexion.php"); // Redirection après déconnexion
        exit();
    }
    
    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        disconnect();
    } 
    $status = getStatus();

?>

<header>
    <nav class='burgerNav'>
        <a href="homepage.php"><img class="logo" src="../img/logo.png" alt="logo"></a>
        <img src="../img/burgerOpen.png" alt="burgerIcon" onclick="changeSrc(); classChange('hidemenu', 'showmenu', 'menu'); classChange('burgerButtonOpen', 'burgerButtonClose', 'open-nav'); classChange('burgerPop', 'burgerPop2', 'open-nav')" class="burgerButtonClose burgerPop" id="open-nav"/>
        <ul class='hidemenu' id="menu">
            <li><a href="mesquiz.php">Mes Quiz</a></li>
            <li><a href="">Paramètres</a></li>
            <li>
                <?php if ($status == 'Connexion'): ?>
                    <a href="connexion.php"><?php echo $status; ?></a>
                <?php else: ?>
                    <a href="?action=logout"><?php echo $status; ?></a>
                <?php endif; ?>
            </li>
        </ul>
    </nav>
</header>