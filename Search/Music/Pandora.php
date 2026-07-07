<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<?php
// --- Server-side folder scanning ---
$scanDir = './Pandora/';   // Change this to your desired server folder

if (isset($_GET['scan']) && $_GET['scan'] === '1') {
    header('Content-Type: application/json');

    $audioExts = ['mp3', 'wav', 'ogg', 'flac', 'm4a', 'aac'];
    $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];

    $realDir = realpath($scanDir);
    if (!$realDir || !is_dir($realDir)) {
        echo json_encode(['error' => 'Directory not found']);
        exit;
    }

    // Collect all files
    $files = array_diff(scandir($realDir), ['.', '..']);
    $audioFiles = [];
    $imageFiles = [];

    foreach ($files as $file) {
        $fullPath = $realDir . DIRECTORY_SEPARATOR . $file;
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $name = pathinfo($file, PATHINFO_FILENAME);

        if (in_array($ext, $audioExts)) {
            $audioFiles[] = [
                'path' => $scanDir . $file,   // relative URL
                'name' => $name,
                'key'  => extractKey($name)
            ];
        } elseif (in_array($ext, $imageExts)) {
            $imageFiles[] = [
                'path' => $scanDir . $file,
                'name' => $name,
                'key'  => extractKey($name)
            ];
        }
    }

    // Match audio with images by key (same logic as your JS)
    $tracks = [];
    foreach ($audioFiles as $audio) {
        $img = array_values(array_filter($imageFiles, function($img) use ($audio) {
            return $img['key'] === $audio['key'];
        }));
        if (!empty($img)) {
            $tracks[] = [
                'audio' => $audio['path'],
                'image' => $img[0]['path'],
                'key'   => $audio['key']
            ];
        }
    }

    // Sort numerically then alphabetically
    usort($tracks, function($a, $b) {
        $numA = intval($a['key']);
        $numB = intval($b['key']);
        if (is_numeric($a['key']) && is_numeric($b['key'])) {
            return $numA - $numB;
        }
        return strcasecmp($a['key'], $b['key']);
    });

    echo json_encode($tracks);
    exit;
}

/**
 * Extract a matching key from a filename:
 * - If it contains digits, use the first group of digits
 * - Otherwise, use the whole filename (lowercase)
 */
function extractKey($name) {
    $name = strtolower($name);
    if (preg_match('/\d+/', $name, $m)) {
        return $m[0];
    }
    return $name;
}
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

<!-- Music Player and Controls -->
<img id="coverImage" src="Sahara/Pandora.jpg" alt="Cover will appear here"><br>
<audio id="audioPlayer" controls></audio><br>
<!-- File Upload Button (Hidden) -->
<input type="file" id="uploadInput" webkitdirectory directory multiple style="display:none;">

<div class="button-container">
  <button onclick="prevTrack()">❮</button>
  <button id="shuffleBtn" onclick="shuffleTracks()">Shuffle: Off</button>
  <button id="playPauseBtn" onclick="togglePlayPause()">▷</button>
  <button id="scanServerBtn" style="margin-top:10px;">Scan</button>
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
  const scanServerBtn = document.getElementById('scanServerBtn');

  let tracks = [];
  let originalOrder = [];      // used to restore order after shuffling (server tracks)
  let currentIndex = 0;
  let isLooping = false;
  let isShuffled = false;
  let sourceType = 'local';   // 'local' or 'server'

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

      return { audio, image, sortKey: audioKey, type: 'local' };
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
    sourceType = 'local';
    originalOrder = [...tracks];
    currentIndex = 0;
    loadTrack(currentIndex);
  } else {
    alert("No matching audio and image pairs found.");
  }
}


  function loadTrack(index) {
    const track = tracks[index];
    if (!track) return;

    // Local file (blob) or server URL?
    if (track.type === 'server') {
      audioPlayer.src = track.audioUrl;
      coverImage.src = track.imageUrl;
    } else {
      audioPlayer.src = URL.createObjectURL(track.audio);
      coverImage.src = URL.createObjectURL(track.image);
    }

    audioPlayer.play().catch(() => {}); // play if allowed
    playPauseBtn.textContent = '∥';
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
    if (tracks.length === 0) return;
    isShuffled = !isShuffled;
    if (isShuffled) {
      // Shuffle a copy of the current track array
      for (let i = tracks.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [tracks[i], tracks[j]] = [tracks[j], tracks[i]];
      }
      shuffleBtn.textContent = "Shuffle: On";
    } else {
      // Restore original order
      if (originalOrder.length > 0) {
        tracks = [...originalOrder];
      } else {
        // Fallback for local files: sort by filename
        tracks.sort((a, b) => (a.sortKey || a.audio?.name || '').localeCompare(b.sortKey || b.audio?.name || '', undefined, { numeric: true }));
      }
      shuffleBtn.textContent = "Shuffle: Off";
    }
    currentIndex = 0;
    loadTrack(currentIndex);
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

  // --- Server folder scanning ---
  scanServerBtn.addEventListener('click', () => {
    fetch('?scan=1')
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
          return;
        }
        if (!Array.isArray(data) || data.length === 0) {
          alert("No audio/image pairs found on the server.");
          return;
        }

        // Transform server response to our track format
        tracks = data.map(item => ({
          audioUrl: item.audio,
          imageUrl: item.image,
          sortKey: item.key,
          type: 'server'
        }));

        sourceType = 'server';
        originalOrder = [...tracks];   // save original sorted order
        isShuffled = false;
        shuffleBtn.textContent = "Shuffle: Off";
        currentIndex = 0;
        loadTrack(currentIndex);
      })
      .catch(err => {
        alert("Error scanning server folder: " + err.message);
      });
  });
</script>

	


	

	

</body>
</html>