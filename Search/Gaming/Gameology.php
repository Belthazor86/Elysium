<?php
if (isset($_GET['embedVideo'])) {
    $path = $_GET['embedVideo'];
    $full = realpath(__DIR__ . '/' . $path);
    $allowed = realpath(__DIR__ . '/Gameology');

    if ($full && strpos($full, $allowed) === 0 && file_exists($full)) {
        header('Content-Type: text/html');
        echo "<!DOCTYPE html>
        <html>
        <body style='margin:0;background:#000;display:flex;justify-content:center;align-items:center;height:100vh;'>
          <video controls autoplay style='max-width:100%;max-height:100%;'>
            <source src='".htmlspecialchars($path)."' type='video/" . pathinfo($full, PATHINFO_EXTENSION) . "'>
          </video>
        </body>
        </html>";
    } else {
        http_response_code(403);
        exit;
    }
    exit;
}

if (isset($_GET['search']) && isset($_GET['category'])) {
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($_GET['search'])));
    $category = basename($_GET['category']);
    $baseDir = __DIR__ . "/Gameology/$category";
    $results = [];

    if (is_dir($baseDir)) {
        foreach (scandir($baseDir) as $folder) {
            if ($folder === '.' || $folder === '..') continue;
            $normalizedFolder = strtolower(str_replace([' ', '_', '-'], '', $folder));
            
            if ($query === '' || strpos($normalizedFolder, $query) !== false) {
                $postersDir = "$baseDir/$folder/Posters";
                $scriptsDir = "$baseDir/$folder/Scripts";

                $images = glob("$postersDir/*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
                // Included audio extensions in the search
                $media = glob("$scriptsDir/*.{mp4,webm,ogg,mp3,wav}", GLOB_BRACE);

                natsort($images);
                natsort($media);
                $images = array_values($images);
                $media = array_values($media);

                for ($i = 0; $i < count($images); $i++) {
                    $results[] = [
                        'title' => $folder,
                        'image' => "Gameology/$category/$folder/Posters/" . basename($images[$i]),
                        'file' => isset($media[$i]) ? "Gameology/$category/$folder/Scripts/" . basename($media[$i]) : null,
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
<title>Gameology</title>
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
#overlay iframe {
  width: 100%;
  height: 100%;
  border: none;
  border-radius: 0 0 10px 10px;
  background: #111;
}
</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
<div class="buttons">
  <?php
  $vetraPath = __DIR__ . '/Gameology';
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
  let currentAudio = null; // Global audio tracker

  document.getElementById('searchInput').addEventListener('input', e => {
    currentSearch = e.target.value.trim();
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
    closeOverlay();

    if (data.length === 0) {
      gallery.textContent = 'No matches found.';
      return;
    }

    for (const item of data) {
      const img = document.createElement('img');
      img.src = item.image;
      img.onclick = () => {
        if (item.file) {
          const ext = item.file.split('.').pop().toLowerCase();
          const isAudio = ['mp3', 'wav', 'ogg'].includes(ext);

          if (isAudio) {
            // Logic for Audio: Play/Stop without overlay
            if (currentAudio) {
              currentAudio.pause();
              currentAudio = null;
            }
            currentAudio = new Audio(item.file);
            currentAudio.play();
          } else {
            // Logic for Video: Open overlay
            openOverlay(item.file);
          }
        } else {
          alert('No media available.');
        }
      };
      gallery.appendChild(img);
    }
  }

  function openOverlay(videoPath) {
    // If audio is playing, stop it when video starts
    if (currentAudio) {
      currentAudio.pause();
      currentAudio = null;
    }
    const overlay = document.getElementById('overlay');
    const iframe = document.getElementById('scriptFrame');
    iframe.src = `?embedVideo=${encodeURIComponent(videoPath)}`;
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