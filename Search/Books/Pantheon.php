


<!doctype html>
<html lang="en"><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Pantheon</title>
</head>	
<style>
    body {
      margin: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
      overflow-x: hidden;
      overflow-y: scroll;
    }

    .search-container {
      display: flex;
      margin: 1rem;
      max-width: 700px;
      width: 100%;
      padding: 0 1rem;
      flex-wrap: wrap;
      gap: 0.5rem;
    }

    input[type="text"] {
      flex: 1 1 100%;
      padding: 1rem;
      font-size: 1.1rem;
      background: #111;
      color: #fff;
      border: 1px solid #333;
      border-radius: 10px;
    }

    .buttons-row {
      display: flex;
      justify-content: center;
      gap: 1rem;
      width: 100%;
      margin-top: 0.5rem;
    }

    button {
      padding: 1rem 1rem;
      font-size: 1.1rem;
      background: linear-gradient(to right, #0af, #09f);
      border: none;
      color: #000;
      font-weight: bold;
      border-radius: 0 10px 10px 0;
      cursor: pointer;
      align-items: center;
    }

    button:hover {
      background: linear-gradient(to right, #09f, #0af);
    }

    .results {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      padding: 2rem;
      width: 100%;
      max-width: 1200px;
    }

    .card {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid #222;
      border-radius: 15px;
      overflow: hidden;
      text-align: center;
      backdrop-filter: blur(6px);
      transition: transform 0.3s, box-shadow 0.3s;
      cursor: pointer;
    }

    .card:hover {
      transform: scale(1.04);
      box-shadow: 0 0 20px #0af;
    }

    .card img {
      width: 100%;
      height: 150px;
      object-fit: cover;
    }

    .card h3 {
      padding: 1rem;
      font-size: 1rem;
      color: #eee;
    }

    .modal {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.9);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      flex-direction: column;
    }

    .modal iframe {
      width: 90%;
      height: 80%;
      border: none;
      box-shadow: 0 0 30px #09f;
      border-radius: 12px;
    }

    .modal .close-btn {
      margin-top: 1rem;
      background: #09f;
      border: none;
      color: #000;
      font-weight: bold;
      padding: 0.6rem 1.5rem;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1rem;
    }

    .no-results {
      font-size: 1.2rem;
      margin-top: 1rem;
      color: #777;
      text-align: center;
    }
	
	
		
</style>
	


<body>
	
	
	
	
  <h2>Pantheon</h2>



  <div class="search-container">
    <input type="text" id="searchInput" placeholder="Search..." autocomplete="off">
    <div class="buttons-row">
      <button onclick="searchWithCategory('guides')">Game Guides & Manuals</button>
      <button onclick="searchWithCategory('comics')">Comics & Graphic Novels</button>
      <button onclick="searchWithCategory('books')">Books & Novels</button>
    </div>
  </div>

  <div id="results" class="results"></div>
  <div id="noResults" class="no-results" style="display: none;">No results found.</div>

  <!-- Modal -->
  <div id="gameModal" class="modal">
    <iframe id="viewer" allowfullscreen></iframe>
    <button class="close-btn" onclick="closeModal()">❌</button>
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
    let currentCategory = null;

    async function searchWithCategory(category) {
      currentCategory = category;
      const queryInput = document.getElementById('searchInput');
      const keyword = queryInput.value.trim();
      await performSearch(keyword, category);
    }

    async function performSearch(keyword, category) {
      const resultsDiv = document.getElementById('results');
      const noResults = document.getElementById('noResults');
      const viewer = document.getElementById('viewer');
      const modal = document.getElementById('gameModal');

      resultsDiv.innerHTML = '';
      noResults.style.display = 'none';
      modal.style.display = 'none';
      viewer.src = '';

      if (!keyword) {
        noResults.style.display = 'block';
        noResults.textContent = 'Please enter keywords to search.';
        return;
      }

      let query = '';
      let mediatype = '';
      switch(category) {
        case 'guides':
          // Game guides and manuals
          query = `${keyword} (title:"game guide" OR title:"game manual" OR subject:"game guide" OR subject:"game manual")`;
          mediatype = 'texts';
          break;
        case 'comics':
          // Comics & graphic novels
          query = `${keyword} (collection:"comics" OR subject:"comics" OR subject:"graphic novel")`;
          mediatype = 'texts';
          break;
        case 'books':
          // Books & novels
          query = `${keyword} (mediatype:books OR subject:"novel" OR subject:"book" OR collection:"opensource")`;
          mediatype = 'texts';
          break;
        default:
          // fallback: generic search
          query = keyword;
          mediatype = '';
      }

      // Compose API URL with proper encoding and mediatype filter
      let apiUrl = `https://archive.org/advancedsearch.php?q=${encodeURIComponent(query)}`;
      if(mediatype) {
        apiUrl += `+AND+mediatype:${mediatype}`;
      }
      apiUrl += '&fl[]=identifier&fl[]=title&rows=50&page=1&output=json';

      try {
        const response = await fetch(apiUrl);
        const data = await response.json();
        const docs = data.response.docs;

        if (!docs.length) {
          noResults.style.display = 'block';
          noResults.textContent = 'No results found.';
          return;
        }

        docs.forEach(item => {
          const card = document.createElement('div');
          card.className = 'card';
          card.innerHTML = `
            <img src="https://archive.org/services/img/${item.identifier}" alt="${item.title}" />
            <h3>${item.title}</h3>
          `;
          card.onclick = () => {
            document.getElementById('viewer').src = `https://archive.org/embed/${item.identifier}?autoplay=1`;
            document.getElementById('gameModal').style.display = 'flex';
          };
          resultsDiv.appendChild(card);
        });
      } catch (err) {
        noResults.style.display = 'block';
        noResults.textContent = 'Something went wrong.';
        console.error(err);
      }
    }

    function closeModal() {
      const modal = document.getElementById('gameModal');
      const viewer = document.getElementById('viewer');
      viewer.src = '';
      modal.style.display = 'none';
    }
  </script>





	

</body>
</html>