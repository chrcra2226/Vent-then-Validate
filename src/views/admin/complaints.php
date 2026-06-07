<?php
require_once __DIR__ . '/../../util/security.php';

if (session_status() === PHP_SESSION_NONE) {
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

$userController->requireAdmin();

$complaints = $complaintController->getAllComplaints();

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

// Handle status filter
$filter = isset($_GET['status']) ? $_GET['status'] : '';
if (!empty($filter)) {
    $complaints = array_filter($complaints, function ($c) use ($filter) {
        return $c['status'] === $filter;
    });
}
?>

<div class="container">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <h2 style="color: #1B3A6B; margin-bottom: 5px;">All Complaints</h2>
                <p style="color: #555555;">Manage and respond to all customer complaints.</p>
            </div>
            <a href="/vent-then-validate/public/admin/main_dashboard.php"
                class="btn btn-primary">Back to Dashboard</a>
        </div>

        <!-- Filter Bar -->
        <div style="margin-bottom: 20px; display: flex; gap: 10px;">
            <a href="/vent-then-validate/public/admin/main_complaints.php"
                class="btn <?php echo empty($filter) ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
            <a href="/vent-then-validate/public/admin/main_complaints.php?status=Open"
                class="btn <?php echo $filter === 'Open' ? 'btn-primary' : 'btn-secondary'; ?>">Open</a>
            <a href="/vent-then-validate/public/admin/main_complaints.php?status=In Review"
                class="btn <?php echo $filter === 'In Review' ? 'btn-primary' : 'btn-secondary'; ?>">In Review</a>
            <a href="/vent-then-validate/public/admin/main_complaints.php?status=Resolved"
                class="btn <?php echo $filter === 'Resolved' ? 'btn-primary' : 'btn-secondary'; ?>">Resolved</a>
            <a href="/vent-then-validate/public/admin/main_complaints.php?status=Closed"
                class="btn <?php echo $filter === 'Closed' ? 'btn-primary' : 'btn-secondary'; ?>">Closed</a>
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
                <?php if (empty($complaints)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #555555; padding: 30px;">
                            No complaints found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($complaints as $complaint): ?>
                        <tr>
                            <td><?php echo $complaint['complaint_id']; ?></td>
                            <td><?php echo htmlspecialchars($complaint['title']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($complaint['user_name']); ?></td>
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