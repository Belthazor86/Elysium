


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
<title>Multi Notepad</title>
</head>	
<style>
		
body {
  font-weight: bold;
  margin: 0;
}
		
#file-input {
  margin: 20px 0;
 }

 #editor {
  width: 100%;
  height: 900px;
  border: 1px solid #ccc;
  padding: 10px;
  font-family: sans-serif;
  background-color: black;
  color: white;
  border: none;
  outline: none; /* Remove the outline on focus */
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
	
	
		
<button class="demo w3-opacity w3-hover-opacity-off button" for="file-input" id="upload-button">Upload</button>
<input type="file" id="file-input" hidden multiple>
<button class="demo w3-opacity w3-hover-opacity-off button" id="save-button">Save</button>
<textarea id="editor" placeholder="Select and edit a text file..." readonly></textarea>
<button class="video-slider-btn left-side" id="prev-button">❮</button>
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
    const fileInput = document.getElementById('file-input');
    const editor = document.getElementById('editor');
    const prevButton = document.getElementById('prev-button');
    const nextButton = document.getElementById('next-button');
    const saveButton = document.getElementById('save-button');
    let currentIndex = 0;
    let files = null;
    let editedText = '';

    const uploadButton = document.getElementById('upload-button');
    uploadButton.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', function (event) {
        files = event.target.files;

        if (files.length === 0) {
            editor.value = "No file selected.";
            editor.setAttribute('readonly', 'readonly');
            saveButton.setAttribute('disabled', 'disabled');
        } else {
            currentIndex = 0;
            loadFile(currentIndex);
            editedText = editor.value;
            if (files.length > 1) {
                prevButton.removeAttribute('disabled');
                nextButton.removeAttribute('disabled');
            }
            saveButton.removeAttribute('disabled');
        }
    });

    prevButton.addEventListener('click', function () {
        currentIndex = (currentIndex - 1 + files.length) % files.length;
        loadFile(currentIndex);
    });

    nextButton.addEventListener('click', function () {
        currentIndex = (currentIndex + 1) % files.length;
        loadFile(currentIndex);
    });

    saveButton.addEventListener('click', function () {
        try {
            const sanitizedText = sanitizeText(editedText);
            const blob = new Blob([sanitizedText], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = sanitizeFilename(files[currentIndex].name);
            a.click();
            window.URL.revokeObjectURL(url);
        } catch (error) {
            alert("Error saving file: " + error.message);
        }
    });

    editor.addEventListener('input', function () {
        editedText = sanitizeText(editor.value);
    });

    function loadFile(index) {
        const reader = new FileReader();

        reader.onload = function (e) {
            editor.value = sanitizeText(e.target.result);
            editor.removeAttribute('readonly');
            editedText = editor.value;
        };

        reader.readAsText(files[index]);

        if (files.length > 1) {
            prevButton.removeAttribute('disabled');
            nextButton.removeAttribute('disabled');
        }
    }

    function sanitizeText(text) {
        // Prevent potential script injection by escaping special characters
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    function sanitizeFilename(filename) {
        // Replace invalid filename characters with underscores
        return filename.replace(/[^a-zA-Z0-9.-]/g, '_');
    }
</script>

	
	

	


		
</body>
</html>