<?php
session_start();
$username = $_SESSION['username'] ?? null;
if (!$username) {
    header('Location: login.php');
    exit;
}

$niveau = isset($_GET['hsk']) && in_array($_GET['hsk'], ['1', '2', '3']) ? $_GET['hsk'] : '1';

function lireUtilisateur($fichier, $username) {
    if (!file_exists($fichier)) return [0, 0, []];
    $lignes = file($fichier, FILE_IGNORE_NEW_LINES);
    foreach ($lignes as $ligne) {
        [$user, $score, $progress, $exclues] = explode('|', $ligne) + [null, 0, 0, ''];
        if ($user === $username) {
            return [(int)$score, (int)$progress, $exclues ? explode(',', $exclues) : []];
        }
    }
    return [0, 0, []];
}

[$user_score, $user_progress, $exclues_final] = lireUtilisateur("hsk{$niveau}.txt", $username);

$json_file = "hsk{$niveau}.json";
if (!file_exists($json_file)) {
    echo "Erreur : syllabus non trouvé.";
    exit;
}
$syllabus = json_decode(file_get_contents($json_file), true);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon dictionnaire HSK<?= $niveau ?></title>
    <link rel="icon" href="icon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
<div class="container">
    <h1>Mon Dictionnaire - HSK <?= $niveau ?></h1>

    <!-- Choix du niveau HSK -->
    <form method="get" style="margin-bottom: 20px;">
        <label for="hsk">Niveau HSK :</label>
        <select name="hsk" id="hsk" onchange="this.form.submit()">
            <?php foreach ([1, 2, 3] as $niv): ?>
                <option value="<?= $niv ?>" <?= $niv == $niveau ? 'selected' : '' ?>>HSK <?= $niv ?></option>
            <?php endforeach; ?>
        </select>
    </form>
<?php if (isset($_GET['reset']) && $_GET['reset'] === 'ok'): ?>
    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;">
        ✅ Progression réinitialisée avec succès !
    </div>
<?php endif; ?>

    <form method="post" action="reset_progression.php?hsk=<?= $niveau ?>" onsubmit="return confirm('Réinitialiser ta progression ?');" style="margin: 20px 0;">
        <input type="hidden" name="hsk" value="<?= $niveau ?>">
        <button type="submit" style="background-color: #e74c3c; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">
            🔄 Réinitialiser la progression
        </button>

        <button type="button" style="background-color: #e74c3c; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; margin-top: 5px;">
           <a href="https://lesitewebdejeanantoine.webhebergia.fr/HSK/hsk.php">| 🏠 |</a>
        </button>
    
    </form>

    <!-- Score utilisateur -->
    <div id="score" style="margin-bottom: 20px;">Score : <?= $user_score ?> | Progression : <?= $user_progress ?>%</div>
    <!-- Barre de recherche -->
    <div>
        <input type="text" id="recherche" placeholder="🔍 caractère, pinyin ou traduction..." style="width:100%; padding:10px; font-size:16px;">
    </div>

    <h2>✅ Caractères découverts</h2>
    <div class="grid">
        <?php
        foreach ($syllabus['syllabus'] as $mot) {
            if (in_array($mot['simplifie'], $exclues_final)) {
                echo "<div class='mot'><strong>{$mot['simplifie']}</strong><br>{$mot['pinyin']}<br>{$mot['traduction']}</div>";
            }
        }
        ?>
    </div>

    <h2>📚 Caractères non encore maîtrisés</h2>
    <div class="grid">
        <?php
        foreach ($syllabus['syllabus'] as $mot) {
            if (!in_array($mot['simplifie'], $exclues_final)) {
                echo "<div class='mot'><strong>{$mot['simplifie']}</strong><br>{$mot['pinyin']}<br>{$mot['traduction']}</div>";
            }
        }
        ?>
    </div>
</div>

<style>
    .container { max-width: 900px; margin: auto; padding: 20px; }
    .grid { display: flex; flex-wrap: wrap; gap: 15px; }
    .mot { border: 1px solid #ccc; padding: 10px; border-radius: 5px; width: 120px; text-align: center; background: #f9f9f9; }
    #recherche {
            width: 100%;
            box-sizing: border-box; /* essentiel pour que padding ne fasse pas dépasser */
            padding: 10px;
            font-size: 16px;
    }
        
    /* Pour petits écrans (téléphones) */
    @media screen and (max-width: 600px) {
        #recherche {
            width: 100%;
            box-sizing: border-box; /* essentiel pour que padding ne fasse pas dépasser */
            padding: 10px;
            font-size: 16px;
        }
        
        .mot {
            width: 45vw; /* chaque carte prend ~45% de la largeur (2 par ligne) */
            font-size: 18px;
            padding: 12px;
        }
    }

    /* Pour très petits écrans (téléphones étroits) */
    @media screen and (max-width: 400px) {
        #recherche {
            width: 100%;
            box-sizing: border-box; /* essentiel pour que padding ne fasse pas dépasser */
            padding: 10px;
            font-size: 16px;
        }

        .mot {
            width: 90vw; /* une seule carte par ligne */
        }
    }
</style>

<script>
function sansAccents(str) {
    return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

document.getElementById('recherche').addEventListener('input', function () {
    const termeBrut = this.value.toLowerCase();
    const terme = sansAccents(termeBrut);

    // Redirection spéciale si le terme est "yoda"
    if (terme === 'yoda') {
        window.location.href = 'yoda.php'; // Remplace par le vrai chemin de ta page
        return;
    }

    document.querySelectorAll('.mot').forEach(el => {
        const contenu = sansAccents(el.innerText.toLowerCase());
        el.style.display = contenu.includes(terme) ? '' : 'none';
    });
});
</script>

</body>
</html>
