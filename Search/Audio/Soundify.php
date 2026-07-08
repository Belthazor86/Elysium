<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';

// Build playlist data from actual files in each folder
$folders = [];
$playlistData = [];
$vetraPath = __DIR__ . '/Soundify';
if (is_dir($vetraPath)) {
    $folders = array_filter(scandir($vetraPath), function($item) use ($vetraPath) {
        return is_dir($vetraPath . '/' . $item) && !in_array($item, ['.', '..']);
    });
    foreach ($folders as $folder) {
        $folderPath = $vetraPath . '/' . $folder;
        $mp3Files = glob($folderPath . '/*.mp3');
        
        // ✅ FIX: Sort the files naturally (1, 2, 3, ... 10, 11)
        natsort($mp3Files);
        
        $playlist = [];
        foreach ($mp3Files as $mp3Path) {
            $basename = pathinfo($mp3Path, PATHINFO_FILENAME);
            $imagePath = null;
            // Try common image extensions
            foreach (['jpg', 'jpeg', 'png'] as $ext) {
                $testPath = $folderPath . '/' . $basename . '.' . $ext;
                if (file_exists($testPath)) {
                    $imagePath = $testPath;
                    break;
                }
            }
            if ($imagePath) {
                $playlist[] = [
                    'song'  => 'Soundify/' . $folder . '/' . $basename . '.mp3',
                    'image' => 'Soundify/' . $folder . '/' . basename($imagePath)
                ];
            }
        }
        $playlistData[$folder] = $playlist;
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
  font-size: 18px;
  margin-right: 15px; 
}

/* Removes margin from the last button so it stays aligned */
button:last-of-type {
  margin-right: 0;
}

.buttons {
  margin-top: 15px;
  margin-bottom: 30px;
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  justify-content: center;
}
.buttons button {
  background: #111;
  border: 2px solid #00aaff;
  color: #00aaff;
  padding: 10px 18px;
  font-weight: 600;
  border-radius: 20px;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease;
}
.buttons button:hover {
  background-color: #00aaff;
  color: #000;
}
.buttons button.active {
  background-color: #00aaff;
  color: #000;
  box-shadow: 0 0 15px #00aaffbb;
}

#overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(18, 18, 18, 0.95);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}
#overlay .container {
  position: relative;
  width: 75vw;
  max-width: 1200px;
  height: 75vh;
  background: #000;
  border-radius: 15px;
  box-shadow: 0 0 40px #00ffc3;
  overflow: hidden;
}
#overlay button.closeBtn {
  position: absolute;
  top: 20px;
  right: 20px;
  border: none;
  padding: 6px 10px;
  font-size: 1.2rem;
  border-radius: 6px;
  cursor: pointer;
  background: transparent;
  color: #fff;
  transition: background 0.2s;
  z-index: 2;
}
#overlay button.closeBtn:hover {
  background: #ff1a1a;
}
#overlay iframe {
  width: 100%;
  height: 100%;
  border: none;
  background: #111;
}

</style>

<body>


<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
<audio id="audioPlayer" onended="playNextAudio()" hidden></audio>


<div class="buttons">
  <?php
  // Buttons are generated from the same $folders array
  foreach ($folders as $folder) {
      echo '<button onclick="setCategory(\'' . htmlspecialchars($folder) . '\')">' . htmlspecialchars($folder) . '</button>';
  }
  ?>
</div>

<!-- Pass the playlist data to JavaScript -->
<script>
var folderPlaylists = <?php echo json_encode($playlistData); ?>;
</script>

<div id="overlay">
<div id="albumArt"><img id="artDisplay" onclick="togglePlayPause()"></div>
<button class="video-slider-btn left-side" onclick="playPreviousAudio()">❮</button>
<button class="video-slider-btn right-side" onclick="playNextAudio()">❯</button>
<button class="closeBtn" onclick="closePlayer()">❌</button>
</div>


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
    const overlay = document.getElementById('overlay');

    function setCategory(folderName) {
        overlay.style.display = 'flex';
        combinedPlaylist = folderPlaylists[folderName] || [];
        if (combinedPlaylist.length === 0) {
            alert('No audio files found in this category.');
            overlay.style.display = 'none';
            return;
        }
        currentAudioIndex = 0;
        playCurrentAudio();
    }

    function playCurrentAudio() {
        if (combinedPlaylist.length > 0) {
            const item = combinedPlaylist[currentAudioIndex];
            audioPlayer.src = item.song;
            artDisplay.src = item.image;
            
            audioPlayer.load();
            audioPlayer.play().catch(() => {}); 
        }
    }

    function playNextAudio() {
        currentAudioIndex++;
        if (currentAudioIndex >= combinedPlaylist.length) {
            currentAudioIndex = 0; // Loop back to the first song
        }
        playCurrentAudio();
    }

    function playPreviousAudio() {
        currentAudioIndex--;
        if (currentAudioIndex < 0) {
            currentAudioIndex = combinedPlaylist.length - 1; // Go to last song
        }
        playCurrentAudio();
    }

    function togglePlayPause() {
        audioPlayer.paused ? audioPlayer.play() : audioPlayer.pause();
    }
    
    function closePlayer() {
        overlay.style.display = 'none';
        audioPlayer.pause();
        audioPlayer.src = ""; 
    }

    artDisplay.addEventListener('load', function() {
    if (this.naturalHeight > this.naturalWidth) {
        this.style.objectFit = 'contain';
    } else {
        this.style.objectFit = 'cover';
    }
});
</script>

                
</body>
</html>