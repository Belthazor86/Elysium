<?php
// AJAX: Search Lorevania and return only JS files from matching folders
if (isset($_GET['search']) && isset($_GET['category'])) {
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($_GET['search'])));
    $category = basename($_GET['category']);
    $baseDir = __DIR__ . "/$category";
    $results = [];

    if (is_dir($baseDir)) {
        foreach (scandir($baseDir) as $folder) {
            if ($folder === '.' || $folder === '..') continue;

            $normalizedFolder = strtolower(str_replace([' ', '_', '-'], '', $folder));
            
            // Modified condition: match folder name OR return all if query is empty
            if ($query === '' || strpos($normalizedFolder, $query) !== false) {
                $folderPath = "$baseDir/$folder";
                $scripts = glob("$folderPath/*.js");

                foreach ($scripts as $script) {
                    $results[] = [
                        'title' => pathinfo($script, PATHINFO_FILENAME), // show only file name
                        'script' => "$category/$folder/" . basename($script), // relative path for <script src="">
                    ];
                }
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
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>WebBox</title>
<style>
body {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
}

input#searchInput {
  width: 300px;
  padding: 10px 16px;
  font-size: 1.1rem;
  border-radius: 6px;
  border: none;
  background-color: #222;
  color: #fff;
  box-shadow: 0 0 8px #00aaffaa;
}
input#searchInput:focus {
  outline: none;
  box-shadow: 0 0 12px #00aaffee;
}
#gallery {
  margin-top: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
}
.file-link {
  color: #00aaff;
  font-size: 1.2rem;
  cursor: pointer;
  transition: color 0.2s;
}
.file-link:hover {
  color: #66d0ff;
}

/* Added a simple style for the preview button to keep layout consistent */
.preview-btn {
  background: #111;
  border: 2px solid #00aaff;
  color: #00aaff;
  padding: 10px 18px;
  font-weight: 600;
  border-radius: 20px;
  margin-top: 20px;
  margin-bottom: 30px;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease;
}
.preview-btn:hover {
  background-color: #00aaff;
  color: #000;
}
.preview-btn.active {
  background-color: #00aaff;
  color: #000;
  box-shadow: 0 0 15px #00aaffbb;
}

</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
<input type="text" id="searchInput" placeholder="Search..." autocomplete="off" />

<button class="preview-btn" onclick="showAll()">Preview All</button>

<div id="gallery"></div>


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
let currentCategory = 'WebBox';
let currentSearch = '';

document.getElementById('searchInput').addEventListener('input', e => {
  currentSearch = e.target.value.trim();

  const gallery = document.getElementById('gallery');

  if (currentSearch === '') {
    gallery.innerHTML = ''; // Clear results when empty
    return;
  }

  if (currentSearch.length >= 2) {
    searchScripts();
  } else {
    gallery.innerHTML = ''; // Clear if fewer than 2 characters
  }
});

// New function to fetch all files
function showAll() {
  currentSearch = '';
  searchScripts();
}

async function searchScripts() {
  const res = await fetch(`?search=${encodeURIComponent(currentSearch)}&category=${currentCategory}`);
  const data = await res.json();

  const gallery = document.getElementById('gallery');
  gallery.innerHTML = '';

  if (data.length === 0) {
    gallery.textContent = 'No matches found.';
    return;
  }

  data.forEach(item => {
    const link = document.createElement('div');
    link.className = 'file-link';
    link.textContent = item.title;
    link.onclick = () => executeScript(item.script);
    gallery.appendChild(link);
  });
}

function executeScript(scriptPath) {
  const script = document.createElement('script');
  script.src = scriptPath; // Load and execute the JS file directly
  document.body.appendChild(script);
}
</script>

</body>
</html>