

<?php
// Serve embedded script in HTML for iframe
if (isset($_GET['embedScript'])) {
    $path = $_GET['embedScript'];
    $full = realpath(__DIR__ . '/' . $path);
    $allowed = realpath(__DIR__ . '/Lorevania');

    if ($full && strpos($full, $allowed) === 0 && file_exists($full)) {
        $scriptContent = file_get_contents($full);
        header('Content-Type: text/html');
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head><meta charset='UTF-8'><title>Script</title>
        <style>
            html, body { 
                margin: 0; 
                padding: 0; 
                width: 100vw; 
                height: 100vh; 
                background: #111; 
                color: #eee; 
                overflow: hidden;         /* Hides scrollbars */
                scrollbar-width: none;    /* Firefox */
            }
            html::-webkit-scrollbar, body::-webkit-scrollbar {
                display: none;            /* Chrome, Safari */
            }
            canvas { display: block; width: 100vw; height: 100vh; }
        </style>
        </head>
        <body>
        <script>
        function makeCanvasFullScreen() {
            const canvas = document.querySelector('canvas');
            if (!canvas) return;
            function resize() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            }
            window.addEventListener('resize', resize);
            resize();
        }
        makeCanvasFullScreen();
        " . $scriptContent . "
        </script>
        </body>
        </html>";
    } else {
        http_response_code(403);
        echo "Access Denied";
    }
    exit;
}

// AJAX: Search Lorevania and return only JS files from matching folders
if (isset($_GET['search']) && isset($_GET['category'])) {
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($_GET['search'])));
    $category = basename($_GET['category']);
    $baseDir = __DIR__ . "/$category";
    $results = [];

    if (is_dir($baseDir)) {
        foreach (scandir($baseDir) as $folder) {
            if ($folder === '.' || $folder === '..') continue;

            $normalizedFolder = strtolower(str_replace([' ', '_', '-'], '', $folder));
            if (strpos($normalizedFolder, $query) !== false) {
                $folderPath = "$baseDir/$folder";
                $scripts = glob("$folderPath/*.js");

                foreach ($scripts as $script) {
                    $results[] = [
                        'title' => pathinfo($script, PATHINFO_FILENAME), // show only file name
                        'script' => "$category/$folder/" . basename($script),
                    ];
                }
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
<title>Lorevania</title>
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

</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
<div id="gallery"></div>

<div id="overlay">
  <div class="container">
    <iframe id="scriptFrame"></iframe>
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
let currentCategory = 'Lorevania';
let currentSearch = '';

document.getElementById('searchInput').addEventListener('input', e => {
  currentSearch = e.target.value.trim();

  const gallery = document.getElementById('gallery');

  if (currentSearch === '') {
    gallery.innerHTML = ''; // Clear results when empty
    return;
  }

  if (currentSearch.length >= 2) {
    searchScripts();
  } else {
    gallery.innerHTML = ''; // Clear if fewer than 2 characters
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
  iframe.src = `?embedScript=${encodeURIComponent(scriptPath)}`;
  overlay.style.display = 'flex';
}

function closeOverlay() {
  const overlay = document.getElementById('overlay');
  const iframe = document.getElementById('scriptFrame');
  iframe.src = '';
  overlay.style.display = 'none';
}

document.getElementById('overlay').addEventListener('click', e => {
  if (e.target === e.currentTarget) closeOverlay();
});
</script>





</body>
</html>
