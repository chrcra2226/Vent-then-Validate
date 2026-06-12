<?php
require_once __DIR__ . '/../../util/security.php';

if (session_status() === PHP_SESSION_NONE) {
    secureSession();
    setSecurityHeaders();
    session_start();
}

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../models/Model.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Category.php';
require_once __DIR__ . '/../../models/Complaint.php';
require_once __DIR__ . '/../../models/StatusHistory.php';
require_once __DIR__ . '/../../models/ComplaintFile.php';
require_once __DIR__ . '/../../util/validation.php';
require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../controllers/ComplaintController.php';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$userController      = new UserController();
$complaintController = new ComplaintController();

$userController->requireLogin();

$errors      = [];
$success     = '';
$title       = '';
$description = '';
$category_id = '';

$categories = $complaintController->getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $category_id = $_POST['category_id'];
    $title       = $_POST['title'];
    $description = $_POST['description'];

    $result = $complaintController->submitComplaint(
        $_SESSION['user_id'],
        $category_id,
        $title,
        $description
    );

    if ($result['success']) {
        $complaint_id = $result['complaint_id'];

        // Handle file uploads if any files were submitted
        if (!empty($_FILES['complaint_files']['name'][0])) {
            $fileModel    = new ComplaintFile();
            $upload_dir = __DIR__ . '/../../../uploads/';
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
            $max_size      = 5242880; // 5MB
            $file_errors   = [];

            foreach ($_FILES['complaint_files']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['complaint_files']['name'][$key];
                $file_size = $_FILES['complaint_files']['size'][$key];
                $file_tmp  = $_FILES['complaint_files']['tmp_name'][$key];

                // Skip empty file slots
                if (empty($file_tmp)) continue;

                // Validate file size
                if ($file_size > $max_size) {
                    $file_errors[] = htmlspecialchars($file_name, ENT_QUOTES | ENT_HTML5, 'UTF-8') . ' exceeds the 5MB size limit.';
                    continue;
                }

                // Validate MIME type using finfo
                $finfo     = finfo_open(FILEINFO_MIME_TYPE);
                $file_type = finfo_file($finfo, $file_tmp);
                finfo_close($finfo);

                if (!in_array($file_type, $allowed_types)) {
                    $file_errors[] = htmlspecialchars($file_name, ENT_QUOTES | ENT_HTML5, 'UTF-8') . ' is not an allowed file type. Only JPG, PNG, GIF, and PDF files are accepted.';
                    continue;
                }

                // Generate unique filename
                $extension    = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_filename = bin2hex(random_bytes(16)) . '.' . strtolower($extension);
                $destination  = $upload_dir . $new_filename;

                // Move file to uploads folder
                if (move_uploaded_file($file_tmp, $destination)) {
                    $fileModel->create(
                        $complaint_id,
                        htmlspecialchars($file_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                        $destination,
                        $file_type
                    );
                } else {
                    $file_errors[] = 'Failed to upload ' . htmlspecialchars($file_name, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '. Please try again.';
                }
            }

            if (!empty($file_errors)) {
                $success .= ' However some files could not be uploaded: ' . implode(', ', $file_errors);
            }
        }

        $success     = $result['message'];
        $title       = '';
        $description = '';
        $category_id = '';
    } else {
        $errors = $result['errors'];
    }
}
?>

<div class="container">
    <div class="card" style="max-width: 700px; margin: 40px auto;">
        <h2 style="color: #1B3A6B; margin-bottom: 5px;">Submit a Complaint</h2>
        <p style="color: #555555; margin-bottom: 25px;">Fill out the form below and we will get back to you as soon as possible.</p>

        <?php displayErrors($errors); ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="POST" action="/vent-then-validate/public/main_submit-complaint.php"
              enctype="multipart/form-data">
            <?php csrfField(); ?>
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category_id" required>
                    <option value="">-- Select a Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>"
                            <?php echo ($category_id == $category['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Complaint Title</label>
                <input type="text" id="title" name="title"
                       placeholder="Brief summary of your complaint"
                       value="<?php echo htmlspecialchars($title, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="6"
                          placeholder="Please describe your complaint in detail..."
                          required><?php echo htmlspecialchars($description, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></textarea>
            </div>
            <div class="form-group">
                <label for="complaint_files">Attach Files (Optional)</label>
                <input type="file" id="complaint_files" name="complaint_files[]"
                       multiple accept=".jpg,.jpeg,.png,.gif,.pdf"
                       style="padding: 8px; border: 1px solid #cccccc; border-radius: 5px; width: 100%;">
                <small style="color: #555555; display: block; margin-top: 5px;">
                    Accepted file types: JPG, PNG, GIF, PDF. Maximum size: 5MB per file.
                </small>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Complaint</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>