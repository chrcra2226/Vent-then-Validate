<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/models/Model.php';
require_once __DIR__ . '/../../../src/models/User.php';
require_once __DIR__ . '/../../../src/models/Category.php';
require_once __DIR__ . '/../../../src/models/Complaint.php';
require_once __DIR__ . '/../../../src/models/StatusHistory.php';
require_once __DIR__ . '/../../../src/models/ComplaintFile.php';
require_once __DIR__ . '/../../../src/util/validation.php';
require_once __DIR__ . '/../../../src/controllers/UserController.php';
require_once __DIR__ . '/../../../src/controllers/ComplaintController.php';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';

$userController      = new UserController();
$complaintController = new ComplaintController();

// Redirect to login if not logged in
$userController->requireLogin();

// Fetch complaints for logged in user
$complaints = $complaintController->getUserComplaints($_SESSION['user_id']);

// Badge helper
function getStatusBadge($status)
{
    $badges = [
        'Open'      => 'badge-open',
        'In Review' => 'badge-review',
        'Resolved'  => 'badge-resolved',
        'Closed'    => 'badge-closed'
    ];
    $class = isset($badges[$status]) ? $badges[$status] : 'badge-open';
    return '<span class="badge ' . $class . '">' . $status . '</span>';
}
?>

<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
            <div>
                <h2 style="color: #1B3A6B; margin-bottom: 4px;">My Complaints</h2>
                <p style="color: #555555;">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>!</p>
            </div>
            <a href="/vent-then-validate/public/main_submit-complaint.php" class="btn btn-primary">+ New Complaint</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Date Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($complaints)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #555555; padding: 30px;">
                            No complaints submitted yet.
                            <a href="/vent-then-validate/src/views/customer/submit-complaint.php" style="color: #2E6DB4;">Submit one now.</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td><?php echo $complaint['complaint_id']; ?></td>
                            <td><?php echo htmlspecialchars($complaint['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($complaint['category_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                            <td><?php echo getStatusBadge($complaint['status']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></td>
                            <td>
                                <a href="/vent-then-validate/public/main_complaint-detail.php?id=<?php echo $complaint['complaint_id']; ?>"
                                    style="color: #2E6DB4;">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>