

<?php
function searchGames($baseDir, $query) {
    $results = [];
    $firstLevelDirs = array_filter(glob($baseDir . '/*'), 'is_dir');
    foreach ($firstLevelDirs as $firstDir) {
        $secondLevelDirs = array_filter(glob($firstDir . '/*'), 'is_dir');
        foreach ($secondLevelDirs as $secondDir) {
            $files = glob($secondDir . '/*.{swf,php}', GLOB_BRACE);
            foreach ($files as $file) {
                if (stripos($file, $query) !== false) {
                    $relativePath = str_replace('\\', '/', substr($file, strlen($baseDir)));
                    // Show file name exactly as is, just remove the extension
                    $name = preg_replace('/\.(php|swf)$/i', '', basename($file));
                    $results[] = ['name' => $name, 'path' => 'Flashbot' . $relativePath];
                }
            }
        }
    }
    return $results;
}

if (isset($_GET['search'])) {
    header('Content-Type: application/json');
    echo json_encode(searchGames(__DIR__ . '/Flashbot', $_GET['search']));
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
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Flashbot</title>
<script src="https://unpkg.com/@ruffle-rs/ruffle"></script>
<style>
body {
  margin: 0;
  padding: 20px;
  overflow:scroll;
}

input[type="text"] {
  width: 100%;
  max-width: 400px;
  display: block;
  margin: 0 auto 30px auto;
  padding: 12px 20px;
  font-size: 18px;
  border-radius: 25px;
  border: 2px solid #00ffcc;
  background: #222;
  color: #fff;
}
input[type="text"]:focus {
  outline: none;
  border-color: #00ffc3;
  box-shadow: 0 0 10px #00ffc3;
}
#results {
  max-width: 600px;
  overflow:scroll;
  margin: 0 auto;
}
.game {
  background: #1f1f1f;
  margin: 8px 0;
  padding: 15px 20px;
  border-radius: 12px;
  cursor: pointer;
  font-size: 18px;
  border: 1px solid transparent;
}
.game:hover {
  background: #00ffc3;
  color: #121212;
  border-color: #00ffc3;
  box-shadow: 0 0 10px #00ffc3;
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
#game-container {
  position: relative;
  width: 75vw;
  max-width: 1200px;
  height: 75vh;
  background: #000;
  border-radius: 15px;
  box-shadow: 0 0 40px #00ffc3;
  overflow: hidden;
}
#container {
  width: 100%;
  height: 100%;
}
ruffle-player, iframe {
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 15px;
  display: block;
}
.close-btn, .fullscreen-btn {
  position: absolute;
  top: 15px;
  font-weight: bold;
  cursor: pointer;
  user-select: none;
  color: #00ffc3;
  text-shadow: 0 0 8px #00ffc3;
  padding: 5px 10px;
  border-radius: 6px;
  background-color: rgba(0, 0, 0, 0.5);
  font-family: monospace;
  font-size: 20px;
  z-index: 10000;
}
.close-btn { right: 20px; }
.close-btn:hover {
  color: #00a07a;
  background-color: rgba(0, 255, 195, 0.2);
}
.fullscreen-btn {
  right: 10px;
  font-size: 18px;
}
.fullscreen-btn:hover {
  color: #00a07a;
  background-color: rgba(0, 255, 195, 0.2);
}

</style>
</head>
<body>



<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>


<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />


<div id="results"></div>
<div id="overlay">
  <div id="game-container">
    <div class="fullscreen-btn" onclick="toggleFullscreen()" title="Toggle Fullscreen">[ ⛶ ]</div>
    <div id="container"></div>
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
const searchInput = document.getElementById('searchInput');
const resultsDiv = document.getElementById('results');
const overlay = document.getElementById('overlay');
const container = document.getElementById('container');

searchInput.addEventListener('input', async () => {
  const q = searchInput.value.trim();
  if (!q) {
    resultsDiv.innerHTML = '';
    return;
  }
  const res = await fetch('?search=' + encodeURIComponent(q));
  const games = await res.json();

  resultsDiv.innerHTML = '';
  if (!games.length) {
    resultsDiv.innerHTML = '<p>No results found.</p>';
    return;
  }
  games.forEach(game => {
    const div = document.createElement('div');
    div.className = 'game';
    div.textContent = game.name;
    div.onclick = () => openGame(game.path);
    resultsDiv.appendChild(div);
  });
});

function openGame(path) {
  container.innerHTML = '';
  overlay.style.display = 'flex';
  const ext = path.split('.').pop().toLowerCase();
  if (ext === 'swf') {
    const ruffle = window.RufflePlayer.newest();
    const player = ruffle.createPlayer();
    container.appendChild(player);
    player.load(path);
  } else if (ext === 'php') {
    const iframe = document.createElement('iframe');
    iframe.src = path;
    iframe.style.width = '100%';
    iframe.style.height = '100%';
    iframe.style.border = 'none';
    iframe.style.display = 'block';

    // Try injecting CSS into iframe content to fix sizing (same origin only)
    iframe.onload = () => {
      try {
        const doc = iframe.contentDocument || iframe.contentWindow.document;
        const style = doc.createElement('style');
        style.textContent = `
          html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            height: 100% !important;
            overflow: hidden !important;
          }
        `;
        doc.head.appendChild(style);
      } catch (e) {
        // Cross-origin iframe, can't inject CSS
      }
    };

    container.appendChild(iframe);
  }
}

function closeOverlay() {
  overlay.style.display = 'none';
  container.innerHTML = '';
}

function toggleFullscreen() {
  const elem = document.getElementById('game-container');
  if (!document.fullscreenElement) {
    elem.requestFullscreen?.() || elem.webkitRequestFullscreen?.() || elem.mozRequestFullScreen?.() || elem.msRequestFullscreen?.();
  } else {
    document.exitFullscreen?.();
  }
}

  // Close overlay by clicking outside container
  document.getElementById('overlay').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeOverlay();
  });
</script>




</body>
</html>
