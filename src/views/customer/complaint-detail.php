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

$complaint_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$complaint    = $complaintController->getComplaint($complaint_id);

if (!$complaint) {
    header('Location: /vent-then-validate/public/main_my-complaint.php');
    exit();
}

if ($_SESSION['user_role'] === 'customer' && $complaint['user_id'] != $_SESSION['user_id']) {
    header('Location: /vent-then-validate/public/main_my-complaint.php');
    exit();
}

$history = $complaintController->getStatusHistory($complaint_id);
$files   = $complaintController->getComplaintFiles($complaint_id);

function getStatusBadge($status)
{
    $badges = [
        'Open'      => 'badge-open',
        'In Review' => 'badge-review',
        'Resolved'  => 'badge-resolved',
        'Closed'    => 'badge-closed'
    ];
    $class = isset($badges[$status]) ? $badges[$status] : 'badge-open';
    return '<span class="badge ' . $class . '">' . htmlspecialchars($status) . '</span>';
}
?>

<div class="container">
    <a href="<?php echo $_SESSION['user_role'] === 'admin'
                    ? '/vent-then-validate/public/admin/main_complaints.php'
                    : '/vent-then-validate/public/main_my-complaint.php'; ?>"
        class="back-link">&larr; Back to Complaints</a>

    <!-- Complaint Details -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="page-header">
            <div>
                <h2><?php echo htmlspecialchars($complaint['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h2>
                <p>
                    Category: <strong><?php echo htmlspecialchars($complaint['category_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong>
                    &nbsp;|&nbsp;
                    Submitted by: <strong><?php echo htmlspecialchars($complaint['user_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></strong>
                    &nbsp;|&nbsp;
                    Date: <strong><?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></strong>
                </p>
            </div>
            <div><?php echo getStatusBadge($complaint['status']); ?></div>
        </div>

        <div style="background-color: #f4f6fa; padding: 20px; border-radius: 5px;">
            <h4 style="color: #1B3A6B; margin-bottom: 10px;">Description</h4>
            <p style="color: #333333; line-height: 1.6;">
                <?php echo nl2br(htmlspecialchars($complaint['description'], ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>
            </p>
        </div>
    </div>

    <!-- Files Section -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="page-header">
            <div>
                <h2>Attached Files</h2>
            </div>
        </div>
        <?php if (empty($files)): ?>
            <p style="color: #555555;">No files attached to this complaint.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Type</th>
                        <th>Uploaded</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['file_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($file['file_type'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($file['uploaded_at'])); ?></td>
                            <td>
                                <button onclick="togglePreview('preview-<?php echo $file['file_id']; ?>')"
                                    class="btn btn-secondary"
                                    style="padding: 5px 15px; font-size: 0.85rem;">
                                    View
                                </button>
                            </td>
                        </tr>
                        <tr id="preview-<?php echo $file['file_id']; ?>" style="display: none;">
                            <td colspan="4" style="padding: 20px; background-color: #f4f6fa;">
                                <?php
                                $image_types = ['image/jpeg', 'image/png', 'image/gif'];
                                $file_path   = $file['file_path'];
                                $file_name   = $file['file_name'];

                                // Convert absolute path to web accessible path
                                $web_path = '/vent-then-validate/uploads/' . basename($file_path);
                                ?>

                                <?php if (in_array($file['file_type'], $image_types)): ?>
                                    <div style="text-align: center;">
                                        <img src="<?php echo htmlspecialchars($web_path, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"
                                            alt="<?php echo htmlspecialchars($file_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>"
                                            style="max-width: 100%; max-height: 500px; border-radius: 5px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                                        <p style="margin-top: 10px; color: #555555; font-size: 0.9rem;">
                                            <?php echo htmlspecialchars($file_name, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>
                                        </p>
                                    </div>
                                <?php elseif ($file['file_type'] === 'application/pdf'): ?>
                                    <div style="text-align: center;">
                                        <p style="color: #555555; margin-bottom: 15px;">
                                            PDF files cannot be previewed directly.
                                            Click below to download.
                                        </p>
                                        <a href="/vent-then-validate/public/download-file.php?id=<?php echo $file['file_id']; ?>"
                                            class="btn btn-primary">
                                            Download PDF
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <p style="color: #555555;">Preview not available for this file type.</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Status History -->
    <div class="card">
        <div class="page-header">
            <div>
                <h2>Status History</h2>
                <p>All status changes made to this complaint</p>
            </div>
        </div>
        <?php if (empty($history)): ?>
            <p style="color: #555555;">No status history available.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Previous Status</th>
                        <th>New Status</th>
                        <th>Changed By</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $record): ?>
                        <tr>
                            <td><?php echo date('M d, Y H:i', strtotime($record['changed_at'])); ?></td>
                            <td><?php echo $record['old_status']
                                    ? getStatusBadge($record['old_status'])
                                    : '<span style="color:#555555;">N/A</span>'; ?>
                            </td>
                            <td><?php echo getStatusBadge($record['new_status']); ?></td>
                            <td><?php echo htmlspecialchars($record['changed_by_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($record['notes'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>