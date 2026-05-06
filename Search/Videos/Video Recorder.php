

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
<title>Video Recorder</title>
</head>	
<style>
		
body {
  font-weight: bold;
  margin: 0;
}
	
#video-player {
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

#video-input {
  margin-top: 20px;
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
	

	
		

<input type="file" id="video-input" style="display: none" multiple accept="video/webm">
<button class="demo w3-opacity w3-hover-opacity-off button" id="upload-button" onclick="document.getElementById('video-input').click()">Upload</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="record-button">Record</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="stop-button">Stop</button>
<button class="demo w3-opacity w3-hover-opacity-off button" id="save-button" disabled>Save</button>
<video id="video-player" controls autoplay></video>


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
document.addEventListener('DOMContentLoaded', function() {
  const videoInput = document.getElementById('video-input');
  const recordButton = document.getElementById('record-button');
  const stopButton = document.getElementById('stop-button');
  const saveButton = document.getElementById('save-button');
  const videoPlayer = document.getElementById('video-player');
  let mediaRecorder;
  let chunks = [];

  // Event listener for file input change with file type validation for WebM files
  videoInput.addEventListener('change', function(e) {
    const files = e.target.files;
    if (files.length > 0) {
      const file = files[0];
      // Check if the file is a valid WebM video
      if (file.type === 'video/webm') {
        const videoUrl = URL.createObjectURL(file);
        videoPlayer.src = videoUrl;
      } else {
        alert('Please upload a valid WebM video file.');
      }
    }
  });

  // Event listener for record button click with permission check
  recordButton.addEventListener('click', function() {
    navigator.mediaDevices.getUserMedia({ video: true, audio: true })
      .then(function(stream) {
        mediaRecorder = new MediaRecorder(stream);
        mediaRecorder.start();

        mediaRecorder.addEventListener('dataavailable', function(e) {
          chunks.push(e.data);
        });
      })
      .catch(function(error) {
        alert('Permission denied for video and audio recording.');
      });
  });

  // Event listener for stop button click
  stopButton.addEventListener('click', function() {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
      mediaRecorder.stop();
      mediaRecorder.addEventListener('stop', function() {
        const blob = new Blob(chunks, { type: 'video/webm' });
        chunks = [];

        const videoUrl = URL.createObjectURL(blob);
        videoPlayer.src = videoUrl;

        saveButton.disabled = false;
        saveButton.addEventListener('click', function() {
          const a = document.createElement('a');
          document.body.appendChild(a);
          a.style.display = 'none';
          a.href = videoUrl;
          a.download = 'recorded_video.webm';
          a.click();
          window.URL.revokeObjectURL(videoUrl);  // Ensure URL is revoked after download
          document.body.removeChild(a);
        });
      });
    }
  });
});
</script>





		
</body>
</html>
