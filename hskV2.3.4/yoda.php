<?php
session_start();
$user = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Maître Yoda</title>
    <link rel="icon" href="icon.png" type="image/png">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { text-align: center; font-family: sans-serif; margin-top: 50px; background-color: black;}
        img { max-width: 300px; }
        .message { font-size: 24px; margin-top: 20px; }
    </style>
</head>
<body>
    <img id="yoda"   src="yoda.png" alt="Maître Yoda en Chinois"> <!-- Remplace par le vrai fichier -->
    <div id="countdown" style="display:none; font-size: 20px; color: green; margin-top: 10px;"></div>

    <div class="message">Que le HSK soit avec toi  <?= $user ?> !</div>
</body>
<script>
let timer;
let countdownInterval;
const countdownElement = document.getElementById('countdown');
const holdDuration = 60000; // 20 s
function actionLonguePression() {
    alert("screen et envoie moi pour +100 de score !");

    countdownElement.style.display = "none";
    clearInterval(countdownInterval);

}

function demarrerCountdown() {
    const start = Date.now();
    countdownElement.style.display = "block";

    countdownInterval = setInterval(() => {
        const elapsed = Date.now() - start;
        const remaining = Math.max(0, holdDuration - elapsed);
        countdownElement.textContent = `Encore ${Math.ceil(remaining / 1000)} secondes...`;

        if (remaining <= 0) {
            clearInterval(countdownInterval);
        }
    }, 200);
}

function arreterCountdown() {
    clearTimeout(timer);
    clearInterval(countdownInterval);
    countdownElement.style.display = "none";
}

const yoda = document.getElementById('yoda');

yoda.addEventListener('mousedown', () => {
    timer = setTimeout(actionLonguePression, holdDuration);
    demarrerCountdown();
});

yoda.addEventListener('mouseup', arreterCountdown);
yoda.addEventListener('mouseleave', arreterCountdown);

// Pour les appareils mobiles
yoda.addEventListener('touchstart', () => {
    timer = setTimeout(actionLonguePression, holdDuration);
    demarrerCountdown();
});

yoda.addEventListener('touchend', arreterCountdown);
</script>


</html>
