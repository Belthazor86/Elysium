<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<?php
/**
 * AJAX Handler: Searches Vidacava (or specified category) 
 * Returns JSON array of video paths and titles.
 */
if (isset($_GET['search']) && isset($_GET['category'])) {
    // Normalize the search query
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($_GET['search'])));
    
    // Safety check on category
    $category = basename($_GET['category']); 
    $baseDir = __DIR__ . "/$category";
    $results = [];

    if (is_dir($baseDir)) {
        // Scan for folders matching the search query
        foreach (scandir($baseDir) as $folder) {
            if ($folder === '.' || $folder === '..') continue;

            $normalizedFolder = strtolower(str_replace([' ', '_', '-'], '', $folder));
            
            // If the folder matches the search or search is empty
            if ($query === '' || strpos($normalizedFolder, $query) !== false) {
                $folderPath = "$baseDir/$folder";
                
                // Look specifically for video formats
                $videos = glob("$folderPath/*.{mp4,webm,ogg}", GLOB_BRACE);

                if ($videos) {
                    foreach ($videos as $video) {
                        $results[] = [
                            'title' => pathinfo($video, PATHINFO_FILENAME),
                            'path' => "$category/$folder/" . basename($video)
                        ];
                    }
                }
            }
        }
    }

    // Set JSON header and output
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
  <div class="container" style="position: relative; background: #000;">
    <!-- VIDEO PLAYER -->
    <video id="mainVideo" controls autoplay style="width: 100%; height: 100%;"></video>
    <!-- Side Navigation Buttons -->
    <!-- Added fixed width/height and flex centering to prevent stretching -->
    <button class="prev" onclick="playPrevious()" style="position: absolute; top: 50%; left: 15px; transform: translateY(-50%); background: rgba(0, 0, 0, 0.5); border: 2px solid #00ffc3; color: #00ffc3; font-size: 25px; width: 50px; height: 60px; display: none; align-items: center; justify-content: center; cursor: pointer; border-radius: 8px; z-index: 10; transition: 0.3s;">❮</button>

    <button class="next" onclick="playNext()" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); background: rgba(0, 0, 0, 0.5); border: 2px solid #00ffc3; color: #00ffc3; font-size: 25px; width: 50px; height: 60px; display: none; align-items: center; justify-content: center; cursor: pointer; border-radius: 8px; z-index: 10; transition: 0.3s;">❯</button>

  </div>
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
let currentCategory = 'Lorevania';
let currentSearch = '';
let activePlaylist = [];
let currentIndex = 0;
const videoPlayer = document.getElementById('mainVideo');

// 1. Handle Search Input
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

// 2. Fetch Search Results
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

  // Save the full list of found video paths
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

// 3. Overlay Controls
function openOverlay() {
  const overlay = document.getElementById('overlay');
  overlay.style.display = 'flex';
  playCurrent();
}

function closeOverlay() {
  const overlay = document.getElementById('overlay');
  videoPlayer.pause();
  videoPlayer.src = '';
  overlay.style.display = 'none';
}

// 4. Core Playback Logic
function playCurrent() {
  if (activePlaylist.length > 0 && currentIndex >= 0 && currentIndex < activePlaylist.length) {
    // Reset player and change source
    videoPlayer.pause();
    videoPlayer.src = activePlaylist[currentIndex];
    videoPlayer.load(); 
    videoPlayer.play().catch(e => console.log("Autoplay blocked."));

    // Handle Button Visibility and prevent "stretching"
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');
    
    if (prevBtn) {
      prevBtn.style.display = (currentIndex === 0) ? 'none' : 'flex';
    }
    
    if (nextBtn) {
      nextBtn.style.display = (currentIndex === activePlaylist.length - 1) ? 'none' : 'flex';
    }
  }
}

// 5. Navigation Functions
function playNext() {
  if (currentIndex < activePlaylist.length - 1) {
    currentIndex++;
    playCurrent();
  }
}

function playPrevious() {
  if (currentIndex > 0) {
    currentIndex--;
    playCurrent();
  }
}

// 6. Event Listeners
videoPlayer.onended = () => playNext();

document.getElementById('overlay').addEventListener('click', e => {
  if (e.target === e.currentTarget) closeOverlay();
});
</script>


</body>
</html>
