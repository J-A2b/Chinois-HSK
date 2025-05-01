<?php
session_start();
$username = $_SESSION['username'] ?? null;
$hsk_level = isset($_GET['hsk']) ? intval($_GET['hsk']) : 0;

function getUserData($username) {
    $file = 'utilisateurs.txt';
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

[$user_score, $user_progress, $exclues_ids] = getUserData($username);
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
</body>
</html>
