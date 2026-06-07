<?php
require_once __DIR__ . '/../models/Model.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../util/validation.php';

class UserController
{

    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Handle user registration
     */
    public function register($name, $email, $password, $confirm_password)
    {
        $errors = [];

        // Sanitize inputs
        $name  = sanitize($name);
        $email = sanitize($email);

        // Validate inputs
        $errors = validateRegistration($name, $email, $password, $confirm_password);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            return [
                'success' => false,
                'errors'  => ['An account with that email address already exists.']
            ];
        }

        // Create the user
        $this->userModel->create($name, $email, $password);

        return [
            'success' => true,
            'message' => 'Account created successfully! You can now <a href="login.php">login here</a>.'
        ];
    }

    /**
     * Handle user login
     */
    public function login($email, $password)
    {
        $errors = [];

        // Sanitize inputs
        $email = sanitize($email);

        // Validate inputs
        $errors = validateLogin($email, $password);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Find user by email
        $user = $this->userModel->getByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($password, $user['password_hash'])) {
            return [
                'success' => false,
                'errors'  => ['Invalid email address or password. Please try again.']
            ];
        }

        // Regenerate session ID for security
        session_regenerate_id(true);

        // Store user info in session
        $_SESSION['user_id']    = $user['user_id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];

        return [
            'success' => true,
            'role'    => $user['role']
        ];
    }

    /**
     * Handle user logout
     */
    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /vent-then-validate/public/main_login.php');
        exit();
    }

    /**
     * Get all users
     */
    public function getAllUsers()
    {
        return $this->userModel->getAll();
    }

    /**
     * Get all customers
     */
    public function getAllCustomers()
    {
        return $this->userModel->getAllCustomers();
    }

    /**
     * Update user details
     */
    public function updateUser($user_id, $name, $email)
    {
        $name  = sanitize($name);
        $email = sanitize($email);

        if (empty($name) || empty($email)) {
            return [
                'success' => false,
                'errors'  => ['Name and email are required.']
            ];
        }

        $this->userModel->update($user_id, $name, $email);

        return ['success' => true, 'message' => 'User updated successfully.'];
    }

    /**
     * Check if a user is logged in
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Check if the logged in user is an admin
     */
    public function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Redirect if not logged in
     */
    public function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            header('Location: /vent-then-validate/public/main_login.php');
            exit();
        }
    }

    /**
     * Redirect if not admin
     */
    public function requireAdmin()
    {
        if (!$this->isAdmin()) {
            header('Location: /vent-then-validate/public/main_login.php');
            exit();
        }
    }
}
