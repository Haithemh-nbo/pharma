<?php

use App\Http\Controllers\AuthController;

require __DIR__.'/../vendor/autoload.php';

session_start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$authController = new AuthController();

switch($uri) {
    case '/login':
        $authController->login();
        break;

    case '/logout':
        $authController->logout();
        break;

    case '/admin/dashboard':
        \App\Http\Middleware\RoleMiddleware::handle('admin');
        include "../resources/views/admin/dashboard.php";
        break;

    case '/supplier/dashboard':
        \App\Http\Middleware\RoleMiddleware::handle('supplier');
        include "../resources/views/supplier/dashboard.php";
        break;

    case '/pharmacy/dashboard':
        \App\Http\Middleware\RoleMiddleware::handle('pharmacy');
        include "../resources/views/pharmacy/dashboard.php";
        break;

    default:
        echo "404 Not Found";
}
