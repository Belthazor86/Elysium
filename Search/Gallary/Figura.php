<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" /> 
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />  
<link href="../../CSS/style.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Figura</title>
<style>
body {
  font-weight: bold;
  margin: 0;
} 

#image {
  position: relative;
  width: 100%;
  max-width: 100%;
  height: 100vh;
  overflow: hidden;
  border-radius: 10px;
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
  background-color: #000;
  /* fade transition – only used by the automatic slideshow */
  transition: opacity 0.5s ease;
}

button {
    background: linear-gradient(135deg, #0d47a1, #1976d2); 
    color: whitesmoke;
    border: none;
    padding: 16px 32px;
    cursor: pointer;
    border-radius: 12px;
    margin: 10px;
    font-size: 1.2em;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    text-transform: capitalize;
}
button:hover {
    background: linear-gradient(135deg, #1976d2, #42a5f5);
    transform: scale(1.12);
    box-shadow: 0 6px 20px rgba(0,0,0,0.7);
}

 .video-slider-btn.left-side {top: 60%;}
	
 .video-slider-btn.right-side {top: 60%;}
</style>
</head> 

<body>

<?php
$folder = "Figura/";
$images = [];

if (is_dir($folder)) {
    $files = scandir($folder);
    foreach ($files as $file) {
        if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
            $images[] = $folder . $file;
        }
    }
}
?>


<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>


<!-- CENTER CONTAINER -->
<div style="width:100%; display:flex; justify-content:center; margin-top:20px;">
    <!-- LOAD BUTTON -->
<button class="demo w3-opacity w3-hover-opacity-off button" id="import">Load</button>
<!-- SLIDESHOW BUTTON -->
<button class="demo w3-opacity w3-hover-opacity-off button" id="slideshow-btn">Slideshow</button>
<input id="file-input" type="file" webkitdirectory multiple style="display: none">
</div>


<img id="image" src="<?php echo isset($images[0]) ? $images[0] : ''; ?>">

<button class="video-slider-btn left-side" id="prev">❮</button>
<button class="video-slider-btn right-side" id="next">❯</button>


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
const images = <?php echo json_encode($images); ?>;

let currentIndex = 0;
const imageElement = document.getElementById("image");

// ---------- Fade transition – used ONLY by the automatic slideshow ----------
function fadeToImage(newIndex) {
  if (images.length === 0) return;
  imageElement.style.opacity = '0';
  setTimeout(() => {
    currentIndex = newIndex;
    imageElement.src = images[currentIndex];
    imageElement.style.opacity = '1';
    if (slideshowActive) {
      clearInterval(slideshowInterval);
      slideshowInterval = setInterval(slideshowTick, 3000);
    }
  }, 500);
}

// ---------- Manual navigation – UNCHANGED, no fade ----------
document.getElementById("prev").addEventListener("click", () => {
  if (images.length === 0) return;
  currentIndex = (currentIndex - 1 + images.length) % images.length;
  imageElement.src = images[currentIndex];
});

document.getElementById("next").addEventListener("click", () => {
  if (images.length === 0) return;
  currentIndex = (currentIndex + 1) % images.length;
  imageElement.src = images[currentIndex];
});

// ---------- Slideshow logic ----------
let slideshowInterval = null;
let slideshowActive = false;

function slideshowTick() {
  if (images.length === 0) return;
  clearInterval(slideshowInterval);
  fadeToImage((currentIndex + 1) % images.length);
}

document.getElementById("slideshow-btn").addEventListener("click", () => {
  if (images.length === 0) return;
  
  if (slideshowActive) {
    // Stop
    clearInterval(slideshowInterval);
    slideshowInterval = null;
    slideshowActive = false;
    document.getElementById("slideshow-btn").textContent = "Start";
  } else {
    // Start
    slideshowActive = true;
    document.getElementById("slideshow-btn").textContent = "Stop";
    slideshowInterval = setInterval(slideshowTick, 3000);
  }
});

// ---------- File import (unchanged) ----------
document.getElementById("import").addEventListener("click", () => {
  document.getElementById("file-input").click();
});

document.getElementById("file-input").addEventListener("change", (event) => {
  const files = event.target.files;
  if (files && files.length > 0) {
    images.length = 0;
    let loadedCount = 0;

    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      if (file.type.startsWith("image/")) {
        const fileReader = new FileReader();
        fileReader.onload = (e) => {
          images.push(e.target.result);
          loadedCount++;
          if (loadedCount === 1) {
            currentIndex = 0;
            imageElement.src = images[0];
          }
        };
        fileReader.readAsDataURL(file);
      }
    }
  }
});

// ---------- Fullscreen toggle (unchanged) ----------
function toggleFullScreen() {
  if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.mozFullScreenElement && !document.msFullscreenElement) {
    if (imageElement.requestFullscreen) {
      imageElement.requestFullscreen();
    } else if (imageElement.webkitRequestFullscreen) {
      imageElement.webkitRequestFullscreen();
    } else if (imageElement.mozRequestFullScreen) {
      imageElement.mozRequestFullScreen();
    } else if (imageElement.msRequestFullscreen) {
      imageElement.msRequestFullscreen();
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    }
  }
}

imageElement.addEventListener('click', toggleFullScreen);
</script>
</body>
</html>