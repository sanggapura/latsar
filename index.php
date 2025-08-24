<?php
session_start();
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/controllers/PartnerController.php";
require_once __DIR__ . "/controllers/KontakController.php";
require_once __DIR__ . "/controllers/AuthController.php";

// Ensure CSRF token exists for POST actions
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$db = (new Database())->getConnection();
$controller = new PartnerController($db);
$kontakController = new KontakController($db);
$auth = new AuthController($db);

$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'login':
        header("Location: auth.php?action=login_form");
        exit;
    case 'register':
        header("Location: auth.php?action=register_form");
        exit;
    case 'logout':
        $auth->logout();
        exit;
    
    // Partner routes
    case 'partners':
        if (!isset($_SESSION['user'])) { header("Location: auth.php?action=login_form"); exit; }
        $controller->index();
        break;
    case 'create':
        if (!isset($_SESSION['user'])) { header("Location: auth.php?action=login_form"); exit; }
        $controller->createForm();
        break;
    case 'store':
        if (!isset($_SESSION['user'])) { header("Location: auth.php?action=login_form"); exit; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
            header('Location: index.php?action=create');
            exit;
        }
        $controller->store($_POST);
        break;
    case 'edit':
        if (!isset($_SESSION['user'])) { header("Location: auth.php?action=login_form"); exit; }
        $controller->editForm($_GET['id']);
        break;
    case 'update':
        if (!isset($_SESSION['user'])) { header("Location: auth.php?action=login_form"); exit; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
            header('Location: index.php?action=partners');
            exit;
        }
        $controller->update($_POST);
        break;
    case 'delete':
        if (!isset($_SESSION['user'])) { header("Location: auth.php?action=login_form"); exit; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
            header('Location: index.php?action=partners');
            exit;
        }
        $controller->delete($_POST['id'] ?? 0);
        break;
    
    // Kontak Mitra routes
    case 'contacts':
        if (!isset($_SESSION['user'])) { header("Location: auth.php?action=login_form"); exit; }
        $kontakController->index();
        break;
    case 'create_kontak':
        if (!isset($_SESSION['user'])) { header("Location: auth.php?action=login_form"); exit; }
        $kontakController->createForm();
        break;
    case 'store_kontak':
        if (!isset($_SESSION['user'])) { header("Location: auth.php?action=login_form"); exit; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
            header('Location: index.php?action=create_kontak');
            exit;
        }
        $kontakController->store($_POST);
        break;
    case 'edit_kontak':
        if (!isset($_SESSION['user'])) { header("Location: auth.php?action=login_form"); exit; }
        $kontakController->editForm($_GET['id']);
        break;
    case 'update_kontak':
        if (!isset($_SESSION['user'])) { header("Location: auth.php?action=login_form"); exit; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
            header('Location: index.php?action=contacts');
            exit;
        }
        $kontakController->update($_POST);
        break;
    case 'delete_kontak':
        if (!isset($_SESSION['user'])) { header("Location: auth.php?action=login_form"); exit; }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
            header('Location: index.php?action=contacts');
            exit;
        }
        $kontakController->delete($_POST['id'] ?? 0);
        break;
    
    case 'dashboard':
    default:
        if (!isset($_SESSION['user'])) { header("Location: auth.php?action=login_form"); exit; }
        include __DIR__ . "/views/dashboard.php";
        break;
}