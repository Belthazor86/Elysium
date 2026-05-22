

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
<title>Video Snapshot</title>
</head>	
<style>
		
body {
  font-weight: bold;
  margin: 0;
  overflow: hidden; /* Prevent full page scrolling */
}
	
#video-player {
  width: 100%;
  height: calc(100vh - 220px);
  object-fit: contain;
  display: block;
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

 .video-slider-btn.left-side {top: 70%;}
	
 .video-slider-btn.right-side {top: 70%;}
		
</style>

	
<body>




<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>



<div style="width:100%; display:flex; justify-content:center; margin-top:20px;">
<button class="demo w3-opacity w3-hover-opacity-off button" id="upload-button">Load</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="snapshot-button">Snapshot</button>
</div>
	
<video id="video-player" controls autoplay></video>
<button class="video-slider-btn left-side" id="previous-button">❮</button>
<button class="video-slider-btn right-side" id="next-button">❯</button>



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
const previousButton = document.getElementById('previous-button');
const nextButton = document.getElementById('next-button');
const snapshotButton = document.getElementById('snapshot-button');

let currentFileIndex = 0;
let files = [];

function validateFile(file) {
  const allowedTypes = ['video/mp4', 'video/webm', 'video/ogg'];
  if (!allowedTypes.includes(file.type)) {
    alert('Invalid file type. Please select a video file.');
    return false;
  }
  return true;
}

uploadButton.addEventListener('click', function() {
  const fileInput = document.createElement('input');
  fileInput.type = 'file';
  fileInput.multiple = true;
  fileInput.addEventListener('change', function() {
    files = Array.from(this.files).filter(validateFile);
    if (files.length > 0) {
      currentFileIndex = 0;
      playCurrentFile();
    } else {
      alert('No valid files selected.');
    }
  });
  fileInput.click();
});

previousButton.addEventListener('click', function() {
  currentFileIndex = (currentFileIndex - 1 + files.length) % files.length;
  playCurrentFile();
});

nextButton.addEventListener('click', function() {
  currentFileIndex = (currentFileIndex + 1) % files.length;
  playCurrentFile();
});

snapshotButton.addEventListener('click', function() {
  takeSnapshot();
});

function playCurrentFile() {
  const file = files[currentFileIndex];
  if (file) {
    const url = URL.createObjectURL(file);
    const source = document.createElement('source');
    source.src = url;
    source.type = file.type;
    videoPlayer.innerHTML = '';
    videoPlayer.appendChild(source);
    videoPlayer.load();
    videoPlayer.play();
  } else {
    alert('No file to play.');
  }
}

function takeSnapshot() {
  const canvas = document.createElement('canvas');
  const context = canvas.getContext('2d');
  canvas.width = 1920;
  canvas.height = 1080;
  context.drawImage(videoPlayer, 0, 0, canvas.width, canvas.height);
  const snapshotUrl = canvas.toDataURL('image/png');
  const link = document.createElement('a');
  link.href = snapshotUrl;
  link.download = 'snapshot.png';
  link.click();
}
</script>
	


	


	
	
</body>
</html>
