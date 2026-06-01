<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/models/Model.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/controllers/UserController.php';

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $userController = new UserController();
    $result = $userController->login($email, $password);

    if ($result['success']) {
        if ($result['role'] === 'admin') {
            header('Location: /vent-then-validate/public/admin/dashboard.php');
        } else {
            header('Location: /vent-then-validate/public/my-complaints.php');
        }
        exit();
    } else {
        $errors = $result['errors'];
        $email  = sanitize($_POST['email']);
    }
}
?>

<div class="container">
    <div class="card" style="max-width: 500px; margin: 40px auto;">
        <h2 style="color: #1B3A6B; margin-bottom: 5px;">Welcome Back</h2>
        <p style="color: #555555; margin-bottom: 25px;">Login to your Vent Then Validate account.</p>

        <?php displayErrors($errors); ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address"
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
            Don't have an account? <a href="register.php" style="color: #2E6DB4;">Register here</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>