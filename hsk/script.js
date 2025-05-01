// script.js
let currentWord = null;
let currentOptions = [];
let currentQuestionType = "";
let correctStreaks = {};

function randomChoice(arr) {
    return arr[Math.floor(Math.random() * arr.length)];
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

    let candidates = syllabus.syllabus.filter(w => !userData.exclues.includes(w.simplifie));
    if (candidates.length === 0) {
        document.getElementById('question').innerText = "🎉 Tu as terminé toutes les questions !";
        document.getElementById('options').innerHTML = "";
        return;
    }

    currentWord = randomChoice(candidates);

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

        if (correctStreaks[currentWord.simplifie] >= 2 && !userData.exclues.includes(currentWord.simplifie)) {
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
    }

    document.getElementById('next').style.display = "inline-block";
}

document.getElementById('next').addEventListener('click', newQuestion);
if (typeof syllabus !== 'undefined') newQuestion();
