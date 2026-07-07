<?php
/**
 * security.php – Portable, USB‑optimized security module
 * 
 * Requires PROJECT_ROOT (defined in config.php) to be set.
 * Include this file at the very top of every script, before any output.
 */

/* ------------------------------------------------------------------ */
/* 1. Browser behaviour & layout headers                              */
/* ------------------------------------------------------------------ */

header("X-Content-Type-Options: nosniff");

// CSP header removed – it was blocking external stylesheets (e.g. W3.CSS CDN).
// If you later host all assets locally, you can re-enable a strict CSP.
// header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline';");

/* ------------------------------------------------------------------ */
/* 2. Safe output for HTML (XSS prevention)                           */
/* ------------------------------------------------------------------ */
function safe_out($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/* ------------------------------------------------------------------ */
/* 3. Portable Path Protection                                        */
/*    Uses PROJECT_ROOT so it works wherever your USB or XAMPP lives   */
/* ------------------------------------------------------------------ */
function is_safe_path($requested_path) {
    static $base = null;
    if ($base === null) {
        if (!defined('PROJECT_ROOT')) {
            die('Security: PROJECT_ROOT constant is not defined. Include config.php first.');
        }
        $base = realpath(PROJECT_ROOT);
        if ($base === false) {
            die('Security: cannot determine base directory from PROJECT_ROOT.');
        }
    }

    // Reject null byte injection
    if (strpos($requested_path, "\0") !== false) {
        return false;
    }
    // Reject obvious directory traversal sequences
    if (strpos($requested_path, '..') !== false) {
        return false;
    }
    // Reject absolute paths (Unix, Windows drive letter, UNC network paths)
    if (preg_match('#^(/|[a-zA-Z]:[\\\\/]|\\\\\\\\)#', $requested_path)) {
        return false;
    }

    // Build the full path and resolve it completely.
    // This follows symlinks and normalizes everything.
    $full = realpath($base . DIRECTORY_SEPARATOR . $requested_path);

    // If the path doesn't exist or resolution failed, refuse access.
    if ($full === false) {
        return false;
    }

    // Confirm the resolved path is still inside the base directory.
    if (strpos($full, $base) === 0 && strlen($full) > strlen($base)) {
        return true;
    }

    return false;
}

/* ------------------------------------------------------------------ */
/* 4. Filename sanitization for path construction                     */
/*    Strips only dangerous characters – keeps spaces, unicode, etc.   */
/* ------------------------------------------------------------------ */
function sanitize_filename($filename) {
    $dangerous = array('/', '\\', "\0", ':', '*', '?', '"', '<', '>', '|');
    return str_replace($dangerous, '', $filename);
}