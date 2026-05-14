

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
<title>Multi HTML Loader</title>
</head>	
<style>
	
body {
  font-weight: bold;
  margin: 0;
}
	
#viewer {
 width: 1500px;
 height: 780px;
 background-color: black;
 border: none;
 outline: none; /* Remove the outline on focus */
 }
 
  /* Navigation */
 .video-slider-btn {
    border: none;
    display: inline-block;
    color: grey;
    font-size: 50px;
    padding: 10px;
    vertical-align: middle;
    overflow: hidden;
    text-decoration: none;
    background-color: transparent;
    text-align: center;
    cursor: pointer;
    white-space: nowrap;
    z-index: 99999;
    opacity: .7;
    transition: all 350ms ease-in-out;
  }
	
 .video-slider-btn:hover {
    opacity: 1;
    transition: all 350ms ease-in-out;
  }
	
 .video-slider-btn.left-side {
    position: absolute;
    top: 50%;
    left: 0%;
    transform: translate(0%,-50%);
    -ms-transform: translate(-0%,-50%);
  }
	
 .video-slider-btn.right-side {
    position: absolute;
    top: 50%;
    right: 0%;
    transform: translate(0%,-50%);
    -ms-transform: translate(0%,-50%);
}

																				
.button {
  background-color: transparent;
  border: none;
  border-radius: 5px;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
  padding: 10px 20px;
}
	
</style>
	
	
	
<body>
	
	

	
	
<button class="demo w3-opacity w3-hover-opacity-off button" onclick="triggerFileInput()">Upload</button>
<button class="demo w3-opacity w3-hover-opacity-off button" onclick="loadPages(0)">Load</button>
<input type="file" id="files" multiple hidden accept=".html">
<iframe id="viewer"></iframe>
<button class="video-slider-btn left-side" onclick="changePage(-1)">❮</button>
<button class="video-slider-btn right-side" onclick="changePage(1)">❯</button>

<!-- Include DOMPurify via CDN for sanitization -->
<script src="https://cdn.jsdelivr.net/npm/dompurify@2.3.8/dist/purify.min.js"></script>



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
    var currentFileIndex = 0;

    // Function to load a page from the file input
    function loadPages(index) {
        var files = document.getElementById("files").files;
        var iframe = document.getElementById("viewer");
        iframe.contentWindow.document.open();

        currentFileIndex = index;
        if (currentFileIndex < 0) currentFileIndex = 0;
        if (currentFileIndex >= files.length) currentFileIndex = files.length - 1;

        var reader = new FileReader();
        reader.onload = function (e) {
            var contents = e.target.result;

            // Sanitize the HTML content to avoid security issues
            var sanitizedContents = DOMPurify.sanitize(contents);

            // Write the sanitized content to the iframe
            iframe.contentWindow.document.write(sanitizedContents);
        };
        reader.readAsText(files[currentFileIndex]);

        iframe.contentWindow.document.close();
    }

    // Function to navigate between pages
    function changePage(direction) {
        loadPages(currentFileIndex + direction);
    }

    // Trigger file input for selecting files
    function triggerFileInput() {
        document.getElementById("files").click();
    }

</script>



		
</body>
</html>
