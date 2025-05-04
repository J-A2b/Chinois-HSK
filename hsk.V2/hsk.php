<?php
session_start();
$username = $_SESSION['username'] ?? null;
$hsk_level = isset($_GET['hsk']) ? intval($_GET['hsk']) : 0;
$_SESSION['hsk'] = $hsk_level;
function getUserData($username, $level) {
    $file = "hsk{$level}.txt";
    if (!file_exists($file)) return [0, 0, []];

    $lines = file($file, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        [$user, $score, $progress, $exclues] = explode('|', $line) + [null, 0, 0, ''];
        if ($user === $username) {
            $ids = $exclues ? explode(',', $exclues) : [];
            return [(int)$score, (int)$progress, $ids];
        }
    }
    return [0, 0, []];
}


[$user_score, $user_progress, $exclues_ids] = getUserData($username, $hsk_level);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Entraînement HSK</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="icon.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
    <div class="container">
        <?php if ($hsk_level == 0): ?>
        <h1>Choisis ton niveau HSK</h1>
        <div class="menu">
            <?php for ($i = 1; $i <= 2; $i++): ?>
                <a class="button" href="?hsk=<?= $i ?>">HSK <?= $i ?></a>
            <?php endfor; ?>
            <a class="button" href="mondico.php">Mon dictionnaire</a>
        </div>

        <!-- Classement des utilisateurs -->
        <div class="classement">
            <h2>🏆 Classement des utilisateurs</h2>
            <div class="classement-wrapper">
            <div class="table-container">
            <?php
            $utilisateurs = [];

            for ($niv = 1; $niv <= 2; $niv++) {
                $fichier = "hsk{$niv}.txt";
                if (!file_exists($fichier)) continue;
                $lignes = file($fichier, FILE_IGNORE_NEW_LINES);
                foreach ($lignes as $ligne) {
                    if (trim($ligne) === '') continue;
                    [$user, $score, $progress, $exclues] = explode('|', $ligne) + [null, 0, 0, ''];
                    if (!isset($utilisateurs[$user])) {
                        $utilisateurs[$user] = ['total' => 0];
                    }
                    $utilisateurs[$user]["hsk{$niv}_score"] = (int)$score;
                    $utilisateurs[$user]["hsk{$niv}_progress"] = (int)$progress;
                    $utilisateurs[$user]['total'] += (int)$score;
                }
            }

            // Tri par score total décroissant
            uasort($utilisateurs, fn($a, $b) => $b['total'] <=> $a['total']);

            echo "<table>";
            echo "<thead><tr><th>Utilisateur</th><th>HSK 1</th><th>HSK 2</th><th>Score total</th></tr></thead><tbody>";
            foreach ($utilisateurs as $user => $data) {
                $h1s = $data['hsk1_score'] ?? 0;
                $h2s = $data['hsk2_score'] ?? 0;
                echo "<tr>
                    <td data-label='Utilisateur'>$user</td>
                    <td data-label='HSK 1'>$h1s pts </td>
                    <td data-label='HSK 2'>$h2s pts </td>
                    <td data-label='Total'><strong>{$data['total']}</strong> pts</td>
                </tr>";
            }
            echo "</tbody></table>";
            
            ?>
        </div>
    <?php else: ?>

            <?php
                $json_file = "hsk{$hsk_level}.json";
                if (file_exists($json_file)) {
                    $json_data = file_get_contents($json_file);
                } else {
                    echo "<p>Erreur : fichier HSK non trouvé.</p>";
                    exit;
                }
            ?>
            <h1>HSK <?= $hsk_level ?> - Entraînement QCM</h1>
            <div id="quiz">
                <div id="score">Score : <?= $user_score ?> | Progression : <?= $user_progress ?>%</div>
                <div id="question"></div>
                <div id="options"></div>
                <div id="feedback"></div>
                <button id="next">Question suivante</button>
            </div>
            <script>
                const syllabus = <?= $json_data ?>;
                const userData = {
                    username: "<?= $username ?>",
                    score: <?= $user_score ?>,
                    progress: <?= $user_progress ?>,
                    exclues: <?= json_encode($exclues_ids) ?>
                };
            </script>
            <script src="script.js"></script>
        <?php endif; ?>
        </div>
        </div>
    </div>
    <footer><h6>Réalisé par Jean-Antoine Dary®. Github pour le code source et demandes d'ajout: <a href="https://github.com/J-A2b/Chinois-HSK">ici</a></h6></footer>
</body>
<style>
    /* style du tableau responsive, simple, couleurs rouges */
    /* Style du tableau de classement */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1em;
    background-color: #fff0f0;
    box-shadow: 0 4px 8px rgba(255, 0, 0, 0.2);
    border-radius: 12px;
    overflow: hidden;
    font-family: sans-serif;
}

thead {
    background-color: #cc0000;
    color: white;
}

thead th {
    padding: 12px;
    text-align: left;
}

tbody tr {
    border-bottom: 1px solid #f2caca;
}

tbody tr:nth-child(even) {
    background-color: #ffe5e5;
}

tbody td {
    padding: 10px 12px;
}

tbody td strong {
    color: #cc0000;
}

/* Responsive - affichage mobile */
@media screen and (max-width: 600px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }

    thead {
        display: none;
    }

    tbody tr {
        margin-bottom: 1em;
        background: #fff5f5;
        border: 1px solid #f0c0c0;
        border-radius: 8px;
        padding: 10px;
    }

    td {
        padding: 8px;
        text-align: right;
        position: relative;
    }

    td::before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        top: 8px;
        font-weight: bold;
        color: #cc0000;
        text-align: left;
    }
}

</style>

</html>
