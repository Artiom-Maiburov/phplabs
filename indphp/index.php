<?php
declare(strict_types=1);

require_once 'config.php';
require_once 'Controllers/AuthController.php';
require_once 'Controllers/MainController.php';
require_once 'Controllers/AdminController.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'auth':
        (new AuthController())->handleRequest();
        break;
    case 'logout':
        (new AuthController())->logout();
        break;
    case 'admin':
        (new AdminController())->index();
        break;
    case 'home':
    default:
        (new MainController())->index();
        break;
}