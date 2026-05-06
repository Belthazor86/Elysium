


<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/style.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/overlay.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Moodify</title>
<style>

  body {
    margin: 0;
    text-align: center;
    overflow-x: hidden;
  }

  #wallpaper {
    width: 100%;
    height: 75vh;
    background-size: contain; /* fit properly */
    background-position: center;
    background-repeat: no-repeat;
    transition: background-image 1s ease-in-out;
    cursor: pointer;
    box-shadow: inset 0 0 100px rgba(0,255,255,0.3);
  }

  #controls {
    margin: 20px auto;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
  }

  button, .fileButton {
    padding: 12px 25px;
    background: #111;
    border: 2px solid #0ff;
    border-radius: 12px;
    color: #0ff;
    font-size: 1em;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    box-shadow: 0 0 10px #0ff;
  }

  button:hover, .fileButton:hover {
    background: #0ff;
    color: #000;
    box-shadow: 0 0 20px #0ff, 0 0 40px #0ff;
    transform: scale(1.05);
  }

  input[type="file"] {
    display: none; /* hide default input */
  }

  #fullscreenOverlay {
    display: none;
    position: fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background: rgba(0,0,0,0.95);
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }

  #fullscreenOverlay img {
    width: 100%;
    height: 100%;
    object-fit: contain; /* fit properly */
    border: 4px solid #0ff;
    border-radius: 10px;
    box-shadow: 0 0 50px #0ff;
    transition: transform 0.3s ease;
  }

  #fullscreenOverlay img:hover {
    transform: scale(1.05);
  }
</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<div id="wallpaper"></div>

<div id="controls">
  <button id="prevBtn">Previous</button>
  <button id="playBtn">Play</button>
  <label for="folderPicker" class="fileButton">Load</label>
  <input type="file" id="folderPicker" webkitdirectory directory multiple>
  <button id="pauseBtn">Pause</button>
  <button id="nextBtn">Next</button>
</div>

<audio id="audio" controls style="display:none;"></audio>
<div id="fullscreenOverlay"><img src="" alt="Fullscreen Wallpaper"></div>



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
let musicFiles = [];
let imageFiles = [];
let currentTrack = 0;
let wallpaperDiv = document.getElementById('wallpaper');
let audio = document.getElementById('audio');
let folderPicker = document.getElementById('folderPicker');
let fullscreenOverlay = document.getElementById('fullscreenOverlay');
let fullscreenImg = fullscreenOverlay.querySelector('img');

// Load folder & numeric sort
folderPicker.addEventListener('change', (e) => {
  musicFiles = [];
  imageFiles = [];
  currentTrack = 0;
  const files = Array.from(e.target.files);

  files.forEach(file => {
    const ext = file.name.split('.').pop().toLowerCase();
    if(['mp3','wav','ogg','m4a'].includes(ext)) musicFiles.push(file);
    if(['jpg','jpeg','png','gif'].includes(ext)) imageFiles.push(file);
  });

  const numericSort = (a, b) => {
  const nameA = a.webkitRelativePath.toLowerCase();
  const nameB = b.webkitRelativePath.toLowerCase();
  return nameA.localeCompare(nameB);
 };

  musicFiles.sort(numericSort);
  imageFiles.sort(numericSort);

  if(musicFiles.length===0) alert('No music files found!');
  if(imageFiles.length===0) alert('No image files found!');

  if(musicFiles.length>0) playTrack(currentTrack); // auto-play on folder load
});

// Play song & update wallpaper
function playTrack(index){
  if(musicFiles.length===0) return;

  audio.src = URL.createObjectURL(musicFiles[index]);
  audio.play();

  if(imageFiles.length>0){
    const wallpaperIndex = index % imageFiles.length;
    wallpaperDiv.style.backgroundImage = `url(${URL.createObjectURL(imageFiles[wallpaperIndex])})`;
  }

  // Automatic next track
  audio.onended = () => {
    if(currentTrack < musicFiles.length - 1){
      currentTrack++;
      playTrack(currentTrack);
    } else {
      audio.pause(); // stop at last song
    }
  };
}

// Next / Previous track
function nextTrack(){ if(currentTrack<musicFiles.length-1){ currentTrack++; playTrack(currentTrack); } }
function prevTrack(){ if(currentTrack>0){ currentTrack--; playTrack(currentTrack); } }

// Buttons
document.getElementById('playBtn').addEventListener('click', ()=>playTrack(currentTrack));
document.getElementById('pauseBtn').addEventListener('click', ()=>audio.pause());
document.getElementById('nextBtn').addEventListener('click', nextTrack);
document.getElementById('prevBtn').addEventListener('click', prevTrack);

// Click wallpaper for fullscreen
wallpaperDiv.addEventListener('click', () => {
  if(imageFiles.length===0) return;
  const wallpaperIndex = currentTrack % imageFiles.length;
  fullscreenImg.src = URL.createObjectURL(imageFiles[wallpaperIndex]);
  fullscreenOverlay.style.display='flex';
});
fullscreenOverlay.addEventListener('click', ()=>fullscreenOverlay.style.display='none');
</script>






</body>
</html>
