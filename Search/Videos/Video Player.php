<?php
// Scan the "video" folder for files
$videoDirectory = 'Video Player/';
$videoFiles = [];

if (is_dir($videoDirectory)) {
    $files = scandir($videoDirectory);
    foreach ($files as $file) {
        // Filter for video extensions
        if (preg_match('/\.(mp4|webm|ogg|flv)$/i', $file)) {
            $videoFiles[] = $videoDirectory . $file;
        }
    }
}
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
<title>Video PLayer</title>
<style>
body {
  font-weight: bold;
  margin: 0;
}
#video-player {
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
}
#video-input {
  margin-top: 20px;
}

button {
  background-color: transparent;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
  margin-right: 15px; 
}

/* Removes margin from the last button so it stays aligned */
button:last-of-type {
  margin-right: 0;
}
</style>
</head> 

<body>

<button class="demo w3-opacity w3-hover-opacity-off button" id="upload-button">Load</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="video-scan-btn">Scan</button>

<video id="video-player" controls autoplay>
  <p>Your browser does not support HTML5 video. Please update your browser or try another.</p>
</video>
<button class="video-slider-btn left-side" id="previous-button">❮</button>
<button class="video-slider-btn right-side" id="next-button">❯</button>

<script src="https://cdn.jsdelivr.net/npm/flv.js@latest"></script>



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
  const videoPlayer = document.getElementById('video-player');
  const uploadButton = document.getElementById('upload-button');
  const scanButton = document.getElementById('video-scan-btn');
  const previousButton = document.getElementById('previous-button');
  const nextButton = document.getElementById('next-button');

  let currentFileIndex = 0;
  let files = []; // This will hold either File objects or URL strings
  let flvPlayer = null;

  // Manual load
  uploadButton.addEventListener('click', function () {
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.multiple = true;
    fileInput.accept = 'video/*'; 
    fileInput.addEventListener('change', function() {
        files = Array.from(this.files);
        currentFileIndex = 0;
        playCurrentFile();
    });
    fileInput.click();
  });

  // Folder scan (Server-side)
  // We pass the PHP array into JS
  const serverFiles = <?php echo json_encode($videoFiles); ?>;

  scanButton.addEventListener('click', function() {
    if (serverFiles.length > 0) {
        files = serverFiles; // Use the list of paths from the server
        currentFileIndex = 0;
        playCurrentFile();
    } else {
        alert("No videos found in the 'video' folder.");
    }
  });

  previousButton.addEventListener('click', function () {
    if (files.length === 0) return;
    currentFileIndex = (currentFileIndex - 1 + files.length) % files.length;
    playCurrentFile();
  });

  nextButton.addEventListener('click', function () {
    if (files.length === 0) return;
    currentFileIndex = (currentFileIndex + 1) % files.length;
    playCurrentFile();
  });

  function playCurrentFile() {
    const file = files[currentFileIndex];
    if (!file) return;

    // Determine if it's a File object (from upload) or a string (from server)
    let url = (typeof file === 'string') ? file : URL.createObjectURL(file);
    let fileName = (typeof file === 'string') ? file : file.name;

    videoPlayer.pause();
    videoPlayer.innerHTML = '';
    videoPlayer.removeAttribute('src');
    videoPlayer.load();

    if (flvPlayer) {
      flvPlayer.pause();
      flvPlayer.unload();
      flvPlayer.detachMediaElement();
      flvPlayer.destroy();
      flvPlayer = null;
    }

    if (fileName.toLowerCase().endsWith('.flv')) {
      if (flvjs.isSupported()) {
        flvPlayer = flvjs.createPlayer({ type: 'flv', url: url });
        flvPlayer.attachMediaElement(videoPlayer);
        flvPlayer.load();
        flvPlayer.play().catch(e => console.error("FLV Error", e));
      } else {
        alert('FLV not supported.');
      }
    } else {
      videoPlayer.src = url;
      videoPlayer.play().catch(error => console.error('Playback error:', error));
    }
  }
</script>
      
</body>
</html>