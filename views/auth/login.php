<?php
/**
 * File Location: views/auth/login.php
 * File Name: login.php
 * Description: Clean, modern, professional black-and-white login view using standard bootstrap layouts.
 */

use App\Core\Security;

// Fetch any security error or successful registration alerts from session
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
$oldEmail = $_SESSION['old_input']['email'] ?? '';

// Clear the flash session variables after loading
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['old_input']);

// Safely load the header template matching the directory structure
$title = "LOGIN";
require_once dirname(__DIR__) . '/templates/header.php';
?>

<div class="container my-5 py-4">
    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8 col-12">
            
            <!-- Minimalist Login Card -->
            <div class="card shadow-sm border border-light-subtle rounded-4 bg-white">
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Title Section -->
                    <div class="text-center mb-4">
                        <h1 class="h3 fw-bold text-dark tracking-tight">Welcome Back</h1>
                        <p class="text-muted small">Please enter your credentials to access RENTORA PH.</p>
                    </div>
                    
                    <!-- Success Banner (e.g. from successful registration redirect) -->
                    <?php if ($success): ?>
                        <div class="alert alert-success d-flex align-items-start alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #f0fff4; color: #22543d;" role="alert">
                            <i class="fa-solid fa-circle-check mt-1 me-3"></i>
                            <div>
                                <span class="fw-semibold small">Success!</span><br>
                                <span class="small"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Error Banner (e.g. invalid credentials or rate limiting lockout) -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-start alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #fff5f5; color: #c53030;" role="alert">
                            <i class="fa-solid fa-circle-exclamation mt-1 me-3"></i>
                            <div>
                                <span class="fw-semibold small">Login Failed:</span><br>
                                <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo BASE_URL; ?>/login" method="POST" class="needs-validation" novalidate>
                        
                        <!-- Anti-Forgery CSRF Field Token Injection -->
                        <?php echo Security::csrfField(); ?>

                        <div class="mb-3">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="juan.delacruz@example.com" 
                                   value="<?php echo htmlspecialchars($oldEmail, ENT_QUOTES, 'UTF-8'); ?>" required autofocus>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="mb-0">Password <span class="text-danger">*</span></label>
                                <a href="<?php echo BASE_URL; ?>/forgot-password" class="text-muted small text-decoration-none">Forgot?</a>
                            </div>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter password" required>
                        </div>

                        <!-- Action Submit Button -->
                        <div class="d-grid pt-2">
                            <button class="btn btn-dark py-2" type="submit">
                                Log In
                            </button>
                            <p class="text-center mt-4 mb-0 small text-muted">
                                Don't have an account? <a href="<?php echo BASE_URL; ?>/register" class="text-dark fw-semibold text-decoration-none border-bottom border-dark">Create one here</a>
                            </p>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
// Include the clean footer template
require_once dirname(__DIR__) . '/templates/footer.php';
?>