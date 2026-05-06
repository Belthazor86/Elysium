


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
<title>Text Reader</title>
<style>
body {
  font-weight: bold;
  margin: 0;
}

#playlist {
  list-style-type: none;
  padding: 0;
  text-align: center; /* Center text within playlist */
}

#playlist li {
  margin-bottom: 5px;
  cursor: pointer;
  color: #fff;
  font-size: 1.2rem;
  transition: color 0.2s;
}

#overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: #000;
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

#overlay .container {
  position: relative;
  width: 90vw;
  max-width: 1200px;
  height: 75vh;
  background: #000;
  border-radius: 15px;
  overflow: hidden;
}

#overlay button {
  position: absolute;
  top: 10px;
  border: none;
  padding: 6px 10px;
  font-size: 1.2rem;
  border-radius: 6px;
  cursor: pointer;
  background: black;
  color: #fff;
  transition: background 0.2s;
}
#overlay button:hover {
  background: #0090dd;
}
#overlay button:first-child {
  right: 10px; /* Close button */
}

#bookContent {
  max-height: 100vh;
  width: 900px;   /* increase or decrease as you want */
  margin: 0 auto;
  overflow-y: auto;
  padding: 20px;
  line-height: 1.6;
  font-size: 18px;
  color: whitesmoke;          
  background: #000;           
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.5);
  scroll-behavior: smooth;    
  text-align: center;
}

#bookContent pre {
  white-space: pre-wrap;      
  font-family: Arial, sans-serif; 
  font-weight:bold;
  margin: 0 auto;             
  display: inline-block;      
  text-align: center;         
}

 .button {
  background-color: transparent;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
  padding: 10px 20px;
}
	
</style>
</head>
<body>

<button id="loadFolderBtn" class="demo w3-opacity w3-hover-opacity-off button">Load</button>
<input type="file" id="folderInput" webkitdirectory directory multiple style="display:none" />

<ul id="playlist"></ul>

<div id="overlay">
  <div class="container" id="overlayContainer">
    <button onclick="closeOverlay()">✖️</button>
    <div id="bookContent"></div>
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
const loadFolderBtn = document.getElementById('loadFolderBtn');
const folderInput = document.getElementById('folderInput');
const playlist = document.getElementById('playlist');

let filesMap = {};

// Open folder selector
loadFolderBtn.addEventListener('click', () => folderInput.click());

// Load files from folder
folderInput.addEventListener('change', e => {
  playlist.innerHTML = '';
  filesMap = {};

  Array.from(e.target.files)
    .filter(file => file.name.toLowerCase().endsWith('.txt'))
    .forEach(file => {
      const nameWithoutExt = file.name.replace(/\.txt$/i, '');
      filesMap[nameWithoutExt] = file;

      const li = document.createElement('li');
      li.textContent = nameWithoutExt;
      li.classList.add("demo", "w3-opacity", "w3-hover-opacity-off");
      li.onclick = () => openOverlay(file);
      playlist.appendChild(li);
    });
});

function openOverlay(file) {
  const overlay = document.getElementById('overlay');
  const contentDiv = document.getElementById('bookContent');
  contentDiv.innerHTML = '';

  const reader = new FileReader();
  reader.onload = () => {
    const pre = document.createElement('pre');
    pre.textContent = reader.result;
    contentDiv.appendChild(pre);
  };
  reader.readAsText(file);

  overlay.style.display = 'flex';
}

function closeOverlay() {
  const overlay = document.getElementById('overlay');
  const contentDiv = document.getElementById('bookContent');
  contentDiv.innerHTML = '';
  overlay.style.display = 'none';
}

</script>




</body>
</html>
