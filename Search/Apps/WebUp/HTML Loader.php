

<!doctype html>

<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../../CSS/w3.css" rel="stylesheet" type="text/css" />	
<link href="../../../CSS/fonts.css" rel="stylesheet" type="text/css" />	

<link href="../../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>HTML Loader</title>
</head>		
<style>
		
body {
  font-weight: bold;
  margin: 0;
}
	
/* Input Styles */
#linkInput {
  width: 80%;
  padding: 8px;
}

/* Button Styles */
#loadButton {
  background-color:transparent;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
}
	
/* Button Styles */
#reloadButton {
  background-color: transparent;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
}
	
/* styles.css */
#resultIframe {
  width: 1500px;
  height: 780px;
  background-color: black;
  border: none;
  top: 0;
  left: 0;
}



						
</style>

	
<body>
	
	
<button class="demo w3-opacity w3-hover-opacity-off button" id="loadButton" onclick="loadHtmlFile()">Upload</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="reloadButton" onclick="reloadHtmlFile()">Reload</button>
<iframe id="resultIframe"></iframe>

<!-- Include DOMPurify via CDN -->
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
  function loadHtmlFile() {
    var input = document.createElement('input');
    input.type = 'file';
    input.accept = '.html'; // Allow HTML files only

    input.onchange = function(event) {
      var file = event.target.files[0];

      if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
          var htmlContent = e.target.result;

          // Use DOMPurify to sanitize the HTML content
          var sanitizedContent = DOMPurify.sanitize(htmlContent);

          // Load the sanitized HTML into the iframe
          document.getElementById('resultIframe').srcdoc = sanitizedContent;
        };

        reader.readAsText(file);
      }
    };

    input.click();
  }

  function reloadHtmlFile() {
    var iframe = document.getElementById('resultIframe');
    iframe.srcdoc = iframe.srcdoc; // Reload the HTML content inside the iframe
  }
</script>

	



	
	

	



				
</body>
</html>