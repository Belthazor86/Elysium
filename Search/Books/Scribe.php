<?php
// AJAX: Search and return only PHP files from matching filenames (directly in Gallerium folder)
if (isset($_GET['search']) && isset($_GET['category'])) {
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($_GET['search'])));
    $category = basename($_GET['category']);
    $baseDir = __DIR__ . "/$category";
    $results = [];

    if (is_dir($baseDir)) {
        // Get all PHP files directly in this folder
        $scripts = glob("$baseDir/*.txt");

        foreach ($scripts as $script) {
            $filename = strtolower(str_replace([' ', '_', '-'], '', pathinfo($script, PATHINFO_FILENAME)));

            // Match search query (if query is empty, it returns all)
            if ($query === '' || strpos($filename, $query) !== false) {
                $results[] = [
                    'title' => pathinfo($script, PATHINFO_FILENAME), // Name without extension
                    'script' => "$category/" . basename($script),    // Path for iframe
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
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Scribe</title>
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
  transition: background 0.2s;
}
#overlay button:hover {
  background: #0090dd;
}
#overlay button:first-child {
  right: 10px; /* Close button */
}
#overlay button:nth-child(2) {
  right: 50px; /* Fullscreen button */
}

#bookContent {
  max-height: 80vh;
  overflow-y: auto;
  padding: 20px;
  line-height: 1.6;
  font-size: 18px;
  color: whitesmoke;          
  background: #000;            
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.5);
  scroll-behavior: smooth;    
  text-align: center;          /* Center the text horizontally */
}

/* Paragraph styling for readability */
#bookContent p {
  text-align: justify;
  margin-bottom: 1.2em;
}

/* Style the <pre> inside bookContent */
#bookContent pre {
  white-space: pre-wrap;      
  font-family: Arial, sans-serif; 
  font-weight:bold;
  margin: 0 auto;             /* Center the pre horizontally */
  display: inline-block;      /* Required for horizontal centering */
  text-align: center;         /* Center text inside pre */
}

/* Added a simple style for the preview button to keep layout consistent */
.preview-btn {
  background: #111;
  border: 2px solid #00aaff;
  color: #00aaff;
  padding: 10px 18px;
  font-weight: 600;
  border-radius: 20px;
  margin-top: 20px;
  margin-bottom: 30px;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease;
}
.preview-btn:hover {
  background-color: #00aaff;
  color: #000;
}
.preview-btn.active {
  background-color: #00aaff;
  color: #000;
  box-shadow: 0 0 15px #00aaffbb;
}
</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
<button class="preview-btn" onclick="showAll()">Preview All</button>

<div id="gallery"></div>

<div id="overlay">
  <div class="container" id="overlayContainer">
    <button onclick="toggleFullscreen()">⛶</button>
    <div id="bookContent"></div>
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
let currentCategory = 'Scribe';
let currentSearch = '';

document.getElementById('searchInput').addEventListener('input', e => {
  currentSearch = e.target.value.trim();
  if (currentSearch.length >= 2) {
    searchScripts();
  } else {
    document.getElementById('gallery').innerHTML = '';
  }
});

// New function to show all files
function showAll() {
    currentSearch = '';
    searchScripts();
}

async function searchScripts() {
  try {
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
  } catch (err) {
    console.error('Search error:', err);
  }
}

function openOverlay(scriptPath) {
  const overlay = document.getElementById('overlay');
  const contentDiv = document.getElementById('bookContent');

  contentDiv.innerHTML = '';

  fetch(scriptPath)
    .then(res => res.text())
    .then(text => {
      const pre = document.createElement('pre');
      pre.textContent = text; 
      contentDiv.appendChild(pre);
    })
    .catch(err => {
      contentDiv.textContent = 'Error loading book.';
      console.error('Load book error:', err);
    });

  overlay.style.display = 'flex';
}

function closeOverlay() {
  const overlay = document.getElementById('overlay');
  const contentDiv = document.getElementById('bookContent');
  contentDiv.innerHTML = '';
  overlay.style.display = 'none';
}

function toggleFullscreen() {
  const container = document.getElementById('overlayContainer');
  
  if (!document.fullscreenElement) {
    container.requestFullscreen().catch(err => {
      alert(`Error enabling fullscreen: ${err.message}`);
    });
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