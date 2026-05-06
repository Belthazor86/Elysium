<?php
// Serve embedded script wrapped in HTML to execute inside iframe
if (isset($_GET['embedScript'])) {
    $path = $_GET['embedScript'];
    $full = realpath(__DIR__ . '/' . $path);
    $allowed = realpath(__DIR__ . '/Retrocast');

    if ($full && strpos($full, $allowed) === 0 && file_exists($full)) {
        $scriptContent = file_get_contents($full);
        header('Content-Type: text/html');
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
          <meta charset='UTF-8'>
          <title>Script Runner</title>
          <style>
            html, body {
              margin: 0; padding: 0; overflow: hidden;
              width: 100vw; height: 100vh;
              background: #111;
              color: #eee;
              font-family: sans-serif;
            }
            canvas, #mainContent {
              display: block;
              width: 100vw !important;
              height: 100vh !important;
              box-sizing: border-box;
            }
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

// Search all folders under Retrocast/
if (isset($_GET['search'])) {
    $rawQuery = trim($_GET['search']);
    $isPreviewAll = ($rawQuery === ""); 
    $query = strtolower(str_replace([' ', '_', '-'], '', $rawQuery));
    $baseDir = __DIR__ . "/Retrocast";
    $results = [];

    if (is_dir($baseDir)) {
        $directories = new RecursiveDirectoryIterator($baseDir);
        $iterator = new RecursiveIteratorIterator($directories, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isDir()) {
                $folder = $fileinfo->getFilename();
                if ($folder === '.' || $folder === '..') continue;

                $normalizedFolder = strtolower(str_replace([' ', '_', '-'], '', $folder));
                
                // If it's a preview request or the query matches
                if ($isPreviewAll || strpos($normalizedFolder, $query) !== false) {
                    $fullPath = $fileinfo->getPathname();
                    $postersDir = "$fullPath/Posters";
                    $scriptsDir = "$fullPath/Scripts";

                    $posterFiles = glob("$postersDir/*.txt");
                    $scripts = glob("$scriptsDir/*.js");

                    natsort($posterFiles);
                    natsort($scripts);

                    $posterFiles = array_values($posterFiles);
                    $scripts = array_values($scripts);

                    $count = count($posterFiles);
                    for ($i = 0; $i < $count; $i++) {
                        $scriptFile = $scripts[$i] ?? null;
                        $link = trim(file_get_contents($posterFiles[$i]));

                        $results[] = [
                            'title' => basename($fileinfo->getPathname()),
                            'image' => $link,
                            'script' => $scriptFile ? substr($scriptFile, strlen(__DIR__) + 1) : null,
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
<title>Retrocast</title>
<style>
body {
  padding: 20px;
  margin: 0;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
}
input#searchInput {
  width: 300px;
  padding: 12px;
  font-size: 1rem;
  border-radius: 8px;
  border: none;
  background-color: #222;
  color: #fff;
  box-shadow: 0 0 6px #00aaffaa;
}
button#searchBtn {
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
.gallery {
  display: flex;
  flex-wrap: wrap;
  gap: 18px;
  justify-content: center;
  margin-top: 30px;
  overflow: scroll;
}
.gallery img {
  width: 150px;
  border-radius: 12px;
  cursor: pointer;
  box-shadow: 0 0 6px #00aaffaa;
  transition: transform 0.25s ease, box-shadow 0.3s ease;
}
.gallery img:hover {
  transform: scale(1.07);
  box-shadow: 0 0 20px #00aaffee;
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
#overlay button.closeBtn {
  position: absolute;
  top: 10px; right: 10px;
  background: #ff4c4c;
  color: white;
  border: none;
  padding: 8px 14px;
  font-size: 18px;
  font-weight: 700;
  cursor: pointer;
  border-radius: 8px;
}
#overlay iframe {
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 15px;
  display: block;
}

</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<div>
  <input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
  <button id="searchBtn" onclick="searchMedia()">Search</button>
  <button id="previewBtn" onclick="doPreview()" style="margin-left: 10px; padding: 12px 20px; font-size: 1rem; border: none; border-radius: 8px; background-color: #ff0055; color: #fff; font-weight: bold; cursor: pointer;">Preview All</button>
</div>

<div class="gallery" id="gallery"></div>

<div id="overlay">
  <div class="container">
    <iframe id="scriptFrame" src=""></iframe>
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
async function fetchAndRender(query = "") {
  const gallery = document.getElementById('gallery');
  gallery.innerHTML = '';
  closeOverlay();

  const resp = await fetch(`?search=${encodeURIComponent(query)}`);
  const data = await resp.json();

  if (data.length === 0) {
    gallery.textContent = 'No matches found.';
    return;
  }

  for (const item of data) {
    const img = document.createElement('img');
    img.src = item.image;
    img.alt = item.title;
    img.title = item.title;
    img.loading = 'lazy';
    img.style.cursor = 'pointer';

    img.onclick = () => {
      if (item.script) {
        openOverlay(item.script);
      } else {
        alert('No script available.');
      }
    };
    gallery.appendChild(img);
  }
}

async function searchMedia() {
  const query = document.getElementById('searchInput').value.trim();
  if (query.length < 2) {
    alert('Type at least 2 characters.');
    return;
  }
  fetchAndRender(query);
}

function doPreview() {
  fetchAndRender("");
}

function openOverlay(scriptPath) {
  document.getElementById('scriptFrame').src = `?embedScript=${encodeURIComponent(scriptPath)}`;
  document.getElementById('overlay').style.display = 'flex';
}

function closeOverlay() {
  document.getElementById('scriptFrame').src = '';
  document.getElementById('overlay').style.display = 'none';
}

document.getElementById('overlay').addEventListener('click', e => {
  if (e.target === e.currentTarget) closeOverlay();
});
</script>

</body>
</html>