<?php
// /updates.php - Complete standalone file

function get_app_updates() {
    $results = [];
    
    // 1. Find the main folder dynamically by looking for .json manifests inside its subfolders
    foreach (glob(__DIR__ . '/*', GLOB_ONLYDIR) as $mainDir) {
        if (!empty(glob($mainDir . '/*/*.json'))) {
            
            // 2. Loop through every web app folder found inside it
            foreach (glob($mainDir . '/*', GLOB_ONLYDIR) as $appFolder) {
                $jsonFiles = glob($appFolder . '/*.json');
                
                if (!empty($jsonFiles)) {
                    $data = json_decode(file_get_contents($jsonFiles[0]), true);
                    $repo = $data['github_repo'];

                    // 3. Check GitHub API for the latest release
                    $opts = ['http' => ['header' => "User-Agent: WebAppPlatform\r\n"]];
                    $res = @file_get_contents("https://api.github.com/repos/{$repo}/releases/latest", false, stream_context_create($opts));

                    if ($res) {
                        $github = json_decode($res, true);
                        $latest = ltrim($github['tag_name'], 'v');

                        // 4. Compare local version against GitHub release tag
                        if (version_compare($data['version'], $latest, '<')) {
                            $results[] = [
                                'name' => $data['name'], 
                                'version' => $data['version'], 
                                'latest' => $latest, 
                                'url' => $github['zipball_url'], 
                                'status' => 'need_update'
                            ];
                            continue;
                        }
                    }
                    $results[] = ['name' => $data['name'], 'version' => $data['version'], 'status' => 'ok'];
                }
            }
            break; 
        }
    }
    return $results;
}

$apps = get_app_updates();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Updates</title>
</head>
<body>
    <h2>Application Updates</h2>
    <ul>
        <?php foreach ($apps as $app): ?>
            <li>
                <strong><?php echo $app['name']; ?></strong> (v<?php echo $app['version']; ?>) - 
                <?php if ($app['status'] === 'need_update'): ?>
                    <a href="<?php echo $app['url']; ?>">Update Available (v<?php echo $app['latest']; ?>) - Download ZIP</a>
                <?php else: ?>
                    <span>Up to date</span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>