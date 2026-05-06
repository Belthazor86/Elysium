


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
<title>Obsidian</title>
</head>
<style>


body {
    display: flex;
    flex-direction: column; /* This forces vertical stacking */
    min-height: 100vh;      /* Ensures the page takes up the full screen height */
    margin: 0;
}

.container {
    flex: 1;                /* This "pushes" the footer down by taking up available space */
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    background-color: transparent;
    padding: 40px;
    margin: 0 auto;         /* Centers the container horizontally */
    width: 90%;
    max-width: 800px;
}

/* Keep your existing input and button styles below */

input[type="text"] {
  width: 90%;
  padding: 12px;
  font-size: 16px;
  border: none;
  border-radius: 8px;
  margin-bottom: 20px;
  outline: none; /* Removes blue outline on focus */
}

.buttons {
  display: flex;
  flex-wrap: wrap; /* Allows wrapping on smaller screens */
  justify-content: center;
  gap: 10px; /* Space between buttons */
}

.buttons button {
  background-color: #222;
  color: whitesmoke;
  border: 3px solid darkblue;
  padding: 10px 20px;
  font-size: 14px;
  font-weight: bold;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s; /* Smooth hover animation */
}

.buttons button:hover {
  background-color: darkblue;
  color: whitesmoke;
}



</style>





<body>
	
	
	

  <div class="container">
   <h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
    <input type="text" id="query" placeholder="Search..." autocomplete="off">

    <div class="buttons">
      <button onclick="search('documents')">Documents</button>
      <button onclick="search('music')">Music</button>
      <button onclick="search('games')">Games</button>
      <button onclick="search('software')">Software</button>
      <button onclick="search('videos')">Videos</button>
    </div>
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
    function search(type) {
      const userQuery = document.getElementById('query').value.trim();

      if (!userQuery) {
        alert("Please enter a search keyword.");
        return;
      }

      let dork = '';

      switch(type) {
        case 'documents':
          dork = `intitle:"index of" +(pdf|doc|docx|ppt|xls) ${userQuery}`;
          break;
        case 'music':
          dork = `intitle:"index of" +(mp3|flac|aac|wav) ${userQuery}`;
          break;
        case 'videos':
          dork = `intitle:"index of" +(mp4|mkv|avi|mov) ${userQuery}`;
          break;
        case 'games':
          dork = `intitle:"index of" +(iso|exe|zip|rar) ${userQuery}`;
          break;
        case 'software':
          dork = `intitle:"index of" +(exe|zip|msi|rar) ${userQuery}`;
          break;
        default:
          dork = userQuery;
      }

      const encodedQuery = encodeURIComponent(dork);
      const searchUrl = `https://www.google.com/search?q=${encodedQuery}`;
      window.open(searchUrl, '_blank');
    }
  </script>
	
	


		
	
	
</body>
</html>
