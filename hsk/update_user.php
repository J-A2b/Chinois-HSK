<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $username = $data['username'] ?? '';
    $score = $data['score'] ?? 0;
    $exclus = $data['exclues'] ?? [];

    if (!preg_match('/^[a-zA-Z0-9_-]{1,30}$/', $username)) {
        http_response_code(400);
        echo "Nom d'utilisateur invalide.";
        exit;
    }

    $filepath = 'utilisateurs.txt'; // 🔄 Corrigé ici aussi
    $lines = file_exists($filepath) ? file($filepath, FILE_IGNORE_NEW_LINES) : [];
    $updated = false;
    $new_lines = [];

    foreach ($lines as $line) {
        list($user, $saved_score, $saved_total, $saved_exclus) = array_pad(explode('|', $line), 4, '');
        if ($user === $username) {
            $exclus_string = implode(',', array_unique($exclus));
            $new_lines[] = "$username|$score|" . count($exclus) . "|$exclus_string";
            $updated = true;
        } else {
            $new_lines[] = $line;
        }
    }

    if (!$updated) {
        $exclus_string = implode(',', array_unique($exclus));
        $new_lines[] = "$username|$score|" . count($exclus) . "|$exclus_string";
    }

    file_put_contents($filepath, implode("\n", $new_lines));
    echo "✅ Données enregistrées";
} else {
    http_response_code(405);
    echo "Méthode non autorisée.";
}
