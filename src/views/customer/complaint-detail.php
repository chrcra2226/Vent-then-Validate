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
    <div style="margin-bottom: 20px;">
        <a href="<?php echo $_SESSION['user_role'] === 'admin'
                        ? '/vent-then-validate/public/admin/main_complaints.php'
                        : '/vent-then-validate/public/main_my-complaint.php'; ?>"
            style="color: #2E6DB4;">&larr; Back to Complaints</a>
    </div>

    <!-- Complaint Details -->
    <div class="card" style="margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
            <div>
                <h2 style="color: #1B3A6B; margin-bottom: 5px;">
                    <?php echo htmlspecialchars($complaint['title']); ?>
                </h2>
                <p style="color: #555555;">
                    Category: <strong><?php echo htmlspecialchars($complaint['category_name']); ?></strong>
                    &nbsp;|&nbsp;
                    Submitted by: <strong><?php echo htmlspecialchars($complaint['user_name']); ?></strong>
                    &nbsp;|&nbsp;
                    Date: <strong><?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></strong>
                </p>
            </div>
            <div><?php echo getStatusBadge($complaint['status']); ?></div>
        </div>

        <div style="background-color: #f4f6fa; padding: 20px; border-radius: 5px;">
            <h4 style="color: #1B3A6B; margin-bottom: 10px;">Description</h4>
            <p style="color: #333333; line-height: 1.6;">
                <?php echo nl2br(htmlspecialchars($complaint['description'])); ?>
            </p>
        </div>
    </div>

    <!-- Files Section -->
    <div class="card" style="margin-bottom: 20px;">
        <h3 style="color: #1B3A6B; margin-bottom: 15px;">Attached Files</h3>
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
                            <td><?php echo htmlspecialchars($file['file_name']); ?></td>
                            <td><?php echo htmlspecialchars($file['file_type']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($file['uploaded_at'])); ?></td>
                            <td><a href="#" style="color: #2E6DB4;">Download</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Status History -->
    <div class="card">
        <h3 style="color: #1B3A6B; margin-bottom: 15px;">Status History</h3>
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
                            <td><?php echo htmlspecialchars($record['changed_by_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['notes']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>