

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
<title>Pandora</title>
</head>	
<style>
		
body {
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  overflow-y: auto;
}

audio {
  width: 100%;
  max-width: 400px;
  background-color: #333;
  border-radius: 10px;
  padding: 10px;
  margin-top: 20px;
}

img {
  width: 250px;
  height: 250px;
  object-fit: cover;
  margin-top: 20px;
  border-radius: 10px;
  border: 2px solid #fff;
}

.button-container {
  margin-top: 20px;
}

button {
  background-color: #1f1f1f;
  color: #fff;
  border: none;
  padding: 10px 20px;
  font-size: 1rem;
  cursor: pointer;
  margin: 10px;
  border-radius: 5px;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: royalblue;
}

#dropArea {
  width: 100%;
  max-width: 350px;
  height: 100px;
  border: 2px dashed #ccc;
  margin: 20px auto;
  text-align: center;
  line-height: 100px;
  color: #ccc;
  background-color: #1f1f1f;
  cursor: pointer;
  border-radius: 10px;
  font-size: 1.2rem;
}

#dropArea.active {
  border-color: #4CAF50;
  color: #4CAF50;
}

/* Responsive Design */
@media (max-width: 768px) {
  img {
    width: 200px;
    height: 200px;
  }

  audio {
    width: 100%;
  }
}
		
</style>
	


<body>
	
		

  
<!-- Drag and Drop Area -->
<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
<div id="dropArea">Click, Drag or Drop to select folder</div>

<!-- File Upload Button (Hidden) -->
<input type="file" id="uploadInput" webkitdirectory directory multiple style="display:none;"><br><br>

<!-- Music Player and Controls -->
<img id="coverImage" src="Sahara/Pandora.jpg" alt="Cover will appear here"><br>
<audio id="audioPlayer" controls></audio><br>

<div class="button-container">
  <button onclick="prevTrack()">❮</button>
  <button id="shuffleBtn" onclick="shuffleTracks()">Shuffle: Off</button>
  <button id="playPauseBtn" onclick="togglePlayPause()">▷</button>
  <button id="loopBtn" onclick="toggleLoop()">Loop: Off</button>
  <button onclick="nextTrack()">❯</button>
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
  const uploadInput = document.getElementById('uploadInput');
  const dropArea = document.getElementById('dropArea');
  const audioPlayer = document.getElementById('audioPlayer');
  const coverImage = document.getElementById('coverImage');
  const playPauseBtn = document.getElementById('playPauseBtn');
  const shuffleBtn = document.getElementById('shuffleBtn');
  const loopBtn = document.getElementById('loopBtn');

  let tracks = [];
  let currentIndex = 0;
  let isLooping = false;
  let isShuffled = false;

  dropArea.addEventListener('click', () => {
    uploadInput.click();
  });

  uploadInput.addEventListener('change', () => {
    const files = Array.from(uploadInput.files);
    processFiles(files);
  });

  dropArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropArea.classList.add('active');
  });

  dropArea.addEventListener('dragleave', () => {
    dropArea.classList.remove('active');
  });

  dropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dropArea.classList.remove('active');
    const files = Array.from(e.dataTransfer.files);
    processFiles(files);
  });

function processFiles(files) {
  const musicFiles = files.filter(f => f.type.startsWith('audio/'));
  const imageFiles = files.filter(f => f.type.startsWith('image/'));

  if (musicFiles.length === 0) {
    alert("No music files found in selected folder.");
    return;
  }

  // Map audio to matching images first
  tracks = musicFiles
    .map(audio => {
      const audioName = audio.name.toLowerCase().replace(/\.[^/.]+$/, "");
      const audioKey = (audioName.match(/\d+/) ? audioName.match(/\d+/)[0] : audioName).trim();

      const image = imageFiles.find(img => {
        const imgName = img.name.toLowerCase().replace(/\.[^/.]+$/, "");
        const imgKey = (imgName.match(/\d+/) ? imgName.match(/\d+/)[0] : imgName).trim();
        return imgKey === audioKey;
      });

      return { audio, image, sortKey: audioKey };
    })
    // Remove any audio without matching image
    .filter(track => track.image)
    // Sort numerically/alphabetically by sortKey
    .sort((a, b) => {
      const numA = parseInt(a.sortKey);
      const numB = parseInt(b.sortKey);
      if (!isNaN(numA) && !isNaN(numB)) return numA - numB;
      return a.sortKey.localeCompare(b.sortKey, undefined, { numeric: true });
    });

  if (tracks.length > 0) {
    currentIndex = 0;
    loadTrack(currentIndex);
  } else {
    alert("No matching audio and image pairs found.");
  }
}


  function loadTrack(index) {
    const track = tracks[index];
    if (track) {
      audioPlayer.src = URL.createObjectURL(track.audio);
      audioPlayer.play();
      playPauseBtn.textContent = '∥';
      if (track.image) {
        coverImage.src = URL.createObjectURL(track.image);
      } else {
        coverImage.src = '';
      }
    }
  }

  audioPlayer.addEventListener('ended', () => {
    nextTrack();
  });

  function nextTrack() {
    if (tracks.length > 0) {
      currentIndex = (currentIndex + 1) % tracks.length;
      loadTrack(currentIndex);
    }
  }

  function prevTrack() {
    if (tracks.length > 0) {
      currentIndex = (currentIndex - 1 + tracks.length) % tracks.length;
      loadTrack(currentIndex);
    }
  }

  function shuffleTracks() {
    isShuffled = !isShuffled;
    if (isShuffled) {
      tracks = tracks.sort(() => Math.random() - 0.5);
      shuffleBtn.textContent = "Shuffle: On";
    } else {
      tracks = tracks.sort((a, b) => a.audio.name.localeCompare(b.audio.name, undefined, { numeric: true }));
      shuffleBtn.textContent = "Shuffle: Off";
    }
    loadTrack(0);
  }

  function toggleLoop() {
    isLooping = !isLooping;
    audioPlayer.loop = isLooping;
    loopBtn.textContent = `Loop: ${isLooping ? 'On' : 'Off'}`;
  }

  function togglePlayPause() {
    if (audioPlayer.paused) {
      audioPlayer.play();
      playPauseBtn.textContent = '∥';
    } else {
      audioPlayer.pause();
      playPauseBtn.textContent = '▷';
    }
  }
</script>

	


	

	

</body>
</html>
