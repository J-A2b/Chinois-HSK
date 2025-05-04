<?php
// Détecter si un niveau HSK est choisi
$hsk_level = isset($_GET['hsk']) ? intval($_GET['hsk']) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Entraînement HSK</title>
    <link rel="stylesheet" href="style.css">
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
                <div id="question"></div>
                <div id="options"></div>
                <div id="feedback"></div>
                <button id="next">Question suivante</button>
            </div>
            <script>
                const syllabus = <?= $json_data ?>;
            </script>
            <script src="script.js"></script>
        <?php endif; ?>

    </div>
</body>
</html>
