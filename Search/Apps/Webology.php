<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>



<?php
// 1. Logic to scan for subfolders only
function scanFolder($folderPath) {
    $folders = [];
    if (is_dir($folderPath)) {
        // glob with GLOB_ONLYDIR is cleaner for finding subfolders
        foreach (glob($folderPath . '/*', GLOB_ONLYDIR) as $dir) {
            $folders[] = [
                'name' => basename($dir),
                'url'  => $dir
            ];
        }
    }
    return $folders;
}

$folderPaths = ['Webology']; 
$allMenuItems = [];
foreach ($folderPaths as $folderPath) {
    $allMenuItems = array_merge($allMenuItems, scanFolder($folderPath));
}

// 2. Helper to find the correct index file in a folder
function getIndexFile($dir) {
    $indices = ['index.php', 'index.html'];
    foreach ($indices as $index) {
        if (file_exists($dir . '/' . $index)) {
            return $dir . '/' . $index;
        }
    }
    return null; // No index file found
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
<title>Webology</title>
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
  font-size: 36px;
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
  font size: 18px;
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


<div class="container">
    <?php foreach ($allMenuItems as $item): 
        $target = getIndexFile($item['url']);
        if ($target): ?>
            <button class="folder-btn" onclick="showOverlay('<?php echo $target; ?>')">
                <?php echo htmlspecialchars($item['name']); ?>
            </button>
        <?php endif; 
    endforeach; ?>
</div>




<div id="overlay">
  <div class="container-inner" id="overlayContainer">
    <div id="output" style="height: 100%;"></div>
  </div>
</div>




<script>
function showOverlay(filePath) {
    const output = document.getElementById('output');
    const overlay = document.getElementById('overlay');
    output.innerHTML = `<iframe src="${filePath}"></iframe>`;
    overlay.style.display = 'flex';
}

function closeOverlay() {
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('output').innerHTML = '';
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

  document.getElementById('overlay').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeOverlay();
  });
  
</script>

</body>
</html>