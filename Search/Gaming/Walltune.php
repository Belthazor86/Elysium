

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Walltune</title>
</head>	
<style>

* {
  box-sizing: border-box;
}

body {
  margin: 0;
  padding: 20px;
  text-align: center;
}

button {
  background: #111;
  border: 2px solid #00ffcc;
  color: #00ffcc;
  padding: 12px 28px;
  font-size: 18px;
  letter-spacing: 1px;
  margin: 10px;
  cursor: pointer;
  transition: 0.3s ease;
  box-shadow: 0 0 10px #00ffcc33;
}

button:hover {
  background: #00ffcc;
  color: #000;
  box-shadow: 0 0 20px #00ffcc88;
}

#image {
  margin-top: 30px;
  max-width: 90%;
  max-height: 60vh;
  border: 4px solid #00ffcc;
  box-shadow: 0 0 20px #00ffcc55;
  border-radius: 10px;
  cursor: pointer;
}
	

									
</style>

	
<body>




<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>


<input type="file" id="folderInput" webkitdirectory directory multiple hidden>
<button onclick="document.getElementById('folderInput').click()">Load Folder</button>

<div id="viewer" style="display:none">
  <img id="image" src="" title="Click to toggle fullscreen">
  <audio id="audio" hidden></audio>
  <div class="controls">
    <button onclick="prev()">⏮</button>
    <button onclick="togglePlayPause()" id="playPauseBtn">⏸</button>
    <button onclick="next()">⏭</button>
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
  const folderInput = document.getElementById('folderInput');
  const imgEl = document.getElementById('image');
  const audioEl = document.getElementById('audio');
  const viewer = document.getElementById('viewer');
  const playPauseBtn = document.getElementById('playPauseBtn');

  let mediaPairs = [];
  let current = 0;

  folderInput.addEventListener('change', () => {
    const files = [...folderInput.files];
    const images = {};
    const audios = {};

    files.forEach(file => {
      // Extract folder path relative to selected folder
      const pathParts = file.webkitRelativePath.split('/');
      const name = file.name.replace(/\.[^/.]+$/, '');
      const folderKey = pathParts.length > 1 ? pathParts[pathParts.length - 2] : '';
      const key = folderKey ? `${folderKey}/${name}` : name;

      if (file.name.match(/\.(jpg|jpeg|png)$/i)) {
        images[key] = file;
      } else if (file.name.match(/\.mp3$/i)) {
        audios[key] = file;
      }
    });

    mediaPairs = Object.keys(images)
      .filter(name => audios[name])
      .sort((a, b) => {
        const numA = parseInt(a.match(/\d+/)?.[0] || a);
        const numB = parseInt(b.match(/\d+/)?.[0] || b);
        return numA - numB;
      })
      .map(name => ({
        name,
        img: images[name],
        audio: audios[name]
      }));

    if (mediaPairs.length > 0) {
      current = 0;
      viewer.style.display = 'block';
      load(current);
    } else {
      alert("⚠ No matching wallpaper + music files found.");
    }
  });

  function load(index) {
    const pair = mediaPairs[index];
    imgEl.src = URL.createObjectURL(pair.img);
    audioEl.src = URL.createObjectURL(pair.audio);
    audioEl.play();
    playPauseBtn.textContent = "⏸";
    audioEl.onended = next;
  }

  function next() {
    current = (current + 1) % mediaPairs.length;
    load(current);
  }

  function prev() {
    current = (current - 1 + mediaPairs.length) % mediaPairs.length;
    load(current);
  }

  function togglePlayPause() {
    if (audioEl.paused) {
      audioEl.play();
      playPauseBtn.textContent = "⏸";
    } else {
      audioEl.pause();
      playPauseBtn.textContent = "▶";
    }
  }

  imgEl.addEventListener('click', () => {
    if (!document.fullscreenElement) {
      if (imgEl.requestFullscreen) imgEl.requestFullscreen();
      else if (imgEl.webkitRequestFullscreen) imgEl.webkitRequestFullscreen();
      else if (imgEl.msRequestFullscreen) imgEl.msRequestFullscreen();
    } else {
      if (document.exitFullscreen) document.exitFullscreen();
      else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
      else if (document.msExitFullscreen) document.msExitFullscreen();
    }
  });
</script>

	

	

	
	
</body>
</html>