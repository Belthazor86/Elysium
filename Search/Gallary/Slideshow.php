<?php
// PHP scan to populate the array from a folder
$dir = "Slideshow/"; 
$serverImages = [];
if (is_dir($dir)) {
    $files = glob($dir . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
    if ($files) { $serverImages = $files; }
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
<title>Slideshow</title>
</head> 
<style>
    
body {
  font-weight: bold;
  margin: 0;
}
        
/* Slideshow Container Styling */
#slideshow {
  position: relative;

  width: 100%;
  max-width: 100%;
  height: 100vh;
  overflow: hidden;
  border-radius: 10px;
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
  background-color: #000;
}

/* Image Styling */
#slideshow img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0;
  transition: opacity 1.5s ease-in-out, transform 1.5s ease-in-out;
}

#slideshow img.show {
  opacity: 1;
  transform: scale(1.05); /* Adds a slight zoom effect */
}

/* Error Message Styling */
.error {
  color: red;
  font-size: 1.2em;
  margin-top: 20px;
}

/* Responsive Design */
@media (max-width: 768px) {
  #slideshow {
    height: 300px;
  }
}
  
.button {
  background-color: transparent;
  border: none;
  border-radius: 5px;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
  padding: 10px 20px;
}
    
</style>

<body>
  
<button id="uploadButton" class="demo w3-opacity w3-hover-opacity-off button">Load</button>

<input type="file" id="imageInput" webkitdirectory directory accept="image/*" style="display: none;">

<div id="slideshow"></div>
<div id="errorMessage" class="error"></div>


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
const uploadButton = document.getElementById('uploadButton');
const imageInput = document.getElementById('imageInput');
const slideshow = document.getElementById('slideshow');
const errorMessage = document.getElementById('errorMessage');

let imageUrls = <?php echo json_encode($serverImages); ?>;
let currentIndex = 0;
let intervalId = null;

const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
const maxFileSize = 8 * 1024 * 1024;

uploadButton.addEventListener('click', () => {
  imageInput.click();
});

// Folder selection handler
imageInput.addEventListener('change', (event) => {
  const files = event.target.files;
  imageUrls = [];
  errorMessage.textContent = '';

  for (let file of files) {
    // Skip non-images
    if (!allowedTypes.includes(file.type)) continue;
    if (file.size > maxFileSize) continue;

    imageUrls.push(URL.createObjectURL(file));
  }

  if (imageUrls.length > 0) {
    currentIndex = 0;
    loadImages();
    startSlideshow();
  }
});

function loadImages() {
  slideshow.innerHTML = '';
  imageUrls.forEach(url => {
    const img = document.createElement('img');
    img.src = url;
    slideshow.appendChild(img);
  });
}

function displayImage() {
  const images = document.querySelectorAll('#slideshow img');
  images.forEach((img, index) => {
    img.classList.remove('show');
    if (index === currentIndex) img.classList.add('show');
  });
}

function startSlideshow() {
  clearInterval(intervalId);
  intervalId = setInterval(() => {
    currentIndex = (currentIndex + 1) % imageUrls.length;
    displayImage();
  }, 3000);
}

window.onload = () => {
  if (imageUrls.length > 0) {
    loadImages();
    startSlideshow();
  }
  displayImage();
};
</script>

</body>
</html>