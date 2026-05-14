


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
<title>Text to HTML</title>
</head>	
<style>
		
body {
  font-weight: bold;
  margin: 0;
}
			
/* Button Styles */
#loadButton {
  background-color: Black;
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
  position: relative; /* Changed from absolute to relative */
 }
	
input[type="file"] {display: none; /* Hide the file input */}
					
</style>

	
<body>
	
	

<input type="file" id="fileInput"/>
<button class="video-slider-btn" id="loadButton" onclick="document.getElementById('fileInput').click()">Load</button>
<button class="video-slider-btn" id="reloadButton" onclick="reloadFile()">Reload</button>
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

function loadFile() {
    const fileInput = document.getElementById('fileInput');
    const resultFrame = document.getElementById('resultFrame');
    
    // Validate that only text files are selected
    fileList = Array.from(fileInput.files).filter(file => file.type === 'text/plain');
    
    if (fileList.length > 0) {
        currentIndex = 0;
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
    if (currentIndex >= 0 && fileList.length > 0) {
        const resultFrame = document.getElementById('resultFrame');
        displayCurrentFile(resultFrame);
    }
}


</script>

	
	


	
					
</body>
</html>