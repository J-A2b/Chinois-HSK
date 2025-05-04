let currentWord = null;
let currentOptions = [];
let currentQuestionType = "";

function randomChoice(arr) {
    return arr[Math.floor(Math.random() * arr.length)];
}

function newQuestion() {
    document.getElementById('feedback').innerText = "";
    document.getElementById('next').style.display = "none";
    
    currentWord = randomChoice(syllabus.syllabus);

    // Choisir un type de question aléatoire
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

    // Mélanger les réponses
    possibleAnswers = possibleAnswers.filter(ans => ans !== correctAnswer);
    currentOptions = [correctAnswer, ...possibleAnswers.sort(() => 0.5 - Math.random()).slice(0, 3)];
    currentOptions.sort(() => 0.5 - Math.random());

    // Afficher question
    document.getElementById('question').innerText = questionText;

    // Afficher options
    const optionsDiv = document.getElementById('options');
    optionsDiv.innerHTML = "";
    currentOptions.forEach((opt, index) => {
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
    } else {
        document.getElementById('feedback').innerText = `❌ Faux ! Réponse : ${correct}`;
        document.getElementById('feedback').style.color = "red";
    }

    document.getElementById('next').style.display = "inline-block";
}

document.getElementById('next').addEventListener('click', newQuestion);

// Démarrer
if (typeof syllabus !== 'undefined') {
    newQuestion();
}
