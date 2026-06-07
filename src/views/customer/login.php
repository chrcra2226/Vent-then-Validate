<?php
require_once __DIR__ . '/../../util/security.php';

// Must be called BEFORE session_start()
secureSession();
setSecurityHeaders();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../Database.php';
require_once __DIR__ . '/../../models/Model.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../util/validation.php';
require_once __DIR__ . '/../../controllers/UserController.php';

$userController = new UserController();

// Handle logout
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    $userController->logout();
    exit();
}

// Handle session timeout message
$timeout_message = '';
if (isset($_GET['timeout']) && $_GET['timeout'] === 'true') {
    $timeout_message = 'Your session has expired due to inactivity. Please login again.';
}

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $userController = new UserController();
    $result = $userController->login($email, $password);

    if ($result['success']) {
        if ($result['role'] === 'admin') {
            header('Location: /vent-then-validate/public/admin/main_dashboard.php');
            exit();
        } else {
            header('Location: /vent-then-validate/public/main_my-complaint.php');
            exit();
        }
    } else {
        $errors = $result['errors'];
        $email  = sanitize($_POST['email']);
    }
}

// No HTML output above this point
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/navbar.php';
?>

<div class="container">
    <div class="card" style="max-width: 500px; margin: 40px auto;">
        <h2 style="color: #1B3A6B; margin-bottom: 5px;">Welcome Back</h2>
        <p style="color: #555555; margin-bottom: 25px;">Login to your Vent Then Validate account.</p>

        <?php displayErrors($errors); ?>

        <?php if (!empty($timeout_message)): ?>
            <div class="alert alert-error"><?php echo $timeout_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="/vent-then-validate/public/main_login.php">
            <?php csrfField(); ?>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                    placeholder="Enter your email address"
                    value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password"
                    placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>

        <p style="text-align: center; margin-top: 20px; color: #555555;">
            Don't have an account?
            <a href="/vent-then-validate/public/main_register.php" style="color: #2E6DB4;">Register here</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>