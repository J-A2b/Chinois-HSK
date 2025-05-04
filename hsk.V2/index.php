<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');

    if ($username !== '') {
        $_SESSION['username'] = $username;
        $fichier1 = 'hsk1.txt';
        $fichier2 = 'hsk2.txt';
        $fichier3 = 'hsk3.txt';
        $fichier = 'utilisateurs.txt';
        $liste = file_exists($fichier) ? file($fichier, FILE_IGNORE_NEW_LINES) : [];
        $utilisateur_existe = false;
        foreach ($liste as $ligne) {
            if (strpos($ligne, $username . '|') === 0 || trim($ligne) === $username) {
                $utilisateur_existe = true;
                break;
            }
        }
        $liste = file_exists($fichier1) ? file($fichier1, FILE_IGNORE_NEW_LINES) : [];
        foreach ($liste as $ligne) {
            if (strpos($ligne, $username . '|') === 0 || trim($ligne) === $username) {
                $utilisateur_existe = true;
                break;
            }
        }
        $liste = file_exists($fichier2) ? file($fichier2, FILE_IGNORE_NEW_LINES) : [];
        foreach ($liste as $ligne) {
            if (strpos($ligne, $username . '|') === 0 || trim($ligne) === $username) {
                $utilisateur_existe = true;
                break;
            }
        }
        $liste = file_exists($fichier3) ? file($fichier3, FILE_IGNORE_NEW_LINES) : [];
        foreach ($liste as $ligne) {
            if (strpos($ligne, $username . '|') === 0 || trim($ligne) === $username) {
                $utilisateur_existe = true;
                break;
            }
        }
        if (!$utilisateur_existe) {
            // Initialisation complète : score=0, progression=0, exclues=""
            $ligne_init = "$username|0|0|\n\n";
            file_put_contents($fichier, $ligne_init, FILE_APPEND);
            file_put_contents($fichier1, $ligne_init, FILE_APPEND);
            file_put_contents($fichier2, $ligne_init, FILE_APPEND);
            file_put_contents($fichier3, $ligne_init, FILE_APPEND);
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
    <title>Entrainement HSK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="icon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Entrainement HSK </h1>
        <?php if (!empty($erreur)) echo "<p class='erreur'>$erreur</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
