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
<title>Storyboard</title>
</head> 
<style>
  
body {
  font-weight: bold;
  margin: 0;
}

.logo img {width: 35%;}

.container {
  padding-top: 10px;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  
 }

pre {
    padding: 15px;
    border: 1px solid whitesmoke;
    border-radius: 6px;
    width: 100%;
    max-width: 800px;
    overflow-x: auto;
    white-space: pre-wrap;
    word-break: break-word;
    font-size: 14px;
    line-height: 1.5;
    background-color: #000;
    color: whitesmoke;
    text-align: center;
    margin: 0 auto;
    display: block;
}

.overlay {overflow-y: scroll;}

 .button-container {
  display: flex;
  flex-direction: column;
  position: fixed;
  right: 0;
  top: 60px;
  z-index: 1;
}

.button {
  background-color: transparent;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
  padding: 10px 20px;
  pointer-events: auto;
  text-align: right;
}
</style>
  
<body>

<div style="text-align:center; padding: 10px;">
    <input type="file" id="folderPicker" webkitdirectory directory multiple style="display:none;" onchange="handleFiles(this.files)">
    
</div>
    
<div class="logo">
    <img id="mainLogo" class="demo w3-opacity w3-hover-opacity-off" src="" alt="" width="25%">
</div>

<div class="button-container">
  <button class="demo w3-opacity w3-hover-opacity-off button" onclick="document.getElementById('folderPicker').click()">Load</button>
  <button class="demo w3-opacity w3-hover-opacity-off button" onclick="openNav()">Story</button>
</div>
      
<div id="myNav" class="overlay">    
<pre id="output"></pre>
<button href="javascript:void(0)" class="video-slider-btn closebtn" onclick="closeNav()">❌</button>  
</div>  

<div class="container">
    <div id="audioList" class="container icon"></div>
</div>

<div id="audioContainer"></div>


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
let audio = null;
let currentSrc = '';



function handleFiles(files) {
    const list = document.getElementById('audioList');
    const output = document.getElementById('output');
    const logoImg = document.getElementById('mainLogo');
    
    list.innerHTML = ''; 
    output.textContent = '';
    
    const fileList = Array.from(files);
    let audioCount = 0; // The counter must start here

    fileList.forEach(file => {
        const url = URL.createObjectURL(file);
        const path = file.webkitRelativePath.toLowerCase();
        const name = file.name.toLowerCase();

        // Logo Logic
        if (path.includes('/logo/') && (name.endsWith('.png') || name.endsWith('.jpg') || name.endsWith('.jpeg'))) {
            logoImg.src = url;
        }

        // Story Logic
        if (path.includes('/story/') && name.endsWith('.txt')) {
            const reader = new FileReader();
            reader.onload = (e) => { output.textContent = e.target.result; };
            reader.readAsText(file);
        }

        // Audio Logic
        if (name.endsWith('.mp3')) {
            audioCount++; // Increment count so the grid logic works
            const div = document.createElement('div');
            const displayName = file.name.replace('.mp3', '');
            div.innerHTML = `<a href="#" onclick="toggleAudio('${url}'); return false;" class="demo w3-opacity w3-hover-opacity-off">${displayName}</a>`;
            list.appendChild(div);
        }
    });

    // Grid Logic - Strictly greater than 3
    if (audioCount > 3) {
        list.style.display = "grid";
        list.style.gridTemplateColumns = "repeat(auto-fit, minmax(200px, 1fr));";
    } else {
        list.style.display = "grid";
        list.style.gridTemplateColumns = "repeat(1, 1fr)";
    }
}

function toggleAudio(src) {
    const container = document.getElementById('audioContainer');
    if (!audio) {
        audio = document.createElement('audio');
        audio.style.display = 'none';
        container.appendChild(audio);
    }
    if (currentSrc === src) {
        audio.paused ? audio.play() : audio.pause();
    } else {
        audio.src = src;
        audio.play();
        currentSrc = src;
    }
}

function openNav() { document.getElementById("myNav").style.height = "100%"; }
function closeNav() { document.getElementById("myNav").style.height = "0%"; }



</script>

</body>
</html>