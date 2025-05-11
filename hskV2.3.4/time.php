<?php
date_default_timezone_set('Europe/Paris');
$config_file = 'reset_config.json';

// 1. Charger ou créer le fichier reset_config.json
if (file_exists($config_file)) {
    $data = json_decode(file_get_contents($config_file), true);
} else {
    $data = [];
}

// 2. Vérifier si "next_reset" est défini, sinon le fixer à dimanche prochain minuit
if (!isset($data['next_reset'])) {
    $dimanche = new DateTime('next Sunday');
    $dimanche->setTime(0, 0, 0);
    $data['next_reset'] = $dimanche->getTimestamp();
    file_put_contents($config_file, json_encode($data, JSON_PRETTY_PRINT));
}

// 3. Comparaison avec le timestamp actuel
$now = time();
$next_reset = $data['next_reset'];
$reset_passed = $now >= $next_reset;
$reset_score = isset($_GET['reset_score']);
$reset_progress = isset($_GET['reset_progress']);
// 4. Si le reset est dépassé, mettre à jour pour le dimanche suivant
if ($reset_passed) {
    session_start();
    $fichiers = ["hsk1.txt", "hsk2.txt", "hsk3.txt"];
    $nouveau_contenu = [];

    foreach ($fichiers as $fichier) {
        if (!file_exists($fichier)) continue;

        $lignes = file($fichier, FILE_IGNORE_NEW_LINES);
        $nouveau_contenu = [];

        foreach ($lignes as $ligne) {
            if (trim($ligne) === '') continue;
            [$user, $score, $progress, $exclues] = explode('|', $ligne) + [null, 0, 0, ''];
            
            $score = $reset_score ? 0 : $score;
            $progress = $reset_progress ? 0 : $progress;

            $nouveau_contenu[] = "$user|$score|$progress|$exclues";
        }

        if (!empty($nouveau_contenu)) {
            file_put_contents($fichier, implode("\n", $nouveau_contenu) . "\n");
        }
    }

    // Calculer le prochain dimanche minuit
    $new_reset = new DateTime('next Sunday');
    $new_reset->setTime(0, 0, 0);
    $data['next_reset'] = $new_reset->getTimestamp();

    file_put_contents($config_file, json_encode($data, JSON_PRETTY_PRINT));

    header("Location: index.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification Reset HSK</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 30px; }
        .box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h1 { color: #cc0000; }
        p { margin: 10px 0; }
    </style>
</head>
<body>
<div class="box">
    <h1>🗓 Vérification du Reset HSK</h1>
    <p><strong>Timestamp actuel :</strong> <?= $now ?></p>
    <p><strong>Date actuelle :</strong> <?= date('Y-m-d H:i:s', $now) ?></p>
    <hr>
    <p><strong>Timestamp du prochain reset (depuis JSON) :</strong> <?= $next_reset ?></p>
    <p><strong>Date du prochain reset :</strong> <?= date('Y-m-d H:i:s', $next_reset) ?></p>
    <hr>
    <p><strong>Le reset est-il dépassé ?</strong>
        <?= $reset_passed ? "<span style='color:green;'>✅ Oui, mis à jour</span>" : "<span style='color:red;'>❌ Non</span>" ?>
    </p>
    <hr>
    <form method="get">
    <label><input type="checkbox" name="reset_score" <?= $reset_score ? 'checked' : '' ?>> Réinitialiser les scores</label><br>
    <label><input type="checkbox" name="reset_progress" <?= $reset_progress ? 'checked' : '' ?>> Réinitialiser la progression</label><br><br>
    <button type="submit">valider</button>
</form>

<p><strong>Option cochée :</strong><br>
    Score : <?= $reset_score ? "✅ Oui" : "❌ Non" ?><br>
    Progression : <?= $reset_progress ? "✅ Oui" : "❌ Non" ?>
</p>



</div>
</body>
</html>
