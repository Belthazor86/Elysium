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




<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>


<!-- CENTER CONTAINER -->
<div style="width:100%; display:flex; justify-content:center; margin-top:20px;">
    <!-- LOAD BUTTON -->
<button class="demo w3-opacity w3-hover-opacity-off button" id="upload-button">Load</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="video-scan-btn">Scan</button>
</div>


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