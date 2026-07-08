<?php
// /updates.php - Complete standalone file

function get_app_updates() {
    $results = [];
    
    // Optional: Add GitHub token for higher rate limits (60 vs 5000 requests/hour)
    $githubToken = getenv('GITHUB_TOKEN') ?: null;
    
    // 1. Find the main folder dynamically by looking for .json manifests
    foreach (glob(__DIR__ . '/*', GLOB_ONLYDIR) as $mainDir) {
        if (!empty(glob($mainDir . '/*/*.json'))) {
            
            // 2. Loop through every web app folder
            foreach (glob($mainDir . '/*', GLOB_ONLYDIR) as $appFolder) {
                $jsonFiles = glob($appFolder . '/*.json');
                
                if (empty($jsonFiles)) {
                    continue;
                }
                
                // Read and validate JSON
                $jsonContent = file_get_contents($jsonFiles[0]);
                if ($jsonContent === false) {
                    error_log("Failed to read: " . $jsonFiles[0]);
                    continue;
                }
                
                $data = json_decode($jsonContent, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    error_log("Invalid JSON in: " . $jsonFiles[0]);
                    continue;
                }
                
                // Validate required fields
                if (!isset($data['github_repo'], $data['name'], $data['version'])) {
                    error_log("Missing required fields in: " . $jsonFiles[0]);
                    continue;
                }
                
                $repo = $data['github_repo'];
                
                // 3. Check GitHub API for latest release
                $contextOptions = [
                    'http' => [
                        'header' => [
                            "User-Agent: WebAppPlatform",
                            "Accept: application/vnd.github.v3+json"
                        ]
                    ]
                ];
                
                // Add authorization header if token exists
                if ($githubToken) {
                    $contextOptions['http']['header'][] = "Authorization: token {$githubToken}";
                }
                
                // Only disable SSL in development
                if (getenv('APP_ENV') === 'development') {
                    $contextOptions['ssl'] = [
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    ];
                }
                
                $context = stream_context_create($contextOptions);
                $res = @file_get_contents(
                    "https://api.github.com/repos/{$repo}/releases/latest",
                    false,
                    $context
                );
                
                // Check for API errors
                if ($res === false) {
                    error_log("Failed to fetch GitHub API for: {$repo}");
                    $results[] = [
                        'name' => $data['name'],
                        'version' => $data['version'],
                        'status' => 'error',
                        'message' => 'Could not check for updates'
                    ];
                    continue;
                }
                
                // Check HTTP response headers for rate limiting
                $httpCode = null;
                if (isset($http_response_header)) {
                    preg_match('#HTTP/\d+\.\d+ (\d+)#', $http_response_header[0], $matches);
                    $httpCode = isset($matches[1]) ? (int)$matches[1] : null;
                }
                
                if ($httpCode === 403 || $httpCode === 429) {
                    $results[] = [
                        'name' => $data['name'],
                        'version' => $data['version'],
                        'status' => 'error',
                        'message' => 'GitHub API rate limit exceeded'
                    ];
                    continue;
                }
                
                $github = json_decode($res, true);
                
                if (json_last_error() !== JSON_ERROR_NONE || !isset($github['tag_name'])) {
                    $results[] = [
                        'name' => $data['name'],
                        'version' => $data['version'],
                        'status' => 'error',
                        'message' => 'Invalid response from GitHub'
                    ];
                    continue;
                }
                
                $latest = ltrim($github['tag_name'], 'v');
                
                // 4. Compare versions
                if (version_compare($data['version'], $latest, '<')) {
                    $results[] = [
                        'name' => $data['name'],
                        'version' => $data['version'],
                        'latest' => $latest,
                        'url' => $github['zipball_url'] ?? $github['html_url'],
                        'status' => 'need_update'
                    ];
                } else {
                    $results[] = [
                        'name' => $data['name'],
                        'version' => $data['version'],
                        'status' => 'ok'
                    ];
                }
            }
            break;
        }
    }
    
    return $results;
}

$apps = get_app_updates();

// Cache the results to avoid hitting API limits on every page load
// Consider adding file-based caching: cache results for 1 hour
?>
<!DOCTYPE html>
<html>
<head>
    <title>Updates</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .status-ok { color: green; }
        .status-need_update { color: orange; }
        .status-error { color: red; }
    </style>
</head>
<body>
    <h2>Application Updates</h2>
    <?php if (empty($apps)): ?>
        <p>No applications found.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($apps as $app): ?>
                <li>
                    <strong><?php echo htmlspecialchars($app['name']); ?></strong>
                    (v<?php echo htmlspecialchars($app['version']); ?>) - 
                    <?php if ($app['status'] === 'need_update'): ?>
                        <span class="status-need_update">
                            <a href="<?php echo htmlspecialchars($app['url']); ?>">
                                Update Available (v<?php echo htmlspecialchars($app['latest']); ?>) - Download ZIP
                            </a>
                        </span>
                    <?php elseif ($app['status'] === 'error'): ?>
                        <span class="status-error">
                            Error: <?php echo htmlspecialchars($app['message']); ?>
                        </span>
                    <?php else: ?>
                        <span class="status-ok">Up to date</span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>