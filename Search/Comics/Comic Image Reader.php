


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
<title>Comic Image Reader</title>	
</head>
<style>	
		
body {
  font-weight: bold;
  margin: 0;
}
	
#image {
  max-width: 35%;
  max-height: 35%;
  width: auto;
  height: auto;
  display: block;
  margin: auto;
  transition: transform 0.5s ease; /* Smooth transition for zoom */
  cursor: pointer; /* Indicate image is clickable */
  object-fit: contain; /* Ensure aspect ratio is maintained */
}

/* Define the zoomed state with scaling */
#image.zoomed {
  cursor: move; /* Cursor indicates the image can be dragged */
  transform-origin: center center; /* This allows zooming from the center initially */
  transform: scale(2.0); /* Zoom in */
  overflow: auto; /* Enable scrolling when zoomed */
}

/* Define the zoomed-out state */
#image.zoomed-out {
  transform: scale(1); /* Return to original size */
  overflow: hidden; /* Disable scrolling when zoomed out */
}



														
  /* Navigation */
 .video-slider-btn {
    border: none;
    display: inline-block;
    color: #fff;
    font-size: 50px;
    padding: 10px;
    vertical-align: middle;
    overflow: hidden;
    text-decoration: none;
    background-color: transparent;
    text-align: center;
    cursor: pointer;
    white-space: nowrap;
    z-index: 99999;
    opacity: .7;
    transition: all 350ms ease-in-out;
  }
	
 .video-slider-btn:hover {
    opacity: 1;
    transition: all 350ms ease-in-out;
  }
	
 .video-slider-btn.left-side {
    position: absolute;
    top: 50%;
    left: 0%;
    transform: translate(0%,-50%);
    -ms-transform: translate(-0%,-50%);
  }
	
 .video-slider-btn.right-side {
    position: absolute;
    top: 50%;
    right: 0%;
    transform: translate(0%,-50%);
    -ms-transform: translate(0%,-50%);
}
		
/* Button Styles */
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
	
	
<button class="demo w3-opacity w3-hover-opacity-off button" id="import">Load</button>
<img id="image" alt="Image" width="35%" class="zoomable">
<input id="file-input" type="file" multiple style="display: none">
<button class="video-slider-btn left-side" id="prev">❮</button>
<button class="video-slider-btn right-side" id="next">❯</button>

<?php
$folder = "Comic Image Reader/";
$images = [];

if (is_dir($folder)) {
    $files = scandir($folder);
    foreach ($files as $file) {
        if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
            $images[] = $folder . $file;
        }
    }
}
?>



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
const images = <?php echo json_encode($images); ?>;

let currentIndex = 0;
const imageElement = document.getElementById("image");

function displayImage(index) {
  imageElement.src = images[index];
  imageElement.classList.remove("zoomed", "zoomed-out");
}

document.getElementById("prev").addEventListener("click", () => {
  currentIndex = (currentIndex - 1 + images.length) % images.length;
  displayImage(currentIndex);
});

document.getElementById("next").addEventListener("click", () => {
  currentIndex = (currentIndex + 1) % images.length;
  displayImage(currentIndex);
});

imageElement.addEventListener("click", function (event) {
  const rect = this.getBoundingClientRect();
  const offsetX = event.clientX - rect.left;
  const offsetY = event.clientY - rect.top;

  if (this.classList.contains("zoomed")) {
    this.classList.remove("zoomed");
    this.classList.add("zoomed-out");
  } else {
    this.classList.remove("zoomed-out");
    this.classList.add("zoomed");
    this.style.transformOrigin = `${(offsetX / rect.width) * 100}% ${(offsetY / rect.height) * 100}%`;
  }
});

document.getElementById("import").addEventListener("click", () => {
  document.getElementById("file-input").click();
});

document.getElementById("file-input").addEventListener("change", (event) => {
  const files = event.target.files;
  const validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];

  if (files && files.length > 0) {
    images.length = 0;
    currentIndex = 0;
    for (let i = 0; i < files.length; i++) {
      const file = files[i];

      if (validImageTypes.includes(file.type)) {
        const fileReader = new FileReader();
        fileReader.onload = () => {
          images.push(fileReader.result);
          if (i === files.length - 1) {
            imageElement.src = images[0];
          }
        };
        fileReader.readAsDataURL(file);
      } else {
        alert('Invalid file type: ' + file.name);
      }
    }
  }
});

displayImage(0);
</script>



	

		
</body>
</html>