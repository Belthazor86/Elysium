<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';

/**
 * AJAX Handler: Searches Lorevania for TEXT FILES
 * Returns JSON array of file paths and titles.
 */
if (isset($_GET['search']) && isset($_GET['category'])) {
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($_GET['search'])));
    $category = basename($_GET['category']); 
    $baseDir = __DIR__ . "/$category";
    $results = [];

    if (is_dir($baseDir)) {
        foreach (scandir($baseDir) as $folder) {
            if ($folder === '.' || $folder === '..') continue;

            $normalizedFolder = strtolower(str_replace([' ', '_', '-'], '', $folder));
            
            if ($query === '' || strpos($normalizedFolder, $query) !== false) {
                $folderPath = "$baseDir/$folder";
                
                // === ONLY CHANGE: look for .txt instead of video formats ===
                $textFiles = glob("$folderPath/*.txt");

                if ($textFiles) {
                    foreach ($textFiles as $file) {
                        $results[] = [
                            'title' => pathinfo($file, PATHINFO_FILENAME),
                            'path' => "$category/$folder/" . basename($file)
                        ];
                    }
                }
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}

// === ADDED: Serve raw text file (exact same logic as Play! code) ===
if (isset($_GET['file'])) {
    $requestedFile = $_GET['file'];
    $requestedFile = str_replace('\\', '/', $requestedFile);
    $parts = explode('/', $requestedFile);
    $safeParts = array_map('basename', $parts);
    $safePath = implode('/', $safeParts);
    $filePath = __DIR__ . '/' . $safePath;

    if (file_exists($filePath) && is_file($filePath)) {
        header('Content-Type: text/plain; charset=utf-8');
        readfile($filePath);
    } else {
        http_response_code(404);
        echo 'File not found';
    }
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
<title>Hematite</title>
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
  background-color: #222;
  color: #fff;
  box-shadow: 0 0 8px #00aaffaa;
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
  width: 75vw;
  max-width: 1200px;
  height: 75vh;
  background: #000;
  border-radius: 15px;
  box-shadow: 0 0 40px #00ffc3;
  overflow: hidden;
}
/* === CHANGED: iframe replaces video player === */
#overlay iframe {
  width: 100%;
  height: 100%;
  border: none;
  background: #fff;
}
#overlay button:first-child {
  right: 10px; /* Close button */
}
#overlay button:nth-child(2) {
  right: 50px; /* Fullscreen button – kept for structure, but hidden by default */
}
/* Navigation buttons (unchanged style) */
#overlay .prev, #overlay .next {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0,0,0,0.5);
  border: 2px solid #00ffc3;
  color: #00ffc3;
  font-size: 25px;
  width: 50px;
  height: 60px;
  display: none;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border-radius: 8px;
  z-index: 10;
  transition: 0.3s;
}
#overlay .prev { left: 15px; }
#overlay .next { right: 15px; }
.close-btn {
    position: absolute;
    top: 12px;
    right: 16px;
    font-size: 20px;
    color: #00ffc3;
    cursor: pointer;
    z-index: 20;
    transition: transform 0.2s, color 0.2s;
}
.close-btn:hover {
    color: #ff5252;
    transform: scale(1.3);
}
</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
<div id="gallery"></div>

<div id="overlay">
  <div class="container">
    <div class="close-btn" onclick="closeOverlay()" title="Close">✖️</div>
    <iframe id="contentFrame"></iframe>
    <button class="prev" onclick="playPrevious()">❮</button>
    <button class="next" onclick="playNext()">❯</button>
  </div>
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
let currentCategory = 'Hematite';
let currentSearch = '';
let activePlaylist = [];
let currentIndex = 0;
const contentFrame = document.getElementById('contentFrame');

// 1. Handle Search Input (unchanged)
document.getElementById('searchInput').addEventListener('input', e => {
  currentSearch = e.target.value.trim();
  const gallery = document.getElementById('gallery');

  if (currentSearch === '') {
    gallery.innerHTML = ''; 
    return;
  }

  if (currentSearch.length >= 2) {
    searchScripts();
  } else {
    gallery.innerHTML = ''; 
  }
});

// 2. Fetch Search Results (unchanged)
async function searchScripts() {
  const res = await fetch(`?search=${encodeURIComponent(currentSearch)}&category=${currentCategory}`);
  const data = await res.json();
  const gallery = document.getElementById('gallery');
  gallery.innerHTML = '';

  if (data.length === 0) {
    gallery.textContent = 'No matches found.';
    activePlaylist = [];
    return;
  }

  activePlaylist = data.map(item => item.path);

  data.forEach((item, index) => {
    const link = document.createElement('div');
    link.className = 'file-link';
    link.textContent = item.title;
    link.onclick = () => {
      currentIndex = index; 
      openOverlay();
    };
    gallery.appendChild(link);
  });
}

// 3. Overlay Controls (unchanged)
function openOverlay() {
  const overlay = document.getElementById('overlay');
  overlay.style.display = 'flex';
  loadCurrentText();   // <-- calls the Play!-style loader
}

function closeOverlay() {
  const overlay = document.getElementById('overlay');
  contentFrame.srcdoc = '';   // clear iframe
  overlay.style.display = 'none';
}

// === ADDED: Text loader – EXACTLY like Play!’s loadServerFile ===
function loadServerFile(filepath, displayName) {
    fetch(`?file=${encodeURIComponent(filepath)}`)
        .then(response => response.text())
        .then(content => {
            const htmlContent = `<html><head><title>${displayName}</title></head><body>${content}</body></html>`;
            contentFrame.srcdoc = htmlContent;
        })
        .catch(err => {
            contentFrame.srcdoc = `<html><body><p style="color:red;">Error: ${err.message}</p></body></html>`;
        });
}

function loadCurrentText() {
    if (activePlaylist.length > 0 && currentIndex >= 0 && currentIndex < activePlaylist.length) {
        const filePath = activePlaylist[currentIndex];
        const displayName = filePath.split('/').pop().replace('.txt', '');
        loadServerFile(filePath, displayName);
    }

    // Show/hide nav buttons
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');
    if (prevBtn) prevBtn.style.display = (currentIndex === 0) ? 'none' : 'flex';
    if (nextBtn) nextBtn.style.display = (currentIndex === activePlaylist.length - 1) ? 'none' : 'flex';
}

// 4. Navigation Functions (unchanged logic, but call loadCurrentText)
function playNext() {
  if (currentIndex < activePlaylist.length - 1) {
    currentIndex++;
    loadCurrentText();
  }
}

function playPrevious() {
  if (currentIndex > 0) {
    currentIndex--;
    loadCurrentText();
  }
}

// 5. Close overlay on backdrop click (unchanged)
document.getElementById('overlay').addEventListener('click', e => {
  if (e.target === e.currentTarget) closeOverlay();
});
</script>

</body>
</html>