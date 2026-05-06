


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
<title>Multi SWF Player</title>
</head>	
<style>
		
body {
  font-weight: bold;
  margin: 0;
}
	
#flashContainer {
    width: 1500px;
    height: 800px;
    margin: 0 auto;
    position: relative;
    background-color: #000000;
    border: none;
    outline: none;
}
								
.button {
  background-color: transparent;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
  padding: 10px 20px;
}
			
</style>
	


<body>
	
	

	
<input type="file" id="fileInput" multiple style="display:none;" accept=".swf">
<button class="demo w3-opacity w3-hover-opacity-off button" onclick="document.getElementById('fileInput').click()">Load</button>
<button class="demo w3-opacity w3-hover-opacity-off button" onclick="loadUploadedFiles()">Play</button>
<div id="flashContainer"></div>
<button class="video-slider-btn left-side" onclick="previousFile()">❮</button>
<button class="video-slider-btn right-side" onclick="nextFile()">❯</button>



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
// List of SWF files to load
let swfFiles = [];
let currentIndex = 0;

// Function to load SWF files
function loadSWFFiles() {
    const container = document.getElementById('flashContainer');
    container.innerHTML = ''; // Clear previous content

    const currentFile = swfFiles[currentIndex];
    if (currentFile) {
        const objectElement = document.createElement('object');
        objectElement.type = 'application/x-shockwave-flash';
        objectElement.data = currentFile;
        objectElement.width = '1500';
        objectElement.height = '800';
        objectElement.bgcolor = '#000000'; // Set background color to black

        container.appendChild(objectElement);
    }
}

// Function to handle file input change
function handleFileInputChange(event) {
    const input = event.target;
    const files = input.files;

    swfFiles.length = 0; // Clear previous files

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        // Validate file type
        if (file.type === 'application/x-shockwave-flash' || file.name.endsWith('.swf')) {
            const fileURL = URL.createObjectURL(file);
            swfFiles.push(fileURL);
        } else {
            alert(`Invalid file type: ${file.name}`);
        }
    }

    currentIndex = 0; // Reset index to the first file
}

// Function to load uploaded SWF files
function loadUploadedFiles() {
    if (swfFiles.length === 0) {
        alert("No valid SWF files loaded.");
        return;
    }
    loadSWFFiles();
}

// Function to go to the previous SWF file
function previousFile() {
    if (swfFiles.length === 0) return;
    currentIndex = (currentIndex - 1 + swfFiles.length) % swfFiles.length;
    loadSWFFiles();
}

// Function to go to the next SWF file
function nextFile() {
    if (swfFiles.length === 0) return;
    currentIndex = (currentIndex + 1) % swfFiles.length;
    loadSWFFiles();
}

// Add event listeners
document.getElementById('fileInput').addEventListener('change', handleFileInputChange);
</script>
	



	

	
<script src="https://unpkg.com/@ruffle-rs/ruffle"></script>



	
</body>
</html>
