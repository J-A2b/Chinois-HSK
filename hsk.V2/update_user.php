<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);

$username = $data['username'];
$score = (int)$data['score'];
$progress = (int)$data['progress'];
$exclues = implode(',', $data['exclues']);
$level = isset($_SESSION['hsk']) ? (int)$_SESSION['hsk'] : 1; // par défaut HSK 1

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

