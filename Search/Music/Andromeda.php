<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<?php
// ========== PHP: Scan & return JSON when called with ?scan=1 ==========
if (isset($_GET['scan'])) {
    header('Content-Type: application/json');

    $baseDir = __DIR__ . '/Andromeda';   // <-- change this to your music folder
    $audioExt = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac', 'webm'];
    $imageExt = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'];

    $library = [];

    if (is_dir($baseDir)) {
        $albumDirs = array_filter(glob($baseDir . '/*'), 'is_dir');
        foreach ($albumDirs as $albumPath) {
            $albumName = basename($albumPath);
            $cover = null;
            $tracks = [];

            $files = scandir($albumPath);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                $fullPath = $albumPath . '/' . $file;
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                if (in_array($ext, $imageExt) && !$cover) {
                    // Build a URL relative to this script
                    $cover = 'Andromeda/' . rawurlencode($albumName) . '/' . rawurlencode($file);
                } elseif (in_array($ext, $audioExt)) {
                    $tracks[] = [
                        'name' => $file,
                        'url'  => 'Andromeda/' . rawurlencode($albumName) . '/' . rawurlencode($file)
                    ];
                }
            }

            // sort tracks numerically
            usort($tracks, function($a, $b) {
                return strnatcasecmp($a['name'], $b['name']);
            });

            $library[$albumName] = [
                'cover'  => $cover,
                'tracks' => $tracks
            ];
        }
    }

    echo json_encode($library);
    exit;
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
<title>Andromeda</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-weight: bold;
    padding:20px;
}

.album-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(180px,1fr));
    gap:20px;
}

.album{
    background:#1b1b1b;
    border-radius:12px;
    overflow:hidden;
    cursor:pointer;
    transition:.25s;
}

.album:hover{
    transform:scale(1.05);
}

.album img{
    width:100%;
    height:180px;
    object-fit:cover;
}

.album-title{
    padding:10px;
    text-align:center;
}

/* Overlay */

.overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.9);
    display:none;
    justify-content:center;
    align-items:center;
    z-index:999;
}

.player{
    width:90%;
    max-width:900px;
    background:#1d1d1d;
    border-radius:20px;
    padding:25px;
}

.close{
    float:right;
    font-size:20px;
    cursor:pointer;
}

.player-content{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
    font-size:20px;
}

.cover-large{
    width:300px;
    height:300px;
    object-fit:cover;
    border-radius:15px;
}

.right{
    flex:1;
}

.track-list{
    margin-top:15px;
    max-height:260px;
    overflow:scroll;
}

.track{
    padding:10px;
    border-bottom:1px solid #333;
    cursor:pointer;
}

.track:hover{
    background:#2a2a2a;
}

.controls{
    display:flex;
    gap:15px;
    justify-content:center;
    margin-top:20px;
}

.controls button{
    background: linear-gradient(135deg, #0d47a1, #1976d2);
    border:none;
    color:ghostwhite;
    padding:10px 18px;
    border-radius:10px;
    cursor:pointer;
}

.controls button:hover {
    background: linear-gradient(135deg, #1976d2, #42a5f5);
    transform: scale(1.12);
    box-shadow: 0 6px 20px rgba(0,0,0,0.7);
}


.progress{
    width:100%;
    margin-top:20px;
}

.now-playing{
    margin-top:10px;
    text-align:center;
    color:#aaa;
}

.load-btn {
    display: inline-block;
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

.load-btn:hover {
    background: linear-gradient(135deg, #1976d2, #42a5f5);
    transform: scale(1.12);
    box-shadow: 0 6px 20px rgba(0,0,0,0.7);
}

#folderInput {
    display: none;
}

#albumName {font-size: 25px;}

</style>
</head>

<body>


<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>


<div style="width:100%; display:flex; justify-content:center; margin-top:20px;">
    <!-- BUTTONS: local Load + server Scan -->
    <label class="load-btn">
        Load
        <input type="file" id="folderInput" webkitdirectory multiple>
    </label>

    <!-- NEW: PHP scan button -->
    <button id="scanServerBtn" class="load-btn">Scan</button>
</div>


<div class="album-grid" id="albums"></div>
<div class="overlay" id="overlay">
    <div class="player">
        <span class="demo w3-opacity w3-hover-opacity-off close" id="closeBtn">✖️</span>
        <div class="player-content">
            <img id="largeCover" class="cover-large">
            <div class="right">
                <h2 id="albumName"></h2>
                <div class="track-list" id="trackList"></div>
            </div>
        </div>
        <audio id="audio"></audio>
        <input class="progress" type="range" id="seek" value="0">
        <div class="now-playing" id="nowPlaying">
            No track playing
        </div>
        <div class="controls">
            <button id="prevBtn">⏮</button>
            <button id="playBtn">▶</button>
            <button id="nextBtn">⏭</button>
        </div>
    </div>
</div>


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

const folderInput = document.getElementById("folderInput");
const scanServerBtn = document.getElementById("scanServerBtn");
const albumsDiv = document.getElementById("albums");

const overlay = document.getElementById("overlay");
const closeBtn = document.getElementById("closeBtn");

const largeCover = document.getElementById("largeCover");
const albumName = document.getElementById("albumName");
const trackList = document.getElementById("trackList");

const audio = document.getElementById("audio");

const playBtn = document.getElementById("playBtn");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");

const seek = document.getElementById("seek");
const nowPlaying = document.getElementById("nowPlaying");

let library = {};
let currentAlbum = null;
let currentTrack = 0;

const audioExt = [
".mp3",".wav",".ogg",".m4a",".aac",
".flac",".webm"
];

// ---------- Local folder load (unchanged) ----------
folderInput.addEventListener("change", e => {

    library = {};
    albumsDiv.innerHTML = "";

    const files = [...e.target.files];

    files.forEach(file => {

        const path = file.webkitRelativePath;

        const parts = path.split("/");

        if(parts.length < 2) return;

        const album = parts[1];

        if(!library[album]){
            library[album] = {
                cover:null,
                tracks:[]
            };
        }

        const lower = file.name.toLowerCase();

        const isImage =
            lower.endsWith(".jpg") ||
            lower.endsWith(".jpeg") ||
            lower.endsWith(".png") ||
            lower.endsWith(".webp") ||
            lower.endsWith(".gif") ||
            lower.endsWith(".bmp");

        if (isImage && !library[album].cover) {
            library[album].cover = URL.createObjectURL(file);
          }

        const isAudio =
            audioExt.some(ext => lower.endsWith(ext));

        if(isAudio){

            library[album].tracks.push({
                name:file.name,
                url:URL.createObjectURL(file)
            });

        }

    });

    // Sort tracks numerically
    Object.keys(library).forEach(album => {
        library[album].tracks.sort((a, b) => {
            return a.name.localeCompare(b.name, undefined, {numeric: true, sensitivity: 'base'});
        });
    });

    renderAlbums();

});

// ---------- Server scan: fetch JSON ----------
scanServerBtn.addEventListener("click", async () => {
    try {
        const response = await fetch("?scan=1");
        if (!response.ok) throw new Error("Scan failed");
        const data = await response.json();

        library = data;   // directly assign because structure matches
        renderAlbums();

    } catch (err) {
        alert("Error scanning server folder: " + err.message);
        console.error(err);
    }
});

function renderAlbums(){

    albumsDiv.innerHTML = "";

    const sortedAlbums = Object.keys(library).sort((a, b) =>
        a.toLowerCase().localeCompare(b.toLowerCase())
    );

    sortedAlbums.forEach(album => {

        const card = document.createElement("div");
        card.className = "album";

        card.innerHTML = `
            <img src="${
                library[album].cover ||
                'https://via.placeholder.com/300?text=Album'
            }">
            <div class="album-title">${album}</div>
        `;

        card.onclick = () => openAlbum(album);

        albumsDiv.appendChild(card);
    });
}

function openAlbum(album){

    currentAlbum = album;
    currentTrack = 0;

    overlay.style.display = "flex";

    albumName.textContent = album;

    largeCover.src =
        library[album].cover ||
        'https://via.placeholder.com/300?text=Album';

    trackList.innerHTML = "";

    library[album].tracks.forEach((track,index)=>{

        const div = document.createElement("div");

        div.className = "track";
        div.textContent = track.name;
        div.onclick = () => {
            currentTrack = index;
            playTrack();
        };
        trackList.appendChild(div);

    });

    if(library[album].tracks.length){
        playTrack();
    }

}

function playTrack(){
    const track =
    library[currentAlbum].tracks[currentTrack];
    audio.src = track.url;
    audio.play();
    playBtn.textContent = "⏸";
    nowPlaying.textContent =
    track.name;
}

playBtn.onclick = ()=>{
    if(audio.paused){
        audio.play();
        playBtn.textContent = "⏸";

    }else{
        audio.pause();
        playBtn.textContent = "▶";
    }

};

nextBtn.onclick = ()=>{
    const tracks =
    library[currentAlbum].tracks;
    currentTrack++;
    if(currentTrack >= tracks.length)
        currentTrack = 0;
    playTrack();
};

prevBtn.onclick = ()=>{

    const tracks =
        library[currentAlbum].tracks;
    currentTrack--;
    if(currentTrack < 0)
        currentTrack = tracks.length - 1;
    playTrack();

};

audio.addEventListener("ended",()=>{
    const tracks = library[currentAlbum].tracks;
    if(currentTrack < tracks.length - 1) {
        currentTrack++;
        playTrack();
    }
});

audio.addEventListener("timeupdate",()=>{
    if(audio.duration){
        seek.value =
        (audio.currentTime /
        audio.duration) * 100;
    }
});

seek.addEventListener("input",()=>{
    if(audio.duration){
        audio.currentTime =
        (seek.value / 100)
        * audio.duration;
    }

});

closeBtn.onclick = ()=>{
    overlay.style.display = "none";
    audio.pause();
};

</script>

</body>
</html>