<nav class="navbar">
    <div class="nav-brand">
        <a href="/vent-then-validate/public/index.php">Vent Then Validate</a>
    </div>
    <div class="nav-links">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="/vent-then-validate/public/admin/main_dashboard.php">Dashboard</a>
                <a href="/vent-then-validate/public/admin/main_complaints.php">All Complaints</a>
            <?php else: ?>
                <a href="/vent-then-validate/public/main_my-complaint.php">My Complaints</a>
                <a href="/vent-then-validate/public/main_submit-complaint.php">Submit Complaint</a>
            <?php endif; ?>
            <a href="/vent-then-validate/public/main_login.php?logout=true">Logout</a> <?php else: ?>
            <a href="/vent-then-validate/public/index.php">Home</a>
            <a href="/vent-then-validate/public/main_login.php">Login</a>
            <a href="/vent-then-validate/public/main_register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>