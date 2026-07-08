<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<?php
$textFolder = __DIR__ . '/Obsidian';

if (isset($_GET['scan']) && $_GET['scan'] === 'true') {
    header('Content-Type: application/json');
    
    $folders = [];
    
    if (is_dir($textFolder)) {
        $scannedItems = scandir($textFolder);
        
        foreach ($scannedItems as $item) {
            if ($item === '.' || $item === '..') continue;
            $itemPath = $textFolder . '/' . $item;
            if (is_dir($itemPath)) {
                $folders[] = [
                    'foldername' => $item,
                    'displayName' => $item
                ];
            }
        }
        
        usort($folders, function($a, $b) {
            return strcasecmp($a['displayName'], $b['displayName']);
        });
    }
    
    echo json_encode(['folders' => $folders]);
    exit;
}

if (isset($_GET['scanFolder'])) {
    header('Content-Type: application/json');
    
    $folderName = basename($_GET['scanFolder']);
    $folderPath = $textFolder . '/' . $folderName;
    $files = [];
    
    if (is_dir($folderPath)) {
        $scannedFiles = scandir($folderPath);
        
        foreach ($scannedFiles as $file) {
            if ($file === '.' || $file === '..') continue;
            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'txt') continue;
            
            $files[] = [
                'filename' => $file,
                'displayName' => pathinfo($file, PATHINFO_FILENAME),
                'filepath' => $folderName . '/' . $file
            ];
        }
        
        usort($files, function($a, $b) {
            return strcasecmp($a['displayName'], $b['displayName']);
        });
    }
    
    echo json_encode(['files' => $files]);
    exit;
}

if (isset($_GET['file'])) {
    $requestedFile = $_GET['file'];
    $requestedFile = str_replace('\\', '/', $requestedFile);
    $parts = explode('/', $requestedFile);
    $safeParts = array_map('basename', $parts);
    $safePath = implode('/', $safeParts);
    $filePath = $textFolder . '/' . $safePath;
    
    if (file_exists($filePath) && is_file($filePath)) {
        header('Content-Type: text/plain; charset=utf-8');
        readfile($filePath);
    } else {
        http_response_code(404);
        echo 'File not found';
    }
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
<title>Obsidian</title>
</head>	
<style>
		
body {
  font-weight: bold;
  margin: 0;
}
			
#resultFrame {
  width: 100%;
  height: 100vh;
  border: none;
  position: relative;
}
	
input[type="file"] {display: none;}

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

#fileListOverlay {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #1e1e2f;
    border: 2px solid #1976d2;
    border-radius: 16px;
    padding: 24px;
    z-index: 1000;
    max-height: 70vh;
    overflow-y: auto;
    box-shadow: 0 10px 40px rgba(0,0,0,0.8);
    min-width: 320px;
}

#overlayBackdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 999;
}

#fileListOverlay h3 {
    color: #42a5f5;
    margin-top: 0;
    text-align: center;
    font-size: 1.4em;
    border-bottom: 1px solid #333;
    padding-bottom: 12px;
}

.file-item {
    display: block;
    width: 100%;
    padding: 12px 20px;
    margin: 6px 0;
    background: #252540;
    color: #e0e0e0;
    border: 1px solid #333;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1.1em;
    font-weight: 500;
    transition: all 0.2s ease;
    text-align: left;
}

.file-item:hover {
    background: #1976d2;
    color: white;
    border-color: #42a5f5;
    transform: scale(1.03);
}

#closeOverlay {
    display: block;
    margin: 16px auto 0;
    background: #c62828;
    font-size: 1em;
    padding: 10px 28px;
}

#closeOverlay:hover {
    background: #e53935;
}

#fileCount {
    text-align: center;
    color: #999;
    font-size: 0.85em;
    margin-bottom: 10px;
    font-weight: normal;
}
					
</style>

<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<!-- CENTER CONTAINER -->
<div style="width:100%; display:flex; justify-content:center; margin-top:20px;">
<input type="file" id="fileInput"/>
<button class="demo w3-opacity w3-hover-opacity-off button" id="loadButton" onclick="document.getElementById('fileInput').click()">Load</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="reloadButton" onclick="reloadFile()">Reload</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="scanButton" onclick="scanTextFolder()">Scan</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="backButton" onclick="goBackToPlaylist()">Playlist</button>
</div>

<!-- Overlay backdrop -->
<div id="overlayBackdrop" onclick="closeOverlay()"></div>

<!-- File list overlay -->
<div id="fileListOverlay">
    <div id="fileCount"></div>
    <div id="fileListContainer"></div>
    <button id="closeOverlay" onclick="closeOverlay()">Close</button>
</div>

<iframe id="resultFrame" frameborder="0"></iframe>

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
let fileList = [];
let currentIndex = -1;

// Track the source of the currently displayed content
let currentSourceType = null; // 'local' or 'server'
let currentServerPath = null;
let currentDisplayName = null;

// Remember the last scanned folder and its files (the "playlist")
let lastFolderName = '';
let lastFiles = [];

function loadFile() {
    const fileInput = document.getElementById('fileInput');
    const resultFrame = document.getElementById('resultFrame');
    
    fileList = Array.from(fileInput.files).filter(file => file.type === 'text/plain');
    
    if (fileList.length > 0) {
        currentIndex = 0;
        currentSourceType = 'local';
        currentServerPath = null;
        displayCurrentFile(resultFrame);
    } else {
        alert('Please choose valid text files before loading.');
    }
}

function navigateFiles(direction) {
    currentIndex += direction;

    if (currentIndex < 0) {
        currentIndex = fileList.length - 1;
    } else if (currentIndex >= fileList.length) {
        currentIndex = 0;
    }

    const resultFrame = document.getElementById('resultFrame');
    currentSourceType = 'local';
    currentServerPath = null;
    displayCurrentFile(resultFrame);
}

function displayCurrentFile(resultFrame) {
    const file = fileList[currentIndex];
    const reader = new FileReader();

    reader.onload = function (e) {
        const content = e.target.result;
        const htmlContent = `<html><head><title>Transformed HTML</title></head><body>${content}</body></html>`;
        resultFrame.srcdoc = htmlContent;
    };

    reader.readAsText(file);
}

document.getElementById('fileInput').addEventListener('change', loadFile);

function reloadFile() {
    if (currentSourceType === 'local' && currentIndex >= 0 && fileList.length > 0) {
        const resultFrame = document.getElementById('resultFrame');
        displayCurrentFile(resultFrame);
    } else if (currentSourceType === 'server' && currentServerPath) {
        // Reload the same server file
        loadServerFile(currentServerPath, currentDisplayName);
    }
    // else nothing to reload (no file currently loaded)
}

function scanTextFolder() {
    fetch('?scan=true')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('fileListContainer');
            const countDiv = document.getElementById('fileCount');
            container.innerHTML = '';
            
            if (data.error) {
                container.innerHTML = `<p style="color:#ff5252; text-align:center;">${data.error}</p>`;
                countDiv.textContent = '';
            } else if (data.folders.length === 0) {
                container.innerHTML = '<p style="color:#999; text-align:center;">No folders found</p>';
                countDiv.textContent = '';
            } else {
                countDiv.textContent = `${data.folders.length} folder(s) found`;
                data.folders.forEach(folder => {
                    const btn = document.createElement('button');
                    btn.className = 'file-item';
                    btn.textContent = folder.displayName;
                    btn.onclick = function() {
                        scanFolderFiles(folder.foldername);
                    };
                    container.appendChild(btn);
                });
            }
            
            document.getElementById('fileListOverlay').style.display = 'block';
            document.getElementById('overlayBackdrop').style.display = 'block';
        })
        .catch(err => {
            alert('Error scanning folder: ' + err.message);
        });
}

function scanFolderFiles(foldername) {
    fetch(`?scanFolder=${encodeURIComponent(foldername)}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('fileListContainer');
            const countDiv = document.getElementById('fileCount');
            container.innerHTML = '';
            
            if (data.error) {
                container.innerHTML = `<p style="color:#ff5252; text-align:center;">${data.error}</p>`;
                countDiv.textContent = '';
            } else if (data.files.length === 0) {
                container.innerHTML = '<p style="color:#999; text-align:center;">No text files found in this folder</p>';
                countDiv.textContent = '';
                // Still remember the folder so Back can reopen it
                lastFolderName = foldername;
                lastFiles = [];
            } else {
                countDiv.textContent = `${data.files.length} file(s) in ${foldername}`;
                // Save this playlist for the Back button
                lastFolderName = foldername;
                lastFiles = data.files;
                
                data.files.forEach(file => {
                    const btn = document.createElement('button');
                    btn.className = 'file-item';
                    btn.textContent = file.displayName;
                    btn.onclick = function() {
                        loadServerFile(file.filepath, file.displayName);
                        closeOverlay();
                    };
                    container.appendChild(btn);
                });
            }
        })
        .catch(err => {
            alert('Error scanning folder: ' + err.message);
        });
}

function loadServerFile(filepath, displayName) {
    currentSourceType = 'server';
    currentServerPath = filepath;
    currentDisplayName = displayName;
    
    fetch(`?file=${encodeURIComponent(filepath)}`)
        .then(response => response.text())
        .then(content => {
            const resultFrame = document.getElementById('resultFrame');
            const htmlContent = `<html><head><title>${displayName}</title></head><body>${content}</body></html>`;
            resultFrame.srcdoc = htmlContent;
        })
        .catch(err => {
            alert('Error loading file: ' + err.message);
        });
}

function closeOverlay() {
    document.getElementById('fileListOverlay').style.display = 'none';
    document.getElementById('overlayBackdrop').style.display = 'none';
}

// New Back button: reopens the last viewed folder's file list
function goBackToPlaylist() {
    if (!lastFolderName) {
        // No previous playlist; do nothing or show a message
        alert('No playlist to go back to. Please scan a folder first.');
        return;
    }
    
    const container = document.getElementById('fileListContainer');
    const countDiv = document.getElementById('fileCount');
    container.innerHTML = '';
    
    if (lastFiles.length === 0) {
        countDiv.textContent = `No text files found in ${lastFolderName}`;
        container.innerHTML = '<p style="color:#999; text-align:center;">No text files found in this folder</p>';
    } else {
        countDiv.textContent = `${lastFiles.length} file(s) in ${lastFolderName}`;
        lastFiles.forEach(file => {
            const btn = document.createElement('button');
            btn.className = 'file-item';
            btn.textContent = file.displayName;
            btn.onclick = function() {
                loadServerFile(file.filepath, file.displayName);
                closeOverlay();
            };
            container.appendChild(btn);
        });
    }
    
    // Show the overlay
    document.getElementById('fileListOverlay').style.display = 'block';
    document.getElementById('overlayBackdrop').style.display = 'block';
}
</script>
					
</body>
</html>