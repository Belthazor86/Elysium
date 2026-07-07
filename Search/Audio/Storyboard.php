<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


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
<link href="../../CSS/sidenav.css" rel="stylesheet" type="text/css" />
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
.overlay { overflow-y: scroll; }

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

</style>
  
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<div style="width:100%; display:flex; justify-content:center; margin-top:20px;">
    <!-- LOAD BUTTON -->
<div class="button-container">
  <button class="demo w3-opacity w3-hover-opacity-off button" onclick="toggleNav()">Load</button>
  <button class="demo w3-opacity w3-hover-opacity-off button" onclick="openNav()">Story</button>
</div>
</div>



<div id="mySidenav" class="sidenav">
    <div class="row">
        <div class="left">
            <input type="text" id="mySearch" onkeyup="myFunction()" placeholder="Search.." title="Type in a category" autocomplete="off">
            <ul id="myMenu">
              <?php
                $mainFolder = "Storyboard/";
                $storyboardData = [];

                if (is_dir($mainFolder)) {
                    $dirs = array_filter(glob($mainFolder . '*'), 'is_dir');
                    foreach ($dirs as $dir) {
                        $folderName = basename($dir);
                        $storyboardData[$folderName] = [];
                        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
                        foreach ($iterator as $file) {
                            if ($file->isFile()) {
                                $storyboardData[$folderName][] = [
                                    'name' => $file->getFilename(),
                                    'path' => $dir . '/' . $iterator->getSubPathName()
                                ];
                            }
                        }
                        echo "<li data-title='".htmlspecialchars($folderName, ENT_QUOTES)."'>
                                <a class='demo w3-opacity w3-hover-opacity-off icon' 
                                   onclick=\"loadFolderData('".addslashes($folderName)."'); toggleNav();\">
                                   ".htmlspecialchars($folderName)."
                                </a>
                              </li>";
                    }
                }
              ?>
            </ul>
        </div> 
    </div>
</div>

<div class="logo">
    <img id="mainLogo" class="demo w3-opacity w3-hover-opacity-off" src="" alt="" width="25%">
</div>
    
<div id="myNav" class="overlay">    
    <pre id="output"></pre>
    <button class="video-slider-btn closebtn" onclick="closeNav()">❌</button>  
</div>  

<div class="container">
    <div id="audioList" class="container icon"></div>
</div>

<div id="audioContainer"></div>


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
const storyboardContent = <?php echo json_encode($storyboardData); ?>;
let audio = null;
let currentSrc = '';

function loadFolderData(folderName) {
    const list = document.getElementById('audioList');
    const output = document.getElementById('output');
    const logoImg = document.getElementById('mainLogo');
    
    list.innerHTML = ''; 
    output.textContent = '';
    logoImg.src = '';

    const files = storyboardContent[folderName];
    if (!files) return;

    let audioCount = 0;

    files.forEach(file => {
        const nameLower = file.name.toLowerCase();
        const pathLower = file.path.toLowerCase();
        const normalizedPath = file.path.replace(/\\/g, '/');

        if ((pathLower.includes('logo') || nameLower.includes('logo')) && (nameLower.endsWith('.png') || nameLower.endsWith('.jpg') || nameLower.endsWith('.jpeg'))) {
            logoImg.src = normalizedPath;
        }

        if ((pathLower.includes('story') || nameLower.includes('story')) && nameLower.endsWith('.txt')) {
            fetch(normalizedPath)
                .then(res => res.text())
                .then(text => { output.textContent = text; });
        }

        if (nameLower.endsWith('.mp3')) {
            audioCount++;
            const div = document.createElement('div');
            const displayName = file.name.replace('.mp3', '');
            const safePath = encodeURI(normalizedPath).replace(/'/g, "\\'");
            
            div.innerHTML = `<a href="javascript:void(0)" onclick="toggleAudio('${safePath}')" class="demo w3-opacity w3-hover-opacity-off">${displayName}</a>`;
            list.appendChild(div);
        }
    });

    if (audioCount > 3) {
        list.style.display = "grid";
        list.style.gridTemplateColumns = "repeat(auto-fit, minmax(200px, 1fr))";
    } else {
        list.style.display = "grid";
        list.style.gridTemplateColumns = "repeat(1, 1fr)";
    }
}

function toggleAudio(encodedSrc) {
    const src = decodeURI(encodedSrc);
    if (!audio) {
        audio = new Audio();
    }
    if (currentSrc === src) {
        audio.paused ? audio.play() : audio.pause();
    } else {
        audio.src = src;
        audio.load();
        audio.play();
        currentSrc = src;
    }
}

function openNav() { document.getElementById("myNav").style.height = "100%"; }
function closeNav() { document.getElementById("myNav").style.height = "0%"; }

function toggleNav() {
  const sidenav = document.getElementById("mySidenav");
  sidenav.style.width = (sidenav.style.width === "250px") ? "0" : "250px";
}

function myFunction() {}
function closePDF() {}
</script>


</body>
</html>