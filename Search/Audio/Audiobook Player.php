


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
<title>Audiobook Player</title>	
</head>
<style>
		
body {
  font-weight: bold;
  margin: 0;
}
			
#playlist {
    list-style-type: none;
    padding: 0;
    text-align: center; /* Center text within playlist */
  }

#playlist li {
    margin-bottom: 5px;
    cursor: pointer;
	  font-size: 18px;
  }
										
.button {
  background-color: transparent;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
  padding: 10px 20px;
}
			
</style>

	
<body>
	
	

<button class="demo w3-opacity w3-hover-opacity-off button" id="play-button">Play/Pause</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="uploadButton" onclick="uploadMusic()">Upload</button>
<input type="file" id="audio-file-input" style="display: none;" webkitdirectory directory>
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
const previousButton = document.getElementById("previous-button");
const nextButton = document.getElementById("next-button");
const audioFileInput = document.getElementById("audio-file-input");
const playlistElement = document.getElementById("playlist");

let playlist = [];
let currentTrack = 0;

// Highlight the current playing track
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

// Add event listener to the file input
audioFileInput.addEventListener("change", handleFileSelect);

// Load a track and start playing
function loadTrack(track) {
  audioPlayer.src = URL.createObjectURL(track);
  audioPlayer.play();
  highlightCurrentTrack();
}

// Display the playlist
function displayPlaylist() {
  playlistElement.innerHTML = "";
  playlist.forEach((track, index) => {
    const li = document.createElement("li");
    // Remove the file extension from the name
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

// Add event listeners to the buttons
playButton.addEventListener("click", () => {
  if (audioPlayer.paused) {
    audioPlayer.play();
    playButton.textContent = "Pause";
  } else {
    audioPlayer.pause();
    playButton.textContent = "Play";
  }
});

previousButton.addEventListener("click", () => {
  audioPlayer.currentTime = Math.max(0, audioPlayer.currentTime - 10);
});

nextButton.addEventListener("click", () => {
  audioPlayer.currentTime = Math.min(audioPlayer.duration, audioPlayer.currentTime + 10);
});

// Update the current track when the audio ends
audioPlayer.addEventListener("ended", () => {
  if (currentTrack < playlist.length - 1) {
    currentTrack++;
    loadTrack(playlist[currentTrack]);
  } else {
    audioPlayer.pause();
    playButton.textContent = "Play";
  }
});

// Function to handle selected files (folder upload)
function handleFileSelect(event) {
  const files = event.target.files;
  playlist = [];
  currentTrack = 0;

  for (let i = 0; i < files.length; i++) {
    const fileExtension = files[i].name.split('.').pop().toLowerCase();
    if (fileExtension.match(/^(mp3|m4b|ogg|wav|aac|flac|m4a|m4p|aax|aa)$/)) {
      playlist.push(files[i]);
    }
  }

  if (playlist.length > 0) {
    loadTrack(playlist[currentTrack]);
    displayPlaylist();
  }

  audioFileInput.value = "";
}

// Function to open file dialog when the button is clicked
function uploadMusic() {
  audioFileInput.click();
}
</script>

	



				
</body>
</html>