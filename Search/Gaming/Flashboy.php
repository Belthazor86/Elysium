<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>

<?php
$folder = __DIR__ . '/flashboy';
$webFolder = 'flashboy';
$files = is_dir($folder) ? glob("$folder/*.swf") : [];
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
<title>Flashboy</title>
<script src="https://unpkg.com/@ruffle-rs/ruffle"></script>
<style>
body {
    margin: 0; padding: 0;
    background: radial-gradient(circle at top, #000 0%, #010101 100%);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    height: 100vh; overflow: hidden;
}

.buttons { display: flex; gap: 20px; margin-bottom: 25px; }
button {
    background: linear-gradient(90deg,#111,#222);
    color: #0ff;
    border: 2px solid #0ff;
    padding: 14px 30px;
    font-size: 1.2em;
    border-radius: 12px;
    cursor: pointer;
    text-transform: uppercase;
    transition: 0.3s;
    box-shadow: 0 0 10px #0ff3, 0 0 30px #0ff inset;
}
button:hover {
    background: #0ff;
    color: #000;
    transform: scale(1.05);
    box-shadow: 0 0 20px #0ff, 0 0 60px #0ff inset;
}

/* New search box styling */
#searchBox {
    width: 300px;
    padding: 12px 20px;
    font-size: 1em;
    background: #111;
    border: 2px solid #0ff6;
    border-radius: 25px;
    color: #0ff;
    outline: none;
    transition: 0.3s;
    box-shadow: 0 0 15px #0ff3;
    margin-bottom: 15px;
    
}
#searchBox::placeholder {
    color: #0ff8;
}
#searchBox:focus {
    border-color: #0ff;
    box-shadow: 0 0 25px #0ff, 0 0 50px #0ff inset;
}

#noResults {
    color: #f0f;
    text-align: center;
    font-size: 1.1em;
    display: none; /* hidden by default */
    margin: 10px 0;
}

#gameList {
    display: none;
    flex-direction: column;
    gap: 15px;
    max-height: 50vh;
    overflow-y: auto;
    color: #0ff;
    width: 300px;
    
}
.game-item {
    background: #111;
    border: 1px solid #0ff2;
    padding: 10px;
    border-radius: 8px;
    cursor: pointer;
    text-align: center;
    transition: 0.2s;
}
.game-item:hover {
    background: #0ff;
    color: #000;
    transform: translateY(-3px);
}

#overlay {
    position: fixed;
    top:0; left:0; width:100%; height:100%;
    background: rgba(0,0,0,0.97);
    display: none;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

#player-container {
    width: 80%;
    height: 80%;
    max-width: 1200px;
    max-height: 800px;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 0 40px #0ff4;
}

ruffle-player {
    width: 100%;
    height: 100%;
}

#controls {
    margin-top: 20px;
    display: flex;
    gap: 15px;
}

#controls button {
    margin: 0;
}

#backBtn, #fullBtn {
    padding: 12px 25px;
    border-radius: 10px;
    border: 2px solid #0ff;
    background: transparent;
    color: #0ff;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
}

#backBtn:hover, #fullBtn:hover {
    background: #0ff;
    color: #000;
    transform: scale(1.05);
}

#gameList::-webkit-scrollbar{width:8px;}
#gameList::-webkit-scrollbar-thumb{background:#0ff3;border-radius:5px;}
</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>



<!-- Search box added here -->
<input type="text" id="searchBox" placeholder="🔍 Search games..." autocomplete="off">



<div class="buttons">
    <button id="showGamesBtn">Show Games</button>
    <button id="loadGameBtn">Load Game</button>
</div>



<div id="gameList">
<?php
if(empty($files)){
    echo "<p>No SWF games found in /flashboy folder.</p>";
} else {
    foreach($files as $f){
        $name = basename($f, '.swf');
        echo "<div class='game-item' data-src='$webFolder/$name.swf'>$name</div>";
    }
}
?>
    <p id="noResults">No games found</p>
</div>

<div id="overlay">
    <div id="player-container">
        <ruffle-player id="player"></ruffle-player>
    </div>
    <div id="controls">
        <button id="backBtn">Back to Menu</button>
        <button id="fullBtn">Fullscreen</button>
    </div>
</div>

<input type="file" id="filePicker" accept=".swf" style="display:none">



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
const filePicker = document.getElementById('filePicker');
const playerContainer = document.getElementById('player-container');

// Search elements
const searchBox = document.getElementById('searchBox');
const noResults = document.getElementById('noResults');
const gameItems = document.querySelectorAll('.game-item');

showBtn.onclick = () => {
    gameList.style.display = gameList.style.display==='flex'?'none':'flex';
};
loadBtn.onclick = () => filePicker.click();

filePicker.onchange = e => {
    const file = e.target.files[0];
    if(file) openGame(URL.createObjectURL(file));
};

document.querySelectorAll('.game-item').forEach(i=>{
    i.onclick = () => openGame(i.dataset.src);
});

backBtn.onclick = () => {
    overlay.style.display='none';
    playerContainer.innerHTML = '<ruffle-player id="player"></ruffle-player>';
};

fullBtn.onclick = () => {
    if(!document.fullscreenElement){
        playerContainer.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
};

function openGame(src){
    overlay.style.display='flex';
    const ruffle = window.RufflePlayer.newest();
    const player = ruffle.createPlayer();
    playerContainer.innerHTML = '';
    playerContainer.appendChild(player);
    player.id = 'player';
    player.load(src);
}

// --- Search functionality ---
searchBox.addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();
    let visibleCount = 0;
    
    gameItems.forEach(item => {
        const title = item.textContent.toLowerCase();
        if (title.includes(query)) {
            item.style.display = '';
            visibleCount++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Show "no results" if no items visible and query not empty
    if (visibleCount === 0 && query.length > 0) {
        noResults.style.display = 'block';
    } else {
        noResults.style.display = 'none';
    }
});
</script>

</body>
</html>