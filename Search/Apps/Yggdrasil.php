<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />	
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />	
<link href="../../CSS/style.css" rel="stylesheet" type="text/css" />	
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Yggdrasil</title>
</head>
<style>
		
body  {
  font-weight: bold;
  overflow:auto;
  margin: 0;
}

/* Logo */
.logo {
  position: relative;
  padding-top: 20px;
  width: 100%;
  text-align: center;
  margin-top: 20px;
}

.logo img {
  display: inline-block;
  width: 20%;
  max-width: 500px;
  min-width: 100px;
  height: auto;
  margin: 0 auto;
}
			
.sidenav {
  height: 100%;
  width: 0;
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  background-color: transparent;
  overflow-x: scroll;
  transition: 0.5s;
  padding-top: 60px;
}

.sidenav a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  font-size: 18px;
  color: #818181;
  display: block;
  transition: 0.3s;
}

.sidenav a:hover {
  color: #f1f1f1;
}

.sidenav .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 25px;
  margin-left: 50px;
}

#main {
  transition: margin-left .5s;
}

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}

#clock {
  font-size: 40px;
  padding-top: 100%;
  font-weight: bold;
  margin-left: auto;
  margin-right: auto;
  display: block;
  text-align: center;
}

#date {
  font-size: 18px;
  color: whitesmoke;
  margin-left: auto;
  margin-right: auto;
  display: block;
  text-align: center;
}

.sidenav {box-shadow: 0 0 20px 6px #004d00;}

/* Main container grid */
.container {font-size: 25px;}

.container a:hover {color: #f1c40f;}
	
						
</style>
	

<body>	


	
<div id="main">
<div class="mySlides fade">
<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
<div class="logo" =>
<?php
$folder = "Yggdrasil/";
$images = scandir($folder);

foreach ($images as $img) {
    if ($img !== "." && $img !== "..") {
        echo '<img class="demo w3-opacity w3-hover-opacity-off" src="'.$folder.$img.'" id="randomPlayButton" width="15%">';
        break; // only first image like your example (1.png)
    }
}
?>
</div>

<div class="container"><a class="demo w3-opacity w3-hover-opacity-off" onclick="toggleNav()">Entrance</a></div>
</div>

<div class="mySlides fade">
<iframe id="contentFrame" src="Yggdrasil/Search.php" frameborder="0" scrolling="auto"></iframe>
</div>

<div class="mySlides fade">
<iframe id="contentFrame" src="Yggdrasil/Search.php" frameborder="0" scrolling="auto"></iframe>
</div>
							
<div class="mySlides fade">
<iframe id="contentFrame" src="Yggdrasil/Search.php" frameborder="0" scrolling="auto"></iframe>
</div>	

<div class="mySlides fade">
<iframe id="contentFrame" src="Yggdrasil/Search.php" frameborder="0" scrolling="auto"></iframe>
</div>

<div class="mySlides fade">
<iframe id="contentFrame" src="Yggdrasil/Search.php" frameborder="0" scrolling="auto"></iframe>
</div>
</div>	

	
<div id="mySidenav" class="sidenav">
    <div class="row">
        <div class="left">
            <ul id="myMenu">
	          <a class="demo w3-opacity w3-hover-opacity-off icon" onclick="currentSlide(1)">Yggdrasil</a>
	          <a class="demo w3-opacity w3-hover-opacity-off icon" onclick="currentSlide(2)">Screen 1</a>
	          <a class="demo w3-opacity w3-hover-opacity-off icon" onclick="currentSlide(3)">Screen 2</a>
	          <a class="demo w3-opacity w3-hover-opacity-off icon" onclick="currentSlide(4)">Screen 3</a>
            <a class="demo w3-opacity w3-hover-opacity-off icon" onclick="currentSlide(5)">Screen 4</a>
            <a class="demo w3-opacity w3-hover-opacity-off icon" onclick="currentSlide(6)">Screen 5</a>
              <div id="clock"></div>
              <div id="date"></div>
            </ul>
        </div>  
    </div>
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
let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}    
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";  
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";  
}
</script>

	
<script>
    function updateClock() {
      var now = new Date();
      var hours = now.getHours();
      var minutes = now.getMinutes();
      var seconds = now.getSeconds();
      var date = now.toDateString();

      var timeString = hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');

      document.getElementById('clock').textContent = timeString;
      document.getElementById('date').textContent = date;
    }

    setInterval(updateClock, 1000); // Update the clock every second
  </script>
		
		
<script>
function toggleNav() {
  const sidenav = document.getElementById("mySidenav");
  const mainContent = document.getElementById("main");
  
  // Check if the sidenav is open (width is 250px)
  if (sidenav.style.width === "250px") {
    // Close the sidenav
    sidenav.style.width = "0";
    mainContent.style.marginLeft = "0";
  } else {
    // Open the sidenav
    sidenav.style.width = "250px";
    mainContent.style.marginLeft = "250px";
  }
}
</script>


<script>
function loadIframeClean(url) {
  const iframe = document.getElementById('contentFrame');
  if (!iframe) {
    console.error('Iframe element not found');
    return;
  }

  iframe.onload = () => {
    if (iframe.src === 'about:blank') {
      iframe.onload = null;  // Remove event listener
      iframe.src = url;      // Load new URL
    }
  };

  iframe.src = 'about:blank';  // Reset iframe first
}
</script>




 	

	
</body>
</html>