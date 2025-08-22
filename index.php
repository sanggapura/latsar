<?php
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/controllers/PartnerController.php";

$db = (new Database())->getConnection();
$controller = new PartnerController($db);

$action = $_GET['action'] ?? 'index';

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
    default:
        $controller->index();
        break;
}
