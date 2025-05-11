<?php
session_start();

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username !== '' && $password !== '') {
        $fichier_mdp = 'mdp.txt';
        $mdp_lignes = file_exists($fichier_mdp) ? file($fichier_mdp, FILE_IGNORE_NEW_LINES) : [];
        $mdp_trouve = false;
        $mdp_valide = false;

        foreach ($mdp_lignes as $ligne) {
            list($nom, $hash) = explode('|', $ligne);
            if ($nom === $username) {
                $mdp_trouve = true;
                if (password_verify($password, $hash)) {
                    $mdp_valide = true;
                }
                break;
            }
        }

        if (!$mdp_trouve) {
            // Nouvel utilisateur (au sens du fichier mdp uniquement)
            $hash = password_hash($password, PASSWORD_DEFAULT);
            file_put_contents($fichier_mdp, "$username|$hash\n", FILE_APPEND);
            $mdp_valide = true;
        }

        if ($mdp_valide) {
            $_SESSION['username'] = $username;

            // Initialisation dans fichiers HSK
            $fichiers = ['utilisateurs.txt', 'hsk1.txt', 'hsk2.txt', 'hsk3.txt'];
            foreach ($fichiers as $fichier) {
                $liste = file_exists($fichier) ? file($fichier, FILE_IGNORE_NEW_LINES) : [];
                $existe = false;
                foreach ($liste as $ligne) {
                    if (strpos($ligne, $username . '|') === 0 || trim($ligne) === $username) {
                        $existe = true;
                        break;
                    }
                }
                if (!$existe) {
                    $ligne_init = "$username|0|0|\n\n";
                    file_put_contents($fichier, $ligne_init, FILE_APPEND);
                }
            }

            header("Location: hsk.php");
            exit;
        } else {
            $erreur = "Mot de passe incorrect.";
        }
    } else {
        $erreur = "Veuillez entrer un nom d'utilisateur et un mot de passe.";
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
    <link rel="manifest" href="/HSK/manifest.json">
    <meta name="theme-color" content="#b30000">

</head>
<body>
    <div class="login-container">
        <h1>Entrainement HSK </h1>
        <?php if (!empty($erreur)) echo "<p class='erreur'>$erreur</p>"; ?>
        

        <form method="post">
        
            <p><h6>le 1er mot de passe entré sera votre mot de passe, vous pouvez désormais enregistrer le site en tant qu'app sur votre telephone</h6></p>
            
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
            <button id="installButton" style="display: none;">Ajouter à l'écran d'accueil</button>
        </form>
    </div>
</body>
<script>
  let deferredPrompt;

  // Enregistrement du Service Worker
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/HSK/sw.js')
        .then(() => console.log('✅ Service Worker enregistré'))
        .catch(err => console.error('❌ Erreur Service Worker :', err));
    });
  }

  // Affichage du bouton d'installation quand possible
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault(); // Empêche l'affichage automatique
    deferredPrompt = e;
    const installBtn = document.querySelector('#installButton');
    if (installBtn) installBtn.style.display = 'flex';
  });

  // Clic sur le bouton "Ajouter à l'écran d'accueil"
  document.addEventListener('DOMContentLoaded', () => {
    const installBtn = document.querySelector('#installButton');
    if (installBtn) {
      installBtn.addEventListener('click', () => {
        if (deferredPrompt) {
          deferredPrompt.prompt();
          deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
              console.log('✅ L\'utilisateur a accepté l\'installation');
            } else {
              console.log('❌ L\'utilisateur a refusé l\'installation');
            }
            deferredPrompt = null;
            installBtn.style.display = 'none';
          });
        }
      });
    }
  });
</script>
<style>
  
/* Conteneur principal de la page */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #fdfdfd;
    color: #333;
}

/* Bloc de connexion */
.login-container {
    max-width: 400px;
    margin: 60px auto;
    padding: 30px;
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    text-align: center;
}

/* Titre */
.login-container h1 {
    margin-bottom: 10px;
    color: #b30000;
}

/* Message d'erreur */
.erreur {
    color: red;
    font-weight: bold;
    margin-bottom: 10px;
}

/* Formulaire */
form {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

/* Inputs */
input[type="text"],
input[type="password"] {
    width: 100%;
    max-width: 300px;
    padding: 12px;
    border: 2px solid #b30000;
    border-radius: 8px;
    font-size: 16px;
    box-sizing: border-box;
    transition: border-color 0.3s;
}

input[type="text"]:focus,
input[type="password"]:focus {
    border-color: #ff0000;
    outline: none;
}

/* Boutons */
button {
    width: 100%;
    max-width: 300px;
    background-color: #b30000;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 12px;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #ff0000;
}

/* Bouton d'installation (masqué par défaut) */
#installButton {
    display: none;
}

/* Astuce affichée */
h6 {
    font-weight: normal;
    color: #666;
    margin-top: 5px;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 500px) {
    .login-container {
        margin: 30px 20px;
        padding: 20px;
    }

    input, button {
        font-size: 16px;
        padding: 10px;
    }
}


</style>
</html>
