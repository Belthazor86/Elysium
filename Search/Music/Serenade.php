<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<?php
// Handle AJAX request for folder scanning
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folder'])) {
    $audioDirectory = 'Serenade/';
    $folderName = $_POST['folder'];
    $folderPath = $audioDirectory . $folderName;
    $audioFiles = [];
    
    if (is_dir($folderPath)) {
        $files = scandir($folderPath);
        foreach ($files as $file) {
            if (preg_match('/\.(mp3|m4a|m4b|ogg|wav|aac|flac|opus|aiff|alac)$/i', $file)) {
                $audioFiles[] = [
                    'name' => $file,
                    'path' => $folderPath . '/' . $file
                ];
            }
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($audioFiles);
    exit;
}

// Scan the "audio" folder for files
$audioDirectory = 'Serenade/';
$audioFiles = [];
$audioFolders = [];

if (is_dir($audioDirectory)) {
    $files = scandir($audioDirectory);
    foreach ($files as $file) {
        // Skip . and ..
        if ($file === '.' || $file === '..') continue;
        
        $fullPath = $audioDirectory . $file;
        
        // Check if it's a directory
        if (is_dir($fullPath)) {
            $audioFolders[] = [
                'name' => $file,
                'path' => $fullPath
            ];
        }
        
        // Filter for audio extensions
        if (preg_match('/\.(mp3|m4a|m4b|ogg|wav|aac|flac|opus|aiff|alac)$/i', $file)) {
            // Store as object-like structure to match JS File object name property
            $audioFiles[] = [
                'name' => $file,
                'path' => $audioDirectory . $file
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
<link href="../../CSS/style.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Serenade</title>
<style>

body {
  font-weight: bold;
  margin: 0;
  overflow: hidden; /* Prevent full page scrolling */
}

/* Search Container Styles */
.search-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    position: relative;
    z-index: 1;
}

.search-input {
    padding: 12px 20px;
    border-radius: 25px;
    border: 2px solid #1976d2;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    font-size: 16px;
    font-weight: 600;
    width: 300px;
    outline: none;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: #42a5f5;
    box-shadow: 0 0 15px rgba(66, 165, 245, 0.5);
    background: rgba(255, 255, 255, 0.2);
}

.search-input::placeholder {
    color: rgba(255, 255, 255, 0.6);
    font-weight: 500;
}

.no-results {
    text-align: center;
    color: rgba(255, 255, 255, 0.7);
    font-size: 18px;
    margin-top: 20px;
    font-style: italic;
}

.control-panel {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 12px;
    margin-top: 18px;
    z-index: 1;
}

.control-button {
    background: linear-gradient(135deg, #0d47a1, #1976d2);
    color: whitesmoke;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 10px;
    font-size: 1em;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    text-transform: capitalize;
    min-width: 80px;
}

.control-button:hover {
    background: linear-gradient(135deg, #1976d2, #42a5f5);
    transform: scale(1.08);
    box-shadow: 0 6px 18px rgba(0,0,0,0.7);
}

.control-button:active {
    transform: scale(0.98);
}


#playlist {
    list-style-type: none;
    padding: 0;
    text-align: center;
    position: relative;
    z-index: 1;
    margin-top: 20px;
    height: 55vh;          /* Adjusted to accommodate search */
    overflow-y: auto;      /* Only playlist scrolls */
    overflow-x: hidden;
}
#playlist li {
    margin-bottom: 5px;
    cursor: pointer;
    font-size: 18px;
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

.folder-buttons-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 20px;
    position: relative;
    z-index: 1;
}

</style>
</head> 

<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<!-- FOLDER BUTTONS -->
<div class="folder-buttons-container">
    <?php foreach ($audioFolders as $folder): ?>
        <button class="demo w3-opacity w3-hover-opacity-off button folder-button" data-folder="<?php echo htmlspecialchars($folder['name']); ?>">
            <?php echo htmlspecialchars($folder['name']); ?>
        </button>
    <?php endforeach; ?>
    <?php if (empty($audioFolders)): ?>
        <p style="color: white;">No folders found in Audio Player directory</p>
    <?php endif; ?>
</div>


<!-- ---------- NEW: Control Panel ---------- -->
<div class="control-panel">
  <button id="prev-button" class="control-button" disabled>⏮ Prev</button>
  <button id="play-pause-button" class="control-button" disabled>▶ Play</button>
  <button id="next-button" class="control-button" disabled>Next ⏭</button>
</div>
<!-- --------------------------------------- -->

<!-- SEARCH CONTAINER -->
<div class="search-container">
    <input type="text" id="search-input" class="search-input" placeholder="Search music...">
</div>



<ul id="playlist"></ul>
<audio id="audio-player" hidden></audio>


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
const audioPlayer = document.getElementById("audio-player");
const playlistElement = document.getElementById("playlist");
const searchInput = document.getElementById("search-input");

// New control button references
const prevButton = document.getElementById("prev-button");
const playPauseButton = document.getElementById("play-pause-button");
const nextButton = document.getElementById("next-button");

let playlist = [];
let currentTrack = 0;
let filteredPlaylist = []; // Store filtered results
let isFiltered = false; // Track if we're showing filtered results

const serverFiles = <?php echo json_encode($audioFiles); ?>;

// ---------- Update control button states ----------
function updateControlButtons() {
    const hasTracks = playlist.length > 0;
    prevButton.disabled = !hasTracks;
    nextButton.disabled = !hasTracks;
    playPauseButton.disabled = !hasTracks;
}

// Update play/pause button text based on audio state
function updatePlayPauseButton() {
    if (audioPlayer.paused) {
        playPauseButton.innerHTML = "▶ Play";
    } else {
        playPauseButton.innerHTML = "⏸ Pause";
    }
}
// --------------------------------------------------------

function highlightCurrentTrack() {
  const items = playlistElement.querySelectorAll("li");
  items.forEach((item, i) => {
    const displayList = isFiltered ? filteredPlaylist : playlist;
    if (displayList[i] === playlist[currentTrack]) {
      item.classList.add("w3-opacity-off");
    } else {
      item.classList.remove("w3-opacity-off");
    }
  });
}

function loadTrack(track) {
  if (!track) return;
  audioPlayer.src = track.path;
  audioPlayer.play();
  
  currentTrack = playlist.indexOf(track);
  if (currentTrack === -1) currentTrack = 0;
  
  highlightCurrentTrack();
  updatePlayPauseButton();
}

function displayPlaylist(tracksToShow = playlist) {
  playlistElement.innerHTML = "";
  
  if (tracksToShow.length === 0 && isFiltered) {
    const noResults = document.createElement("div");
    noResults.className = "no-results";
    noResults.textContent = "No matching songs found";
    playlistElement.appendChild(noResults);
    return;
  }
  
  const displayList = isFiltered ? filteredPlaylist : playlist;
  
  displayList.forEach((track, index) => {
    const li = document.createElement("li");
    li.textContent = track.name.replace(/\.[^/.]+$/, "");
    li.classList.add("demo", "w3-opacity", "w3-hover-opacity-off");
    
    if (track === playlist[currentTrack]) {
      li.classList.add("w3-opacity-off");
    }
    
    // ---------- CHANGED: Click restarts current track instead of pausing ----------
    li.addEventListener("click", () => {
      if (track === playlist[currentTrack]) {
        audioPlayer.currentTime = 0;
        audioPlayer.play();
      } else {
        currentTrack = playlist.indexOf(track);
        loadTrack(track);
      }
    });
    // -----------------------------------------------------------------
    
    playlistElement.appendChild(li);
  });
  
  updateControlButtons();
}

// Search functionality
function performSearch() {
  const searchTerm = searchInput.value.trim().toLowerCase();
  
  if (searchTerm === "") {
    isFiltered = false;
    filteredPlaylist = [];
    displayPlaylist(playlist);
    highlightCurrentTrack();
    return;
  }
  
  filteredPlaylist = playlist.filter(track => {
    const trackName = track.name.replace(/\.[^/.]+$/, "").toLowerCase();
    return trackName.includes(searchTerm);
  });
  
  isFiltered = true;
  displayPlaylist(filteredPlaylist);
}

searchInput.addEventListener("input", performSearch);

// Auto-advance to next track when current song ends
audioPlayer.addEventListener("ended", () => {
  nextTrack();
});

// ---------- Navigation functions ----------
function nextTrack() {
  if (playlist.length === 0) return;
  
  if (isFiltered && filteredPlaylist.length > 0) {
    const currentFilteredIndex = filteredPlaylist.indexOf(playlist[currentTrack]);
    if (currentFilteredIndex < filteredPlaylist.length - 1) {
      const next = filteredPlaylist[currentFilteredIndex + 1];
      currentTrack = playlist.indexOf(next);
      loadTrack(next);
    }
  } else if (currentTrack < playlist.length - 1) {
    currentTrack++;
    loadTrack(playlist[currentTrack]);
  }
}

function prevTrack() {
  if (playlist.length === 0) return;
  
  if (isFiltered && filteredPlaylist.length > 0) {
    const currentFilteredIndex = filteredPlaylist.indexOf(playlist[currentTrack]);
    if (currentFilteredIndex > 0) {
      const prev = filteredPlaylist[currentFilteredIndex - 1];
      currentTrack = playlist.indexOf(prev);
      loadTrack(prev);
    }
  } else if (currentTrack > 0) {
    currentTrack--;
    loadTrack(playlist[currentTrack]);
  }
}
// -------------------------------------------------

// Control button event listeners
prevButton.addEventListener("click", prevTrack);
nextButton.addEventListener("click", nextTrack);
playPauseButton.addEventListener("click", () => {
  if (audioPlayer.paused) {
    audioPlayer.play();
  } else {
    audioPlayer.pause();
  }
});

audioPlayer.addEventListener("play", updatePlayPauseButton);
audioPlayer.addEventListener("pause", updatePlayPauseButton);

// Folder button click handler
document.querySelectorAll('.folder-button').forEach(button => {
  button.addEventListener('click', function() {
    const folderName = this.getAttribute('data-folder');
    
    const formData = new FormData();
    formData.append('folder', folderName);
    
    fetch(window.location.href, {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(files => {
      if (files.length > 0) {
        playlist = files;
        currentTrack = 0;
        isFiltered = false;
        filteredPlaylist = [];
        searchInput.value = "";
        loadTrack(playlist[currentTrack]);
        displayPlaylist();
      }
    });
  });
});
</script>

</body>
</html>