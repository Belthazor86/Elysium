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
<title>Image Viewer</title>
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
}

/* Button Styles */
button {
  background-color: transparent;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
}
</style>
</head> 

<body>

<?php
$folder = "Image Viewer/";
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

<button class="demo w3-opacity w3-hover-opacity-off button" id="import">Load</button>
<input id="file-input" type="file" multiple style="display: none">

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
          // Show the first image as soon as it's loaded
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