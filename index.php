<?php
session_start();

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/includes/functions.php";

// Initialize CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get action from URL parameter
$action = $_GET['action'] ?? 'landing';

// Check if user is logged in for protected routes
function requireAuth() {
    if (!isset($_SESSION['user'])) {
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => 'Anda harus login terlebih dahulu'
        ];
        header("Location: auth.php?action=login_form");
        exit;
    }
}

// Validate CSRF token for POST requests
function validateCsrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Token keamanan tidak valid. Silakan coba lagi.'
            ];
            return false;
        }
    }
    return true;
}

// Route handler
switch ($action) {
    // Landing page (public)
    case 'landing':
        include __DIR__ . "/views/landing.php";
        break;

    // Authentication routes (redirect to auth.php)
    case 'login':
        header("Location: auth.php?action=login_form");
        exit;
    
    case 'register':
        header("Location: auth.php?action=register_form");
        exit;
    
    case 'logout':
        header("Location: auth.php?action=logout");
        exit;

    // Dashboard routes (protected)
    case 'dashboard':
        requireAuth();
        include __DIR__ . "/views/user_dashboard.php";
        break;

    // Contact management routes
    case 'contacts':
        requireAuth();
        require_once __DIR__ . "/controllers/KontakController.php";
        $db = (new Database())->getConnection();
        $controller = new KontakController($db);
        $controller->index();
        break;

    case 'create_kontak':
        requireAuth();
        require_once __DIR__ . "/controllers/KontakController.php";
        $db = (new Database())->getConnection();
        $controller = new KontakController($db);
        $controller->createForm();
        break;

    case 'store_kontak':
        requireAuth();
        if (!validateCsrf()) {
            header("Location: index.php?action=create_kontak");
            exit;
        }
        require_once __DIR__ . "/controllers/KontakController.php";
        $db = (new Database())->getConnection();
        $controller = new KontakController($db);
        $controller->store($_POST);
        break;

    case 'edit_kontak':
        requireAuth();
        $id = $_GET['id'] ?? 0;
        require_once __DIR__ . "/controllers/KontakController.php";
        $db = (new Database())->getConnection();
        $controller = new KontakController($db);
        $controller->editForm($id);
        break;

    case 'update_kontak':
        requireAuth();
        if (!validateCsrf()) {
            header("Location: index.php?action=contacts");
            exit;
        }
        require_once __DIR__ . "/controllers/KontakController.php";
        $db = (new Database())->getConnection();
        $controller = new KontakController($db);
        $controller->update($_POST);
        break;

    case 'delete_kontak':
        requireAuth();
        if (!validateCsrf()) {
            header("Location: index.php?action=contacts");
            exit;
        }
        $id = $_POST['id'] ?? 0;
        require_once __DIR__ . "/controllers/KontakController.php";
        $db = (new Database())->getConnection();
        $controller = new KontakController($db);
        $controller->delete($id);
        break;

    // Stages management routes
    case 'stages':
        requireAuth();
        require_once __DIR__ . "/controllers/StageController.php";
        $db = (new Database())->getConnection();
        $controller = new StageController($db);
        $controller->index();
        break;

    case 'create_stage':
        requireAuth();
        require_once __DIR__ . "/controllers/StageController.php";
        $db = (new Database())->getConnection();
        $controller = new StageController($db);
        $controller->createForm();
        break;

    case 'store_stage':
        requireAuth();
        if (!validateCsrf()) {
            header("Location: index.php?action=create_stage");
            exit;
        }
        require_once __DIR__ . "/controllers/StageController.php";
        $db = (new Database())->getConnection();
        $controller = new StageController($db);
        $controller->store($_POST);
        break;

    // Documents management routes
    case 'documents':
        requireAuth();
        require_once __DIR__ . "/controllers/DocumentController.php";
        $db = (new Database())->getConnection();
        $controller = new DocumentController($db);
        $controller->index();
        break;

    case 'upload_document':
        requireAuth();
        require_once __DIR__ . "/controllers/DocumentController.php";
        $db = (new Database())->getConnection();
        $controller = new DocumentController($db);
        $controller->uploadForm();
        break;

    case 'store_document':
        requireAuth();
        if (!validateCsrf()) {
            header("Location: index.php?action=upload_document");
            exit;
        }
        require_once __DIR__ . "/controllers/DocumentController.php";
        $db = (new Database())->getConnection();
        $controller = new DocumentController($db);
        $controller->store($_POST, $_FILES);
        break;

    // Schedule management routes
    case 'schedule':
        requireAuth();
        require_once __DIR__ . "/controllers/ScheduleController.php";
        $db = (new Database())->getConnection();
        $controller = new ScheduleController($db);
        $controller->index();
        break;

    case 'create_schedule':
        requireAuth();
        require_once __DIR__ . "/controllers/ScheduleController.php";
        $db = (new Database())->getConnection();
        $controller = new ScheduleController($db);
        $controller->createForm();
        break;

    case 'store_schedule':
        requireAuth();
        if (!validateCsrf()) {
            header("Location: index.php?action=create_schedule");
            exit;
        }
        require_once __DIR__ . "/controllers/ScheduleController.php";
        $db = (new Database())->getConnection();
        $controller = new ScheduleController($db);
        $controller->store($_POST);
        break;

    // Admin panel redirect
    case 'admin':
        header("Location: loginadm.php");
        exit;

    // Default case - redirect to landing or dashboard
    default:
        if (isset($_SESSION['user'])) {
            header("Location: index.php?action=dashboard");
        } else {
            header("Location: index.php?action=landing");
        }
        exit;
}