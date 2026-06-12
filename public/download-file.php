<?php
require_once __DIR__ . '/../src/util/security.php';

if (session_status() === PHP_SESSION_NONE) {
    secureSession();
    setSecurityHeaders();
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/models/Model.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/models/Complaint.php';
require_once __DIR__ . '/../src/models/ComplaintFile.php';
require_once __DIR__ . '/../src/controllers/UserController.php';

$userController = new UserController();

// Must be logged in
$userController->requireLogin();

// Get file ID from URL
$file_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($file_id === 0) {
    http_response_code(400);
    die('Invalid file request.');
}

// Get file record from database
$fileModel = new ComplaintFile();
$file      = $fileModel->getById($file_id);

if (!$file) {
    http_response_code(404);
    die('File not found.');
}

// Get the complaint this file belongs to
$complaintModel = new Complaint();
$complaint      = $complaintModel->getById($file['complaint_id']);

if (!$complaint) {
    http_response_code(404);
    die('Associated complaint not found.');
}

// Security check - customers can only download files 
// from their own complaints
if (
    $_SESSION['user_role'] === 'customer' &&
    $complaint['user_id'] != $_SESSION['user_id']
) {
    http_response_code(403);
    die('You do not have permission to access this file.');
}

// Verify the file exists on disk
if (!file_exists($file['file_path'])) {
    http_response_code(404);
    die('File no longer exists on the server.');
}

// Serve the file securely
$file_name = $file['file_name'];
$file_type = $file['file_type'];
$file_path = $file['file_path'];
$file_size = filesize($file_path);

// Set appropriate headers
header('Content-Description: File Transfer');
header('Content-Type: ' . $file_type);
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Content-Length: ' . $file_size);
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Clear output buffer and send file
ob_clean();
flush();
readfile($file_path);
exit();
