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

$userController->requireAdmin();

$stats            = $complaintController->getDashboardStats();
$recentComplaints = $complaintController->getRecentComplaints(5);

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

    <!-- Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
        <div>
            <h2 style="color: #1B3A6B; font-size: 1.6rem; margin-bottom: 4px;">Admin Dashboard</h2>
            <p style="color: #555555;">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>!</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        <div class="stat-card total">
            <h3><?php echo $stats['total']; ?></h3>
            <p>Total Complaints</p>
        </div>
        <div class="stat-card open">
            <h3><?php echo $stats['open']; ?></h3>
            <span class="badge badge-open">Open</span>
        </div>
        <div class="stat-card review">
            <h3><?php echo $stats['in_review']; ?></h3>
            <span class="badge badge-review">In Review</span>
        </div>
        <div class="stat-card resolved">
            <h3><?php echo $stats['resolved']; ?></h3>
            <span class="badge badge-resolved">Resolved</span>
        </div>
        <div class="stat-card closed">
            <h3><?php echo $stats['closed']; ?></h3>
            <span class="badge badge-closed">Closed</span>
        </div>
    </div>

    <!-- Recent Complaints -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
            <div>
                <h2 style="color: #1B3A6B; font-size: 1.4rem; margin-bottom: 4px;">Recent Complaints</h2>
                <p style="color: #555555;">The 5 most recently submitted complaints</p>
            </div>
            <a href="/vent-then-validate/public/admin/main_complaints.php" class="btn btn-primary">View All</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Submitted By</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentComplaints)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #555555; padding: 30px;">
                            No complaints to display yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentComplaints as $complaint): ?>
                        <tr>
                            <td><?php echo $complaint['complaint_id']; ?></td>
                            <td><?php echo htmlspecialchars($complaint['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($complaint['category_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($complaint['user_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></td>
                            <td><?php echo getStatusBadge($complaint['status']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></td>
                            <td>
                                <a href="/vent-then-validate/public/admin/main_manage-complaint.php?id=<?php echo $complaint['complaint_id']; ?>"
                                    style="color: #2E6DB4;">Manage</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>