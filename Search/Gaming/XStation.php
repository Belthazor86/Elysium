<?php
// 1. AJAX: return only HTML files in requested folder
if (isset($_GET['getFiles']) && !empty($_GET['getFiles'])) {
    $folder = rtrim($_GET['getFiles'], '/');
    if (is_dir($folder)) {
        $files = array_values(array_filter(scandir($folder), function($f) {
            return strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'html';
        }));
        header('Content-Type: application/json');
        echo json_encode($files);
        exit;
    } else {
        http_response_code(400);
        echo json_encode([]);
        exit;
    }
}

// 2. Normal page load: build folder list for buttons
function scanFolder($folderPath) {
    $folders = [];
    if (is_dir($folderPath)) {
        foreach (array_diff(scandir($folderPath), ['.', '..']) as $file) {
            if (is_dir($folderPath . '/' . $file)) {
                $folders[] = [
                    'name' => $file,
                    'url' => $folderPath . '/' . $file,
                ];
            }
        }
    }
    return $folders;
}

$folderPaths = ['XStation']; 
$allMenuItems = [];
foreach ($folderPaths as $folderPath) {
    $allMenuItems = array_merge($allMenuItems, scanFolder($folderPath));
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>XStation</title>
<style>

html, body { 
 font-weight: bold; 
 margin: 0; 
 background-color: #111; 
 color: white; }

/* Right Sidenav */
.right {
  height: 100%;
  width: 0;
  position: fixed;
  z-index: 10;
  top: 0;
  right: 0;
  background-color: #111;
  overflow-x: hidden;
  transition: 0.5s;
  padding-top: 60px;
  border-left: 1px solid #333;
}

.right a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  font-size: 18px;
  color: #818181;
  display: block;
  transition: 0.3s;
}

#main {
  transition: margin-right .5s;
}


.right a:hover { color: #f1f1f1; }

.right .closebtn {
  position: absolute;
  top: 10px;
  right: 25px;
  font-size: 25px;
  background: none;
  border: none;
  color: white;
  cursor: pointer;
}

/* Button Grid */
.container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
    padding: 40px;
}

button.folder-btn {
    background: linear-gradient(135deg, #0d47a1, #1976d2); 
    color: whitesmoke;
    border: none;
    padding: 16px 32px;
    cursor: pointer;
    border-radius: 12px;
    font-size: 1.2em;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    text-transform: capitalize;
}

button.folder-btn:hover {
    background: linear-gradient(135deg, #1976d2, #42a5f5);
    transform: scale(1.12);
}

/* Overlay */
#overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(18, 18, 18, 0.95);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

#overlay .container-inner {
  position: relative;
  width: 85vw;
  max-width: 1200px;
  height: 85vh;
  background: #000;
  border-radius: 15px;
  box-shadow: 0 0 40px #00ffc3;
}

#overlay iframe { 
  width: 100%; 
  height: 100%; 
  border: none; 
  border-radius: 15px;
 }

#overlay .ctrl-btn { 
  position: absolute; 
  top: 10px; 
  z-index: 10000;
  background: black; 
  color: white; 
  border: none; 
  padding: 5px 10px; 
  cursor: pointer; 
}

#closeBtn { right: 10px; }
#fullscreenBtn { right: 50px; }
</style>
</head> 

<body>



<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>



<div id="main" class="container">
    <?php foreach ($allMenuItems as $item): ?>
        <button class="folder-btn" onclick="openRightNavAndLoad('<?php echo $item['url']; ?>')">
            <?php echo htmlspecialchars($item['name']); ?>
        </button>
    <?php endforeach; ?>

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
</div>



<div id="mySidenavRight" class="right">
    <button class="closebtn" onclick="closeRightNav()">❌</button>
    <ul id="results" style="list-style:none; padding:0; margin-top:20px;">
        <!-- Files loaded here -->
    </ul>
</div>

<div id="overlay">
  <div class="container-inner" id="overlayContainer">
    <button id="closeBtn" class="ctrl-btn">❌</button>
    <button id="fullscreenBtn" class="ctrl-btn" onclick="toggleFullscreen()">⛶</button>
    <div id="output" style="height: 100%;"></div>
  </div>
</div>




<script>
function openRightNavAndLoad(folder) {
    const rightNav = document.getElementById('mySidenavRight');
    const mainContent = document.getElementById("main");
    const results = document.getElementById('results');

    // Handle the Side Nav toggle and Margin shift
    rightNav.style.width = '300px';
    if (mainContent) {
        mainContent.style.marginRight = '300px';
    }

    // Fetch and load the files
    fetch('?getFiles=' + encodeURIComponent(folder))
    .then(res => res.json())
    .then(files => {
        if (!files || !files.length) {
            results.innerHTML = '<li><a href="#">No HTML files found</a></li>';
            return;
        }

        let html = '';
        files.forEach(file => {
            const nameWithoutExt = file.replace(/\.[^/.]+$/, '');
            const fileUrl = folder + '/' + file;
            html += `<li><a href="javascript:void(0)" onclick="showOverlayWithFile('${fileUrl}')">${nameWithoutExt}</a></li>`;
        });
        results.innerHTML = html;
    })
    .catch(() => {
        results.innerHTML = '<li><a href="#">Error loading files</a></li>';
    });
}

function closeRightNav() {
    document.getElementById('mySidenavRight').style.width = '0';
    const mainContent = document.getElementById("main");
    if (mainContent) {
        mainContent.style.marginRight = "0";
    }
}

function showOverlayWithFile(filePath) {
    const output = document.getElementById('output');
    const overlay = document.getElementById('overlay');
    output.innerHTML = `<iframe src="${filePath}" style="width:100%; height:100%; border:none;"></iframe>`;
    overlay.style.display = 'flex';
}

// Ensure the close button listener exists
const closeBtn = document.getElementById('closeBtn');
if (closeBtn) {
    closeBtn.addEventListener('click', () => {
        document.getElementById('overlay').style.display = 'none';
        document.getElementById('output').innerHTML = '';
    });
}

function toggleFullscreen() {
    const elem = document.getElementById('overlayContainer');
    if (!elem) return;
    if (!document.fullscreenElement) {
        elem.requestFullscreen?.() || elem.webkitRequestFullscreen?.();
    } else {
        document.exitFullscreen?.();
    }
}

</script>

</body>
</html>