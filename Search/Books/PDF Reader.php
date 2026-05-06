


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
<title>PDF Reader</title>
</head>	
<style>
		
body {
  font-weight: bold;
  margin: 0;
}
	
#pdf-viewer {
 width: 100%;
 height: 100vh;
 }
																											
/* Button Styles */
.button {
  background-color: transparent;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
  padding: 10px 20px;
}
	
	

				
</style>
	
	
<body>
	
	
		
	

<button class="demo w3-opacity w3-hover-opacity-off button" id="import-button">Upload</button>
<div id="pdf-viewer"></div>
<button class="video-slider-btn left-side" id="previous-button">❮</button>
<button class="video-slider-btn right-side" id="next-button">❯</button>	


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
        var currentPDFIndex = 0; // Track the currently displayed PDF index

        // Array to store file URLs of selected PDFs
        var fileURLs = [];

        document.getElementById('import-button').addEventListener('click', function() {
            var input = document.createElement('input');
            input.type = 'file';
            input.accept = '.pdf';
            input.multiple = true;
            input.addEventListener('change', function(e) {
                fileURLs = []; // Reset the array when new files are selected

                for (var i = 0; i < e.target.files.length; i++) {
                    var file = e.target.files[i];
                    var fileURL = URL.createObjectURL(file);
                    fileURLs.push(fileURL);
                }

                displayPDF(currentPDFIndex); // Display the first PDF
            });

            input.click();
        });

        function displayPDF(index) {
            var pdfViewer = document.getElementById('pdf-viewer');
            pdfViewer.innerHTML = '';

            if (fileURLs.length > 0) {
                var embed = document.createElement('embed');
                embed.src = fileURLs[index] + '#toolbar=0&navpanes=0';
                embed.type = 'application/pdf';
                embed.width = '100%';
                embed.height = '100%';
                pdfViewer.appendChild(embed);
            }
        }

        document.getElementById('previous-button').addEventListener('click', function() {
            if (fileURLs.length > 0) {
                currentPDFIndex = (currentPDFIndex - 1 + fileURLs.length) % fileURLs.length;
                displayPDF(currentPDFIndex);
            }
        });

        document.getElementById('next-button').addEventListener('click', function() {
            if (fileURLs.length > 0) {
                currentPDFIndex = (currentPDFIndex + 1) % fileURLs.length;
                displayPDF(currentPDFIndex);
            }
        });
    </script>
	
	



	
		
</body>
</html>