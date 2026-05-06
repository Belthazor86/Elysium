<?php
$comicFolder = 'Wallverine';

// Helper to get all images in a specific subfolder
function get_images_in_path($relativeDirPath) {
    $fullPath = __DIR__ . '/' . $relativeDirPath;
    if (!is_dir($fullPath)) return [];
    $files = array_filter(scandir($fullPath), function($f) {
        return preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $f);
    });
    natcasesort($files);
    $output = [];
    foreach ($files as $file) {
        $output[] = $relativeDirPath . '/' . $file;
    }
    return $output;
}

// Helper to get the first image and folder path for the grid
function get_folder_preview($folderPath) {
    global $comicFolder;
    $files = array_filter(scandir($folderPath), function($f) {
        return preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $f);
    });
    natcasesort($files);
    if (!empty($files)) {
        $firstImg = reset($files);
        // Calculate the relative path for the frontend
        $relative = str_replace(__DIR__ . '/', '', $folderPath);
        return [
            'thumb' => $relative . '/' . $firstImg,
            'folder' => $relative
        ];
    }
    return null;
}

if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $results = [];

    if (isset($_GET['category'])) {
        $categoryDir = __DIR__ . '/' . $comicFolder . '/' . $_GET['category'];
        if (is_dir($categoryDir)) {
            $subs = array_filter(scandir($categoryDir), function($item) use ($categoryDir) {
                return is_dir($categoryDir . '/' . $item) && !in_array($item, ['.', '..']);
            });
            foreach ($subs as $sub) {
                $preview = get_folder_preview($categoryDir . '/' . $sub);
                if ($preview) $results[] = $preview;
            }
        }
    } elseif (isset($_GET['q'])) {
        $q = strtolower($_GET['q']);
        $rootDir = __DIR__ . '/' . $comicFolder;
        
        // Deep search through all categories and subfolders
        $categories = array_filter(scandir($rootDir), function($item) use ($rootDir) {
            return is_dir($rootDir . '/' . $item) && !in_array($item, ['.', '..']);
        });

        foreach ($categories as $cat) {
            $catPath = $rootDir . '/' . $cat;
            $subs = array_filter(scandir($catPath), function($item) use ($catPath) {
                return is_dir($catPath . '/' . $item) && !in_array($item, ['.', '..']);
            });
            
            foreach ($subs as $sub) {
                if (stripos($sub, $q) !== false || stripos($cat, $q) !== false) {
                    $preview = get_folder_preview($catPath . '/' . $sub);
                    if ($preview) $results[] = $preview;
                }
            }
        }
    } elseif (isset($_GET['loadFolder'])) {
        echo json_encode(get_images_in_path($_GET['loadFolder']));
        exit;
    }
    echo json_encode($results);
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
<title>Wallverine</title>
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

.nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0, 0, 0, 0.5);
  color: #00ffc3;
  border: none;
  font-size: 40px;
  padding: 20px;
  cursor: pointer;
  z-index: 10000;
  border-radius: 8px;
}
.prev-btn { left: 20px; }
.next-btn { right: 20px; }
                  
</style>

<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<div class="search-box">
  <input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
  <button onclick="doSearch()">Search</button>
</div>

<div class="buttons" style="text-align:center; margin-bottom:20px;">
  <?php
  $vetraPath = __DIR__ . '/Wallverine';
  if (is_dir($vetraPath)) {
      $folders = array_filter(scandir($vetraPath), function($item) {
          return !in_array($item, ['.', '..']);
      });
      foreach ($folders as $folder) {
          echo '<button onclick="setCategory(\'' . htmlspecialchars($folder) . '\')">' . htmlspecialchars($folder) . '</button>';
      }
  }
  ?>
</div>

<div id="results"></div>

<div id="overlay" class="overlay">
  <div class="close-btn" onclick="closeOverlay()" title="Close">✖️</div>
  <button class="nav-btn prev-btn" onclick="changeImage(-1)">❮</button>
  <img id="overlayImg" src="" onclick="toggleZoom(event)">
  <button class="nav-btn next-btn" onclick="changeImage(1)">❯</button>
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
let subfolderImages = [];
let currentIndex = 0;

function renderResults(data) {
  const results = document.getElementById("results");
  results.innerHTML = '';
  if (!data || !data.length) return;

  data.forEach(item => {
      const img = document.createElement('img');
      img.src = item.thumb;
      img.className = 'thumb';
      img.onclick = () => loadSubfolderAndOpen(item.folder);
      results.appendChild(img);
  });
}

function loadSubfolderAndOpen(folderPath) {
  fetch(`?ajax=1&loadFolder=${encodeURIComponent(folderPath)}`)
    .then(r => r.json())
    .then(images => {
        subfolderImages = images;
        currentIndex = 0;
        updateOverlayImage();
        document.getElementById("overlay").style.display = 'flex';
    });
}

function setCategory(cat) {
  fetch(`?ajax=1&category=${encodeURIComponent(cat)}`)
    .then(r => r.json())
    .then(renderResults);
}

function doSearch() {
  const q = document.getElementById("searchInput").value.trim();
  if (!q) return;
  fetch(`?ajax=1&q=${encodeURIComponent(q)}`)
    .then(r => r.json())
    .then(renderResults);
}

function updateOverlayImage() {
  const img = document.getElementById("overlayImg");
  img.src = subfolderImages[currentIndex];
  document.getElementById("overlay").classList.remove("zoomed");
}

function changeImage(step) {
  if (!subfolderImages.length) return;
  currentIndex += step;
  if (currentIndex >= subfolderImages.length) currentIndex = 0;
  if (currentIndex < 0) currentIndex = subfolderImages.length - 1;
  updateOverlayImage();
}

function closeOverlay() {
  document.getElementById("overlay").style.display = 'none';
  document.getElementById("overlayImg").src = '';
}

function toggleZoom(e) {
  const overlay = document.getElementById("overlay");
  const img = e.target;
  if (overlay.classList.contains("zoomed")) {
    overlay.classList.remove("zoomed");
  } else {
    const rect = img.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    img.style.transformOrigin = `${x}% ${y}%`;
    overlay.classList.add("zoomed");
  }
}

document.getElementById('overlay').onclick = function(e) {
  if (e.target.id === 'overlay') closeOverlay();
};

document.addEventListener('keydown', (e) => {
  if (document.getElementById("overlay").style.display === 'flex') {
    if (e.key === "ArrowLeft") changeImage(-1);
    if (e.key === "ArrowRight") changeImage(1);
    if (e.key === "Escape") closeOverlay();
  }
});
</script>

</body>
</html>