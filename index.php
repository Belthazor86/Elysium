<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security.php';
?>


<?php
// --- AJAX Handler: Fetches files from the folders ---
if (isset($_GET['ajax_folder'])) {
    $folder = $_GET['ajax_folder'];
    $basePath = "Search/" . $folder . "/";
    $phpFiles = [];

    if (is_dir($basePath)) {
        $files = scandir($basePath);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
                $phpFiles[] = [
                    'name' => pathinfo($file, PATHINFO_FILENAME), // No extension
                    'path' => $basePath . $file
                ];
            }
        }
    }
    header('Content-Type: application/json');
    echo json_encode($phpFiles);
    exit; 
}
?>



<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="whitesmoke">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<link rel="icon" href="Logos/1.png" type="image/png">
<link rel="apple-touch-icon" href="Logos/2.png">
<link href="CSS/w3.css" rel="stylesheet" type="text/css" />	
<link href="CSS/fonts.css" rel="stylesheet" type="text/css" />	
<link href="CSS/theme.css" rel="stylesheet" type="text/css" />	
<link href="CSS/style.css" rel="stylesheet" type="text/css" />	
<link href="CSS/hidden.css" rel="stylesheet" type="text/css" />	
<link href="CSS/sidenav.css" rel="stylesheet" type="text/css" />
<link href="CSS/scroll.css" rel="stylesheet" type="text/css" />
<title>Elysium</title>
</head>
<style>

/* Allow full responsive scaling */
html, body {
    width: 100%;
    max-width: 100%;
    overflow-x: hidden;
    margin: 0;
    padding: 0;
}

/* DO NOT wipe all CSS on mobile — use proper adjustments */
@media (max-width: 768px) {
    body {
        padding: 10px;
    }
}
		
body  {
  font-weight: bold;
  overflow-y: auto;
  margin-top: 100px;
  margin: 0;
}

.overlay {background-color: black; /* semi-transparent black */}


#message-box {
  font-size: 20px;
  padding: 20px;
  text-align: center;
}

/* Main container grid */
.container {font-size: 25px;}

.container a:hover {color: #f1c40f;}

/* Shared Sidebar Styling */
.sidenav, .right-nav {
  height: 100%;
  width: 250px;
  position: fixed;
  z-index: 1;
  top: 0;
  background-color: #111;
  overflow-y: auto;
  padding-top: 60px;
  transition: 0.5s;
  box-shadow: 0 0 20px 6px #004d00;
}

.sidenav { left: 0; }

.right-nav { 
  right: 0; 
  width: 0; /* Hidden initially */
}

/* Identical Link Styling for both sides */
.sidenav a, .right-nav a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  font-size: 18px;
  color: #818181;
  display: block;
  cursor: pointer;
  transition: 0.3s;
}

.sidenav a:hover, .right-nav a:hover {
  color: #f1f1f1;
}

#main {
  margin-left: 250px;
  transition: margin-left 0.5s, margin-right 0.5s;
}

/* Input Styling to match the left sidebar look */
#mySearch {
  width: 80%;
  margin: 0 auto 20px 20px;
  display: block;
  padding: 8px;
  background: transparent;
  border: 1px solid #333;
  color: white;
}

.closebtn {
  position: absolute;
  top: 10px;
  background: none;
  color: white;
  font-size: 20px;
  border: none;
  cursor: pointer;
}

/* Control buttons positions */
.closebtn:nth-of-type(1) {right: 135px;}
.closebtn:nth-of-type(2) {right: 90px;}
.closebtn:nth-of-type(3) {right: 40px;}
.closebtn:nth-of-type(4) {right: 5px;}

ul { list-style-type: none; padding: 0; }
							
</style>
	

<body>	



<div id="myNav" class="overlay">
<h2><?php echo basename(dirname($_SERVER['SCRIPT_FILENAME'])); ?></h2>
<div id="message-box" class="demo w3-opacity w3-hover-opacity-off">Loading message...</div>
<div class="logo">
<?php
$folder = "Logos/";
$images = scandir($folder);

foreach ($images as $img) {
    if ($img !== "." && $img !== "..") {
        echo '<img class="demo w3-opacity w3-hover-opacity-off" src="'.$folder.$img.'" alt="Company Logo" onclick="openNav()" width="15%">';
        break; // only first image like your example (1.png)
    }
}
?>
</div>
<div class="container"><a class="demo w3-opacity w3-hover-opacity-off" onclick="closeNav()">Welcome</a></div>
</div>


<div id="main">
    <iframe id="contentFrame" src="Images/Search.php" frameborder="0" scrolling="auto" style="width:100%; height:100vh;"></iframe>
</div>


<div id="mySidenav" class="sidenav">
    <div class="row">
        <div class="left">
            <input type="text" id="mySearch" onkeyup="myFunction()" placeholder="Search .." title="Type in a category" autocomplete="off">
            <ul id="myMenu">
              <?php
                $root = "Search/";
                if (is_dir($root)) {
                    $items = scandir($root);
                    foreach ($items as $item) {
                        if ($item !== '.' && $item !== '..' && is_dir($root . $item)) {
                            $folderName = htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
                            echo "<li>
                                    <a class='demo w3-opacity w3-hover-opacity-off' 
                                       onclick=\"fetchFilesForFolder('{$folderName}')\">
                                       {$folderName}
                                    </a>
                                  </li>";
                        }
                    }
                }
              ?>
            </ul>
        </div>
        <button class="closebtn w3-opacity w3-hover-opacity-off" onclick="openNav()">🗏</button>           
        <button class="closebtn w3-opacity w3-hover-opacity-off" onclick="toggleFullscreen()">⛶ </button>    
        <button class="closebtn w3-opacity w3-hover-opacity-off" onclick="toggleRightNav()">❔</button> 
        <button class="closebtn w3-opacity w3-hover-opacity-off" onclick="navigateToIframe('Images/Search.php')">⚙</button> 
    </div>
</div>

<div id="mySidenavRight" class="right-nav">
    <div class="row">
        <div class="left">
            <div style="padding: 0 8px 20px 32px; color: #555; font-size: 14px; text-transform: uppercase;"></div>
            <ul id="rightMenu">
                </ul>
        </div>
    </div>
</div>


	
<script>
// message loading 

let messages = [];
let remainingMessages = [];

function shuffle(array) {
  for (let i = array.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [array[i], array[j]] = [array[j], array[i]];
  }
}

function showNextMessage() {
  if (remainingMessages.length === 0) {
    // Reset and reshuffle when all messages have been shown
    remainingMessages = [...messages];
    shuffle(remainingMessages);
  }
  const message = remainingMessages.pop();
  document.getElementById('message-box').textContent = message;
}

// Updated for .txt file
fetch('Messages/index.txt')
  .then(res => res.text()) // Fetch as plain text
  .then(data => {
    // Split by new line and filter out empty lines
    messages = data.split(/\r?\n/).filter(line => line.trim() !== "");
    
    remainingMessages = [...messages];
    shuffle(remainingMessages);
    showNextMessage();
    setInterval(showNextMessage, 5000);
  })
  .catch(() => {
    document.getElementById('message-box').textContent = 'Failed to load messages.';
  });

</script>


<script>
// Search Filter for Left Sidebar
function myFunction() {
  var input, filter, ul, li, a, i;
  input = document.getElementById("mySearch");
  filter = input.value.toUpperCase();
  ul = document.getElementById("myMenu");
  li = ul.getElementsByTagName("li");
  for (i = 0; i < li.length; i++) {
    a = li[i].getElementsByTagName("a")[0];
    if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
      li[i].style.display = "";
    } else {
      li[i].style.display = "none";
    }
  }
}

// Fetch files and inject into Right Nav with identical classes
function fetchFilesForFolder(folderName) {
    fetch(`?ajax_folder=${folderName}`)
        .then(response => response.json())
        .then(files => {
            const rightMenu = document.getElementById('rightMenu');
            rightMenu.innerHTML = ''; 

            files.forEach(file => {
                const li = document.createElement('li');
                // Using identical classes: demo w3-opacity w3-hover-opacity-off
                li.innerHTML = `
                    <a class="demo w3-opacity w3-hover-opacity-off" 
                       onclick="navigateToIframe('${file.path}')">
                       ${file.name}
                    </a>`;
                rightMenu.appendChild(li);
            });

            // Auto-open right nav if closed
            const sidenav = document.getElementById("mySidenavRight");
            if (sidenav.style.width !== "250px") {
                toggleRightNav();
            }
        })
        .catch(err => console.error('Error:', err));
}

function toggleRightNav() {
  const sidenav = document.getElementById("mySidenavRight");
  const mainContent = document.getElementById("main");

  if (sidenav.style.width === "250px") {
    sidenav.style.width = "0";
    mainContent.style.marginRight = "0";
  } else {
    sidenav.style.width = "250px";
    mainContent.style.marginRight = "250px";
  }
}

function toggleFullscreen() {
  const elem = document.getElementById('main');
  if (!document.fullscreenElement) {
    elem.requestFullscreen?.() || elem.webkitRequestFullscreen?.() || elem.mozRequestFullScreen?.() || elem.msRequestFullscreen?.();
  } else {
    document.exitFullscreen?.();
  }
}

</script>


<script>

// Function to handle iframe navigation
function navigateToIframe(page) {
    const iframe = document.getElementById('contentFrame'); // Ensure the iframe ID matches
    if (iframe) {
        iframe.src = page;
    } else {
        console.error("Iframe with ID 'contentFrame' not found.");
    }
}

// Function to handle regular navigation
function navigateTo(page) {
    if (page.includes('.php') || page.includes('.php')) {
        window.location.href = page; // Navigate to the corresponding page
    } else {
        window.location.href = page + '.php'; // Add .php extension by default
    }
}

</script>



<script>

 // Open and Close Overlay

function openNav() {
  document.getElementById("myNav").style.height = "100%";
}

function closeNav() {
  document.getElementById("myNav").style.height = "0%";
}
</script>

	
</body>
</html>