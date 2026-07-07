<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<?php
$comicFolder = 'Gallerium'; // Main folder containing category folders

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
    if (isset($_GET['q'])) {
        header('Content-Type: application/json');
        echo json_encode(search_comics($_GET['q']));
        exit;
    } elseif (isset($_GET['preview'])) {
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
  <input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
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
  <div class="close-btn" onclick="closeOverlay()" title="Close">✖️</div>
  <img id="overlayImg" src="" alt="Comic Image">
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
let debounceTimer;

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
    img.onclick = () => openSlideshow(item.subfolder);
    results.appendChild(img);
  });
}

// Live search as you type (debounced to avoid excessive requests)
document.getElementById("searchInput").addEventListener("input", function() {
  const query = this.value.trim();
  clearTimeout(debounceTimer);
  
  if (query === '') {
    // Clear results when search box is empty
    document.getElementById("results").innerHTML = '';
    return;
  }
  
  // Wait 300ms after user stops typing before fetching
  debounceTimer = setTimeout(() => {
    fetch(`?ajax=1&q=${encodeURIComponent(query)}`)
      .then(r => r.json())
      .then(data => renderResults(data))
      .catch(err => {
        console.error(err);
        document.getElementById("results").innerHTML = "<p style='text-align:center;'>Error loading results.</p>";
      });
  }, 300);
});

function doPreview() {
  fetch(`?ajax=1&preview=1`)
    .then(r => r.json())
    .then(data => renderResults(data));
}

function loadCategory(cat) {
  fetch(`?ajax=category&cat=${encodeURIComponent(cat)}`)
    .then(r => r.json())
    .then(data => renderResults(data));
}

function openSlideshow(subfolderPath) {
  fetch(`?ajax=subfolderImages&subfolder=${encodeURIComponent(subfolderPath)}`)
    .then(r => r.json())
    .then(images => {
      if (!images.length) return alert("No images found in this folder.");
      currentImages = images;
      currentIndex = 0;
      showOverlayImage(currentImages[currentIndex]);
    });
}

function showOverlayImage(src) {
  const overlay = document.getElementById("overlay");
  const overlayImg = document.getElementById("overlayImg");
  overlayImg.src = src;
  overlayImg.style.transformOrigin = 'center center';
  overlay.classList.remove("zoomed");
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
  const overlay = document.getElementById("overlay");
  overlay.style.display = 'none';
  document.getElementById("overlayImg").src = '';
  overlay.classList.remove("zoomed");
}

// Zoom on click
document.getElementById("overlayImg").onclick = function(e) {
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

// Close when clicking outside image
document.getElementById('overlay').addEventListener('click', e => {
  if (e.target === e.currentTarget) closeOverlay();
});
</script>
</body>
</html>