<?php

/**
 * Generate a CSRF token and store it in the session
 */
function generateCsrfToken()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate the CSRF token from a form submission
 */
function validateCsrfToken($token)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output a hidden CSRF token field for use in forms
 */
function csrfField()
{
    $token = generateCsrfToken();
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Verify CSRF token on POST requests and die if invalid
 */
function verifyCsrf()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if (!validateCsrfToken($token)) {
            http_response_code(403);
            die('Invalid or missing CSRF token. Please go back and try again.');
        }
    }
}

/**
 * Set secure session settings
 */
function secureSession()
{
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Strict');
}

/**
 * Check if session has timed out (30 minute timeout)
 */
function checkSessionTimeout()
{
    $timeout = 1800; // 30 minutes in seconds
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > $timeout) {
            session_unset();
            session_destroy();
            header('Location: /vent-then-validate/public/main_login.php?timeout=true');
            exit();
        }
    }
    $_SESSION['last_activity'] = time();
}

/**
 * Add Content Security Policy header
 */
function setSecurityHeaders()
{
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: strict-origin-when-cross-origin");
}
