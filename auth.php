<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/includes/functions.php';

// Ensure CSRF token exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize database and controller
$db = (new Database())->getConnection();
$controller = new AuthController($db);

// Get action parameter
$action = $_GET['action'] ?? 'login_form';

// CSRF validation helper for POST requests
function validateCsrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Sesi keamanan berakhir. Silakan coba lagi.'
            ];
            return false;
        }
    }
    return true;
}

// Route handling
switch ($action) {
    // Show login form
    case 'login_form':
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user'])) {
            header('Location: index.php?action=dashboard');
            exit;
        }
        $controller->loginForm();
        break;

    // Show registration form (if enabled)
    case 'register_form':
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user'])) {
            header('Location: index.php?action=dashboard');
            exit;
        }
        $controller->registerForm();
        break;

    // Process login
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: auth.php?action=login_form');
            exit;
        }
        
        if (!validateCsrf()) {
            header('Location: auth.php?action=login_form');
            exit;
        }
        
        $controller->login($_POST);
        break;

    // Process registration
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: auth.php?action=register_form');
            exit;
        }
        
        if (!validateCsrf()) {
            header('Location: auth.php?action=register_form');
            exit;
        }
        
        $controller->register($_POST);
        break;

    // Logout
    case 'logout':
        $controller->logout();
        break;

    // Password reset form
    case 'forgot_password':
        $controller->forgotPasswordForm();
        break;

    // Process password reset
    case 'reset_password':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: auth.php?action=forgot_password');
            exit;
        }
        
        if (!validateCsrf()) {
            header('Location: auth.php?action=forgot_password');
            exit;
        }
        
        $controller->resetPassword($_POST);
        break;

    // Change password form (for logged in users)
    case 'change_password':
        if (!isset($_SESSION['user'])) {
            header('Location: auth.php?action=login_form');
            exit;
        }
        $controller->changePasswordForm();
        break;

    // Process password change
    case 'update_password':
        if (!isset($_SESSION['user'])) {
            header('Location: auth.php?action=login_form');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: auth.php?action=change_password');
            exit;
        }
        
        if (!validateCsrf()) {
            header('Location: auth.php?action=change_password');
            exit;
        }
        
        $controller->updatePassword($_POST);
        break;

    // Default: show login form
    default:
        $controller->loginForm();
        break;
}