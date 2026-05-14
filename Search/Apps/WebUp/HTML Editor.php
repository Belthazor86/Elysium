


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
<title>HTML Editor</title>
</head>	
<style>
		
body {
  font-weight: bold;
  margin: 0;
}
	
#editor {
 width: 1500px;
 height: 780px;
 border: none;
 outline: none; /* Remove the outline on focus */
 padding: 10px;
 overflow: auto;
 font-weight: normal;
 font-size: 20px;
 
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
	
	

		
<button class="demo w3-opacity w3-hover-opacity-off button" id="uploadButton">Upload</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="saveButton">Save</button>
<input type="file" id="fileInput" hidden accept=".html">
<div id="editor" contenteditable="true"></div>



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
    document.getElementById('uploadButton').addEventListener('click', function() {
        document.getElementById('fileInput').click();
    });

    document.getElementById('fileInput').addEventListener('change', handleFile);
    document.getElementById('saveButton').addEventListener('click', saveAsHTML);

    function handleFile(event) {
        const fileInput = event.target;
        const file = fileInput.files[0];

        // Validate file type (only allow .html files)
        if (file && file.type === 'text/html') {
            const reader = new FileReader();

            reader.onload = function (e) {
                const content = e.target.result;
                document.getElementById('editor').innerHTML = content;  // Use innerHTML to allow HTML content to be loaded
            };

            reader.readAsText(file);
        } else {
            alert("Please upload a valid HTML file.");
        }
    }

    function saveAsHTML() {
        const content = document.getElementById('editor').innerHTML;  // Save the editable content as HTML
        const blob = new Blob([content], { type: 'text/html' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'edited_file.html';
        a.click();
    }
</script>

	
	
	

	

	
</body>
</html>
