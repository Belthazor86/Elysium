<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<?php
$comicFolder = 'Graphique';

/**
 * Search for comics.
 * @param string $query   Search term for game/series folder name (empty for all).
 * @param string $category Category subfolder ('' for all categories).
 * @return array          Array of image paths (first image of each issue folder).
 */
function search_comics($query, $category = '') {
    global $comicFolder;
    $q = strtolower($query);
    $results = [];
    $rootDir = __DIR__ . '/' . $comicFolder;

    if (!is_dir($rootDir)) return $results;

    if ($category !== '') {
        $catPath = $rootDir . '/' . $category;
        if (is_dir($catPath)) {
            find_issues($catPath, $q, $category, $results);
        }
    } else {
        // Scan all subdirectories
        foreach (glob($rootDir . '/*', GLOB_ONLYDIR) as $catPath) {
            $catName = basename($catPath);
            find_issues($catPath, $q, $catName, $results);
        }
    }

    sort($results, SORT_STRING);
    return $results;
}

/**
 * Helper: scan a category folder for game/series folders and their issues.
 */
function find_issues($catPath, $query, $catName, &$results) {
    global $comicFolder;
    foreach (glob($catPath . '/*', GLOB_ONLYDIR) as $gamePath) {
        $gameName = basename($gamePath);

        if ($query !== '' && stripos($gameName, $query) === false) continue;

        foreach (glob($gamePath . '/*', GLOB_ONLYDIR) as $issuePath) {
            $issueName = basename($issuePath);
            $files = array_values(array_filter(scandir($issuePath), function($f) use ($issuePath) {
                $fullPath = $issuePath . '/' . $f;
                return is_file($fullPath) && preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $f);
            }));

            if (empty($files)) continue;
            sort($files, SORT_NATURAL);
            $cover = $files[0];
            $results[] = "$comicFolder/$catName/$gameName/$issueName/$cover";
        }
    }
}

// AJAX Handlers
if (isset($_GET['ajax'])) {
    $category = isset($_GET['cat']) ? trim($_GET['cat']) : '';

    if (isset($_GET['q'])) {
        header('Content-Type: application/json');
        echo json_encode(search_comics($_GET['q'], $category));
        exit;
    } elseif (isset($_GET['preview'])) {
        header('Content-Type: application/json');
        echo json_encode(search_comics('', $category));
        exit;
    }
}

if (isset($_GET['ajax']) && $_GET['ajax'] === 'images' && isset($_GET['folder'])) {
    $requestedFolder = $_GET['folder'];
    $realBase = realpath(__DIR__ . '/' . $comicFolder);
    $realRequest = realpath(__DIR__ . '/' . $requestedFolder);
    if ($realRequest === false || strpos($realRequest, $realBase) !== 0) {
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }

    $files = [];
    if (is_dir($realRequest)) {
        foreach (scandir($realRequest) as $file) {
            if (preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $file)) {
                $files[] = $requestedFolder . '/' . $file;
            }
        }
        sort($files, SORT_NATURAL);
    }
    header('Content-Type: application/json');
    echo json_encode($files);
    exit;
}

// Scan categories for buttons
$categories = [];
$rootDir = __DIR__ . '/' . $comicFolder;
if (is_dir($rootDir)) {
    foreach (glob($rootDir . '/*', GLOB_ONLYDIR) as $catPath) {
        $categories[] = basename($catPath);
    }
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
  overflow: scroll;
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

/* Category bar */
.category-bar {
  text-align: center;
  margin-bottom: 20px;
}
.cat-btn {
  margin: 0 8px;
  padding: 8px 24px;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  background-color: #00aaff;
  color: #000;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.2s, color 0.2s;
  border: 2px solid #00aaff;
}
.cat-btn:hover {
  background-color: #00aaffbb;
  color: #000;
}
.cat-btn.active {
  background-color: #00aaff;
  color: #fff;
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
.close-btn {
  position: absolute;
  top: 15px; right: 20px;
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
</style>

<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<div class="search-box">
  <input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
</div>

<div class="category-bar" id="categoryBar">
  <?php foreach ($categories as $cat): ?>
    <button class="cat-btn" onclick="setCategory('<?php echo htmlspecialchars($cat, ENT_QUOTES); ?>')">
      <?php echo htmlspecialchars($cat); ?>
    </button>
  <?php endforeach; ?>
</div>

<div id="results"></div>

<div id="overlay" class="overlay">
  <div class="close-btn" onclick="closeOverlay()">✖️</div>
  <img id="overlayImg" src="">
  <button class="video-slider-btn left-side" style="background-color: #00aaff;" onclick="showPreviousImage()">❮</button>
  <button class="video-slider-btn right-side" style="background-color: #00aaff;" onclick="showNextImage()">❯</button>
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
let currentCategory = '';
let debounceTimer = null;

function updateCategoryButtons() {
  document.querySelectorAll('.cat-btn').forEach(btn => {
    btn.classList.remove('active');
    if (btn.textContent.trim() === currentCategory) {
      btn.classList.add('active');
    }
  });
}

function setCategory(cat) {
  currentCategory = cat;
  updateCategoryButtons();
  // Re-run filter with current query
  filterResults();
}

function filterResults() {
  const q = document.getElementById("searchInput").value.trim();
  if (q === '') {
    doPreview();
  } else {
    doSearch(q);
  }
}

function renderResults(data) {
  const results = document.getElementById("results");
  results.innerHTML = '';
  if (data.length === 0) {
    results.innerHTML = '<p style="color:#aaa;">No comics found.</p>';
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

function doSearch(q) {
  let url = `?ajax=1&q=${encodeURIComponent(q)}`;
  if (currentCategory) {
    url += `&cat=${encodeURIComponent(currentCategory)}`;
  }
  fetch(url).then(r => r.json()).then(data => renderResults(data));
}

function doPreview() {
  let url = `?ajax=1&preview=1`;
  if (currentCategory) {
    url += `&cat=${encodeURIComponent(currentCategory)}`;
  }
  fetch(url).then(r => r.json()).then(data => renderResults(data));
}

// Live filtering on input with debounce
document.getElementById("searchInput").addEventListener("input", function() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(filterResults, 300); // wait 300ms after last keystroke
});

// Keep the rest of your existing functions unchanged:
// openSlideshow, showOverlayImage, showNextImage, showPreviousImage, closeOverlay,
// overlay click zoom logic, etc.

function openSlideshow(imagePath) {
  const issueFolder = imagePath.substring(0, imagePath.lastIndexOf('/'));
  fetch(`?ajax=images&folder=${encodeURIComponent(issueFolder)}`)
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