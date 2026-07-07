<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<?php
$comicFolder = 'Imagen'; // Main folder containing category folders

function get_images_from_subfolder($subfolderPath) {
    $images = [];
    if (!is_dir($subfolderPath)) return $images;
    $files = scandir($subfolderPath);
    foreach ($files as $file) {
        if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
            $images[] = $subfolderPath . '/' . $file;
        }
    }
    sort($images, SORT_NATURAL);
    return $images;
}

function search_comics($query, $all = false) {
    global $comicFolder;
    $q = strtolower($query);
    $results = [];
    $rootDir = __DIR__ . '/' . $comicFolder;

    if (!is_dir($rootDir)) return $results;

    // Dynamically scan category folders
    $categories = array_filter(glob($rootDir . '/*', GLOB_ONLYDIR), 'is_dir');
    $categories = array_map('basename', $categories);

    foreach ($categories as $cat) {
        $catPath = $rootDir . '/' . $cat;
        foreach (glob($catPath . '/*', GLOB_ONLYDIR) as $itemPath) {
            $itemName = basename($itemPath);
            if (!$all && stripos($itemName, $q) === false) continue;

            foreach (scandir($itemPath) as $sub) {
                if ($sub === '.' || $sub === '..') continue;
                $subPath = $itemPath . '/' . $sub;
                if (!is_dir($subPath)) continue;

                $images = get_images_from_subfolder($subPath);
                if (empty($images)) continue;

                $cover = $images[0];
                $coverRelative = str_replace(__DIR__ . '/', '', $cover);
                $results[] = [
                    'cover' => $coverRelative,
                    'subfolder' => "$comicFolder/$cat/$itemName/$sub"
                ];
            }
        }
    }
    return $results;
}

// AJAX endpoints
if (isset($_GET['ajax'])) {
    if (isset($_GET['preview'])) {
        header('Content-Type: application/json');
        echo json_encode(search_comics('', true));
        exit;
    } elseif ($_GET['ajax'] === 'subfolderImages' && isset($_GET['subfolder'])) {
        $subfolderPath = __DIR__ . '/' . $_GET['subfolder'];
        $images = get_images_from_subfolder($subfolderPath);
        $relativeImages = array_map(function($img) {
            return str_replace(__DIR__ . '/', '', $img);
        }, $images);
        header('Content-Type: application/json');
        echo json_encode($relativeImages);
        exit;
    } elseif ($_GET['ajax'] === 'category' && isset($_GET['cat'])) {
        $cat = $_GET['cat'];
        $catPath = __DIR__ . '/' . $comicFolder . '/' . $cat;
        if (!is_dir($catPath)) {
            echo json_encode([]);
            exit;
        }
        $results = [];
        foreach (glob($catPath . '/*', GLOB_ONLYDIR) as $itemPath) {
            $itemName = basename($itemPath);
            foreach (scandir($itemPath) as $sub) {
                if ($sub === '.' || $sub === '..') continue;
                $subPath = $itemPath . '/' . $sub;
                if (!is_dir($subPath)) continue;
                $images = get_images_from_subfolder($subPath);
                if (empty($images)) continue;
                $cover = $images[0];
                $coverRelative = str_replace(__DIR__ . '/', '', $cover);
                $results[] = [
                    'cover' => $coverRelative,
                    'subfolder' => "$comicFolder/$cat/$itemName/$sub"
                ];
            }
        }
        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }
}

// Dynamically get category folder names for the buttons
$rootDir = __DIR__ . '/' . $comicFolder;
$categoryFolders = [];
if (is_dir($rootDir)) {
    $allDirs = glob($rootDir . '/*', GLOB_ONLYDIR);
    $categoryFolders = array_map('basename', $allDirs);
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
<title>Imagen</title>
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

.video-slider-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0,0,0,0.6);
  color: white;
  border: none;
  font-size: 30px;
  padding: 10px 20px;
  cursor: pointer;
  z-index: 10001;
  border-radius: 8px;
}
.video-slider-btn.left-side { left: 20px; }
.video-slider-btn.right-side { right: 20px; }
</style>

<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>


<div class="search-box">
  <input type="text" id="searchInput" placeholder="Filter results..." autocomplete="off" />
  <button onclick="doPreview()">All</button>
</div>


<div class="buttons">
  <?php foreach ($categoryFolders as $folder): ?>
    <button onclick="loadCategory('<?php echo htmlspecialchars($folder); ?>')">
      <?php echo htmlspecialchars($folder); ?>
    </button>
  <?php endforeach; ?>
</div>


<div id="results"></div>

<div id="overlay" class="overlay">
  <div class="close-btn" onclick="closeOverlay()">✖️</div>
  <img id="overlayImg" src="">
  <button class="video-slider-btn left-side" onclick="showPreviousImage()">❮</button>
  <button class="video-slider-btn right-side" onclick="showNextImage()">❯</button>
</div>

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
  if (!data.length) {
    results.innerHTML = "<p style='text-align:center;'>No items found.</p>";
    return;
  }
  data.forEach(item => {
    const img = document.createElement('img');
    img.src = item.cover;
    img.className = 'thumb';
    img.setAttribute('data-subfolder', item.subfolder); // needed for filtering
    img.onclick = () => openSlideshow(item.subfolder);
    results.appendChild(img);
  });
}

// Client‑side filtering – show/hide thumbnails as you type
document.getElementById("searchInput").addEventListener("input", function() {
  const query = this.value.trim().toLowerCase();
  const thumbnails = document.querySelectorAll("#results .thumb");
  thumbnails.forEach(img => {
    const subfolder = img.getAttribute('data-subfolder') || '';
    img.style.display = (query === '' || subfolder.toLowerCase().includes(query)) ? '' : 'none';
  });
});

function loadCategory(cat) {
  fetch(`?ajax=category&cat=${encodeURIComponent(cat)}`)
    .then(r => r.json())
    .then(data => renderResults(data));
}

function doPreview() {
  fetch(`?ajax=1&preview=1`)
    .then(r => r.json())
    .then(data => renderResults(data));
}

function openSlideshow(subfolderPath) {
  fetch(`?ajax=subfolderImages&subfolder=${encodeURIComponent(subfolderPath)}`)
    .then(r => r.json())
    .then(images => {
      if (!images.length) return alert("No images found.");
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