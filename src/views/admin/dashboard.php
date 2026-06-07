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
    <h2 style="color: #1B3A6B; margin-bottom: 20px;">
        Admin Dashboard — Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
    </h2>

    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        <div class="card" style="flex: 1; text-align: center;">
            <h3 style="color: #1B3A6B; font-size: 2rem;"><?php echo $stats['total']; ?></h3>
            <p style="color: #555555;">Total Complaints</p>
        </div>
        <div class="card" style="flex: 1; text-align: center;">
            <h3 style="color: #E8A020; font-size: 2rem;"><?php echo $stats['open']; ?></h3>
            <p style="color: #555555;">Open</p>
        </div>
        <div class="card" style="flex: 1; text-align: center;">
            <h3 style="color: #2E6DB4; font-size: 2rem;"><?php echo $stats['in_review']; ?></h3>
            <p style="color: #555555;">In Review</p>
        </div>
        <div class="card" style="flex: 1; text-align: center;">
            <h3 style="color: #2E7D32; font-size: 2rem;"><?php echo $stats['resolved']; ?></h3>
            <p style="color: #555555;">Resolved</p>
        </div>
        <div class="card" style="flex: 1; text-align: center;">
            <h3 style="color: #555555; font-size: 2rem;"><?php echo $stats['closed']; ?></h3>
            <p style="color: #555555;">Closed</p>
        </div>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: #1B3A6B;">Recent Complaints</h3>
            <a href="/vent-then-validate/src/views/admin/complaints.php" class="btn btn-primary">View All</a>
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
                            <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['user_name']); ?></td>
                            <td><?php echo getStatusBadge($complaint['status']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></td>
                            <td>
                                <a href="/vent-then-validate/src/views/admin/manage-complaint.php?id=<?php echo $complaint['complaint_id']; ?>"
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