<?php
// Scan the "video" folder for files
$videoDirectory = 'Multi Video Player/';
$videoFiles = [];

if (is_dir($videoDirectory)) {
    $files = scandir($videoDirectory);
    foreach ($files as $file) {
        // Filter for video extensions
        if (preg_match('/\.(mp4|webm|ogg|flv)$/i', $file)) {
            $videoFiles[] = $videoDirectory . $file;
        }
    }
}
?>

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
<title>Multi Video Player</title>
</head> 
<style>
body {
  font-weight: bold;
  margin: 0;
}           
.container {
    text-align: center;
    width: 100%;
    max-width: 100%;
}
#videoContainer {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    margin-top: 20px;
}
.video-wrapper {
    width: 300px;
    display: flex;
    justify-content: center;
}
video {
    width: 100%;
    height: auto;
    border: 2px solid #ccc;
    border-radius: 8px;
    background: #000;
}

button {
    background: linear-gradient(135deg, #0d47a1, #1976d2); 
    color: whitesmoke;
    border: none;
    padding: 16px 32px;
    cursor: pointer;
    border-radius: 12px;
    margin: 10px;
    font-size: 1.2em;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    text-transform: capitalize;
}
button:hover {
    background: linear-gradient(135deg, #1976d2, #42a5f5);
    transform: scale(1.12);
    box-shadow: 0 6px 20px rgba(0,0,0,0.7);
}




</style>

<body>





<h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>



<div style="width:100%; display:flex; justify-content:center; margin-top:20px;">
<button id="uploadButton" class="demo w3-opacity w3-hover-opacity-off button">Load</button>
<button id="scanButton" class="demo w3-opacity w3-hover-opacity-off button">Scan</button>
<input type="file" id="videoUpload" accept="video/*" multiple style="display: none;">
</div>


<div class="container">
    <div id="videoContainer"></div>
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
    const videoContainer = document.getElementById('videoContainer');
    const serverVideos = <?php echo json_encode($videoFiles); ?>;

    // Function to create and append video elements
    function addVideoToContainer(sourceUrl) {
        const videoWrapper = document.createElement('div');
        videoWrapper.classList.add('video-wrapper');

        const videoPlayer = document.createElement('video');
        videoPlayer.controls = true;
        videoPlayer.src = sourceUrl;

        videoWrapper.appendChild(videoPlayer);
        videoContainer.appendChild(videoWrapper);
    }

    // Trigger the file input when the Upload button is clicked
    document.getElementById('uploadButton').addEventListener('click', function () {
        document.getElementById('videoUpload').click();
    });

    // Handle the Video scan button
    document.getElementById('scanButton').addEventListener('click', function () {
        videoContainer.innerHTML = ''; // Clear existing players
        if (serverVideos.length > 0) {
            serverVideos.forEach(path => {
                addVideoToContainer(path);
            });
        } else {
            alert("No videos found in the 'video' folder.");
        }
    });

    // Handle manual file selection
    document.getElementById('videoUpload').addEventListener('change', function (event) {
        videoContainer.innerHTML = ''; // Clear existing players
        const maxFileSizeMB = 100; 

        Array.from(event.target.files).forEach((file) => {
            if (!file.type.startsWith('video/')) {
                alert('Only video files are allowed!');
                return;
            }

            if (file.size > maxFileSizeMB * 1024 * 1024) {
                alert(`File size should not exceed ${maxFileSizeMB} MB`);
                return;
            }

            addVideoToContainer(URL.createObjectURL(file));
        });
    });
</script>
                
</body>
</html>