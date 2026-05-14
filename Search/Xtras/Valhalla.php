

<?php
if (isset($_GET['search'])) {
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($_GET['search'])));
    $baseDir = __DIR__ . "/../../Search";
    $results = [];

    if (is_dir($baseDir)) {
        foreach (scandir($baseDir) as $folder) {
            if ($folder === '.' || $folder === '..') continue;

            $folderPath = "$baseDir/$folder";
            if (!is_dir($folderPath)) continue;

            $files = glob("$folderPath/*.php");
            foreach ($files as $file) {
                $normalizedFile = strtolower(str_replace([' ', '_', '-'], '', pathinfo($file, PATHINFO_FILENAME)));

                // Add normalized name to results
                $results[] = [
                    'title' => pathinfo($file, PATHINFO_FILENAME),
                    'normalized' => $normalizedFile,
                    'script' => "../../Search/$folder/" . basename($file),
                ];
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>The Abyssal Kingdom</title>
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/style.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/overlay.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<style>
body {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
  font-weight: bold;
  margin-top: 100px;
  margin: 0;
}

#message-box {
  font-size: 20px;
  padding: 20px;
  text-align: center;
}
	

.logo img {
  width: 18%;
  margin-top: -40px; /* Adjust the value as needed */
}

input#searchInput {
  width: 400px;
  padding: 10px 16px;
  font-size: 1.1rem;
  border-radius: 6px;
  border: none;
  background-color: transparent;
  color: whitesmoke;
  box-shadow: 0 0 40px #00ffc3;
  margin-top: 40px;
}

input#searchInput:focus {
  outline: none;
  box-shadow: 0 0 40px #00ffc3;
}

.overlay .closebtn {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 20px;
  border: none;
  outline: none;
}


</style>
</head>
<body>



<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
<div id="message-box" class="demo w3-opacity w3-hover-opacity-off">Loading message...</div>


<div class="logo" =>
<?php
$folder = "The Abyssal Kingdom/";
$images = scandir($folder);

foreach ($images as $img) {
    if ($img !== "." && $img !== "..") {
        echo '<img class="demo w3-opacity w3-hover-opacity-off" src="'.$folder.$img.'" id="randomPlayButton" width="15%">';
        break; // only first image like your example (1.png)
    }
}
?>
</div>




<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />
<p id="errorMessage" style="color:whitesmoke; margin-top:10px; display:none;"></p>



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
fetch('../../Messages/The Abyssal Kingdom.txt')
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
const errorMessages = [
  "Ha.Ha.Ha Try Again.",
  "You Shall Never Leave.",
  "Your Soul is Ours.",
];

function showRandomError() {
  const randomIndex = Math.floor(Math.random() * errorMessages.length);
  return errorMessages[randomIndex];
}

const errorMsg = document.getElementById('errorMessage');
const searchInput = document.getElementById('searchInput');
let debounceTimeout;

searchInput.addEventListener('input', () => {
  errorMsg.style.display = 'none';
  errorMsg.textContent = '';
  clearTimeout(debounceTimeout);

  const rawQuery = searchInput.value.trim();

  if (rawQuery.length < 3) return;

  debounceTimeout = setTimeout(async () => {
    const normalizedQuery = rawQuery.toLowerCase().replace(/[\s_-]/g, '');
    try {
      const res = await fetch(`?search=${encodeURIComponent(rawQuery)}`);
      if (!res.ok) throw new Error('Network response was not ok');
      const data = await res.json();
      const exactMatch = data.find(item => item.normalized === normalizedQuery);

      if (exactMatch) {
        errorMsg.style.display = 'none';
        window.location.href = exactMatch.script;
      } else {
        errorMsg.textContent = showRandomError();
        errorMsg.style.display = 'block';
      }
    } catch (e) {
      errorMsg.textContent = 'Server error. Please try again.';
      errorMsg.style.display = 'block';
    }
  }, 500);
});

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
