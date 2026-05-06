


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
<title>Guilds</title>
</head>
<style>
			
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    display: flex;
    flex-direction: column; /* This forces vertical stacking */
    min-height: 100vh; 
    justify-content: center;
    align-items: center;   
    font-size: 18px;  /* Ensures the page takes up the full screen height */
    margin: 0;
}

.form-container {
    background-color: #222;
    padding: 30px;
    border-radius: 15px;
    width: 100%;
    max-width: 900px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.6);
    flex: 1;  
}

label {
    display: block;
    margin: 10px 0 5px;
    font-weight: bold;
    text-align: left;
}

input, 
textarea, 
select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    background-color: #333;
    border: 1px solid #444;
    border-radius: 5px;
    color: white;
    font-size: 14px;
}

input[type="checkbox"] {
    width: auto;
}

.checkbox-group label {
    display: inline-block;
    margin-right: 20px;
}

textarea {
    height: 80px;
    resize: vertical;
}

button {
    width: 100%;
    padding: 12px;
    background-color: darkslateblue;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #45a049;
}

.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.form-row .form-group {
    flex: 1;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
    }

    .form-row .form-group {
        flex: none;
    }
}
	
								
</style>
	
	

<body>

	
<div class="form-container" id="form-container">
<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
    <form onsubmit="return false;">
        <div class="form-row">
            <div class="form-group">
                <label for="brand">CPU</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="cpu[]" value="Intel">Intel</label>
                    <label><input type="checkbox" name="cpu[]" value="AMD">AMD</label>
                </div>
            </div>
            <div class="form-group">
                <label for="model">GPU</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="gpu[]" value="Nvidia">Nvidia</label>
                    <label><input type="checkbox" name="gpu[]" value="AMD">AMD</label>
                </div> 
            </div>
        <div class="form-group">
                <label for="brand">Hard Drives</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="storage[]" value="HDD">HDD</label>
                    <label><input type="checkbox" name="storage[]" value="SSD">SSD</label>
                    <label><input type="checkbox" name="storage[]" value="NVMe">NVMe</label>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="model">Memory</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="ram[]" value="8">8GB</label>
                    <label><input type="checkbox" name="ram[]" value="16">16GB</label>
                    <label><input type="checkbox" name="ram[]" value="32">32GB</label>
                </div> 
            </div>
          <div class="form-group">
                <label for="brand">Computers</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="pc[]" value="Desktop">Desktop</label>
                    <label><input type="checkbox" name="pc[]" value="Laptop">Laptop</label>
                    <label><input type="checkbox" name="pc[]" value="Mini">Mini PC</label>
                </div>
            </div>
            <div class="form-group">
                <label for="model">Graphics Card</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="card[]" value="Intergrated">Intergrated</label>
                    <label><input type="checkbox" name="card[]" value="Dedicated">Dedicated</label>
                </div> 
            </div>

        </div>
		
        <div class="form-row">
        </div>
        <div class="form-group">
            <label for="additional">Additional Information:</label>
            <textarea id="additional" name="additional" required></textarea>
        </div>
		
        <div style="display: flex; gap: 10px;">
            <button type="button" onclick="searchGaming()">Build</button>
        </div>
    </form>
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
    function searchGaming() {
        const form = document.querySelector('form');
        const formData = new FormData(form);

        let keywords = [];

        // Gather selected checkbox values
        ['cpu[]', 'gpu[]', 'storage[]', 'ram[]', 'pc[]', 'card[]'].forEach(field => {
            formData.getAll(field).forEach(value => keywords.push(value));
        });

        // Include additional info if provided
        const additional = formData.get("additional");
        if (additional.trim()) {
            keywords.push(additional.trim());
        }

        // Prevent empty search
        if (keywords.length === 0) {
            alert("Please select at least one option or enter additional information.");
            return;
        }

        // Build the Amazon-focused search query
        const query = encodeURIComponent(`site:amazon.com ${keywords.join(" ")}`);
        const searchUrl = `https://www.google.com/search?q=${query}`;
        window.open(searchUrl, '_blank');

        // Clear all checkboxes and textarea
        form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        form.querySelector('textarea').value = '';
    }
</script>




	 		
</body>
</html>