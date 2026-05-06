


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
<title>Flash Player</title>
<style>
body {
  font-weight: bold;
  margin: 0;
}	
	
#flashplayer {
    width: 100%;
    max-width: 1500px;
    height: 85vh;
    margin: 0 auto;
    position: relative;
    background-color: #000000;
    border: none;
    outline: none;
    overflow:scroll;
}

#flashplayer object {
    width: 100% !important;
    height: 100% !important;
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

<button class="demo w3-opacity w3-hover-opacity-off button" id="browse">Load</button>
<input type="file" hidden id="file-input" accept=".swf">
<div id="flashplayer">
    <object width="1500" height="720"></object>
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


<script type="text/javascript">
    const fileInput = document.getElementById('file-input');
    const browseButton = document.getElementById('browse');
    const flashPlayer = document.getElementById('flashplayer');

    function resizeFlashGame() {
        const flashObject = flashPlayer.querySelector('object');
        if (flashObject) {
            flashObject.style.width = '100%';
            flashObject.style.height = '100%';
        }
    }

    browseButton.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', () => {
        const file = fileInput.files[0];
        if (file && file.name.toLowerCase().endsWith('.swf')) {
            const reader = new FileReader();
            reader.onload = () => {
                flashPlayer.innerHTML = `
                    <object type="application/x-shockwave-flash" data="${reader.result}">
                        <param name="movie" value="${reader.result}">
                        <param name="allowFullScreen" value="true">
                        <param name="wmode" value="transparent">
                        <p>Your browser does not support Flash Player.</p>
                    </object>`;
                resizeFlashGame();
            };
            reader.readAsDataURL(file);
        } else {
            alert("Please select a valid SWF file.");
        }
    });

    window.addEventListener('resize', resizeFlashGame);
    document.addEventListener('fullscreenchange', resizeFlashGame);
</script>



<script src="https://unpkg.com/@ruffle-rs/ruffle"></script>



</body>
</html>
