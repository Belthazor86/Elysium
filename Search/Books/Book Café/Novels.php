
<?php include '../../../Security.php'; ?>


<?php
// Start the session
session_start();

// Define the allowed directory relative to the document root
$allowedDirectory = '/Elysium/Codes/Zenith/BookBind/';  // Adjust this to match your actual structure

// Get the current file's path relative to the document root
$currentFilePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']);

// Check if the current file is not in the allowed directory and is not the index page
if (strpos($currentFilePath, $allowedDirectory) !== 0 && basename($currentFilePath) !== 'index.php') {
    // Redirect to the home page if not allowed
    header("Location: /Elysium/index.php"); // Adjust path if index.php is in a different directory
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../../CSS/style.css" rel="stylesheet" type="text/css" />
<link href="../../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<title>Novels</title>
<style>

  body {margin: 0;}


  .folder-section {
    margin-bottom: 50px;
  }
  .grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
  }
  .poster {
    width: 180px;
    border-radius: 6px;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    transition: transform 0.2s ease;
  }
  .poster:hover {
    transform: scale(1.05);
  }

.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(18, 18, 18, 0.95);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}
.overlay-content {
  position: relative;
  width: 75vw;
  max-width: 1200px;
  height: 75vh;
  background: #000;
  border-radius: 15px;
  box-shadow: 0 0 40px #00ffc3;
  overflow: hidden;
}
.overlay iframe {
  width: 100%;
  height: 100%;
  border: none;
}

.overlay-close {
  position: absolute;
  top: 10px; right: 10px;
  background: #000;
  color: whitesmoke;
  border: none;
  padding: 5px 10px;
  font-weight: bold;
  border-radius: 4px;
  cursor: pointer;
  z-index: 10;
}

.slideshow-container {
  position: relative;
  width: 100%;
  max-width: 100vw;
  height: 100vh; /* optional: set a container height */
  margin: auto;
  overflow: hidden;
}

.slide {
  display: none;
  width: 100%;
  height: 100%;
}

.slide img {
  width: 100%;
  height: 100%;
  object-fit: contain; /* 'cover' if you want to fill fully */
  display: block;
  cursor: pointer;
}



button.prev, button.next {
  position: fixed; /* change from absolute to fixed */
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0, 0, 0, 0.5);
  border: none;
  color: #fff;
  font-size: 2rem;
  padding: 0.5rem 1rem;
  cursor: pointer;
  user-select: none;
  z-index: 1000; /* ensures buttons stay on top */
}

button.prev:hover, button.next:hover {
  background: rgba(0, 0, 0, 0.8);
}

button.prev { left: 10px; border-radius: 4px; }
button.next { right: 10px; border-radius: 4px; }


</style>
</head>
<body>


<?php
if (isset($_GET['embedScript'])) {
    $path = $_GET['embedScript'];
    $baseAllowed = realpath(__DIR__ . '/../../../Search/Book Café/Novels/');
    $full = realpath($baseAllowed . '/' . $path);
    if ($full && strpos($full, $baseAllowed) === 0 && file_exists($full)) {
        $scriptContent = file_get_contents($full);
        header('Content-Type: text/html');
        echo "<!DOCTYPE html>
<html lang='en'>
<head><meta charset='UTF-8'><title>Embed Script</title>
<style>
  html, body {margin:0; padding:0; width:100vw; height:100vh;}
  canvas, #mainContent {display:block; width:100vw; height:100vh;}
</style>
</head>
<body>
<script>
function makeCanvasFullScreen() {
  const canvas = document.querySelector('canvas');
  if (!canvas) return;
  function resize() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
  }
  window.addEventListener('resize', resize);
  resize();
}
makeCanvasFullScreen();
$scriptContent
</script>
</body>
</html>";
        exit;
    } else {
        http_response_code(403);
        echo "Access Denied";
        exit;
    }
}

$baseDir = __DIR__ . "/../../../Search/Book Café/Novels/";
if (!is_dir($baseDir)) {
    echo "<p>Books directory not found.</p>";
    exit;
}

$folders = array_filter(scandir($baseDir), function($f) use ($baseDir) {
    return $f !== '.' && $f !== '..' && is_dir($baseDir . $f);
});

$allPosters = []; // Collect all posters for slideshow

foreach ($folders as $folder) {
    $posterDir = $baseDir . $folder . "/Posters/";
    $scriptDir = $baseDir . $folder . "/Scripts/";

    if (!is_dir($posterDir) || !is_dir($scriptDir)) continue;

    $textFiles = array_filter(scandir($posterDir), function($f) use ($posterDir) {
        return is_file($posterDir . $f) && strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'txt';
    });
    natsort($textFiles);

    if (count($textFiles) === 0) continue;

    foreach ($textFiles as $textFile) {
        $fileId = pathinfo($textFile, PATHINFO_FILENAME);
        $scriptFile = $scriptDir . $fileId . ".js";

        if (!file_exists($scriptFile)) continue;

        $imageUrl = trim(file_get_contents($posterDir . $textFile));

        $allPosters[] = [
            'img' => $imageUrl,
            'script' => str_replace('\\', '/', substr($scriptFile, strlen(__DIR__) + 1))
        ];
    }
}

if (count($allPosters) === 0) {
    echo "<p>No posters found.</p>";
    exit;
}
?>

<div class="slideshow-container">
    <?php foreach ($allPosters as $index => $poster): ?>
        <div class="slide" style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
            <img src="<?= htmlspecialchars($poster['img']) ?>" class="poster" 
                 data-script="<?= htmlspecialchars($poster['script'], ENT_QUOTES) ?>" alt="Poster">
        </div>
    <?php endforeach; ?>

    <button class="prev" onclick="changeSlide(-1)">❮</button>
    <button class="next" onclick="changeSlide(1)">❯</button>
</div>


<div class="overlay" id="overlay">
  <div class="overlay-content">
    <button class="overlay-close" onclick="closeOverlay()">❌</button>
    <iframe id="embedFrame" src="about:blank" sandbox="allow-scripts allow-same-origin"></iframe>
  </div>
</div>

<script>
let slideIndex = 0;
const slides = document.querySelectorAll('.slide');
const overlay = document.getElementById('overlay');
const embedFrame = document.getElementById('embedFrame');

function showSlide(index) {
    if (index >= slides.length) slideIndex = 0;
    if (index < 0) slideIndex = slides.length - 1;
    slides.forEach((slide, i) => slide.style.display = i === slideIndex ? 'block' : 'none');
}

function changeSlide(n) {
    slideIndex += n;
    showSlide(slideIndex);
}

slides.forEach(slide => {
    slide.querySelector('img').addEventListener('click', () => {
        const scriptPath = slide.querySelector('img').getAttribute('data-script');
        embedFrame.src = '?embedScript=' + encodeURIComponent(scriptPath);
        overlay.style.display = 'flex';
    });
});

function closeOverlay() {
    overlay.style.display = 'none';
    embedFrame.src = 'about:blank';
}
</script>

<script src="../../../Scripts/Redirect.js"></script>


<script src="../../../Scripts/Download.js"></script>

</body>
</html>
