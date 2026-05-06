

<!doctype html>
<html lang="en"><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/style.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Gutendex</title>
</head>	
<style>
		
body {
  margin: 0;
  padding: 40px 20px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

input, select {
  padding: 12px 16px;
  width: 300px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  background-color: #1e1e1e;
  color: #fff;
  margin-right: 10px;
  outline: none;
  transition: box-shadow 0.3s;
}

input:focus, select:focus {
  box-shadow: 0 0 0 2px #00bfff;
}

button {
  padding: 12px 20px;
  font-size: 16px;
  background-color: #00bfff;
  color: #121212;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #0095cc;
}

#results {
  margin-top: 30px;
  width: 90%;
  max-width: 800px;
  overflow:scroll;
}

.book {
  background-color: #1e1e1e;
  padding: 16px;
  margin-bottom: 15px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.3);
  transition: transform 0.2s;
}

.book:hover {
  transform: translateY(-4px);
}

.book-title {
  font-size: 18px;
  font-weight: bold;
  color: #00bfff;
}

.book-author {
  color: #aaa;
  margin-bottom: 10px;
}

.read-button {
  text-decoration: none;
  color: #ffffff;
  background-color: #00bfff;
  padding: 8px 12px;
  border-radius: 6px;
  transition: background-color 0.3s;
  display: inline-block;
  cursor: pointer;
}

.read-button:hover {
  background-color: #0095cc;
}

.overlay {
  height: 0%;
  width: 100%;
  position: absolute;
  z-index: 2;
  top: 0;
  left: 0;
  background-color: lightgrey;
  overflow-y: hidden;
  transition: 0.5s;
}

.overlay a {
  padding: 8px;
  text-decoration: none;
  font-size: 20px;
  color: whitesmoke;
  display: block;
  transition: 0.3s;
}

.overlay .closebtn {
  position: absolute;
  top: 50px;
  right: 8px;
  font-weight: bold;
  font-size: 1.2rem;
}

@media screen and (max-height: 450px) {
  .overlay { overflow-y: auto; }
  .overlay a { font-size: 25px }
  .overlay .closebtn {
    font-size: 30px;
    top: 15px;
    right: 35px;
  }
}
		
</style>
	


<body>
	
	
	
	
 <h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>

  <div>
    <input type="text" id="search" placeholder="Search..." autocomplete="off" />
    <button onclick="searchBooks()">Search</button>
  </div>

  <div id="results"></div>


  <div id="myPDF" class="overlay">
  <iframe id="contentFrame" frameborder="0"></iframe>
  <button class="video-slider-btn closebtn" onclick="closePDF()">❌</button>
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
    async function searchBooks() {
      const query = document.getElementById("search").value.trim();
      if (!query) return;

      const response = await fetch(`https://gutendex.com/books?search=${encodeURIComponent(query)}`);
      const data = await response.json();

      const results = document.getElementById("results");
      results.innerHTML = "";

      data.results.forEach(book => {
        const title = book.title;
        const author = book.authors.length > 0 ? book.authors[0].name : "Unknown Author";
        const link = book.formats["text/html; charset=utf-8"] || book.formats["text/html"] || book.formats["text/plain"];

        if (link) {
          const bookDiv = document.createElement("div");
          bookDiv.classList.add("book");

          bookDiv.innerHTML = `
            <div class="book-title">${title}</div>
            <div class="book-author">by ${author}</div>
            <span class="read-button" onclick="openPDF('${link}')">Read Book</span>
          `;

          results.appendChild(bookDiv);
        }
      });

      if (data.results.length === 0) {
        results.innerHTML = "<p>No results found.</p>";
      }
    }

    function openOverlay(link) {
      fetch(link)
        .then(response => response.text())
        .then(text => {
          document.getElementById("bookContent").innerHTML = `<pre>${text}</pre>`;
          document.getElementById("overlay").style.height = "100%";
        });
    }

    function closeOverlay() {
      document.getElementById("overlay").style.height = "0%";
    }

    function openPDF(link) {
      const pdfLink = link.replace("text/plain", "pdf");
      document.getElementById("contentFrame").src = pdfLink;
      document.getElementById("myPDF").style.height = "100%";
    }

    function closePDF() {
      document.getElementById("myPDF").style.height = "0%";
    }
  </script>







	

</body>
</html>
