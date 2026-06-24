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
use App\Controllers\AdminController;
use App\Controllers\AdminUserController;
use App\Controllers\AdminRejectedController;
use App\Controllers\OwnerController;
use App\Controllers\OwnerRejectedController;
use App\Controllers\OwnerApplicationController;
use App\Controllers\OwnerTenantController;
use App\Controllers\RoomController;
use App\Controllers\TenantController;
use App\Controllers\TenantApplicationController;
use App\Controllers\TenantApplicationStatusController;

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
// New Owner Rejected Property Management Handler
$router->get('/owner/rejected-houses', [OwnerRejectedController::class, 'rejectedList']);
// New Owner Rooms Management Handlers
$router->get('/owner/rooms/{houseId}', [RoomController::class, 'index']);
$router->get('/owner/room/add/{houseId}', [RoomController::class, 'showAddForm']);
$router->post('/owner/room/add', [RoomController::class, 'addRoom']);
$router->get('/owner/room/edit/{houseId}/{roomId}', [RoomController::class, 'showEditForm']);
$router->post('/owner/room/edit', [RoomController::class, 'editRoom']);
$router->post('/owner/room/delete', [RoomController::class, 'deleteRoom']);
// Owner Tenancy Applications Review Handlers
$router->get('/owner/applications', [OwnerApplicationController::class, 'index']);
$router->get('/owner/application/view/{id}', [OwnerApplicationController::class, 'view']);
$router->post('/owner/application/approve', [OwnerApplicationController::class, 'approve']);
$router->post('/owner/application/reject', [OwnerApplicationController::class, 'reject']);
$router->post('/owner/application/delete', [OwnerApplicationController::class, 'delete']);
// Owner Active Tenants Management Handlers
$router->get('/owner/tenants', [OwnerTenantController::class, 'index']);
$router->get('/owner/tenants/{roomId}', [OwnerTenantController::class, 'index']);
$router->post('/owner/tenant/checkout', [OwnerTenantController::class, 'checkout']);


// Admin Route Handlers
$router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
$router->get('/admin/verify-house/{houseId}', [AdminController::class, 'showVerifyForm']);
$router->post('/admin/verify-house', [AdminController::class, 'handleVerify']);
// New Approved Property Management Handlers
$router->get('/admin/approved-houses', [AdminController::class, 'approvedList']);
$router->get('/admin/approved-house/view/{houseId}', [AdminController::class, 'showApprovedDetail']);
$router->post('/admin/approved-house/delete', [AdminController::class, 'handleDelete']);
// New Rejected Property Management Handlers
$router->get('/admin/rejected-houses', [AdminRejectedController::class, 'rejectedList']);
$router->get('/admin/rejected-house/view/{houseId}', [AdminRejectedController::class, 'showRejectedDetail']);
$router->post('/admin/rejected-house/reason', [AdminRejectedController::class, 'updateReason']);
$router->post('/admin/rejected-house/delete', [AdminRejectedController::class, 'handleDelete']);
// New Admin User & Owner Account Management Handlers
$router->get('/admin/users', [AdminUserController::class, 'index']);
$router->post('/admin/user/toggle-status', [AdminUserController::class, 'toggleStatus']);


// New Tenant Onboarding & Browsing Handlers
$router->get('/tenant/dashboard', [TenantController::class, 'dashboard']);
$router->get('/tenant/browse', [TenantController::class, 'index']);
$router->get('/tenant/house/view/{houseId}', [TenantController::class, 'viewHouse']);
$router->get('/tenant/house/inquire/{houseId}', [TenantController::class, 'inquire']);
// New Tenant Apply Request Handlers
$router->get('/tenant/house/apply/{houseId}', [TenantApplicationController::class, 'showApplyForm']);
$router->post('/tenant/house/apply', [TenantApplicationController::class, 'handleApply']);
// New Tenant Applications History Status Handler
$router->get('/tenant/applications', [TenantApplicationStatusController::class, 'index']);

// Fire router engine to resolve the request path
$router->resolve();