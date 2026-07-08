<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/style.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Audiofy</title>
<style>
    /* Reset default styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      padding: 20px;
    }

    textarea {
      width: 100%;
      max-width: 500px;
      height: 150px;
      padding: 15px;
      border-radius: 12px;
      border: none;
      outline: none;
      font-size: 1rem;
      background-color: #1e1e1e;
      color: whitesmoke;
      resize: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }

    button {
      margin-top: 20px;
      padding: 15px 40px;
      font-size: 1rem;
      border: none;
      border-radius: 12px;
      background: linear-gradient(90deg, #4b79ff, #283eaf); /* blue gradient */
      color: #ffffff;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
      box-shadow: 0 4px 12px rgba(0,0,0,0.4);
    }

    button:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.5);
    }

    .controls {
      display: flex;
      gap: 15px;
      margin-top: 20px;
      flex-wrap: wrap;
      justify-content: center;
    }

    .controls label {
      display: flex;
      flex-direction: column;
      font-size: 0.9rem;
      color: #ccc;
      text-align: center;
    }

    input[type="range"] {
      width: 150px;
      margin-top: 5px;
    }

    select {
      padding: 8px;
      border-radius: 8px;
      border: none;
      background-color: #1e1e1e;
      color: #fff;
      font-size: 0.9rem;
      cursor: pointer;
    }

#stop-btn {
  background: linear-gradient(90deg, #4b79ff, #283eaf); /* blue gradient */
  margin-left: 10px;
}
</style>
</head>
<body>



<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
<textarea id="text-input" placeholder="Type something..."></textarea>

  <div class="controls">
    <label>
      Voice
      <select id="voice-select"></select>
    </label>
    <label>
      Pitch
      <input type="range" id="pitch" min="0.5" max="2" step="0.1" value="1">
    </label>
    <label>
      Rate
      <input type="range" id="rate" min="0.5" max="2" step="0.1" value="1">
    </label>
  </div>

  <div>
    <button id="speak-btn">Speak</button>
    <button id="upload-btn">Upload Text File</button>
    <input type="file" id="file-input" accept=".txt" style="display:none;">
    <button id="stop-btn">Stop</button>
  </div>



<!-- Footer -->
<footer class="site-footer">
  <div class="footer-content">
    <p class="footer-main">
      © 2025 <?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?> | <a href="../Xtras/Guides.php">Visit Guides for documentation</a>
    </p>
    <p class="footer-specific">
    Powered by <a href="https://github.com/Belthazor86/Elysium.git" target="_blank" rel="noopener noreferrer">Elysium</a> 
    </p>
  </div>
</footer>



<script>
  const textInput = document.getElementById('text-input');
  const speakBtn = document.getElementById('speak-btn');
  const stopBtn = document.getElementById('stop-btn');
  const pitchInput = document.getElementById('pitch');
  const rateInput = document.getElementById('rate');
  const voiceSelect = document.getElementById('voice-select');

  const uploadBtn = document.getElementById("upload-btn");
  const fileInput = document.getElementById("file-input");

  let voices = [];

  // Populate voices in dropdown
  function populateVoices() {
    voices = window.speechSynthesis.getVoices().filter(v => v.lang.startsWith('en'));
    if (voiceSelect.options.length === 0) { // only populate once
      voices.forEach((voice, index) => {
        const option = document.createElement('option');
        option.value = index;
        option.textContent = `${voice.name} (${voice.lang})`;
        voiceSelect.appendChild(option);
      });
    }
  }

  window.speechSynthesis.onvoiceschanged = populateVoices;
  populateVoices();

  // Speak button
  speakBtn.addEventListener('click', () => {
    const text = textInput.value.trim();
    if (!text) return;

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.pitch = parseFloat(pitchInput.value);
    utterance.rate = parseFloat(rateInput.value);
    utterance.voice = voices[voiceSelect.value];

    window.speechSynthesis.speak(utterance);
  });

  // Stop button
  stopBtn.addEventListener('click', () => {
    window.speechSynthesis.cancel();
  });

  // Stop speech on page unload
  window.addEventListener('beforeunload', () => {
    window.speechSynthesis.cancel();
  });

  // Upload text file
  uploadBtn.addEventListener("click", () => fileInput.click());

  fileInput.addEventListener("change", (event) => {
    const file = event.target.files[0];
    if (file && file.type === "text/plain") {
      const reader = new FileReader();
      reader.onload = function(e) {
        const text = e.target.result;
        const utterance = new SpeechSynthesisUtterance(text);
        // Use selected voice, pitch, and rate
        utterance.voice = voices[voiceSelect.value];
        utterance.pitch = parseFloat(pitchInput.value);
        utterance.rate = parseFloat(rateInput.value);
        window.speechSynthesis.speak(utterance);
      }
      reader.readAsText(file);
    }
  });
</script>




</body>
</html>
