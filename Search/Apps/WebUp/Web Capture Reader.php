


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
<title>Web Capture Reader</title>	
</head>
<style>	
		
body {
  font-weight: bold;
  margin: 0;
}
	
#image {
 display: flex;
 justify-content: center;
 align-items: center;
 height: 100%; /* Use the full height of the viewport */
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
		
/* Hide scrollbar for WebKit browsers */
::-webkit-scrollbar {
    width: 0px;
    background: transparent; /* Optional: Make scrollbar background transparent */
}

/* Hide scrollbar for Firefox */
scrollbar-width: none; /* Firefox 64+ */

/* Hide scrollbar for Internet Explorer 10+ and Edge */
-ms-overflow-style: none;
										
</style>

	
<body>
	
	
<button class="demo w3-opacity w3-hover-opacity-off button" id="import">Load</button> 
<img id="image">
<input id="file-input" type="file" multiple style="display: none">
<button class="video-slider-btn left-side" id="prev">❮</button>
<button class="video-slider-btn right-side" id="next">❯</button>     


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
  // Content Security Policy (CSP) as a script
  let metaTag = document.createElement('meta');
  metaTag.setAttribute('http-equiv', 'Content-Security-Policy');
  metaTag.setAttribute('content', "default-src 'self'; img-src 'self' data:; script-src 'self'");
  document.getElementsByTagName('head')[0].appendChild(metaTag);

  const images = ["Logos/1.jpg"];

  let currentIndex = 0;
  const imageElement = document.getElementById("image");

  document.getElementById("prev").addEventListener("click", () => {
    currentIndex = (currentIndex - 1 + images.length) % images.length;
    imageElement.src = images[currentIndex];
  });

  document.getElementById("next").addEventListener("click", () => {
    currentIndex = (currentIndex + 1) % images.length;
    imageElement.src = images[currentIndex];
  });

  document.getElementById("import").addEventListener("click", () => {
    document.getElementById("file-input").click();
  });

  document.getElementById("file-input").addEventListener("change", (event) => {
    const files = event.target.files;
    if (files && files.length > 0) {
      images.length = 0;
      currentIndex = 0;
      for (let i = 0; i < files.length; i++) {
        const fileReader = new FileReader();
        fileReader.onload = () => {
          images.push(fileReader.result);
          if (i === files.length - 1) {
            imageElement.src = images[0];
          }
        };
        fileReader.readAsDataURL(files[i]);
      }
    }
  });

  function toggleFullScreen() {
    if (!document.fullscreenElement && !document.webkitFullscreenElement && !document.mozFullScreenElement && !document.msFullscreenElement ) {
      if (imageElement.requestFullscreen) {
        imageElement.requestFullscreen();
      } else if (imageElement.webkitRequestFullscreen) {
        imageElement.webkitRequestFullscreen();
      } else if (imageElement.mozRequestFullScreen) {
        imageElement.mozRequestFullScreen();
      } else if (imageElement.msRequestFullscreen) {
        imageElement.msRequestFullscreen();
      }
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
      }
    }
  }

  imageElement.addEventListener('click', toggleFullScreen);
</script>

	
	


		
</body>
</html>