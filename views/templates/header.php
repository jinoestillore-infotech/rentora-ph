<?php


// Establish login state variables safely
$isLoggedIn = isset($_SESSION['user_id']);
$userFirstName = $_SESSION['firstname'] ?? '';
$userRole = $_SESSION['role'] ?? '';

// Determine active page state based on the request URI to swap guest options
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$isLoginPage = (strpos($requestUri, '/login') !== false);
$isRegisterPage = (strpos($requestUri, '/register') !== false);

// Determine role-based dashboard landing pages
$dashboardUrl = BASE_URL . '/';
if ($isLoggedIn) {
    switch ($userRole) {
        case 'Admin':
            $dashboardUrl = BASE_URL . '/admin/dashboard';
            break;
        case 'Owner':
            $dashboardUrl = BASE_URL . '/owner/dashboard';
            break;
        case 'Tenant':
        default:
            $dashboardUrl = BASE_URL . '/tenant/dashboard';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RENTORA PH - <?php echo $title ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        /* Modern Minimalist Grayscale Theme */
        body {
            background-color: #fafafa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #1a1a1a;
            -webkit-font-smoothing: antialiased;
        }
        
        /* Clean, sharp inputs with subtle focus animations */
        .form-control, .form-select {
            border: 1px solid #dcdcdc;
            border-radius: 4px;
            font-size: 0.95rem;
            color: #1a1a1a;
            padding: 0.6rem 0.85rem;
            transition: all 0.2s ease-in-out;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #000000;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.08);
            outline: none;
        }

        /* Standardized form labels */
        label {
            font-weight: 500;
            font-size: 0.9rem;
            color: #333333;
            margin-bottom: 0.35rem;
        }

        /* Professional Monochrome Buttons */
        .btn-dark {
            background-color: #1a1a1a;
            border-color: #1a1a1a;
            border-radius: 4px;
            font-size: 0.95rem;
            font-weight: 500;
            padding: 0.65rem 1.5rem;
            transition: all 0.2s ease;
        }

        .btn-dark:hover, .btn-dark:focus {
            background-color: #000000;
            border-color: #000000;
        }

        /* Minimalist Navigation Bar */
        .navbar {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e5e5e5;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
            color: #000000 !important;
        }

        .nav-link {
            color: #555555 !important;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .nav-link:hover {
            color: #000000 !important;
        }
    </style>
</head>
<body>

<!-- Minimalist Navigation Bar -->
<nav class="navbar navbar-expand-lg py-3">
    <div class="container">
        <!-- Main Brand Link dynamically resolving based on root directory -->
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>/">
            <i class="fa-solid fa-house-chimney me-2 text-dark"></i>RENTORA PH
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                
                <?php if ($isLoggedIn): ?>
                    <!-- LOGGED IN NAVIGATION STATES -->
                    <?php if ($userRole === 'Owner'): ?>
                        <!-- Owners see Owner Dashboard and Logout -->
                        <li class="nav-item">
                            <a class="nav-link px-3" href="<?php echo $dashboardUrl; ?>">
                                <i class="fa-solid fa-chart-line me-1"></i> Owner Dashboard
                            </a>
                        </li>
                    <?php elseif ($userRole === 'Tenant'): ?>
                        <!-- Tenants see Browse Rooms and Logout -->
                        <li class="nav-item">
                            <a class="nav-link px-3" href="<?php echo $dashboardUrl; ?>">
                                <i class="fa-solid fa-gauge me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="<?php echo BASE_URL; ?>/tenant/browse">
                                <i class="fa-solid fa-magnifying-glass me-1"></i> Browse Rooms
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Admins see Admin Dashboard and Logout -->
                        <li class="nav-item">
                            <a class="nav-link px-3" href="<?php echo $dashboardUrl; ?>">
                                <i class="fa-solid fa-gauge me-1"></i> Admin Dashboard
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- <li class="nav-item border-start border-light-subtle ps-2 ms-2 d-none d-lg-block">
                        <span class="navbar-text text-muted small pe-2">
                            Hi, <strong><?php echo htmlspecialchars($userFirstName); ?></strong>!
                        </span>
                    </li> -->
                    
                    <li class="nav-item">
                        <a class="nav-link px-3 fw-bold text-danger" href="<?php echo BASE_URL; ?>/logout">
                            <i class="fa-solid fa-right-from-bracket me-1"></i> Log Out
                        </a>
                    </li>

                <?php else: ?>
                    <!-- GUEST (NOT LOGGED IN) NAVIGATION STATES -->
                    <?php if ($isLoginPage): ?>
                        <!-- If on Login page, show only Register link -->
                        <li class="nav-item">
                            <a class="btn btn-dark btn-sm text-white py-1.5 px-3" href="<?php echo BASE_URL; ?>/register">
                                Register
                            </a>
                        </li>
                    <?php elseif ($isRegisterPage): ?>
                        <!-- If on Register page, show only Login link -->
                        <li class="nav-item">
                            <a class="nav-link px-3 fw-bold text-dark" href="<?php echo BASE_URL; ?>/login">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> Log In
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Default view for home page or other pages when logged out -->
                        <li class="nav-item">
                            <a class="nav-link px-3" href="<?php echo BASE_URL; ?>/login">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> Log In
                            </a>
                        </li>
                        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                            <a class="btn btn-dark btn-sm text-white py-1.5 px-3" href="<?php echo BASE_URL; ?>/register">
                                Register
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
                
            </ul>
        </div>
    </div>
</nav>