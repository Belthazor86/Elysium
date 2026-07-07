<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<?php
$comicFolder = 'Novella';

/**
 * Search for PDFs.
 * @param string $query    Search term for series folder name (empty for all).
 * @param string $category Category subfolder ('' for all categories).
 * @param bool   $all      If true, ignore $query and return all PDFs.
 * @return array           Array of relative PDF paths.
 */
function search_comics($query, $category = '', $all = false) {
    global $comicFolder;
    $q = strtolower($query);
    $results = [];
    $rootDir = __DIR__ . '/' . $comicFolder;

    if (!is_dir($rootDir)) return $results;

    if ($category !== '') {
        $catPath = $rootDir . '/' . $category;
        if (is_dir($catPath)) {
            find_pdfs_in_category($catPath, $q, $all, $results, $category);
        }
    } else {
        // Scan all category subdirectories
        foreach (glob($rootDir . '/*', GLOB_ONLYDIR) as $catPath) {
            $catName = basename($catPath);
            find_pdfs_in_category($catPath, $q, $all, $results, $catName);
        }
    }

    sort($results, SORT_STRING);
    return $results;
}

/**
 * Helper: scan a category folder for series folders and their PDFs.
 */
function find_pdfs_in_category($catPath, $query, $all, &$results, $catName) {
    global $comicFolder;
    foreach (glob($catPath . '/*', GLOB_ONLYDIR) as $seriesPath) {
        $seriesName = basename($seriesPath);

        if (!$all && $query !== '' && stripos($seriesName, $query) === false) continue;

        foreach (glob($seriesPath . '/*.pdf') as $pdfPath) {
            $pdfName = basename($pdfPath);
            $results[] = "$comicFolder/$catName/$seriesName/$pdfName";
        }
    }
}

// AJAX: search or preview (with optional category)
if (isset($_GET['ajax'])) {
    $category = isset($_GET['cat']) ? trim($_GET['cat']) : '';

    if (isset($_GET['q'])) {
        header('Content-Type: application/json');
        echo json_encode(search_comics($_GET['q'], $category, false));
        exit;
    } elseif (isset($_GET['preview'])) {
        header('Content-Type: application/json');
        echo json_encode(search_comics('', $category, true));
        exit;
    }
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
<title>Novella</title>

<script src="../../Scripts/PDF/pdf.min.js"></script>

<script>
  pdfjsLib.GlobalWorkerOptions.workerSrc = '../../Scripts/PDF/pdf.worker.min.js';
</script>

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
  box-shadow: 0 0 6px #001f2eaa;
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

/* Thumbnail canvas style */
.thumb {
  width: 180px;
  border: 2px solid #333;
  border-radius: 6px;
  cursor: pointer;
  transition: transform 0.2s;
  background: #111;
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
  flex-direction: column;
}

/* Canvas inside overlay */
#pdfCanvas {
  display: block;
  max-width: 100%;
  max-height: 100%;
  cursor: zoom-in;
  transition: transform 0.3s ease;
  transform-origin: center center;
  background: #000;
  border-radius: 15px;
  box-shadow: 0 0 40px #00ffc3;
}
.overlay.zoomed #pdfCanvas {
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

/* Navigation buttons inside overlay */
.video-slider-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  font-size: 2rem;
  padding: 10px;
  background: rgba(0,170,255,0.7);
  border: none;
  color: white;
  cursor: pointer;
  border-radius: 4px;
  z-index: 10001;
}
.left-side { left: 10px; }
.right-side { right: 10px; }

/* Page indicator */
.page-indicator {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  color: #00ffc3;
  font-family: monospace;
  font-size: 1.2rem;
  text-shadow: 0 0 8px #00ffc3;
  background: rgba(0,0,0,0.7);
  padding: 4px 12px;
  border-radius: 4px;
  z-index: 10001;
}
</style>
</head>

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
  <canvas id="pdfCanvas"></canvas>
  <div class="page-indicator" id="pageIndicator"></div>
  <button class="video-slider-btn left-side" onclick="prevPage()">❮</button>
  <button class="video-slider-btn right-side" onclick="nextPage()">❯</button>
</div>

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

let currentPdf = null;
let currentPageNum = 1;
let totalPages = 0;
let currentCategory = '';

// Token to cancel stale rendering loops
let renderToken = 0;

// ================== CATEGORY HANDLING ==================
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
  const q = document.getElementById("searchInput").value.trim();
  if (q) {
    doSearch();
  } else {
    doPreview();
  }
}

// ================== THUMBNAIL RENDERING (FIXED) ==================
async function renderResults(pdfList) {
  const resultsDiv = document.getElementById("results");
  resultsDiv.innerHTML = '';

  if (pdfList.length === 0) {
    resultsDiv.innerHTML = '<p style="color:#aaa;">No PDFs found.</p>';
    return;
  }

  // Increment the token to invalidate any previous render loop
  const thisToken = ++renderToken;

  for (const pdfPath of pdfList) {
    // Stop if a new render has started
    if (thisToken !== renderToken) return;

    try {
      const pdf = await pdfjsLib.getDocument(pdfPath).promise;

      // Check again after async operation
      if (thisToken !== renderToken) {
        pdf.destroy();
        return;
      }

      const page = await pdf.getPage(1);
      if (thisToken !== renderToken) {
        pdf.destroy();
        return;
      }

      const viewport = page.getViewport({ scale: 1 });

      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');
      const thumbWidth = 180;
      const thumbHeight = (viewport.height / viewport.width) * thumbWidth;
      canvas.width = thumbWidth;
      canvas.height = thumbHeight;
      canvas.className = 'thumb';
      canvas.title = pdfPath.split('/').pop();

      const renderContext = {
        canvasContext: ctx,
        viewport: page.getViewport({ scale: thumbWidth / viewport.width }),
      };
      await page.render(renderContext).promise;

      // Final check before appending to DOM
      if (thisToken !== renderToken) {
        pdf.destroy();
        return;
      }

      canvas.onclick = () => openPdfViewer(pdfPath);
      resultsDiv.appendChild(canvas);
    } catch (err) {
      console.error('Failed to load PDF thumbnail:', pdfPath, err);
    }
  }
}

// ================== PDF VIEWER ==================
async function openPdfViewer(pdfPath) {
  const overlay = document.getElementById("overlay");
  const canvas = document.getElementById("pdfCanvas");

  currentPdf = await pdfjsLib.getDocument(pdfPath).promise;
  totalPages = currentPdf.numPages;
  currentPageNum = 1;

  overlay.style.display = 'flex';
  overlay.classList.remove("zoomed");
  await renderCurrentPage();
}

async function renderCurrentPage() {
  if (!currentPdf) return;

  const canvas = document.getElementById("pdfCanvas");
  const indicator = document.getElementById("pageIndicator");

  const page = await currentPdf.getPage(currentPageNum);
  const desiredWidth = window.innerWidth * 0.8;
  const viewport = page.getViewport({ scale: 1 });
  const scale = desiredWidth / viewport.width;
  const scaledViewport = page.getViewport({ scale });

  canvas.width = scaledViewport.width;
  canvas.height = scaledViewport.height;

  const ctx = canvas.getContext('2d');
  await page.render({
    canvasContext: ctx,
    viewport: scaledViewport,
  }).promise;

  indicator.textContent = `Page ${currentPageNum} of ${totalPages}`;
}

function nextPage() {
  if (currentPdf && currentPageNum < totalPages) {
    currentPageNum++;
    renderCurrentPage();
  }
}

function prevPage() {
  if (currentPdf && currentPageNum > 1) {
    currentPageNum--;
    renderCurrentPage();
  }
}

function closeOverlay() {
  document.getElementById("overlay").style.display = 'none';
  currentPdf = null;
}

// ================== ZOOM (canvas click) ==================
document.getElementById("pdfCanvas").addEventListener("click", function(e) {
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
});

// Close overlay on background click
document.getElementById("overlay").addEventListener("click", function(e) {
  if (e.target === this) closeOverlay();
});

// ================== SEARCH / PREVIEW ==================
function doSearch() {
  const q = document.getElementById("searchInput").value.trim();
  if (!q) return;
  // Invalidate any running render
  renderToken++;
  let url = `?ajax=1&q=${encodeURIComponent(q)}`;
  if (currentCategory) {
    url += `&cat=${encodeURIComponent(currentCategory)}`;
  }
  fetch(url)
    .then(r => r.json())
    .then(data => renderResults(data));
}

function doPreview() {
  // Invalidate any running render
  renderToken++;
  let url = `?ajax=1&preview=1`;
  if (currentCategory) {
    url += `&cat=${encodeURIComponent(currentCategory)}`;
  }
  fetch(url)
    .then(r => r.json())
    .then(data => renderResults(data));
}

// ================== AUTO-SEARCH ON INPUT ==================
function debounce(fn, delay) {
  let timeout;
  return function (...args) {
    clearTimeout(timeout);
    timeout = setTimeout(() => fn.apply(this, args), delay);
  };
}

const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('input', debounce(function () {
  const q = this.value.trim();
  if (q) {
    doSearch();
  } else {
    doPreview();
  }
}, 300));

</script>




</body>
</html>