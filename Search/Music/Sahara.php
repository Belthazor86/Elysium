

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/fonts.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/style.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/scroll.css" rel="stylesheet" type="text/css" />
<link href="../../CSS/footer.css" rel="stylesheet" type="text/css" />
<title>Sahara</title>
</head>	
<style>
	
    body {
      margin: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      overflow: scroll;
    }
	
    .logo {
      margin-bottom: 40px;
      width: 250px;
      height: auto;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.4);
      transition: transform 0.3s ease;
    }

    .logo:hover {
      transform: scale(1.1);
    }

    .player-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 0 40px rgba(255, 255, 255, 0.5);
    }

    select, .play-pause-btn {
      margin-top: 20px;
      padding: 10px 20px;
      font-size: 1.2em;
      border-radius: 30px;
      border: none;
      cursor: pointer;
    }

    select {
      background: #111;
      color: #fff;
    }

    .play-pause-btn {
      background: #ff1e00;
      color: white;
      transition: background 0.3s, transform 0.2s;
    }

    .play-pause-btn:hover {
      background: #e61400;
      transform: scale(1.1);
    }

    audio {
      width: 100%;
      max-width: 450px;
      background-color: #111;
      border: none;
      border-radius: 15px;
      margin-top: 20px;
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
    }

    .info-text {
      margin-top: 25px;
      font-size: 1.1em;
      color: rgba(255, 255, 255, 0.8);
    }
		
</style>
	


<body>
	
		
  <div class="player-container">
<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
  <img src="Sahara/Sahara.jpg" alt="Radio Logo" class="logo" id="radioLogo"/>

  <select id="stationSelector" onchange="changeStation()">
  <option data-logo="Covers/Sahara.jpg">Select a Station</option>
  <option value="http://media-ice.musicradio.com/CapitalMP3" data-logo="Sahara/Capital.png">Capital FM</option>
  <option value="http://media-ice.musicradio.com/HeartUKMP3" data-logo="Sahara/Heart.png">Heart FM</option>
  <option value="http://media-ice.musicradio.com/ClassicFMMP3" data-logo="Sahara/Classic FM.png">Classic FM</option>
  <option value="http://media-ice.musicradio.com/SmoothUKMP3" data-logo="Sahara/smooth.png">Smooth Radio</option>
  <option value="http://media-ice.musicradio.com/LBCUKMP3" data-logo="Sahara/LBC.png">LBC</option>
  <option value="http://media-ice.musicradio.com/RadioXUKMP3" data-logo="Sahara/X.png">Radio X</option>
  </select>
	  
	  
    <audio id="player">
      <source src="http://media-ice.musicradio.com/CapitalMP3" type="audio/mp3" />
      Your browser does not support the audio element.
    </audio>
    <button class="play-pause-btn" onclick="togglePlay()" aria-pressed="false">Play</button>
    <p class="info-text">Select a station and press Play to enjoy the music!</p>
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
    const player = document.getElementById('player');
    const button = document.querySelector('.play-pause-btn');
    const selector = document.getElementById('stationSelector');
    const logo = document.getElementById('radioLogo');

    function togglePlay() {
      if (player.paused) {
        player.play();
        button.textContent = "Pause";
        button.setAttribute('aria-pressed', 'true');
      } else {
        player.pause();
        button.textContent = "Play";
        button.setAttribute('aria-pressed', 'false');
      }
    }

    function changeStation() {
      const selected = selector.options[selector.selectedIndex];
      const newStream = selected.value;
      const newLogo = selected.getAttribute('data-logo');

      player.pause();
      player.src = newStream;
      player.load();
      player.play();

      logo.src = newLogo;
      button.textContent = "Pause";
      button.setAttribute('aria-pressed', 'true');
    }
  </script>	
	




	

</body>
</html>
