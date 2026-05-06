


<?php
$mainFolder = __DIR__ . '/Nexora';

// Get all subfolders
$subfolders = array_filter(scandir($mainFolder), function($f) use ($mainFolder) {
    return $f !== '.' && $f !== '..' && is_dir($mainFolder . '/' . $f);
});

// Get JS files in a folder
function getJSFiles($folderPath) {
    $files = array_filter(scandir($folderPath), function($f) use ($folderPath) {
        return is_file($folderPath . '/' . $f) && pathinfo($f, PATHINFO_EXTENSION) === 'txt';
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
<title>Nexora</title>
<style>


.container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
    padding: 40px;
}

button {
    background: linear-gradient(135deg, #6a1b9a, #8e24aa); /* purple gradient */
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
    background: linear-gradient(135deg, #8e24aa, #9c27b0); /* lighter purple on hover */
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

#overlay .search-bar {
    text-align: center;
    margin: 30px 0;
}

#overlay input[type="text"] {
    padding: 16px 18px;
    width: 50%;
    max-width: 450px;
    border-radius: 12px;
    border: 5px solid #7e57c2; /* purple border */
    font-size: 1.1em;
    background-color: #000; /* dark purple background */
    color: whitesmoke;
    outline: none;
    transition: border 0.3s, box-shadow 0.3s;
}
#overlay input[type="text"]:focus {
    border-color: #b39ddb; /* lighter purple on focus */
    box-shadow: 0 0 10px #b39ddb;
}

#overlay .file-buttons {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 18px;
    margin-bottom: 35px;
}

#overlay .file-buttons button {
    background: linear-gradient(135deg, #6a1b9a, #8e24aa); /* purple gradient */
    border: none;
    font-weight: bold;
    padding: 12px 24px;
    border-radius: 10px;
    transition: all 0.25s;
    box-shadow: 0 3px 10px rgba(0,0,0,0.5);
}
#overlay .file-buttons button:hover {
    background: linear-gradient(135deg, #8e24aa, #9c27b0); /* lighter purple hover */
    transform: scale(1.08);
}

#overlay .close-btn {
    position: absolute;
    top: 25px; right: 25px;
    background: linear-gradient(135deg, #000, #000); /* purple gradient */
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
    background: linear-gradient(135deg, #8e24aa, #9c27b0); /* lighter purple hover */
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
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search files..." onkeyup="filterFiles()" autocomplete="off" />
    </div>
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
    document.getElementById('searchInput').value = '';
    renderFiles();
}

// Render all file buttons
function renderFiles() {
    const container = document.getElementById('fileButtons');
    container.innerHTML = '';
    const files = allFiles[currentFolder] || [];
    files.forEach(f => {
        const btn = document.createElement('button');
        btn.innerText = f.replace(/\.txt$/, '');
        btn.onclick = () => runJSFile(f);
        container.appendChild(btn);
    });
}

// Run the JS file and open search result
function runJSFile(fileName) {
    const query = document.getElementById('searchInput').value.trim();
    if (!query) return;

    fetch(`Nexora/${currentFolder}/` + fileName)
        .then(res => res.text())
        .then(txt => {
            // Replace the template variable in the JS file with the typed search term
            const url = txt.replace('${encodeURIComponent(query)}', encodeURIComponent(query));
            window.open(url, '_blank');
        });
}

// Close overlay
function closeOverlay() {
    document.getElementById('overlay').style.display = 'none';
}

</script>


	

</body>
</html>
