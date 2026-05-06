<?php
// Serve embedded script wrapped in HTML to execute inside iframe
if (isset($_GET['embedScript'])) {
    $path = $_GET['embedScript'];
    $full = realpath(__DIR__ . '/' . $path);
    $allowed = realpath(__DIR__ . '/Book Café');

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

// AJAX: Search media folders and return matching posters/scripts
if (isset($_GET['category'])) {
    $searchRaw = isset($_GET['search']) ? $_GET['search'] : '';
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($searchRaw)));
    $category = basename($_GET['category']);
    $baseDir = __DIR__ . "/Book Café/$category";
    $results = [];

    if (is_dir($baseDir)) {
        foreach (scandir($baseDir) as $folder) {
            if ($folder === '.' || $folder === '..') continue;
            
            $normalizedFolder = strtolower(str_replace([' ', '_', '-'], '', $folder));
            
            // If query is empty, show everything. Otherwise, filter.
            if ($query === '' || strpos($normalizedFolder, $query) !== false) {
                $postersDir = "$baseDir/$folder/Posters";
                $scriptsDir = "$baseDir/$folder/Scripts";

                $posterTxts = glob("$postersDir/*.txt");
                $scripts = glob("$scriptsDir/*.js");

                if ($posterTxts) {
                    natsort($posterTxts);
                    $posterTxts = array_values($posterTxts);
                }
                if ($scripts) {
                    natsort($scripts);
                    $scripts = array_values($scripts);
                }

                $count = count($posterTxts);
                for ($i = 0; $i < $count; $i++) {
                    $scriptFile = $scripts[$i] ?? null;
                    $imageLink = trim(file_get_contents($posterTxts[$i]));

                    $results[] = [
                        'title' => $folder,
                        'image' => $imageLink,
                        'script' => $scriptFile ? "Book Café/$category/$folder/Scripts/" . basename($scriptFile) : null,
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
<link href="../../CSS/style.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Book Café</title>
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
  width: 320px;
  max-width: 90vw;
  padding: 12px 18px;
  font-size: 1.1rem;
  border-radius: 8px;
  border: none;
  background-color: #222;
  color: #fff;
  box-shadow: 0 0 6px #00aaffaa;
  transition: box-shadow 0.3s ease;
}
input#searchInput:focus {
  outline: none;
  box-shadow: 0 0 12px #00aaffee;
}
.buttons {
  margin-top: 15px;
  margin-bottom: 30px;
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
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
.gallery {
  display: flex;
  flex-wrap: wrap;
  gap: 18px;
  justify-content: center;
  max-width: 1200px;
  width: 100%;
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
  top: 10px;
  right: 10px;
  border: none;
  padding: 6px 10px;
  font-size: 1.2rem;
  border-radius: 6px;
  cursor: pointer;
  background: black;
  color: #fff;
  transition: background 0.2s;
  z-index: 2;
}
#overlay button.closeBtn:hover {
  background: #ff1a1a;
}
#overlay iframe {
  width: 100%;
  height: 100%;
  border: none;
  background: #111;
}
</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />

<div class="buttons">
  <?php
  $vetraPath = __DIR__ . '/Book Café';
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

<div class="gallery" id="gallery"></div>

<div id="overlay">
  <div class="container">
    <button class="closeBtn" onclick="closeOverlay()">✕</button>
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
  let currentSearch = '';
  let currentCategory = '';

  document.getElementById('searchInput').addEventListener('input', e => {
    currentSearch = e.target.value.trim();
    if (currentCategory) searchMedia();
  });

  function setCategory(cat, event) {
    currentCategory = cat;
    document.querySelectorAll('.buttons button').forEach(b => b.classList.remove('active'));
    event.target.classList.add('active');
    searchMedia();
  }

  async function searchMedia() {
    const resp = await fetch(`?search=${encodeURIComponent(currentSearch)}&category=${encodeURIComponent(currentCategory)}`);
    const data = await resp.json();

    const gallery = document.getElementById('gallery');
    gallery.innerHTML = '';

    if (data.length === 0) {
      gallery.textContent = 'No matches found.';
      return;
    }

    for (const item of data) {
      const img = document.createElement('img');
      img.src = item.image;
      img.title = item.title;
      img.onclick = () => {
        if (item.script) {
          openOverlay(item.script);
        } else {
          alert('No script available for this image.');
        }
      };
      gallery.appendChild(img);
    }
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