<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/style.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Soundify</title>
</head> 
<style>

body {
  font-weight: bold;
  margin: 0;
}

#albumArt {
    width: 100%;
    height: 100vh;
    text-align: center;
    overflow: hidden;
    position: relative;
}

#albumArt img {
    width: 100%;
    height: 100%;
    border: none;
    object-fit: cover; /* Keeps aspect ratio, fills container */
    display: block;
    margin: 0 auto;
    cursor: pointer;
    position: absolute;
    top: 0;
    left: 0;
}
	
button {
  background-color: transparent;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
  margin-right: 15px; 
}

/* Removes margin from the last button so it stays aligned */
button:last-of-type {
  margin-right: 0;
}
</style>

<body>


<button class="demo w3-opacity w3-hover-opacity-off button"onclick="document.getElementById('folderInput').click()">Load</button>
<div id="setup" style="position:fixed; z-index:99; top:20px; left:20px;">
    <input type="file" id="folderInput" webkitdirectory directory multiple style="display:none;">
</div>

<div id="albumArt">
    <img id="artDisplay" onclick="togglePlayPause()">
</div>

<audio id="audioPlayer" onended="playNextAudio()" hidden></audio>

<button class="video-slider-btn left-side" onclick="playPreviousAudio()">❮</button>
<button class="video-slider-btn right-side" onclick="playNextAudio()">❯</button>


<!-- Footer -->
<footer class="site-footer">
  <div class="footer-content">
    <p class="footer-main">
      © 2026 <?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?> | <a href="../Xtras/Guides.php">Visit Guides for documentation</a>
    </p>
    <p class="footer-specific">
    Powered by <a href="https://github.com/Belthazor86/Elysium.git" target="_blank" rel="noopener noreferrer">Elysium</a> 
    </p>
  </div>
</footer>

<script>
    let combinedPlaylist = [];
    let currentAudioIndex = 0;
    const audioPlayer = document.getElementById('audioPlayer');
    const artDisplay = document.getElementById('artDisplay');
    const folderInput = document.getElementById('folderInput');

    folderInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        const audioMap = {};
        const imageMap = {};

        files.forEach(file => {
            const name = file.name.split('.').slice(0, -1).join('.');
            const ext = file.name.split('.').pop().toLowerCase();
            const url = URL.createObjectURL(file);

            if (['mp3', 'wav', 'ogg'].includes(ext)) audioMap[name] = url;
            if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) imageMap[name] = url;
        });

        combinedPlaylist = Object.keys(audioMap).map(name => ({
            song: audioMap[name],
            image: imageMap[name] || '' 
        }));

        if (combinedPlaylist.length > 0) {
            document.getElementById('setup').style.display = 'none';
            playCurrentAudio();
        }
    });

    function playCurrentAudio() {
        if (combinedPlaylist.length > 0) {
            audioPlayer.src = combinedPlaylist[currentAudioIndex].song;
            artDisplay.src = combinedPlaylist[currentAudioIndex].image;
            audioPlayer.play();
        }
    }

    function playPreviousAudio() {
        currentAudioIndex = (currentAudioIndex > 0) ? currentAudioIndex - 1 : combinedPlaylist.length - 1;
        playCurrentAudio();
    }

    function playNextAudio() {
        currentAudioIndex = (currentAudioIndex < combinedPlaylist.length - 1) ? currentAudioIndex + 1 : 0;
        playCurrentAudio();
    }

    function togglePlayPause() {
        audioPlayer.paused ? audioPlayer.play() : audioPlayer.pause();
    }
</script>
                
</body>
</html>