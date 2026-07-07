<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<?php
if (isset($_GET['embedScript'])) {
    $path = $_GET['embedScript'];
    $full = realpath(__DIR__ . '/' . $path);
    $allowed = realpath(__DIR__ . '/Wavora');

    if ($full && strpos($full, $allowed) === 0 && file_exists($full)) {
        $scriptContent = file_get_contents($full);
        header('Content-Type: text/html');
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
          <meta charset='UTF-8'>
          <title>Script Runner</title>
          <style>
            html, body {
              margin: 0; padding: 0; overflow: hidden;
              width: 100vw; height: 100vh;
              background: #111;
              color: #eee;
              font-family: sans-serif;
            }
            canvas, #mainContent {
              display: block;
              width: 100vw !important;
              height: 100vh !important;
              box-sizing: border-box;
            }
          </style>
        </head>
        <body>
          <script>
            function makeCanvasFullScreen() {
              const canvas = document.querySelector('canvas');
              if (!canvas) return;
              function resize() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
              }
              window.addEventListener('resize', resize);
              resize();
            }
            makeCanvasFullScreen();
            " . $scriptContent . "
          </script>
        </body>
        </html>";
    } else {
        http_response_code(403);
        echo "Access Denied";
    }
    exit;
}

// ============================================================
// AJAX: Search media folders → one entry per folder with all audio
// ============================================================
if (isset($_GET['category'])) {
    $searchRaw = isset($_GET['search']) ? $_GET['search'] : '';
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($searchRaw)));
    $category = basename($_GET['category']);
    $baseDir = __DIR__ . "/Wavora/$category";
    $results = [];

    if (is_dir($baseDir)) {
        foreach (scandir($baseDir) as $titleFolder) {
            if ($titleFolder === '.' || $titleFolder === '..') continue;
            $titlePath = "$baseDir/$titleFolder";
            if (!is_dir($titlePath)) continue;

            $normalizedTitle = strtolower(str_replace([' ', '_', '-'], '', $titleFolder));
            if ($query !== '' && strpos($normalizedTitle, $query) === false) continue;

            $imageFiles = glob("$titlePath/*.{jpg,jpeg,png,gif,webp,bmp}", GLOB_BRACE);
            $audioFiles = glob("$titlePath/*.{mp3,wav,ogg,m4a,aac,flac}", GLOB_BRACE);

            if (empty($imageFiles) || empty($audioFiles)) continue;

            natsort($imageFiles);
            natsort($audioFiles);

            // Use first image as cover
            $coverUrl = "Wavora/$category/$titleFolder/" . basename($imageFiles[0]);

            // Collect all audio file URLs
            $audioUrls = [];
            foreach ($audioFiles as $af) {
                $audioUrls[] = "Wavora/$category/$titleFolder/" . basename($af);
            }

            $results[] = [
                'title' => $titleFolder,
                'image' => $coverUrl,
                'audio' => $audioUrls,   // array of audio URLs
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}
?>

<!DOCTYPE html>
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
<title>Wavora</title>
<style>
body {
  padding: 20px;
  margin: 0;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
}
input#searchInput {
  width: 320px;
  max-width: 90vw;
  padding: 12px 18px;
  font-size: 1.1rem;
  border-radius: 8px;
  border: none;
  background-color: #222;
  color: #fff;
  box-shadow: 0 0 6px #00aaffaa;
  transition: box-shadow 0.3s ease;
}
input#searchInput:focus {
  outline: none;
  box-shadow: 0 0 12px #00aaffee;
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
.gallery {
  display: flex;
  flex-wrap: wrap;
  gap: 18px;
  justify-content: center;
  max-width: 1200px;
  width: 100%;
}
.gallery img {
  width: 150px;
  border-radius: 12px;
  cursor: pointer;
  box-shadow: 0 0 6px #00aaffaa;
  transition: transform 0.25s ease, box-shadow 0.3s ease;
}
.gallery img:hover {
  transform: scale(1.07);
  box-shadow: 0 0 20px #00aaffee;
}

/* --- Overlay & controls --- */
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
  display: flex;
  justify-content: center;
  align-items: center;
  overflow: hidden;
}
#overlay .container img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}
#overlay button.closeBtn {
  position: absolute;
  top: 10px;
  right: 10px;
  border: none;
  padding: 6px 10px;
  font-size: 1.2rem;
  border-radius: 6px;
  cursor: pointer;
  background: black;
  color: #fff;
  transition: background 0.2s;
  z-index: 2;
}
#overlay button.closeBtn:hover {
  background: #ff1a1a;
}

/* --- Audio control bar --- */
.audio-controls {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 15px;
  background: rgba(0, 0, 0, 0.75);
  padding: 10px 20px;
  border-radius: 30px;
  backdrop-filter: blur(6px);
  z-index: 2;
}
.ctrl-btn {
  background: #111;
  border: 2px solid #00aaff;
  color: #00aaff;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  font-size: 1.3rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
  padding: 0;
  line-height: 1;
}
.ctrl-btn:hover {
  background-color: #00aaff;
  color: #000;
  box-shadow: 0 0 15px #00aaffee;
}
.ctrl-btn:disabled {
  opacity: 0.35;
  cursor: default;
  background: #111;
  color: #555;
  border-color: #555;
}
.ctrl-btn:disabled:hover {
  background: #111;
  color: #555;
  box-shadow: none;
}
</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />

<div class="buttons">
  <?php
  $vetraPath = __DIR__ . '/Wavora';
  if (is_dir($vetraPath)) {
      $folders = array_filter(scandir($vetraPath), function($item) use ($vetraPath) {
          return is_dir($vetraPath . '/' . $item) && !in_array($item, ['.', '..']);
      });
      foreach ($folders as $folder) {
          echo '<button onclick="setCategory(\'' . htmlspecialchars($folder) . '\', event)">' . htmlspecialchars($folder) . '</button>';
      }
  }
  ?>
</div>

<div class="gallery" id="gallery"></div>

<!-- Overlay with cover image + transport controls -->
<div id="overlay">
  <div class="container">
    <button class="closeBtn" onclick="closeOverlay()">✕</button>
    <img id="overlayImage" src="" alt="Cover" />
    <audio id="overlayAudio" style="display:none;"></audio>

    <div class="audio-controls">
      <button id="prevBtn" class="ctrl-btn" title="Previous">⏮</button>
      <button id="playPauseBtn" class="ctrl-btn" title="Play/Pause">▶</button>
      <button id="nextBtn" class="ctrl-btn" title="Next">⏭</button>
    </div>
  </div>
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
  let currentSearch = '';
  let currentCategory = '';
  let currentPlaylist = [];        // albums: {title, image, audio (array)}
  let currentAlbumIndex = 0;
  let currentTrackIndex = 0;

  const overlayAudio = document.getElementById('overlayAudio');
  const overlayImage = document.getElementById('overlayImage');
  const playPauseBtn = document.getElementById('playPauseBtn');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');

  function getCurrentAlbum() {
    return currentPlaylist[currentAlbumIndex];
  }

  function updatePlayPauseButton() {
    playPauseBtn.innerHTML = overlayAudio.paused ? '▶' : '⏸';
  }

  function updateButtonsState() {
    const album = getCurrentAlbum();
    if (!album) {
      prevBtn.disabled = true;
      nextBtn.disabled = true;
      return;
    }
    prevBtn.disabled = (currentTrackIndex <= 0);
    nextBtn.disabled = (currentTrackIndex >= album.audio.length - 1);
  }

  playPauseBtn.addEventListener('click', () => {
    if (overlayAudio.paused) {
      overlayAudio.play().catch(console.warn);
    } else {
      overlayAudio.pause();
    }
  });

  prevBtn.addEventListener('click', () => {
    if (currentTrackIndex > 0) {
      currentTrackIndex--;
      loadAndPlayCurrentTrack();
    }
  });

  nextBtn.addEventListener('click', () => {
    const album = getCurrentAlbum();
    if (album && currentTrackIndex < album.audio.length - 1) {
      currentTrackIndex++;
      loadAndPlayCurrentTrack();
    }
  });

  overlayAudio.addEventListener('play', updatePlayPauseButton);
  overlayAudio.addEventListener('pause', updatePlayPauseButton);

  document.getElementById('searchInput').addEventListener('input', e => {
    currentSearch = e.target.value.trim();
    if (currentCategory) searchMedia();
  });

  function setCategory(cat, event) {
    currentCategory = cat;
    document.querySelectorAll('.buttons button').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
    closeOverlay();
    searchMedia();
  }

  async function searchMedia() {
    const resp = await fetch(`?search=${encodeURIComponent(currentSearch)}&category=${encodeURIComponent(currentCategory)}`);
    const data = await resp.json();

    const gallery = document.getElementById('gallery');
    gallery.innerHTML = '';

    if (data.length === 0) {
      gallery.textContent = 'No matches found.';
      return;
    }

    currentPlaylist = data;

    data.forEach((album, index) => {
      const img = document.createElement('img');
      img.src = album.image;
      img.title = album.title;
      img.onclick = () => {
        openAlbum(index);
      };
      gallery.appendChild(img);
    });
  }

  function openAlbum(albumIndex) {
    if (!currentPlaylist.length) return;
    document.getElementById('overlay').style.display = 'flex';
    currentAlbumIndex = albumIndex;
    currentTrackIndex = 0;
    loadAndPlayCurrentTrack();
  }

  function loadAndPlayCurrentTrack() {
    const album = getCurrentAlbum();
    if (!album) {
      closeOverlay();
      return;
    }

    overlayImage.src = album.image;
    overlayAudio.src = album.audio[currentTrackIndex];
    overlayAudio.load();

    overlayAudio.onended = () => {
      currentTrackIndex++;
      if (currentTrackIndex < album.audio.length) {
        // Next track in the same album
        loadAndPlayCurrentTrack();
      } else {
        // Album finished – try next album
        currentAlbumIndex++;
        if (currentAlbumIndex < currentPlaylist.length) {
          currentTrackIndex = 0;
          loadAndPlayCurrentTrack();
        } else {
          closeOverlay();
        }
      }
    };

    overlayAudio.play().then(() => {
      updatePlayPauseButton();
    }).catch(e => {
      console.warn('Audio play failed:', e);
    });

    updateButtonsState();
  }

  function closeOverlay() {
    const overlay = document.getElementById('overlay');
    overlayAudio.pause();
    overlayAudio.removeAttribute('src');
    overlayAudio.onended = null;
    overlay.style.display = 'none';
    updatePlayPauseButton();
    updateButtonsState();
  }

  document.getElementById('overlay').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeOverlay();
  });
</script>

</body>
</html>