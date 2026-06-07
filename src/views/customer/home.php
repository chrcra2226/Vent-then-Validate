<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container">
    <div class="card" style="text-align: center; padding: 60px 30px;">
        <h1 style="color: #1B3A6B; font-size: 2.5rem; margin-bottom: 10px;">Vent Then Validate</h1>
        <p style="color: #555555; font-size: 1.2rem; margin-bottom: 30px;">
            A safe space to voice your concerns and get the resolution you deserve.
        </p>
        <a href="/vent-then-validate/public/main_register.php" class="btn btn-primary" style="margin-right: 10px;">Get Started</a>
        <a href="/vent-then-validate/public/main_login.php" class="btn btn-secondary">Login</a>
    </div>

    <div style="display: flex; gap: 20px; margin-top: 20px;">
        <div class="card" style="flex: 1; text-align: center;">
            <h2 style="color: #1B3A6B; margin-bottom: 10px;">Vent</h2>
            <p style="color: #555555;">Submit your complaint quickly and easily. We make it simple to voice your concerns.</p>
        </div>
        <div class="card" style="flex: 1; text-align: center;">
            <h2 style="color: #1B3A6B; margin-bottom: 10px;">Track</h2>
            <p style="color: #555555;">Monitor the progress of your complaint every step of the way with real-time status updates.</p>
        </div>
        <div class="card" style="flex: 1; text-align: center;">
            <h2 style="color: #1B3A6B; margin-bottom: 10px;">Validate</h2>
            <p style="color: #555555;">Receive confirmation and resolution. Your concerns are heard and acted upon.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>