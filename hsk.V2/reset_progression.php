<?php
session_start();
$username = $_SESSION['username'] ?? null;
$niveau = isset($_GET['hsk']) && in_array($_GET['hsk'], ['1', '2']) ? $_GET['hsk'] : '1';
$fichier = "hsk{$niveau}.txt";

if (!$username) {
    header('Location: login.php');
    exit;
}

if (!file_exists($fichier)) {
    header("Location: dictionnaire.php?hsk=$niveau");
    exit;
}

$lignes = file($fichier, FILE_IGNORE_NEW_LINES);
$nouveau_contenu = [];

foreach ($lignes as $ligne) {
    [$user, $score, $progress, $exclues] = explode('|', $ligne) + [null, 0, 0, ''];
    if ($user === $username) {
        $nouveau_contenu[] = "$user|$score|0|";
    } else {
        $nouveau_contenu[] = $ligne;
    }
}
file_put_contents($fichier, implode("\n", $nouveau_contenu) . "\n");
header("Location: mondico.php");
exit;
