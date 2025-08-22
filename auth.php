<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';

// Ensure CSRF token exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$db = (new Database())->getConnection();
$controller = new AuthController($db);

$action = $_GET['action'] ?? 'login_form';

// Helper to validate CSRF for POST requests
$validateCsrf = function () {
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Sesi keamanan berakhir. Silakan coba lagi.'
        ];
        header('Location: auth.php?action=login_form');
        exit;
    }
};

switch ($action) {
    case 'login_form':
        $controller->loginForm();
        break;
    case 'register_form':
        $controller->registerForm();
        break;
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: auth.php?action=login_form');
            exit;
        }
        $validateCsrf();
        $controller->login($_POST);
        break;
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: auth.php?action=register_form');
            exit;
        }
        $validateCsrf();
        $controller->register($_POST);
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        $controller->loginForm();
        break;
}
