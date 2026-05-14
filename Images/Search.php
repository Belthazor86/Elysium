<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link href="../CSS/w3.css" rel="stylesheet" type="text/css" />
<link href="../CSS/scroll.css" rel="stylesheet" type="text/css" />
<title>Search</title>
<style>
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow: hidden;
        background-color: black;
    }
            
    #artContainer {
        width: 100%;
        height: 100vh; 
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    #albumArt {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        cursor: pointer;
    }
</style>    
</head>

<body>
        
<?php
$folder = 'Search/';
$images = [];

if (is_dir($folder)) {
    $files = array_diff(scandir($folder), array('..', '.'));
    foreach ($files as $file) {
        if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $file)) {
            $images[] = $file;
        }
    }
    // Shuffle once on page load to create a random "playlist" order
    shuffle($images);
}
?>
    
<div id="artContainer">
    <img id="albumArt" alt="albumArt">
</div>
    
<script>
    // Pass the shuffled array to JS
    const images = <?php echo json_encode($images); ?>;
    const folderPath = '<?php echo $folder; ?>';
    let currentIndex = 0;

    function showNextImage() {
        if (images.length === 0) return;

        // If we reached the end of the shuffled list, reset to 0
        if (currentIndex >= images.length) {
            currentIndex = 0; 
        }

        const imgElement = document.getElementById('albumArt');
        imgElement.src = folderPath + images[currentIndex];
        
        // Move to the next index for the next call
        currentIndex++;
    }

    // Load the first image immediately
    showNextImage();

    // Change image every 30 seconds
    setInterval(showNextImage, 30000);

    // Manual skip on click 
    // document.getElementById('albumArt').addEventListener('click', showNextImage);
</script>

</body>
</html>