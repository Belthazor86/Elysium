


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
<title>Galaxy Prime</title>
</head>	
<style>
    body {
      margin: 0;
      padding: 20px;
      text-align: center;
      overflow: scroll;
    }

    input {
      padding: 10px;
      width: 280px;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      background-color: #1e1e1e;
      color: #ffffff;
      outline: none;
    }

    button {
      padding: 10px 20px;
      font-size: 16px;
      margin-left: 10px;
      border: none;
      border-radius: 5px;
      background-color: #00bcd4;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #0097a7;
    }

    .results {
      margin-top: 30px;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
    }

    .video {
      background-color: #1e1e1e;
      border-radius: 8px;
      overflow: hidden;
      width: 320px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
      transition: transform 0.3s;
      cursor: pointer;
    }

    .video:hover {
      transform: scale(1.03);
    }

    iframe {
      width: 100%;
      height: 180px;
      border: none;
    }

    .title {
      padding: 10px;
      font-size: 15px;
      color: #f0f0f0;
      text-align: left;
    }

		
</style>
	


<body>
	
	
<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
	
	

  <input type="text" id="searchQuery" placeholder="Search..." autocomplete="off">
  <button onclick="searchVideos()">Search</button>

  <div class="results" id="results"></div>


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

      const url = `https://api.dailymotion.com/videos?search=${encodeURIComponent(query)}&limit=50&fields=title,id`;


      fetch(url)
        .then(response => response.json())
        .then(data => {
          const resultsDiv = document.getElementById('results');
          resultsDiv.innerHTML = '';

          if (!data.list || data.list.length === 0) {
            resultsDiv.innerHTML = '<p>No results found.</p>';
            return;
          }

          data.list.forEach(video => {
            const videoDiv = document.createElement('div');
            videoDiv.className = 'video';
            
            // Create clickable container for each video
            videoDiv.innerHTML = `
              <div class="video-content">
                <img src="https://www.dailymotion.com/thumbnail/video/${video.id}" width="320" height="180">
                <div class="title">${video.title}</div>
                <button onclick="openVideo('${video.id}')">Watch Video</button>
              </div>
            `;

            // Append to the results container
            resultsDiv.appendChild(videoDiv);
          });
        })
        .catch(error => {
          console.error('Error fetching videos:', error);
        });
    }

function openVideo(videoId) {
  const videoPlayer = document.createElement('iframe');
  videoPlayer.src = `https://www.dailymotion.com/embed/video/${videoId}`;
  videoPlayer.setAttribute('allowfullscreen', 'true');  // Explicit full-screen support
  videoPlayer.setAttribute('webkitallowfullscreen', 'true');  // Add webkit compatibility
  videoPlayer.setAttribute('mozallowfullscreen', 'true');  // Add moz compatibility
  videoPlayer.style.width = '100%';
  videoPlayer.style.height = '360px';
  videoPlayer.style.border = 'none';
  
  const resultsDiv = document.getElementById('results');
  resultsDiv.innerHTML = ''; // Clear previous results
  resultsDiv.appendChild(videoPlayer); // Add video player
}

</script>




	

	

</body>
</html>
