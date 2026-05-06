

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
<title>Text Editor</title>
</head>		
<style>
		
body {
  font-weight: bold;
  margin: 0;
}
	
#textArea {
  width: 100%;
  height: 900px;
  padding: 10px;
  font-size: 16px;
  line-height: 1.5;
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
	
	
	
<button class="demo w3-opacity w3-hover-opacity-off button" id="saveBtn">Save</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="loadBtn">Load</button>	  
<button class="demo w3-opacity w3-hover-opacity-off button" id="specificLoadBtn">Upload</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="specificSaveBtn">Download</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="clearBtn">Clear</button>
<textarea id="textArea"></textarea>



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
  // Get the text area element
  const textArea = document.getElementById("textArea");

  // Sanitize text content by escaping potentially harmful characters
  function sanitizeText(text) {
    const element = document.createElement("div");
    if (text) {
      element.innerText = text; // Create a safe copy of the text
      return element.innerHTML; // Return sanitized version
    }
    return "";
  }

  // Save the text to local storage
  function saveText() {
    const sanitizedText = sanitizeText(textArea.value);
    localStorage.setItem("text", sanitizedText);
  }

  // Load the text from local storage
  function loadText() {
    const savedText = localStorage.getItem("text");
    if (savedText !== null) {
      textArea.value = savedText;
    }
  }

  // Clear the text
  function clearText() {
    textArea.value = "";
  }

  // Save the text to a specific location
  function specificSaveText() {
    const sanitizedText = sanitizeText(textArea.value); // Sanitize text before saving
    const file = new Blob([sanitizedText], {type: "text/plain"});
    const a = document.createElement("a");
    const url = URL.createObjectURL(file);
    a.href = url;
    a.download = "document.txt";
    document.body.appendChild(a);
    a.click();
    setTimeout(function() {
      document.body.removeChild(a);
      window.URL.revokeObjectURL(url);  
    }, 0);
  }

  // Load the text from a specific location
  function specificLoadText() {
    const input = document.createElement("input");
    input.type = "file";
    input.accept = "text/plain";
    input.onchange = function(event) {
      const file = event.target.files[0];

      // Ensure only text files are processed
      if (file && file.type === "text/plain") {
        const reader = new FileReader();
        reader.readAsText(file);
        reader.onload = function() {
          const sanitizedText = sanitizeText(reader.result); // Sanitize the loaded content
          textArea.value = sanitizedText;
        }
      } else {
        alert("Please upload a valid text file.");
      }
    };
    input.click();
  }

  // Attach event listeners to buttons
  const saveBtn = document.getElementById("saveBtn");
  saveBtn.addEventListener("click", saveText);

  const loadBtn = document.getElementById("loadBtn");
  loadBtn.addEventListener("click", loadText);

  const clearBtn = document.getElementById("clearBtn");
  clearBtn.addEventListener("click", clearText);

  const specificSaveBtn = document.getElementById("specificSaveBtn");
  specificSaveBtn.addEventListener("click", specificSaveText);

  const specificLoadBtn = document.getElementById("specificLoadBtn");
  specificLoadBtn.addEventListener("click", specificLoadText);
</script>
	
	
	



	
		
</body>
</html>