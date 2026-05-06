


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
<title>Comic PDF Reader</title>	
</head>
<style>
		
body {
  font-weight: bold;
  margin: 0;
}
	
#comic-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center; /* center horizontally */
  align-items: center; /* center vertically */
  margin-top: 20px; /* add some margin from the button */
}
		
/* Button Styles */
button {
  background-color: transparent;
  border: none;
  color: #ffffff;
  cursor: pointer;
  font-size: 20px;
}
	

					
</style>
	

	
<body>
	
	
<button id="import-button" class="demo w3-opacity w3-hover-opacity-off button">Load</button> 
<div id="comic-container"></div>



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
const importButton = document.getElementById("import-button");
const comicContainer = document.getElementById("comic-container");

importButton.addEventListener("click", () => {
  const fileInput = document.createElement("input");
  fileInput.type = "file";
  fileInput.accept = "application/pdf";
  fileInput.addEventListener("change", () => {
    const file = fileInput.files[0];
    if (!file) return;

    // Security: Check file type and size
    if (file.type !== "application/pdf") {
      alert("Please upload a valid PDF file.");
      return;
    }
    if (file.size > 100 * 1024 * 1024) { // 100MB size limit
      alert("The file is too large. Please upload a file smaller than 100MB.");
      return;
    }

    importButton.disabled = true;
    importButton.textContent = "Importing...";

    const reader = new FileReader();
    reader.onload = () => {
      const pdfData = new Uint8Array(reader.result);
      pdfjsLib.getDocument({ data: pdfData }).promise.then(pdf => {
        const totalPages = pdf.numPages;
        comicContainer.innerHTML = ''; // Clear previous content

        const targetWidth = 600; // Target width for regular PDF size rendering

        for (let i = 1; i <= totalPages; i++) {
          pdf.getPage(i).then(page => {
            const viewport = page.getViewport({ scale: 1 }); // Get the actual page size
            const pageWidth = viewport.width;
            const pageHeight = viewport.height;

            // Determine scale to adjust size (too large or too small)
            let scale = 1;
            if (pageWidth > targetWidth) {
              scale = targetWidth / pageWidth; // Reduce size if it's too large
            } else if (pageWidth < targetWidth) {
              scale = targetWidth / pageWidth; // Increase size if it's too small
            }

            const adjustedViewport = page.getViewport({ scale: scale });
            const canvas = document.createElement("canvas");
            const context = canvas.getContext("2d");
            canvas.width = adjustedViewport.width;
            canvas.height = adjustedViewport.height;
            comicContainer.appendChild(canvas);

            page.render({
              canvasContext: context,
              viewport: adjustedViewport
            });
          }).catch(err => {
            console.error("Error rendering page:", err);
            alert("An error occurred while rendering the PDF.");
          });
        }

        importButton.disabled = false;
        importButton.textContent = "Import PDF";
      }).catch(err => {
        console.error("Error loading PDF:", err);
        alert("An error occurred while loading the PDF.");
        importButton.disabled = false;
        importButton.textContent = "Import PDF";
      });
    };
    reader.readAsArrayBuffer(file);
  });
  fileInput.click();
});
</script>

		
	

	
	

<script src="PDF Reader/build/pdf.js"></script>	


	
		
</body>
</html>