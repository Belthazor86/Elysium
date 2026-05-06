


<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Nova Video</title>
</head>	
<style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      overflow: auto;
    }

    main {
      flex-grow: 1;
      display: flex;
      padding: 20px 40px;
      gap: 30px;
      background: #121212;
    }

    /* Left panel for search */
    #searchPanel {
      width: 320px;
      background: #1f1f1f;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 0 15px #00aaff99;
      display: flex;
      flex-direction: column;
      align-items: stretch;
    }

    #searchPanel input[type="text"] {
      padding: 12px 15px;
      font-size: 1rem;
      border-radius: 8px;
      border: none;
      background: #2a2a2a;
      color: #eee;
      outline: none;
      margin-bottom: 15px;
      transition: background 0.3s ease;
    }

    #searchPanel input[type="text"]:focus {
      background: #404040;
    }

    #searchPanel button {
      background: #00bcd4;
      color: white;
      font-weight: 700;
      padding: 12px;
      font-size: 1rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    #searchPanel button:hover {
      background-color: #0097a7;
    }

    #loadingMessage {
      margin-top: 15px;
      font-size: 1rem;
      color: #ffb84d;
      min-height: 20px;
      text-align: center;
    }

    /* Right panel for results and player */
    #resultsContainer {
      flex-grow: 1;
      background: #1c1c1c;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 0 25px #00bcd499;
      display: flex;
      flex-direction: column;
      gap: 20px;
      overflow-y: auto;
      max-height: 80vh;
    }

    .results {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }

    .video {
      background: #282828;
      border-radius: 10px;
      overflow: hidden;
      cursor: pointer;
      box-shadow: 0 0 12px #0097a7cc;
      display: flex;
      flex-direction: column;
      transition: transform 0.3s ease;
    }

    .video:hover {
      transform: scale(1.05);
      box-shadow: 0 0 18px #00bcd4cc;
    }

    .video-content img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-bottom: 1px solid #111;
    }

    .title {
      padding: 10px 15px;
      font-size: 1rem;
      color: #cce7ff;
      flex-grow: 1;
      font-weight: 600;
      line-height: 1.2;
      text-align: left;
    }

    .video-content button {
      background: #00bcd4;
      border: none;
      color: white;
      padding: 10px 0;
      font-weight: 700;
      font-size: 1rem;
      cursor: pointer;
      border-radius: 0 0 10px 10px;
      transition: background-color 0.3s ease;
      width: 100%;
    }

    .video-content button:hover {
      background-color: #0097a7;
    }

    iframe {
      width: 1200px;
      height: 600px;
      border: none;
      border-radius: 12px;
      box-shadow: 0 0 30px #00bcd4cc;
    }

    /* Scrollbar styling */
    #resultsContainer::-webkit-scrollbar {
      width: 10px;
    }
    #resultsContainer::-webkit-scrollbar-track {
      background: #121212;
    }
    #resultsContainer::-webkit-scrollbar-thumb {
      background-color: #00bcd4;
      border-radius: 10px;
    }

    /* Responsive */
    @media (max-width: 900px) {
      main {
        flex-direction: column;
        padding: 20px;
      }
      #searchPanel {
        width: 100%;
        margin-bottom: 20px;
      }
      #resultsContainer {
        max-height: none;
      }
      iframe {
        height: 300px;
      }
    }


</style>
	


<body>
	
	
<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
	
	
  <main>
    <section id="searchPanel">
      <input type="text" id="searchQuery" placeholder="Search..." autocomplete="off" />
      <button onclick="searchVideos()">Search</button>
      <div id="loadingMessage"></div>
    </section>

    <section id="resultsContainer">
      <div class="results" id="results"></div>
    </section>
  </main>



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
    function searchVideos() {
      const query = document.getElementById('searchQuery').value.trim();
      if (!query) return;

      const loadingMessage = document.getElementById('loadingMessage');
      loadingMessage.textContent = 'Loading...';

      const url = `https://archive.org/advancedsearch.php?q=${encodeURIComponent(query)}+AND+(mediatype:(audio%20OR%20movies))&fl[]=identifier,title,mediatype,filesize&rows=50&page=1&output=json`;

      fetch(url)
        .then(response => response.json())
        .then(data => {
          const resultsDiv = document.getElementById('results');
          resultsDiv.innerHTML = '';
          loadingMessage.textContent = '';

          if (!data.response.docs || data.response.docs.length === 0) {
            resultsDiv.innerHTML = '<p>No results found.</p>';
            return;
          }

          data.response.docs.forEach(video => {
            const videoDiv = document.createElement('div');
            videoDiv.className = 'video';

            videoDiv.innerHTML = `
              <div class="video-content">
                <img src="https://archive.org/services/img/${video.identifier}?width=320&height=180" alt="Thumbnail" />
                <div class="title">${video.title}</div>
                <button onclick="openVideo('${video.identifier}')">Watch/Listen</button>
              </div>
            `;

            resultsDiv.appendChild(videoDiv);
          });
        })
        .catch(error => {
          console.error('Error fetching videos:', error);
          const loadingMessage = document.getElementById('loadingMessage');
          loadingMessage.textContent = 'Error loading results.';
        });
    }

    function openVideo(identifier) {
      const resultsDiv = document.getElementById('results');
      resultsDiv.innerHTML = ''; // Clear previous results

      const iframe = document.createElement('iframe');
      iframe.src = `https://archive.org/embed/${identifier}`;
      iframe.allowFullscreen = true;

      resultsDiv.appendChild(iframe);
    }
  </script>





	

</body>
</html>
