<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');

    if ($username !== '') {
        $_SESSION['username'] = $username;
        $fichier = 'utilisateurs.txt';
        $liste = file_exists($fichier) ? file($fichier, FILE_IGNORE_NEW_LINES) : [];

        $utilisateur_existe = false;
        foreach ($liste as $ligne) {
            if (strpos($ligne, $username . '|') === 0 || trim($ligne) === $username) {
                $utilisateur_existe = true;
                break;
            }
        }

        if (!$utilisateur_existe) {
            // Initialisation complète : score=0, progression=0, exclues=""
            $ligne_init = "$username|0|0|\n";
            file_put_contents($fichier, $ligne_init, FILE_APPEND);
        }

        header("Location: hsk.php");
        exit;
    } else {
        $erreur = "Veuillez entrer un nom d'utilisateur.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="icon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Connexion</h1>
        <?php if (!empty($erreur)) echo "<p class='erreur'>$erreur</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
