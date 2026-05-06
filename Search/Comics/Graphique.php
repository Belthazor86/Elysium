<?php
$comicFolder = 'Graphique'; 

function search_comics($query, $all = false) {
    global $comicFolder;
    $q = strtolower($query);
    $results = [];
    $rootDir = __DIR__ . '/' . $comicFolder;

    if (!is_dir($rootDir)) return $results;

    foreach (glob($rootDir . '/*') as $folderPath) {
        if (!is_dir($folderPath)) continue;
        $folderName = basename($folderPath);

        if (!$all && stripos($folderName, $q) === false) continue;

        $postersPath = $folderPath . '/posters';
        if (!is_dir($postersPath)) continue;

        $files = array_filter(scandir($postersPath), function($f) {
            return preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $f);
        });

        usort($files, function($a, $b) {
            preg_match('/^(\d+)/', $a, $matchesA);
            preg_match('/^(\d+)/', $b, $matchesB);
            $numA = isset($matchesA[1]) ? intval($matchesA[1]) : 0;
            $numB = isset($matchesB[1]) ? intval($matchesB[1]) : 0;
            return $numA <=> $numB;
        });

        foreach ($files as $file) {
            $results[] = "$comicFolder/$folderName/posters/$file";
        }
    }
    return $results;
}

if (isset($_GET['ajax'])) {
    if (isset($_GET['q'])) {
        header('Content-Type: application/json');
        echo json_encode(search_comics($_GET['q']));
        exit;
    } elseif (isset($_GET['preview'])) {
        header('Content-Type: application/json');
        echo json_encode(search_comics('', true));
        exit;
    }
}

if (isset($_GET['ajax']) && $_GET['ajax'] === 'images' && isset($_GET['folder'])) {
    $folder = __DIR__ . '/' . $_GET['folder'];
    $files = [];
    if (is_dir($folder)) {
        foreach (scandir($folder) as $file) {
            if (preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $file)) {
                $files[] = $_GET['folder'] . '/' . $file;
            }
        }
        sort($files, SORT_NATURAL);
    }
    header('Content-Type: application/json');
    echo json_encode($files);
    exit;
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/style.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Graphique</title>
</head>

<style>
body {
  margin: 0;
  padding: 20px;
  overflow:scroll;
}

.search-box {
  text-align: center;
  margin-bottom: 30px;
}

input[type="text"] {
  width: 300px;
  padding: 12px;
  font-size: 1rem;
  border-radius: 8px;
  border: none;
  background-color: #222;
  color: #fff;
  box-shadow: 0 0 6px #00aaffaa;
}

button {
  margin-left: 10px;
  padding: 12px 20px;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  background-color: #00aaff;
  color: #000;
  font-weight: bold;
  cursor: pointer;
}

#results {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 15px;
}

.thumb {
  width: 180px;
  border: 2px solid #333;
  border-radius: 6px;
  cursor: pointer;
  transition: transform 0.2s;
}

.thumb:hover {
  transform: scale(1.1);
  border-color: crimson;
}

.overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.95);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.overlay img {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: contain;
  cursor: zoom-in;
  transition: transform 0.3s ease;
  transform-origin: center center;
  background: #000;
  border-radius: 15px;
  box-shadow: 0 0 40px #00ffc3;
}

.overlay.zoomed img {
  transform: scale(2);
  cursor: zoom-out;
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
.fullscreen-btn { right: 90px; }
</style>

<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<div class="search-box">
  <input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
  <button onclick="doSearch()">Search</button>
  <button onclick="doPreview()" style="background-color: #ff0055; color: #fff;">Preview All</button>
</div>

<div id="results"></div>

<div id="overlay" class="overlay">
  <div class="close-btn" onclick="closeOverlay()">✖️</div>
  <img id="overlayImg" src="">
  <button class="video-slider-btn left-side" style="background-color: #00aaff;" onclick="showPreviousImage()">❮</button>
  <button class="video-slider-btn right-side" style="background-color: #00aaff;" onclick="showNextImage()">❯</button>
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
let currentImages = [];
let currentIndex = 0;

function renderResults(data) {
  const results = document.getElementById("results");
  results.innerHTML = '';
  data.forEach(imagePath => {
    const img = document.createElement('img');
    img.src = imagePath;
    img.className = 'thumb';
    img.onclick = () => openSlideshow(imagePath);
    results.appendChild(img);
  });
}

function doSearch() {
  const q = document.getElementById("searchInput").value.trim();
  if (!q) return;
  fetch(`?ajax=1&q=${encodeURIComponent(q)}`).then(r => r.json()).then(data => renderResults(data));
}

function doPreview() {
  fetch(`?ajax=1&preview=1`).then(r => r.json()).then(data => renderResults(data));
}

function openSlideshow(imagePath) {
  const parts = imagePath.split('/');
  const gameFolder = parts[1];
  const imgFile = parts[3];
  const folderNum = imgFile.split('.')[0];
  const wallpapersPath = `${parts[0]}/${gameFolder}/wallpapers/${folderNum}/`;

  fetch(`?ajax=images&folder=${encodeURIComponent(wallpapersPath)}`)
    .then(r => r.json())
    .then(images => {
      currentImages = images;
      currentIndex = 0;
      showOverlayImage(currentImages[currentIndex]);
    });
}

function showOverlayImage(src) {
  const overlay = document.getElementById("overlay");
  const img = document.getElementById("overlayImg");
  img.src = src;
  overlay.classList.remove("zoomed");
  img.style.transformOrigin = 'center center';
  overlay.style.display = 'flex';
}

function showNextImage() {
  if (!currentImages.length) return;
  currentIndex = (currentIndex + 1) % currentImages.length;
  showOverlayImage(currentImages[currentIndex]);
}

function showPreviousImage() {
  if (!currentImages.length) return;
  currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
  showOverlayImage(currentImages[currentIndex]);
}

function closeOverlay() {
  document.getElementById("overlay").style.display = 'none';
}

// Zoom trigger
document.getElementById("overlayImg").onclick = function (e) {
  const overlay = document.getElementById("overlay");
  if (overlay.classList.contains("zoomed")) {
    overlay.classList.remove("zoomed");
    this.style.transformOrigin = 'center center';
  } else {
    const rect = this.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    this.style.transformOrigin = `${x}% ${y}%`;
    overlay.classList.add("zoomed");
  }
};

document.getElementById('overlay').onclick = e => { if (e.target.id === 'overlay') closeOverlay(); };
</script>
</body>
</html>