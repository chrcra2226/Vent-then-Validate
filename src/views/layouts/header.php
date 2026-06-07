<?php
if (!function_exists('secureSession')) {
    require_once __DIR__ . '/../../util/security.php';
}

if (session_status() === PHP_SESSION_NONE) {
    secureSession();
    setSecurityHeaders();
    session_start();
}

if (isset($_SESSION['user_id'])) {
    checkSessionTimeout();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vent Then Validate</title>
    <link rel="stylesheet" href="/vent-then-validate/public/css/style.css">
</head>

<body>