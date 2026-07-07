<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<?php
if (isset($_GET['search']) && isset($_GET['category'])) {
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($_GET['search'])));
    
    // We expect 'category' to be just the folder name, e.g., "Science"
    $categoryName = basename($_GET['category']); 
    $baseDir = __DIR__ . "/Scribe/" . $categoryName; 
    $results = [];

    if (is_dir($baseDir)) {
        // Find all .txt files. CHANGE '.txt' TO '.php' IF YOUR FILES ARE PHP
        $files = glob("$baseDir/*.txt");

        foreach ($files as $file) {
            $filenameOnly = pathinfo($file, PATHINFO_FILENAME);
            $cleanName = strtolower(str_replace([' ', '_', '-'], '', $filenameOnly));

            if ($query === '' || strpos($cleanName, $query) !== false) {
                $results[] = [
                    'title' => $filenameOnly,
                    'script' => "Scribe/" . $categoryName . "/" . basename($file)
                ];
            }
        }
    } else {
        // This helps us debug if the path is wrong
        $results = ["debug_error" => "Directory not found: $baseDir"];
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

.buttons {
  margin-top: 30px;
  margin-bottom: 30px;
  display: grid;
  /* Auto-fit will automatically adjust columns based on available space */
  grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
  gap: 12px;
  width: 100%;
  max-width: 400px; /* Adjusted for better layout with more buttons */
  justify-content: center;
}

/* Optional: Make it responsive for small screens */
@media (max-width: 600px) {
  .buttons {
    grid-template-columns: repeat(auto-fit, minmax(60px, 1fr));
  }
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
</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
<div class="buttons">
  <?php
  $vetraPath = __DIR__ . '/Scribe';
  if (is_dir($vetraPath)) {
      $folders = array_filter(scandir($vetraPath), function($item) use ($vetraPath) {
          return is_dir($vetraPath . '/' . $item) && !in_array($item, ['.', '..']);
      });
      foreach ($folders as $folder) {
          // We pass the folder name to the JS function
          echo '<button onclick="setCategory(\'' . htmlspecialchars($folder) . '\', event)">' . htmlspecialchars($folder) . '</button>';
      }
  }
  ?>
</div>

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
let currentCategory = ''; 
let currentSearch = '';

// 1. Function to handle button clicks
function setCategory(folderName, event) {
    currentCategory = folderName; // Just "Science", not "Guides/Science"
    
    // UI Cleanup
    document.getElementById('searchInput').value = '';
    currentSearch = '';
    document.querySelectorAll('.buttons button').forEach(btn => btn.classList.remove('active'));
    if (event) event.target.classList.add('active');

    // Fetch the files
    searchScripts();
}

// 2. Function to handle search input
document.getElementById('searchInput').addEventListener('input', e => {
    currentSearch = e.target.value.trim();
    // Search if 2+ chars OR if box is empty (to show all again)
    if (currentSearch.length >= 2 || currentSearch.length === 0) {
        searchScripts();
    }
});

// 3. The Core Fetcher
async function searchScripts() {
    if (!currentCategory) return;

    try {
        const url = `?search=${encodeURIComponent(currentSearch)}&category=${encodeURIComponent(currentCategory)}`;
        const res = await fetch(url);
        const data = await res.json();

        // Debugging: If the PHP sent an error, show it in the console
        if (data.debug_error) {
            console.error("PHP Error:", data.debug_error);
            return;
        }

        const gallery = document.getElementById('gallery');
        gallery.innerHTML = '';

        if (data.length === 0) {
            gallery.textContent = 'No files found in this category.';
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
        console.error('Fetch error:', err);
    }
}

// 4. Overlay Logic
function openOverlay(path) {
    const overlay = document.getElementById('overlay');
    const contentDiv = document.getElementById('bookContent');
    contentDiv.innerHTML = 'Loading...';
    overlay.style.display = 'flex';

    fetch(path)
        .then(res => res.text())
        .then(text => {
            contentDiv.innerHTML = '';
            const pre = document.createElement('pre');
            pre.textContent = text; 
            contentDiv.appendChild(pre);
        })
        .catch(err => {
            contentDiv.textContent = 'Error loading file.';
        });
}

function closeOverlay() {
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('bookContent').innerHTML = '';
}

function toggleFullscreen() {
    const container = document.getElementById('overlayContainer');
    if (!document.fullscreenElement) {
        container.requestFullscreen().catch(err => alert(err.message));
    } else {
        document.exitFullscreen();
    }
}

// Close on background click
document.getElementById('overlay').addEventListener('click', e => {
    if (e.target.id === 'overlay') closeOverlay();
});

</script>

</body>
</html>