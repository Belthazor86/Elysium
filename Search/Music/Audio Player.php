<?php
// Scan the "audio" folder for files
$audioDirectory = 'Audio Player/';
$audioFiles = [];

if (is_dir($audioDirectory)) {
    $files = scandir($audioDirectory);
    foreach ($files as $file) {
        // Filter for audio extensions
        if (preg_match('/\.(mp3|m4a|m4b|ogg|wav|aac|flac|opus|aiff|alac)$/i', $file)) {
            // Store as object-like structure to match JS File object name property
            $audioFiles[] = [
                'name' => $file,
                'path' => $audioDirectory . $file
            ];
        }
    }
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" /> 
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />  
<link href="../../CSS/style.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Audio Player</title>
<style>

body {
  font-weight: bold;
  margin: 0;
  overflow: hidden; /* Prevent full page scrolling */
}
#playlist {
    list-style-type: none;
    padding: 0;
    text-align: center;
    position: relative;
    z-index: 1;
    margin-top: 60px;
    height: 60vh;          /* Playlist height */
    overflow-y: auto;      /* Only playlist scrolls */
    overflow-x: hidden;
}
#playlist li {
    margin-bottom: 5px;
    cursor: pointer;
    font-size: 18px;
}
button {
    background: linear-gradient(135deg, #0d47a1, #1976d2); 
    color: whitesmoke;
    border: none;
    padding: 16px 32px;
    cursor: pointer;
    border-radius: 12px;
    margin: 10px;
    font-size: 1.2em;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    text-transform: capitalize;
}
button:hover {
    background: linear-gradient(135deg, #1976d2, #42a5f5);
    transform: scale(1.12);
    box-shadow: 0 6px 20px rgba(0,0,0,0.7);
}

</style>
</head> 

<body>





<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>


<!-- CENTER CONTAINER -->
<div style="width:100%; display:flex; justify-content:center; margin-top:20px;">
    <!-- BUTTONS -->
<input type="file" id="audio-file-input" accept="audio/*" style="display: none;" webkitdirectory directory>
<button class="demo w3-opacity w3-hover-opacity-off button" id="play-button">Play</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="uploadButton" onclick="uploadMusic()">Load</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="audio-scan-btn">Scan</button>
</div>

<ul id="playlist"></ul>
<audio id="audio-player" hidden></audio>
<button class="video-slider-btn left-side" id="previous-button">❮</button>
<button class="video-slider-btn right-side" id="next-button">❯</button>


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
const audioPlayer = document.getElementById("audio-player");
const playButton = document.getElementById("play-button");
const scanButton = document.getElementById("audio-scan-btn");
const previousButton = document.getElementById("previous-button");
const nextButton = document.getElementById("next-button");
const audioFileInput = document.getElementById("audio-file-input");
const playlistElement = document.getElementById("playlist");

let playlist = [];
let currentTrack = 0;

const serverFiles = <?php echo json_encode($audioFiles); ?>;

function highlightCurrentTrack() {
  const items = playlistElement.querySelectorAll("li");
  items.forEach((item, i) => {
    if (i === currentTrack) {
      item.classList.add("w3-opacity-off");
    } else {
      item.classList.remove("w3-opacity-off");
    }
  });
}

audioFileInput.addEventListener("change", handleFileSelect);

// Modified loadTrack to handle both local blobs and server paths
function loadTrack(track) {
  if (!track) return;
  const source = track.path ? track.path : URL.createObjectURL(track);
  audioPlayer.src = source;
  audioPlayer.play();
  highlightCurrentTrack();
  playButton.textContent = "Pause";
}

function displayPlaylist() {
  playlistElement.innerHTML = "";
  playlist.forEach((track, index) => {
    const li = document.createElement("li");
    li.textContent = track.name.replace(/\.[^/.]+$/, "");
    li.classList.add("demo", "w3-opacity", "w3-hover-opacity-off");
    li.addEventListener("click", () => {
      currentTrack = index;
      loadTrack(track);
    });
    playlistElement.appendChild(li);
  });
  highlightCurrentTrack();
}

playButton.addEventListener("click", () => {
  if (audioPlayer.paused) {
    audioPlayer.play();
    playButton.textContent = "Pause";
  } else {
    audioPlayer.pause();
    playButton.textContent = "Play";
  }
});

// Scan folder event
scanButton.addEventListener("click", () => {
    if (serverFiles.length > 0) {
        playlist = serverFiles;
        currentTrack = 0;
        loadTrack(playlist[currentTrack]);
        displayPlaylist();
    } else {
        alert("No audio found in 'audio' folder.");
    }
});

previousButton.addEventListener("click", () => {
  if (currentTrack > 0) {
    currentTrack--;
    loadTrack(playlist[currentTrack]);
  }
});

nextButton.addEventListener("click", () => {
  if (currentTrack < playlist.length - 1) {
    currentTrack++;
    loadTrack(playlist[currentTrack]);
  }
});

audioPlayer.addEventListener("ended", () => {
  if (currentTrack < playlist.length - 1) {
    currentTrack++;
    loadTrack(playlist[currentTrack]);
  } else {
    audioPlayer.pause();
    playButton.textContent = "Play";
  }
});

function handleFileSelect(event) {
  const files = Array.from(event.target.files);
  playlist = [];
  currentTrack = 0;

  for (let i = 0; i < files.length; i++) {
    const fileExtension = files[i].name.split('.').pop().toLowerCase();
    if (fileExtension.match(/^(mp3|m4a|m4b|ogg|wav|aac|flac|opus|aiff|alac)$/)) {
      playlist.push(files[i]);
    }
  }

  if (playlist.length > 0) {
    loadTrack(playlist[currentTrack]);
    displayPlaylist();
  }
  audioFileInput.value = "";
}

function uploadMusic() {
  audioFileInput.click();
}
</script>
</body>
</html>