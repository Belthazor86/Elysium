


<?php
$mainFolder = __DIR__ . '/Cosmos';

// Get all subfolders
$subfolders = array_filter(scandir($mainFolder), function($f) use ($mainFolder) {
    return $f !== '.' && $f !== '..' && is_dir($mainFolder . '/' . $f);
});

// Get JS files in a folder
function getJSFiles($folderPath) {
    $files = array_filter(scandir($folderPath), function($f) use ($folderPath) {
        return is_file($folderPath . '/' . $f) && pathinfo($f, PATHINFO_EXTENSION) === 'js';
    });
    return array_values($files);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Cosmos</title>
<style>

.container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
    padding: 40px;
}

button {
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
button:hover {
    background: linear-gradient(135deg, #1976d2, #42a5f5);
    transform: scale(1.12);
    box-shadow: 0 6px 20px rgba(0,0,0,0.7);
}

#overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(15,15,15,0.98);
    color: whitesmoke;
    overflow-y: auto;
    padding: 60px 40px;
    box-sizing: border-box;
    backdrop-filter: blur(8px);
    z-index: 999;
}

#overlay h2 {
    text-align: center;
    margin-top: 0;
    letter-spacing: 1px;
    color: whitesmoke;
}

#overlay .file-buttons {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 18px;
    margin-bottom: 35px;
}

#overlay .file-buttons button {
    background: linear-gradient(135deg, #0d47a1, #1976d2);
    border: none;
    font-weight: bold;
    padding: 12px 24px;
    border-radius: 10px;
    transition: all 0.25s;
    box-shadow: 0 3px 10px rgba(0,0,0,0.5);
}
#overlay .file-buttons button:hover {
    background: linear-gradient(135deg, #1976d2, #42a5f5);
    transform: scale(1.08);
}

#overlay .close-btn {
    position: absolute;
    top: 25px; right: 25px;
    background: linear-gradient(135deg, #0b3c75, #0d47a1);
    color: whitesmoke;
    border: none;
    padding: 14px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    font-size: 1.5em;
    box-shadow: 0 3px 10px rgba(0,0,0,0.5);
    transition: all 0.25s;
}
#overlay .close-btn:hover {
    background: linear-gradient(135deg, #1976d2, #42a5f5);
    transform: scale(0.8);
}

@media (max-width: 768px) {
    #overlay input[type="text"] { width: 80%; }
    button { padding: 14px 24px; font-size: 1em; }
    #overlay #fileContent { font-size: 0.95em; padding: 20px; }
}



</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<div class="container">
<?php foreach ($subfolders as $sub): ?>
    <button onclick="openOverlay('<?php echo addslashes($sub); ?>')">
        <?php echo htmlspecialchars($sub); ?>
    </button>
<?php endforeach; ?>
</div>

<div id="overlay">
    <button class="close-btn" onclick="closeOverlay()">✖️</button>
    <h2 id="overlayTitle"></h2>
    <div class="file-buttons" id="fileButtons"></div>
</div>

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
let currentFolder = '';
let allFiles = {};

// PHP-generated mapping
<?php
foreach ($subfolders as $sub) {
    $files = getJSFiles("$mainFolder/$sub");
    $safeSub = addslashes($sub);
    echo "allFiles['$safeSub'] = [";
    foreach ($files as $f) { echo "'" . addslashes($f) . "',"; }
    echo "];\n";
}
?>

// Open overlay for a folder
function openOverlay(folderName) {
    currentFolder = folderName;
    document.getElementById('overlayTitle').innerText = folderName;
    document.getElementById('overlay').style.display = 'block';
    renderFiles();
}

// Render all file buttons
function renderFiles() {
    const container = document.getElementById('fileButtons');
    container.innerHTML = '';
    const files = allFiles[currentFolder] || [];
    files.forEach(f => {
        const btn = document.createElement('button');
        btn.innerText = f.replace(/\.js$/, '');
        btn.onclick = () => runJSFile(f);
        container.appendChild(btn);
    });
}

// Run the JS file directly by creating a script element
function runJSFile(fileName) {
    const script = document.createElement('script');
    script.src = `Cosmos/${currentFolder}/${fileName}`;
    script.type = 'text/javascript';
    document.body.appendChild(script);
}

// Close overlay
function closeOverlay() {
    document.getElementById('overlay').style.display = 'none';
}
</script>




</body>
</html>
