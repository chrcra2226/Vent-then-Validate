<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container">
    <!-- Hero Section -->
    <div class="hero">
        <h1>Vent Then Validate</h1>
        <p>A safe space to voice your concerns and get the resolution you deserve.</p>
        <div class="hero-buttons">
            <a href="/vent-then-validate/public/main_register.php" class="btn-hero-primary">Get Started</a>
            <a href="/vent-then-validate/public/main_login.php" class="btn-hero-secondary">Login</a>
        </div>
    </div>

    <!-- Feature Cards -->
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">📢</div>
            <h2>Vent</h2>
            <p>Submit your complaint quickly and easily. We make it simple to voice your concerns with full file attachment support.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔍</div>
            <h2>Track</h2>
            <p>Monitor the progress of your complaint every step of the way with real-time status updates and full history.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">✅</div>
            <h2>Validate</h2>
            <p>Receive confirmation and resolution. Your concerns are heard, reviewed, and acted upon by our team.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>