<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../security.php';
?>



<?php
// ------------------- Recursively scan all directories and their media files -------------------
$allFolders = [];
$baseDir = __DIR__;
$videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'flv', 'wmv', 'm4v'];
$audioExtensions = ['mp3', 'wav', 'flac', 'aac', 'wma'];

$dirIterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($dirIterator as $item) {
    if (!$item->isDir()) continue;
    $dirPath = $item->getRealPath();
    if ($dirPath === $baseDir) continue;

    $relativeDir = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $dirPath);
    $relativeDir = str_replace('\\', '/', $relativeDir);
    $folderName = basename($dirPath);

    $mediaFiles = [];
    $fileIterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($fileIterator as $file) {
        if (!$file->isFile()) continue;
        $ext = strtolower($file->getExtension());
        $isVideo = in_array($ext, $videoExtensions);
        $isAudio = in_array($ext, $audioExtensions);
        if ($isVideo || $isAudio) {
            $relPath = str_replace($baseDir . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $relPath = str_replace('\\', '/', $relPath);
            $mediaFiles[] = [
                'name' => $file->getFilename(),
                'path' => $relPath,
                'type' => $isVideo ? 'video' : 'audio'
            ];
        }
    }

    $allFolders[] = [
        'name'   => $folderName,
        'path'   => $relativeDir,
        'media'  => $mediaFiles
    ];
}

$foldersJson = json_encode($allFolders, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
?>


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
    <title>Nova Video – All Files</title>
    
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

        .thumbnail-video {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-bottom: 1px solid #111;
            pointer-events: none;
        }

        .audio-placeholder {
            width: 100%;
            height: 160px;
            background: #1a1a2e;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #111;
        }

        .audio-placeholder svg {
            width: 50px;
            height: 50px;
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

        video {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 12px;
            box-shadow: 0 0 30px #00bcd4cc;
        }

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
            video {
                height: 300px;
            }
        }

        .close-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
            background: #00bcd4;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 700;
            font-size: 1rem;
        }
    </style>
</head>
<body>


  <h2><?php echo pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME); ?></h2>
    
    <main>
        <section id="searchPanel">
            <input type="text" id="searchQuery" placeholder="Search by folder name..." autocomplete="off" />
            <button onclick="filterMedia()">Search</button>
            <div id="loadingMessage"></div>
        </section>

        <section id="resultsContainer">
            <div class="results" id="results"></div>
        </section>
    </main>

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
        const allFolders = <?php echo $foldersJson; ?>;

        // Holds the currently playing playlist and position (for autoplay)
        let currentPlaylist = [];
        let currentIndex = -1;

        /**
         * Filters folders by name, sorts media naturally, and displays them.
         */
        function filterMedia() {
            const query = document.getElementById('searchQuery').value.trim().toLowerCase();
            if (query === '') {
                displayMedia([]);
                return;
            }

            let mediaToShow = [];
            allFolders.forEach(folder => {
                if (folder.name.toLowerCase().includes(query)) {
                    mediaToShow = mediaToShow.concat(folder.media);
                }
            });

            displayMedia(mediaToShow);
        }

        /**
         * Renders media array after natural sorting by filename.
         */
        function displayMedia(mediaArray) {
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = '';

            // Natural sort: 1,2,10 instead of 1,10,2
            mediaArray.sort((a, b) =>
                a.name.localeCompare(b.name, undefined, { numeric: true, sensitivity: 'base' })
            );

            if (mediaArray.length === 0) {
                resultsDiv.innerHTML = '<p style="color:#eee;">No matching videos found.</p>';
                return;
            }

            mediaArray.forEach((video, idx) => {
                const videoDiv = document.createElement('div');
                videoDiv.className = 'video';

                let thumbnailHTML = '';
                if (video.type === 'video') {
                    thumbnailHTML = `<video src="${encodeURI(video.path)}" class="thumbnail-video" muted preload="metadata"></video>`;
                } else {
                    thumbnailHTML = `
                        <div class="audio-placeholder">
                            <svg viewBox="0 0 24 24" fill="#00bcd4">
                                <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                            </svg>
                        </div>`;
                }

                const titleDiv = document.createElement('div');
                titleDiv.className = 'title';
                titleDiv.textContent = video.name.replace(/\.[^/.]+$/, '');

                const button = document.createElement('button');
                button.textContent = 'Watch';
                button.onclick = function() {
                    // Pass the sorted media array and index to start autoplay chain
                    openVideo(mediaArray, idx);
                };

                const contentDiv = document.createElement('div');
                contentDiv.className = 'video-content';
                contentDiv.innerHTML = thumbnailHTML;
                contentDiv.appendChild(titleDiv);
                contentDiv.appendChild(button);

                videoDiv.appendChild(contentDiv);
                resultsDiv.appendChild(videoDiv);
            });
        }

        /**
         * Opens a video player and sets up autoplay through the playlist.
         * @param {Array} playlist - Sorted media array
         * @param {number} index - Starting index
         */
        function openVideo(playlist, index) {
            currentPlaylist = playlist;
            currentIndex = index;

            const resultsContainer = document.getElementById('resultsContainer');
            resultsContainer.innerHTML = '';
            resultsContainer.style.position = 'relative';

            const closeBtn = document.createElement('button');
            closeBtn.textContent = 'X';
            closeBtn.className = 'close-btn';
            closeBtn.onclick = function() {
                // Reset playlist, clear container, restore search results
                currentPlaylist = [];
                currentIndex = -1;
                resultsContainer.innerHTML = '';
                const resultsDiv = document.createElement('div');
                resultsDiv.className = 'results';
                resultsDiv.id = 'results';
                resultsContainer.appendChild(resultsDiv);
                filterMedia(); // re-apply current search input
            };

            const video = document.createElement('video');
            video.src = encodeURI(playlist[index].path);
            video.controls = true;
            video.autoplay = true;
            video.style.width = '100%';
            video.style.height = '100%';

            // Autoplay next video when current ends
            video.addEventListener('ended', playNext);

            resultsContainer.appendChild(closeBtn);
            resultsContainer.appendChild(video);
        }

        /**
         * Advances to the next media in the current playlist.
         */
        function playNext() {
            if (currentPlaylist.length === 0) return;

            currentIndex++;
            if (currentIndex >= currentPlaylist.length) {
                // End of playlist – close the player
                const closeBtn = document.querySelector('.close-btn');
                if (closeBtn) closeBtn.click();
                return;
            }

            const video = document.querySelector('#resultsContainer video');
            if (video) {
                video.src = encodeURI(currentPlaylist[currentIndex].path);
                video.load();
                video.play();
            }
        }
    </script>

    
</body>
</html>