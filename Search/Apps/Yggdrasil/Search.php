<?php
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../security.php';
?>


<?php
if (isset($_GET['search']) && isset($_GET['category'])) {
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($_GET['search'])));
    $category = $_GET['category'];
    $baseDir = __DIR__ . "/$category";

    if (!is_dir($baseDir)) {
        header('Content-Type: application/json');
        echo json_encode([]);
        exit;
    }

    $results = [];

    // Scan for subfolders inside the search folder
    $subfolders = array_filter(glob($baseDir . '/*'), 'is_dir');

    foreach ($subfolders as $folder) {
        // Scan for PHP files only inside those subfolders
        $scripts = glob($folder . '/*.php');

        foreach ($scripts as $script) {
            $filename = strtolower(str_replace([' ', '_', '-'], '', pathinfo($script, PATHINFO_FILENAME)));

            if (strpos($filename, $query) !== false) {
                $results[] = [
                    'title' => pathinfo($script, PATHINFO_FILENAME),
                    // Path relative to the base category
                    'script' => "$category/" . basename($folder) . "/" . basename($script),
                ];
            }
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
<link href="../../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<title>Search</title>
<style>
body {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
}
input#searchInput {
  width: 300px;
  padding: 10px 16px;
  font-size: 1.1rem;
  border-radius: 6px;
  border: none;
  background-color: transparent;
  color: #fff;
  box-shadow: 0 0 45px #00aaffaa;
}
input#searchInput:focus {
  outline: none;
  box-shadow: 0 0 12px #00aaffee;
}
#gallery {
  margin-top: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
}
.file-link {
  color: #00aaff;
  font-size: 1.2rem;
  cursor: pointer;
  transition: color 0.2s;
}
.file-link:hover {
  color: #66d0ff;
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
#overlay .container {
  position: relative;
  width: 85vw;
  max-width: 1200px;
  height: 85vh;
  background: #000;
  border-radius: 15px;
  box-shadow: 0 0 12px #00aaffee;
  overflow: hidden;
}
#overlay iframe {
  width: 100%;
  height: 100%;
  border: none;
}
#overlay button {
  position: absolute;
  top: 10px;
  border: none;
  padding: 6px 10px;
  font-size: 1.2rem;
  border-radius: 6px;
  cursor: pointer;
  background: black;
  color: #fff;
  z-index: 10000;
}
#overlay .close-btn { right: 10px; }
#overlay .full-btn { right: 60px; }
</style>
</head>
<body>

<h2><?php echo basename(dirname($_SERVER['SCRIPT_FILENAME'])); ?></h2>

<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
<div id="gallery"></div>

<div id="overlay">
  <div class="container" id="overlayContainer">

    <iframe id="scriptFrame"></iframe>
  </div>
</div>

<script>
let currentCategory = '../../../Search';
let currentSearch = '';

document.getElementById('searchInput').addEventListener('input', e => {
  currentSearch = e.target.value.trim();
  if (currentSearch.length >= 2) {
    searchScripts();
  } else {
    document.getElementById('gallery').innerHTML = '';
  }
});

async function searchScripts() {
  const res = await fetch(`?search=${encodeURIComponent(currentSearch)}&category=${currentCategory}`);
  const data = await res.json();
  const gallery = document.getElementById('gallery');
  gallery.innerHTML = '';

  if (data.length === 0) {
    gallery.textContent = 'No matches found.';
    return;
  }

  data.forEach(item => {
    const link = document.createElement('div');
    link.className = 'file-link';
    link.textContent = item.title;
    link.onclick = () => openOverlay(item.script);
    gallery.appendChild(link);
  });
}

function openOverlay(scriptPath) {
  const overlay = document.getElementById('overlay');
  const iframe = document.getElementById('scriptFrame');
  iframe.src = scriptPath;
  overlay.style.display = 'flex';
}

function closeOverlay() {
  const overlay = document.getElementById('overlay');
  const iframe = document.getElementById('scriptFrame');
  iframe.src = '';
  overlay.style.display = 'none';
}

function toggleFullscreen() {
  const container = document.getElementById('overlayContainer');
  if (!document.fullscreenElement) {
    container.requestFullscreen();
  } else {
    document.exitFullscreen();
  }
}

document.getElementById('overlay').addEventListener('click', e => {
  if (e.target === e.currentTarget) closeOverlay();
});
</script>

</body>
</html>