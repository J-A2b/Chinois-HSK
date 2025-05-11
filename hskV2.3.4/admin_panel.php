<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'jean_antoine') {
    die("Accès refusé.");
}

$levels = [1, 2, 3];
$modif_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $level = (int)$_POST['level'];
    $username = $_POST['username'];
    $score = (int)$_POST['score'];
    $progress = (int)$_POST['progress'];
    $exclues = trim($_POST['exclues']);

    $fichier = "hsk{$level}.txt";
    $lines = file_exists($fichier) ? file($fichier, FILE_IGNORE_NEW_LINES) : [];
    $updated = false;

    
    foreach ($lines as &$line) {
        [$user] = explode('|', $line);
        if ($user === $username) {
            $line = "$username|$score|$progress|$exclues";
            $updated = true;
            break;
        }
    }

    if (!$updated) {
        $lines[] = "$username|$score|$progress|$exclues";
    }

    file_put_contents($fichier, implode("\n", $lines) . "\n");
    $modif_msg = "✅ Données mises à jour pour $username dans HSK $level.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="icon" href="icon.png" type="image/png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Panneau Admin - Jean Antoine</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }
        .form-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            display: table;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }
        th {
            background: #eee;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            box-sizing: border-box;
        }

        @media screen and (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead {
                display: none;
            }

            tr {
                margin-bottom: 15px;
                border: 1px solid #ccc;
                border-radius: 5px;
                padding: 10px;
                background: #fafafa;
            }

            td {
                position: relative;
                padding-left: 50%;
                border: none;
                border-bottom: 1px solid #eee;
            }

            td:before {
                position: absolute;
                top: 10px;
                left: 10px;
                width: 45%;
                white-space: nowrap;
                font-weight: bold;
            }
            td:nth-child(1):before { content: "Nom "; }
            td:nth-child(2):before { content: "Score"; }
            td:nth-child(3):before { content: "Progression"; }
            td:nth-child(4):before { content: "Exclues"; }
            td:nth-child(5):before { content: "Modifier"; }



        }
    </style>
</head>
<body>

<h1>Panneau d'administration</h1>

<?php if ($modif_msg): ?>
    <p style="color: green; text-align: center;"><strong><?= $modif_msg ?></strong></p>
<?php endif; ?>

<?php foreach ($levels as $level): ?>
    <div class="form-box">
        <h2>HSK <?= $level ?></h2>
        <table>
            <thead>
            <tr>
                <th>Nom d'utilisateur</th>
                <th>Score</th>
                <th>Progression</th>
                <th>Exclues</th>
                <th>Modifier</th>
            </tr>
            </thead>
        <tbody>
            <?php
            $fichier = "hsk{$level}.txt";
            $lines = file_exists($fichier) ? file($fichier, FILE_IGNORE_NEW_LINES) : [];

            foreach ($lines as $line):
                // Ignore les lignes vides ou avec seulement des espaces
                if (empty(trim($line)) || preg_match('/^\|0\|0\|$/', $line)) continue;

                [$user, $score, $progress, $exclues] = explode('|', $line) + [0, 0, 0, ''];
                ?>
                <form method="post">
                    <input type="hidden" name="level" value="<?= $level ?>">
                    <input type="hidden" name="username" value="<?= htmlspecialchars($user) ?>">
                    <tr>
                        <td><?= htmlspecialchars($user) ?></td>
                        <td><input type="number" name="score" value="<?= $score ?>" required></td>
                        <td><input type="number" name="progress" value="<?= $progress ?>" required></td>
                        <td><input type="text" name="exclues" value="<?= htmlspecialchars($exclues) ?>"></td>
                        <td><button type="submit">💾</button></td>
                    </tr>
                </form>
            <?php endforeach; ?>
        </tbody>
        
        </table>
    </div>
<?php endforeach; ?>

</body>
</html>
