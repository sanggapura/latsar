<?php
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/controllers/AuthController.php";

$db = (new Database())->getConnection();
$controller = new AuthController($db);

$action = $_GET['action'] ?? 'login';

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login($_POST);
        } else {
            $controller->loginForm();
        }
        break;
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register($_POST);
        } else {
            $controller->registerForm();
        }
        break;
    case 'logout':
        $controller->logout();
        break;
    default:
        $controller->loginForm();
        break;
}
