<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>


<?php
// Scan the "images" folder for files
$imageDirectory = 'Boardify/';
$imageFiles = [];

if (isset($_GET['scan'])) {
    $imageDirectory = 'Boardify/';
    $imageFiles = [];
    if (is_dir($imageDirectory)) {
        $files = scandir($imageDirectory);
        foreach ($files as $file) {
            if (preg_match('/\.(jpg|jpeg|png|gif|webp|bmp|svg)$/i', $file)) {
                $imageFiles[] = ['name' => $file, 'path' => $imageDirectory . $file];
            }
        }
    }
    header('Content-Type: application/json');
    echo json_encode($imageFiles);
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
<title>Boardify</title>	
</head>
<style>
		
body {
  font-weight: bold;
  margin: 0;
}

#image-board {
    column-count: 4;
    column-gap: 15px;
    padding: 20px;
    margin-top: 60px;
    margin-bottom: 100px;
}

#image-board img {
    width: 100%;
    border-radius: 12px;
    margin-bottom: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    break-inside: avoid;
}

#image-board img:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0,0,0,0.7);
}

#image-board img.enlarged {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: auto;
    height: auto;
    max-width: 100vw;
    max-height: 100vh;
    z-index: 1000;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.9);
}

#overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 999;
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
			
</style>

	
<body>
	
	
<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>



<!-- CENTER CONTAINER -->
<div style="width:100%; display:flex; justify-content:center; margin-top:20px;">
<button class="demo w3-opacity w3-hover-opacity-off button" id="uploadButton" onclick="uploadImages()">Load</button>
<input type="file" id="image-file-input" style="display: none;" webkitdirectory directory multiple accept="image/*">
<button class="demo w3-opacity w3-hover-opacity-off button" id="image-scan-btn">Scan</button>
</div>


<div id="overlay"></div>
<div id="image-board"></div>


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
const imageBoard = document.getElementById("image-board");
const scanButton = document.getElementById("image-scan-btn");
const imageFileInput = document.getElementById("image-file-input");
const overlay = document.getElementById("overlay");

let images = [];

const serverFiles = <?php echo json_encode($imageFiles); ?>;

// Add event listener to the file input
imageFileInput.addEventListener("change", handleFileSelect);

// Display the images on the board
function displayImages() {
  imageBoard.innerHTML = "";
  images.forEach((image) => {
    const img = document.createElement("img");
    
    if (image.path) {
      img.src = image.path;
    } else {
      img.src = URL.createObjectURL(image);
    }
    
    img.alt = image.name;
    img.classList.add("demo");
    
    img.addEventListener("click", function() {
      if (this.classList.contains("enlarged")) {
        this.classList.remove("enlarged");
        overlay.style.display = "none";
      } else {
        this.classList.add("enlarged");
        overlay.style.display = "block";
      }
    });
    
    imageBoard.appendChild(img);
  });
}

// Close enlarged image when clicking overlay
overlay.addEventListener("click", function() {
  const enlargedImg = document.querySelector("#image-board img.enlarged");
  if (enlargedImg) {
    enlargedImg.classList.remove("enlarged");
    overlay.style.display = "none";
  }
});

// Scan folder event
scanButton.addEventListener("click", () => {
    fetch(window.location.pathname + '?scan=1')
        .then(r => r.json())
        .then(data => {
            images = data;
            if (data.length > 0) displayImages();
            else alert("No images found.");
        })
        .catch(() => alert('Scan failed'));
});

// Function to handle selected files
function handleFileSelect(event) {
  const files = event.target.files;
  images = [];

  for (let i = 0; i < files.length; i++) {
    const fileExtension = files[i].name.split('.').pop().toLowerCase();
    if (fileExtension.match(/^(jpg|jpeg|png|gif|webp|bmp|svg)$/)) {
      images.push(files[i]);
    }
  }

  if (images.length > 0) {
    displayImages();
  }

  imageFileInput.value = "";
}

// Function to open file dialog when the button is clicked
function uploadImages() {
  imageFileInput.click();
}
</script>

	



				
</body>
</html>