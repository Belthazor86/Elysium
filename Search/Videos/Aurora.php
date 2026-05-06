<?php
if (isset($_GET['search']) && isset($_GET['category'])) {
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($_GET['search'])));
    $category = basename($_GET['category']);
    $baseDir = __DIR__ . "/Aurora/$category";
    $results = [];

    if (is_dir($baseDir)) {
        foreach (scandir($baseDir) as $folder) {
            if ($folder === '.' || $folder === '..') continue;
            $normalizedFolder = strtolower(str_replace([' ', '_', '-'], '', $folder));
            
            if ($query === '' || strpos($normalizedFolder, $query) !== false) {
                $postersDir = "$baseDir/$folder/Posters";
                $scriptsDir = "$baseDir/$folder/Scripts";

                $images = glob("$postersDir/*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE) ?: [];
                $videos = glob("$scriptsDir/*.{mp4,webm,ogg}", GLOB_BRACE) ?: [];

                natsort($images);
                natsort($videos);
                
                $images = array_values($images);
                $videos = array_values($videos);

                $videoPaths = array_map(function($v) use ($category, $folder) {
                    return "Aurora/$category/$folder/Scripts/" . basename($v);
                }, $videos);

                for ($i = 0; $i < count($images); $i++) {
                    $results[] = [
                        'title' => $folder,
                        'image' => "Aurora/$category/$folder/Posters/" . basename($images[$i]),
                        'playlist' => $videoPaths 
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
<title>Aurora</title>
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
/* This tag is required for the video to fill your existing container */
#mainVideo {
  width: 100%;
  height: 100%;
  background: #000;
}

</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
<div class="buttons">
  <?php
  $vetraPath = __DIR__ . '/Aurora';
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
  <div class="container" style="position: relative;">
    <video id="mainVideo" autoplay></video>
    <!-- Previous Button -->
    <button class="prev" onclick="playPrevious()" style="position: absolute; top: 50%; left: 15px; transform: translateY(-50%); background: rgba(0, 0, 0, 0.5); border: 2px solid #00aaff; color: #00aaff; font-size: 25px; padding: 15px 10px; cursor: pointer; border-radius: 8px; z-index: 10; transition: 0.3s;">❮</button>
    <!-- Next Button -->
    <button class="next" onclick="playNext()" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); background: rgba(0, 0, 0, 0.5); border: 2px solid #00aaff; color: #00aaff; font-size: 25px; padding: 15px 10px; cursor: pointer; border-radius: 8px; z-index: 10; transition: 0.3s;">❯</button>
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
  let activePlaylist = [];
  let currentIndex = 0;

  const videoPlayer = document.getElementById('mainVideo');

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
        if (item.playlist && item.playlist.length > 0) {
          startPlaylist(item.playlist);
        } else {
          alert('No videos available.');
        }
      };
      gallery.appendChild(img);
    }
  }

  function startPlaylist(list) {
    activePlaylist = list;
    currentIndex = 0;
    playCurrent();
    document.getElementById('overlay').style.display = 'flex';
  }

  function playCurrent() {
    if (currentIndex < activePlaylist.length) {
      videoPlayer.src = activePlaylist[currentIndex];
      videoPlayer.play();
    } else {
      closeOverlay();
    }
  }

  videoPlayer.onended = () => {
    currentIndex++;
    playCurrent();
  };

  function closeOverlay() {
    const overlay = document.getElementById('overlay');
    videoPlayer.pause();
    videoPlayer.src = '';
    overlay.style.display = 'none';
  }

  document.getElementById('overlay').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeOverlay();
  });


function playCurrent() {
  if (currentIndex >= 0 && currentIndex < activePlaylist.length) {
    videoPlayer.src = activePlaylist[currentIndex];
    videoPlayer.play();
  } else if (currentIndex >= activePlaylist.length) {
    // If we've reached the end, close the overlay
    closeOverlay();
  } else {
    // Prevent going below index 0
    currentIndex = 0;
  }
}

// Next Button Function
function playNext() {
  currentIndex++;
  if (currentIndex < activePlaylist.length) {
    playCurrent();
  } else {
    // Optional: Loop back to start or close
    closeOverlay();
  }
}

// Previous Button Function
function playPrevious() {
  if (currentIndex > 0) {
    currentIndex--;
    playCurrent();
  }
}

// Auto-play next when current ends
videoPlayer.onended = () => {
  playNext();
};











</script>

</body>
</html>