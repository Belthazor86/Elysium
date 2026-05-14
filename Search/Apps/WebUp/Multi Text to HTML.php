<!doctype html>

<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../../CSS/w3.css" rel="stylesheet" type="text/css" />	
<link href="../../../CSS/fonts.css" rel="stylesheet" type="text/css" />	
<link href="../../../CSS/style.css" rel="stylesheet" type="text/css" />
<link href="../../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Multi Text to HTML</title>
</head> 
<style>
        
body {
  font-weight: bold;
  margin: 0;
}
            
/* Button Styles */
#loadButton {
  background-color: transparent;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
}
    
/* Button Styles */
#reloadButton {
  background-color: transparent;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
}
    
#resultFrame {
  width: 100%;
  height: 100vh;
  border: none;
  position: relative;
 }
    
input[type="file"] {display: none; }
    


                    
</style>

<body>
    
<?php
// CHANGE THIS TO YOUR FOLDER NAME
$dir = "Multi Text to HTML"; 

$phpFiles = [];
if (is_dir($dir)) {
    foreach (glob("$dir/*.txt") as $filename) {
        $phpFiles[] = $filename;
    }
}
$jsonFiles = json_encode($phpFiles);
?>
    
<input type="file" id="fileInput" multiple accept=".txt" />
<button class="video-slider-btn" id="loadButton" onclick="document.getElementById('fileInput').click()">Upload</button>

<!-- THE ADDITIONAL BUTTON -->
<button class="video-slider-btn" id="reloadButton" onclick="scanFolder()">Scan</button>

<button class="video-slider-btn left-side" id="prevButton">❮</button>
<button class="video-slider-btn right-side" id="nextButton">❯</button>
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
let isPHPMode = false;

function scanFolder() {
    fileList = <?php echo $jsonFiles; ?>;
    isPHPMode = true;
    if (fileList.length > 0) {
        currentIndex = 0;
        displayCurrentFile();
    } else {
        alert('No files found in folder.');
    }
}

function loadFile() {
    const fileInput = document.getElementById('fileInput');
    isPHPMode = false;
    fileList = Array.from(fileInput.files).filter(file => file.type === 'text/plain');
    
    if (fileList.length > 0) {
        currentIndex = 0;
        displayCurrentFile();
    } else {
        alert('Please choose valid text files before loading.');
    }
}

function navigateFiles(direction) {
    if (fileList.length === 0) return;
    currentIndex += direction;

    if (currentIndex < 0) {
        currentIndex = fileList.length - 1;
    } else if (currentIndex >= fileList.length) {
        currentIndex = 0;
    }

    displayCurrentFile();
}

function displayCurrentFile() {
    const resultFrame = document.getElementById('resultFrame');
    
    if (isPHPMode) {
        fetch(fileList[currentIndex])
            .then(r => r.text())
            .then(content => {
                resultFrame.srcdoc = `<html><body><pre style="white-space:pre-wrap; font-weight:bold; font-family:inherit;">${content}</pre></body></html>`;
            });
    } else {
        const file = fileList[currentIndex];
        const reader = new FileReader();
        reader.onload = function (e) {
            const content = e.target.result;
            resultFrame.srcdoc = `<html><head><title>Transformed HTML</title></head><body><pre style="white-space:pre-wrap; font-weight:bold; font-family:inherit;">${content}</pre></body></html>`;
        };
        reader.readAsText(file);
    }
}

document.getElementById('fileInput').addEventListener('change', loadFile);
document.getElementById('prevButton').addEventListener('click', () => navigateFiles(-1));
document.getElementById('nextButton').addEventListener('click', () => navigateFiles(1));
</script>
                    
</body>
</html>