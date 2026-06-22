<?php

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

$scriptName = $_SERVER['SCRIPT_NAME'] ?? ''; // e.g., "/rentora-ph/index.php"
$requestUri = $_SERVER['REQUEST_URI'] ?? ''; // e.g., "/rentora-ph/register"

$basePath = str_replace('\\', '/', dirname($scriptName)); // Extracts "/rentora-ph"
$baseUrl = $basePath === '/' ? '' : $basePath;

// Define a globally accessible BASE_URL constant for safe subfolder routing
define('BASE_URL', $baseUrl);

if ($basePath !== '/' && strpos($requestUri, $basePath) === 0) {
    // Strip the "/rentora-ph" prefix so the Router only has to match "/register", "/login" or "/"
    $_SERVER['REQUEST_URI'] = substr($requestUri, strlen($basePath));
}

use App\Core\Security;
use App\Core\Router;
use App\Controllers\UserController;
use App\Controllers\AuthController;
use App\Controllers\OwnerController;

Security::startSecureSession();

$router = new Router();

// Root redirecting to Registration Page
$router->get('/', [UserController::class, 'showRegisterForm']);

// User Registration Route Handlers
$router->get('/register', [UserController::class, 'showRegisterForm']);
$router->post('/register', [UserController::class, 'handleRegister']);

// Authentication Route Handlers
$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'handleLogin']);
$router->get('/logout', [AuthController::class, 'handleLogout']);

// Owner Dashboard Route Handlers
$router->get('/owner/dashboard', [OwnerController::class, 'dashboard']);
$router->get('/owner/add-house', [OwnerController::class, 'showAddHouseForm']);
$router->post('/owner/add-house', [OwnerController::class, 'addHouse']);

// Fire router engine to resolve the request path
$router->resolve();