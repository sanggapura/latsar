<?php
session_start();
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/controllers/PartnerController.php";

if (!isset($_SESSION['user'])) {
    header("Location: auth.php?action=login");
    exit;
}

$db = (new Database())->getConnection();
$controller = new PartnerController($db);

$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'create':
        $controller->createForm();
        break;
    case 'store':
        $controller->store($_POST);
        break;
    case 'edit':
        $controller->editForm($_GET['id']);
        break;
    case 'update':
        $controller->update($_POST);
        break;
    case 'delete':
        $controller->delete($_GET['id']);
        break;
    case 'dashboard':
    default:
        include __DIR__ . "/views/dashboard.php";
        break;
}
