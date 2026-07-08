<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>

<?php
if (isset($_GET['search']) && isset($_GET['category'])) {
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($_GET['search'])));
    $category = basename($_GET['category']);
    $baseDir = __DIR__ . "/Themata/$category";
    $results = [];

    if (is_dir($baseDir)) {
        foreach (scandir($baseDir) as $folder) {
            if ($folder === '.' || $folder === '..') continue;
            $normalizedFolder = strtolower(str_replace([' ', '_', '-'], '', $folder));

            if ($query === '' || strpos($normalizedFolder, $query) !== false) {
                $folderPath = "$baseDir/$folder";
                if (!is_dir($folderPath)) continue;

                // Scan item subfolders inside this title group
                $itemFolders = glob("$folderPath/*", GLOB_ONLYDIR);
                if (empty($itemFolders)) continue;

                foreach ($itemFolders as $itemPath) {
                    $itemFolderName = basename($itemPath);

                    // === Get all files and filter case‑insensitively ===
                    $allFiles = glob("$itemPath/*");
                    if ($allFiles === false) $allFiles = [];

                    // images
                    $imageExts = ['jpg','jpeg','png','gif','webp'];
                    $images = [];
                    foreach ($allFiles as $f) {
                        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                        if (in_array($ext, $imageExts)) {
                            $images[] = $f;
                        }
                    }
                    natsort($images);
                    $images = array_values($images);

                    // audio (same case‑insensitive, wider list)
                    $audioExts = ['mp3','ogg','wav','flac','m4a','aac','opus','wma','weba'];
                    $audios = [];
                    foreach ($allFiles as $f) {
                        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                        if (in_array($ext, $audioExts)) {
                            $audios[] = $f;
                        }
                    }
                    natsort($audios);
                    $audios = array_values($audios);

                    // === Relaxed conditions ===
                    // Must have at least one image (cover)
                    if (count($images) === 0) continue;

                    $coverImage = $images[0]; // first image as cover
                    // Wallpaper is second image if it exists, else null
                    $wallpaperImage = count($images) >= 2 ? $images[1] : null;
                    // Audio is optional
                    $audioFile = count($audios) >= 1 ? $audios[0] : null;

                    // Build relative paths
                    $coverRel = "Themata/$category/$folder/$itemFolderName/" . basename($coverImage);
                    $wallpaperRel = $wallpaperImage ? "Themata/$category/$folder/$itemFolderName/" . basename($wallpaperImage) : null;
                    $audioRel = $audioFile ? "Themata/$category/$folder/$itemFolderName/" . basename($audioFile) : null;

                    $results[] = [
                        'title' => $folder,
                        'cover' => $coverRel,
                        'wallpaper' => $wallpaperRel,
                        'audio' => $audioRel
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
<title>Vetra Audio</title>
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

/* Overlay for wallpaper + audio */
#overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.9);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  flex-direction: column;
}
#overlay img {
  max-width: 90%;
  max-height: 70vh;
  object-fit: contain;
  border-radius: 10px;
  box-shadow: 0 0 25px #00aaff;
}
#overlay audio {
  margin-top: 20px;
  width: 80%;
  max-width: 500px;
}
#overlay .closeBtn {
  position: absolute;
  top: 20px;
  right: 30px;
  background: black;
  color: white;
  border: 2px solid #00aaff;
  padding: 8px 16px;
  font-size: 1.2rem;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
  z-index: 2;
}
#overlay .closeBtn:hover {
  background: #00aaff;
  color: black;
}
</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
<div class="buttons">
  <?php
  $vetraPath = __DIR__ . '/Themata';
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

<!-- Overlay for wallpaper and audio -->
<div id="overlay">
  <button class="closeBtn" onclick="closeOverlay()">✕</button>
  <img id="wallpaperImg" src="" alt="Wallpaper" />
  <audio id="audioPlayer" hidden autoplay></audio>
</div>

<!-- Footer -->
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
  let currentSearch = '';
  let currentCategory = '';

  document.getElementById('searchInput').addEventListener('input', e => {
    currentSearch = e.target.value.trim();
    if(currentCategory) searchMedia();
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
      img.src = item.cover;
      img.onclick = () => {
        openOverlay(item.wallpaper, item.audio);
      };
      gallery.appendChild(img);
    }
  }

  function openOverlay(wallpaperPath, audioPath) {
    const overlay = document.getElementById('overlay');
    const img = document.getElementById('wallpaperImg');
    const audio = document.getElementById('audioPlayer');

    // Show wallpaper if present, else hide the image element
    if (wallpaperPath) {
      img.src = wallpaperPath;
      img.style.display = '';
    } else {
      img.style.display = 'none';
    }

    // Play audio if present, else stop any previous audio
    if (audioPath) {
      audio.src = audioPath;
      audio.load();
      audio.play();
    } else {
      audio.pause();
      audio.src = '';
    }

    overlay.style.display = 'flex';
  }

  function closeOverlay() {
    document.getElementById('overlay').style.display = 'none';
    const audio = document.getElementById('audioPlayer');
    audio.pause();
    audio.src = '';
  }

  document.getElementById('overlay').addEventListener('click', e => {
    if (e.target === document.getElementById('overlay')) closeOverlay();
  });
</script>

</body>
</html>