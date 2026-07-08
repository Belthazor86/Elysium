<?php
if (isset($_GET['search']) && isset($_GET['category'])) {
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($_GET['search'])));
    
    $categoryName = basename($_GET['category']); 
    $baseDir = __DIR__ . "/Guides/" . $categoryName; 
    $results = [];

    if (is_dir($baseDir)) {
        // Find all .md files
        $files = glob("$baseDir/*.md");

        foreach ($files as $file) {
            $filenameOnly = pathinfo($file, PATHINFO_FILENAME);
            $cleanName = strtolower(str_replace([' ', '_', '-'], '', $filenameOnly));

            if ($query === '' || strpos($cleanName, $query) !== false) {
                $results[] = [
                    'title' => $filenameOnly,
                    'script' => "Guides/" . $categoryName . "/" . basename($file)
                ];
            }
        }
    } else {
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

<!-- Markdown Parser -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<!-- Mermaid Parser -->
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>

<script>
  mermaid.initialize({
    startOnLoad: false,
    theme: 'dark',
    securityLevel: 'loose'
  });
</script>

<title>Guides</title>

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
  right: 10px;
}

#overlay button:nth-child(2) {
  right: 50px;
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
}

#bookContent p {margin-bottom: 1.2em;}

#bookContent pre {
  background: #111;
  padding: 10px;
  overflow-x: auto;
}

.mermaid {
  background: #1a1a1a;
  padding: 20px;
  border-radius: 8px;
  margin: 20px 0;
  text-align: center;
}

.mermaid svg {
  max-width: 100%;
  height: auto;
}

.buttons {
  margin-top: 15px;
  margin-bottom: 30px;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 12px;
  width: 100%;
  max-width: 100px;
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
</style>
</head>

<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />

<div class="buttons">
  <?php
  $vetraPath = __DIR__ . '/Guides';
  if (is_dir($vetraPath)) {
      $folders = array_filter(scandir($vetraPath), function($item) use ($vetraPath) {
          return is_dir($vetraPath . '/' . $item) && !in_array($item, ['.', '..']);
      });

      foreach ($folders as $folder) {
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


<footer class="site-footer">
  <div class="footer-content">
    <p class="footer-main">
      © 2026 <?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?> | 
      <a href="../Xtras/Guides.php">Visit Guides for documentation</a>
    </p>
    <p class="footer-specific">
      Powered by <a href="https://github.com/Belthazor86/Elysium.git" target="_blank" rel="noopener noreferrer">Elysium</a> 
    </p>
  </div>
</footer>


<script>
let currentCategory = ''; 
let currentSearch = '';

function setCategory(folderName, event) {
    currentCategory = folderName;
    
    document.getElementById('searchInput').value = '';
    currentSearch = '';

    document.querySelectorAll('.buttons button').forEach(btn => btn.classList.remove('active'));
    if (event) event.target.classList.add('active');

    searchScripts();
}

document.getElementById('searchInput').addEventListener('input', e => {
    currentSearch = e.target.value.trim();

    if (currentSearch.length >= 2 || currentSearch.length === 0) {
        searchScripts();
    }
});

async function searchScripts() {
    if (!currentCategory) return;

    try {
        const url = `?search=${encodeURIComponent(currentSearch)}&category=${encodeURIComponent(currentCategory)}`;
        const res = await fetch(url);
        const data = await res.json();

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

function openOverlay(path) {
    const overlay = document.getElementById('overlay');
    const contentDiv = document.getElementById('bookContent');

    contentDiv.innerHTML = 'Loading...';
    overlay.style.display = 'flex';

    fetch(path)
        .then(res => res.text())
        .then(text => {
            let html = marked.parse(text);
            contentDiv.innerHTML = html;
            
            const mermaidBlocks = contentDiv.querySelectorAll('pre code.language-mermaid');
            
            mermaidBlocks.forEach((block) => {
                const pre = block.parentElement;
                const mermaidCode = block.textContent;
                
                const container = document.createElement('div');
                container.className = 'mermaid';
                container.textContent = mermaidCode;
                
                pre.parentElement.replaceChild(container, pre);
            });
            
            mermaid.run({
                nodes: contentDiv.querySelectorAll('.mermaid')
            }).catch(err => {
                console.error('Mermaid rendering error:', err);
            });
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

document.getElementById('overlay').addEventListener('click', e => {
    if (e.target.id === 'overlay') closeOverlay();
});
</script>

</body>
</html>