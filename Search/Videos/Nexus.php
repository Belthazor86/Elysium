

<?php
$folder = __DIR__ . '/Nexus';
$webFolder = 'Nexus';
$files = [];

if (is_dir($folder)) {
    foreach (scandir($folder) as $f) {
        if ($f === '.' || $f === '..') continue;

        $path = $folder . '/' . $f;

        // Only grab HTML files directly in Nexus/
        if (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'html') {
            $files[] = [
                'name' => pathinfo($f, PATHINFO_FILENAME),
                'path' => $webFolder . '/' . $f
            ];
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
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Nexus</title>
<style>
body {
    margin:0; padding:0;
    background: radial-gradient(circle at top, #000 0%, #010101 100%);
    display:flex; 
    flex-direction:column;
    align-items:center; 
    justify-content:center;
    height:100vh; 
    overflow:hidden;
}

.buttons { 
    display:flex; gap:20px; 
    margin-bottom:25px; 
}

button {
    background: linear-gradient(90deg,#111,#222);
    color: #ff0000; 
    border:2px solid #ff0000; 
    padding:14px 30px; 
    font-size:1.2em;
    border-radius:12px; 
    cursor:pointer; 
    text-transform:uppercase;
    transition:0.3s; 
    box-shadow:0 0 10px #ff00003,0 0 30px #ff0000 inset;
}

button:hover { 
    background:#ff0000; 
    color:#000; 
    transform:scale(1.05); 
    box-shadow:0 0 20px #ff0000,0 0 60px #ff0000 inset; 
}

#gameList {
    display:none; 
    flex-direction:column; 
    gap:15px; 
    max-height:50vh; 
    overflow-y:auto; 
    width:300px;
}

.game-item {
    background:#111; 
    border:1px solid #ff00002; 
    padding:10px; 
    border-radius:8px;
    cursor:pointer; 
    text-align:center; 
    transition:0.2s;
}

.game-item:hover { 
    background:#ff0000; 
    color:#000; 
    transform:translateY(-3px); 
}

#overlay {
    position:fixed; 
    top:0; left:0; 
    width:100%; height:100%;
    background: rgba(0,0,0,0.97); 
    display:none; 
    flex-direction:column; 
    justify-content:center; 
    align-items:center;
    z-index:1000;
}

#iframe-container { 
    width:80%; 
    height:80%; 
    border-radius:20px; 
    overflow:hidden; 
    box-shadow:0 0 40px #ff00004; 
}

iframe { 
    width:100%; 
    height:100%; 
    border:none; 
}

#controls { 
    margin-top:20px; 
    display:flex; 
    gap:15px; 
}

#backBtn, #fullBtn { 
    padding:12px 25px; 
    border-radius:10px; 
    border:2px solid #ff0000; 
    background:transparent; 
    color:#ff0000; 
    cursor:pointer; 
    font-weight:bold; 
    transition:0.3s; 
}

#backBtn:hover, #fullBtn:hover { 
    background:#ff0000; 
    color:#000; 
    transform:scale(1.05); 
}

#gameList::-webkit-scrollbar{width:8px;}
#gameList::-webkit-scrollbar-thumb{background:#ff00003;border-radius:5px;}



</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
<div class="buttons">
    <button id="showGamesBtn">Show HTML5</button>
    <button id="loadGameBtn">Load HTML5</button>
</div>

<div id="gameList">
<?php
if(empty($files)){
    echo "<p>No PHP games found in Web U folder.</p>";
} else {
    foreach($files as $f){
        echo "<div class='game-item' data-src='{$f['path']}'>{$f['name']}</div>";
    }
}
?>
</div>

<div id="overlay">
    <div id="iframe-container">
        <iframe id="player"></iframe>
    </div>
    <div id="controls">
        <button id="backBtn">Back to Menu</button>
        <button id="fullBtn">Fullscreen</button>
    </div>
</div>

<input type="file" id="filePicker" accept=".html" style="display:none">



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
const showBtn = document.getElementById('showGamesBtn');
const loadBtn = document.getElementById('loadGameBtn');
const gameList = document.getElementById('gameList');
const overlay = document.getElementById('overlay');
const backBtn = document.getElementById('backBtn');
const fullBtn = document.getElementById('fullBtn');
const player = document.getElementById('player');
const filePicker = document.getElementById('filePicker');

showBtn.onclick = () => { gameList.style.display = gameList.style.display==='flex'?'none':'flex'; };
loadBtn.onclick = () => filePicker.click();

filePicker.onchange = e => {
    const file = e.target.files[0];
    if(file) openGame(URL.createObjectURL(file));
};

document.querySelectorAll('.game-item').forEach(i=>{
    i.onclick = () => openGame(i.dataset.src);
});

backBtn.onclick = () => {
    overlay.style.display = 'none';
    player.src = '';
};

fullBtn.onclick = () => {
    if(!document.fullscreenElement){
        document.getElementById('iframe-container').requestFullscreen();
    } else {
        document.exitFullscreen();
    }
};

function openGame(src){
    player.src = src;
    overlay.style.display = 'flex';
}
</script>





</body>
</html>
