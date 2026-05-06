<?php
$comicFolder = 'Gallerium'; // Main folder containing game folders

function search_comics($query, $all = false) {
    global $comicFolder;
    $q = strtolower($query);
    $results = [];
    $rootDir = __DIR__ . '/' . $comicFolder;

    if (!is_dir($rootDir)) return $results;

    foreach (glob($rootDir . '/*') as $folderPath) {
        if (!is_dir($folderPath)) continue;
        $folderName = basename($folderPath);

        // If 'all' is true, we skip the name check
        if (!$all && stripos($folderName, $q) === false) continue;

        $postersPath = $folderPath . '/posters';
        if (!is_dir($postersPath)) continue;

        $files = array_filter(scandir($postersPath), function($f) {
            return preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $f);
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

// Updated AJAX to handle preview
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

// Return images for a specific wallpapers folder
if (isset($_GET['ajax']) && $_GET['ajax'] === 'images' && isset($_GET['folder'])) {
    $folder = __DIR__ . '/' . $_GET['folder'];
    $images = [];
    if (is_dir($folder)) {
        foreach (scandir($folder) as $file) {
            if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                $images[] = $_GET['folder'] . '/' . $file; 
            }
        }
        sort($images, SORT_NATURAL);
    }
    header('Content-Type: application/json');
    echo json_encode($images);
    exit;
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
<title>Gallerium</title>
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

/* Overlay styling */
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
.close-btn:hover {
  color: #00a07a;
  background-color: rgba(0, 255, 195, 0.2);
}
.fullscreen-btn {
  right: 90px;
  font-size: 18px;
}
.fullscreen-btn:hover {
  color: #00a07a;
  background-color: rgba(0, 255, 195, 0.2);
}
                  
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
  <div class="close-btn" onclick="closeOverlay()" title="Close">✖️</div>
  <img id="overlayImg" src="" alt="Comic Image">
  <button class="video-slider-btn left-side" onclick="showPreviousImage()">❮</button>
  <button class="video-slider-btn right-side" onclick="showNextImage()">❯</button>
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

let currentImages = []; // array of images for slideshow
let currentIndex = 0;   // current image index

function renderResults(data) {
  const results = document.getElementById("results");
  results.innerHTML = '';

  if (!data.length) {
    results.innerHTML = "<p style='text-align:center;'>No items found.</p>";
    return;
  }

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
  if (!q) return alert("Enter a keyword to search.");

  fetch(`?ajax=1&q=${encodeURIComponent(q)}`)
    .then(r => r.json())
    .then(data => renderResults(data));
}

function doPreview() {
  fetch(`?ajax=1&preview=1`)
    .then(r => r.json())
    .then(data => renderResults(data));
}

// Open slideshow for wallpapers corresponding to clicked poster
function openSlideshow(imagePath) {
  const pathParts = imagePath.split('/'); // Gallerium/GameName/posters/1.jpg
  const gameFolder = pathParts[1];           // GameName
  const fileName = pathParts[3];             // 1.jpg
  const folderNum = fileName.split('.')[0];   // 1
  const wallpapersPath = `Gallerium/${gameFolder}/wallpapers/${folderNum}/`;

  fetchImages(wallpapersPath).then(images => {
    if (!images.length) return alert("No wallpapers found for this cover.");
    currentImages = images;
    currentIndex = 0;
    showOverlayImage(currentImages[currentIndex]);
  });
}

// Fetch all images from a wallpapers folder
function fetchImages(folderPath) {
  return fetch(`?ajax=images&folder=${encodeURIComponent(folderPath)}`)
    .then(r => r.json())
    .catch(() => []);
}

// Show image in overlay
function showOverlayImage(src) {
  const overlay = document.getElementById("overlay");
  const overlayImg = document.getElementById("overlayImg");
  overlayImg.src = src;
  overlayImg.style.transformOrigin = 'center center';
  overlay.classList.remove("zoomed");
  overlay.style.display = 'flex';
}

// Manual slideshow buttons
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

// Close overlay
function closeOverlay() {
  const overlay = document.getElementById("overlay");
  overlay.style.display = 'none';
  document.getElementById("overlayImg").src = '';
  overlay.classList.remove("zoomed");
}

// Zoom functionality on click
document.getElementById("overlayImg").onclick = function (e) {
  const overlay = document.getElementById("overlay");
  const img = e.target;

  if (overlay.classList.contains("zoomed")) {
    overlay.classList.remove("zoomed");
    img.style.transformOrigin = 'center center';
  } else {
    const rect = img.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    img.style.transformOrigin = `${x}% ${y}%`;
    overlay.classList.add("zoomed");
  }
};

document.getElementById('overlay').addEventListener('click', e => {
  if (e.target === e.currentTarget) closeOverlay();
});
</script>


<script>

function toggleFullscreen() {
  const elem = document.getElementById('overlay');
  if (!document.fullscreenElement) {
    elem.requestFullscreen?.() || elem.webkitRequestFullscreen?.() || elem.mozRequestFullScreen?.() || elem.msRequestFullscreen?.();
  } else {
    document.exitFullscreen?.();
  }
}

</script>

</body>
</html>