// script.js
let currentWord = null;
let currentOptions = [];
let currentQuestionType = "";
let correctStreaks = {};
let currentBatch = [];
const BATCH_SIZE = 24;

function randomChoice(arr) {
    return arr[Math.floor(Math.random() * arr.length)];
}

function loadBatch() {
    const stored = localStorage.getItem("currentBatch");
    if (stored) {
        const ids = JSON.parse(stored);
        currentBatch = ids
            .map(id => syllabus.syllabus.find(w => w.simplifie === id))
            .filter(w => w); // filtre ceux qui n’existent plus
    } else {
        currentBatch = [];
    }
}

function saveBatch() {
    const ids = currentBatch.map(w => w.simplifie);
    localStorage.setItem("currentBatch", JSON.stringify(ids));
}


function updateBatch() {
    currentBatch = currentBatch.filter(w => !userData.exclues.includes(w.simplifie));

    let needed = BATCH_SIZE - currentBatch.length;
    if (needed > 0) {
        let remaining = syllabus.syllabus.filter(w =>
            !userData.exclues.includes(w.simplifie) &&
            !currentBatch.some(b => b.simplifie === w.simplifie)
        );

        for (let i = 0; i < needed && i < remaining.length; i++) {
            currentBatch.push(remaining[i]);
        }
    }

    saveBatch(); // on sauve après toute mise à jour
}



function saveUserData() {
    fetch('update_user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            username: userData.username,
            score: userData.score,
            progress: userData.progress,
            exclues: userData.exclues
        })
    });
}

function newQuestion() {
    document.getElementById('feedback').innerText = "";
    document.getElementById('next').style.display = "none";

    if (currentBatch.length === 0) loadBatch(); // si pas encore chargé
    updateBatch();

    if (currentBatch.length === 0) {
        document.getElementById('question').innerText = "🎉 Tu as terminé toutes les questions !";
        document.getElementById('options').innerHTML = "Félicitation à toi ";
        return;
    }

    currentWord = randomChoice(currentBatch);

    const types = ["chinois->francais", "francais->chinois", "chinois->pinyin", "pinyin->chinois"];
    currentQuestionType = randomChoice(types);

    let questionText = "";
    let correctAnswer = "";
    let possibleAnswers = [];

    if (currentQuestionType === "chinois->francais") {
        questionText = currentWord.simplifie;
        correctAnswer = currentWord.traduction.split(",")[0].trim();
        possibleAnswers = syllabus.syllabus.map(w => w.traduction.split(",")[0].trim());
    } else if (currentQuestionType === "francais->chinois") {
        questionText = currentWord.traduction.split(",")[0].trim();
        correctAnswer = currentWord.simplifie;
        possibleAnswers = syllabus.syllabus.map(w => w.simplifie);
    } else if (currentQuestionType === "chinois->pinyin") {
        questionText = currentWord.simplifie;
        correctAnswer = currentWord.pinyin;
        possibleAnswers = syllabus.syllabus.map(w => w.pinyin);
    } else if (currentQuestionType === "pinyin->chinois") {
        questionText = currentWord.pinyin;
        correctAnswer = currentWord.simplifie;
        possibleAnswers = syllabus.syllabus.map(w => w.simplifie);
    }

    possibleAnswers = possibleAnswers.filter(ans => ans !== correctAnswer);
    currentOptions = [correctAnswer, ...possibleAnswers.sort(() => 0.5 - Math.random()).slice(0, 3)];
    currentOptions.sort(() => 0.5 - Math.random());

    document.getElementById('question').innerText = questionText;
    const optionsDiv = document.getElementById('options');
    optionsDiv.innerHTML = "";
    currentOptions.forEach((opt) => {
        const btn = document.createElement('button');
        btn.innerText = opt;
        btn.onclick = () => checkAnswer(opt, correctAnswer);
        optionsDiv.appendChild(btn);
    });
}

function checkAnswer(selected, correct) {
    if (selected === correct) {
        document.getElementById('feedback').innerText = "✅ Correct !";
        document.getElementById('feedback').style.color = "green";
        correctStreaks[currentWord.simplifie] = (correctStreaks[currentWord.simplifie] || 0) + 1;
        userData.score++;

        if (correctStreaks[currentWord.simplifie] >= 4 && !userData.exclues.includes(currentWord.simplifie)) {
            userData.exclues.push(currentWord.simplifie);
        }
        
        let total = syllabus.syllabus.length;
        userData.progress = Math.round(userData.exclues.length / total * 100);
        
        document.getElementById('score').innerText = `Score : ${userData.score} | Progression : ${userData.progress}%`;
        saveUserData();
        
    } else {
        document.getElementById('feedback').innerText = `❌ Faux ! Réponse : ${correct}`;
        document.getElementById('feedback').style.color = "red";
        correctStreaks[currentWord.simplifie] = 0;
        userData.score--;
        let total = syllabus.syllabus.length;
        userData.progress = Math.round(userData.exclues.length / total * 100);
        document.getElementById('score').innerText = `Score : ${userData.score} | Progression : ${userData.progress}%`;
        saveUserData();
    }

    document.getElementById('next').style.display = "inline-block";
}

document.getElementById('next').addEventListener('click', newQuestion);
if (typeof syllabus !== 'undefined') newQuestion();
