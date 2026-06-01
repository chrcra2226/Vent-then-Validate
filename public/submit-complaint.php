<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/models/Model.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/models/Category.php';
require_once __DIR__ . '/../src/models/Complaint.php';
require_once __DIR__ . '/../src/models/StatusHistory.php';
require_once __DIR__ . '/../src/models/ComplaintFile.php';
require_once __DIR__ . '/../src/controllers/UserController.php';
require_once __DIR__ . '/../src/controllers/ComplaintController.php';

$userController      = new UserController();
$complaintController = new ComplaintController();

// Redirect to login if not logged in
$userController->requireLogin();

$errors      = [];
$success     = '';
$title       = '';
$description = '';
$category_id = '';

// Fetch categories
$categories = $complaintController->getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $title       = $_POST['title'];
    $description = $_POST['description'];

    $result = $complaintController->submitComplaint(
        $_SESSION['user_id'],
        $category_id,
        $title,
        $description
    );

    if ($result['success']) {
        $success     = $result['message'];
        $title       = '';
        $description = '';
        $category_id = '';
    } else {
        $errors = $result['errors'];
    }
}
?>

<div class="container">
    <div class="card" style="max-width: 700px; margin: 40px auto;">
        <h2 style="color: #1B3A6B; margin-bottom: 5px;">Submit a Complaint</h2>
        <p style="color: #555555; margin-bottom: 25px;">Fill out the form below and we will get back to you as soon as possible.</p>

        <?php displayErrors($errors); ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="submit-complaint.php">
            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category_id" required>
                    <option value="">-- Select a Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>"
                            <?php echo ($category_id == $category['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Complaint Title</label>
                <input type="text" id="title" name="title"
                    placeholder="Brief summary of your complaint"
                    value="<?php echo htmlspecialchars($title); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="6"
                    placeholder="Please describe your complaint in detail..."
                    required><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Complaint</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>