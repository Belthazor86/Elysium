<?php
// Recursive function to scan folders for files
function scanForRarFilesByFolder($dir, $query, $baseCategory, $relativePath = '', $folderMatches = false) {
    $results = [];

    if (!is_dir($dir)) return $results;
    $items = scandir($dir);
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $path = $dir . DIRECTORY_SEPARATOR . $item;
        $currentRelativePath = $relativePath === '' ? $item : $relativePath . DIRECTORY_SEPARATOR . $item;

        if (is_dir($path)) {
            // Normalize folder name for searching
            $folderNameNormalized = strtolower(str_replace([' ', '_', '-'], '', $item));
            
            // It matches if:
            // 1. The parent already matched ($folderMatches)
            // 2. The search query is empty
            // 3. The current folder name contains the query
            $newFolderMatches = $folderMatches || ($query === '') || (strpos($folderNameNormalized, $query) !== false);

            $results = array_merge($results, scanForRarFilesByFolder($path, $query, $baseCategory, $currentRelativePath, $newFolderMatches));
        } else {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($ext === 'zip' || $ext === 'rar') {
                // ALSO check if the filename itself matches the query, even if the folder didn't
                $fileNameNormalized = strtolower(str_replace([' ', '_', '-'], '', $item));
                $fileMatchesQuery = ($query !== '' && strpos($fileNameNormalized, $query) !== false);

                if ($folderMatches || $query === '' || $fileMatchesQuery) {
                    $relativeFilePath = $baseCategory . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $currentRelativePath);
                    $results[] = [
                        'title' => pathinfo($path, PATHINFO_FILENAME),
                        'script' => $relativeFilePath,
                    ];
                }
            }
        }
    }
    return $results;
}

if (isset($_GET['search']) && isset($_GET['category'])) {
    $query = strtolower(str_replace([' ', '_', '-'], '', trim($_GET['search'])));
    $category = basename($_GET['category']);
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'AppBarn' . DIRECTORY_SEPARATOR . $category;

    $results = [];
    // This allows results to show even if the search query is empty
    if (is_dir($baseDir)) {
        $results = scanForRarFilesByFolder($baseDir, $query, 'AppBarn/' . $category);
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
<title>AppBarn</title>
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
  margin-top: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  overflow:scroll;
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

.buttons {
  margin-top: 15px;
  margin-bottom: 30px;
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  justify-content: center;
}
.buttons button {
  background: #111;
  border: 2px solid #00aaff;
  color: #00aaff;
  padding: 10px 18px;
  font-weight: 600;
  border-radius: 20px;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease;
}
.buttons button:hover {
  background-color: #00aaff;
  color: #000;
}
.buttons button.active {
  background-color: #00aaff;
  color: #000;
  box-shadow: 0 0 15px #00aaffbb;
}

</style>
</head>
<body>

<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<input type="text" id="searchInput" placeholder="Search..." />
<div class="buttons">
  <?php
  $vetraPath = __DIR__ . '/AppBarn';
  if (is_dir($vetraPath)) {
      $folders = array_filter(scandir($vetraPath), function($item) use ($vetraPath) {
          return is_dir($vetraPath . '/' . $item) && !in_array($item, ['.', '..']);
      });
      foreach ($folders as $folder) {
          echo '<button onclick="setCategory(\'' . htmlspecialchars($folder) . '\', event)">' . htmlspecialchars($folder) . '</button>';
      }
  }
  ?>
</div>


<div id="gallery"></div>



<script>
let currentCategory = '';
let currentSearch = '';

function setCategory(folderName, event) {
    currentCategory = folderName;
    
    // Highlight the button you clicked
    document.querySelectorAll('.buttons button').forEach(btn => btn.classList.remove('active'));
    event.currentTarget.classList.add('active');
    
    // Immediately search inside this folder
    searchArchives();
}

document.getElementById('searchInput').addEventListener('input', e => {
    currentSearch = e.target.value.trim();
    // Re-run the search every time you type
    searchArchives();
});

async function searchArchives() {
    // Don't do anything until a folder is clicked
    if (!currentCategory) return;

    const res = await fetch(`?search=${encodeURIComponent(currentSearch)}&category=${encodeURIComponent(currentCategory)}`);
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
        link.onclick = () => {
            const a = document.createElement('a');
            a.href = item.script;
            a.download = '';
            a.click();
        };
        gallery.appendChild(link);
    });
}
</script>


</body>
</html>