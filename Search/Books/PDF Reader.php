


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
																											
button {
    background: linear-gradient(135deg, #0d47a1, #1976d2); 
    color: whitesmoke;
    border: none;
    padding: 16px 32px;
    cursor: pointer;
    border-radius: 12px;
    margin: 10px;
    font-size: 1.2em;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    text-transform: capitalize;
}
button:hover {
    background: linear-gradient(135deg, #1976d2, #42a5f5);
    transform: scale(1.12);
    box-shadow: 0 6px 20px rgba(0,0,0,0.7);
}	
					
</style>
	
	
<body>
	
	
		
<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

<!-- CENTER CONTAINER -->
<div style="width:100%; display:flex; justify-content:center; margin-top:20px;">
<button class="demo w3-opacity w3-hover-opacity-off button" id="import-button">Load</button>
</div>


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