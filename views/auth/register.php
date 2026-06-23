<?php
/**
 * File Location: views/auth/register.php
 * File Name: register.php
 * Description: Clean, modern, and professional black-and-white registration view using standard sizing.
 */

use App\Core\Security;

// Fetch old form values and session messages if validation errors occurred
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
$old = $_SESSION['old_input'] ?? [];

// Clear the flash session alerts after loading
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['old_input']);

// Include the clean header template
$title = "REGISTER";
require_once dirname(__DIR__) . '/templates/header.php';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-xl-7 col-lg-9 col-md-10 col-12">
            
            <!-- Minimalist Registration Card -->
            <div class="card shadow-sm border border-light-subtle rounded-5 bg-white">
                <div class="card-body p-4 p-md-5">
                    
                    <!-- Title Section -->
                    <div class="text-center mb-4">
                        <h1 class="h3 fw-bold text-dark tracking-tight">Create Account</h1>
                    </div>
                    
                    <!-- Alert Banners -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-start alert-dismissible fade show p-3 border-0 rounded-1" style="background-color: #fff5f5; color: #c53030;" role="alert">
                            <i class="fa-solid fa-circle-exclamation mt-1 me-3"></i>
                            <div>
                                <span class="fw-semibold small">Please check the following error:</span><br>
                                <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success d-flex align-items-start alert-dismissible fade show p-3 border-0 rounded-1" style="background-color: #f0fff4; color: #22543d;" role="alert">
                            <i class="fa-solid fa-circle-check mt-1 me-3"></i>
                            <div>
                                <span class="fw-semibold small">Registration Successful!</span><br>
                                <span class="small"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Use BASE_URL for explicit submission destination -->
                    <form action="<?php echo BASE_URL; ?>/register" method="POST" class="needs-validation" novalidate>
                        
                        <!-- Anti-Forgery CSRF Field Token Injection -->
                        <?php echo Security::csrfField(); ?>

                        <!-- Row 1: Personal Details -->
                        <div class="row g-3">
                            <div class="col-12 mt-4 mb-0 pb-0">
                                <h2 class="h6 fw-bold text-uppercase text-secondary tracking-wider" style="font-size: 0.75rem; border-bottom: 1px solid #eaeaea; padding-bottom: 0.5rem;">
                                    Personal Information
                                </h2>
                            </div>

                            <!-- First Name -->
                            <div class="col-md-6">
                                <label for="firstname">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="firstname" name="firstname" 
                                       placeholder="Juan" value="<?php echo htmlspecialchars($old['firstname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>

                            <!-- Last Name -->
                            <div class="col-md-6">
                                <label for="lastname">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="lastname" name="lastname" 
                                       placeholder="Dela Cruz" value="<?php echo htmlspecialchars($old['lastname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>

                            <!-- Mobile/Contact Number -->
                            <div class="col-md-6">
                                <label for="contact">Contact Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="contact" name="contact" 
                                       placeholder="e.g., 09123456789" value="<?php echo htmlspecialchars($old['contact'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>

                            <!-- Role/Account Type -->
                            <div class="col-md-6">
                                <label for="role">Account Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="" disabled <?php echo empty($old['role']) ? 'selected' : ''; ?>>Select Your Role</option>
                                    <option value="Tenant" <?php echo (isset($old['role']) && $old['role'] === 'Tenant') ? 'selected' : ''; ?>>Tenant (Looking for boarding)</option>
                                    <option value="Owner" <?php echo (isset($old['role']) && $old['role'] === 'Owner') ? 'selected' : ''; ?>>Owner (Renting out properties)</option>
                                </select>
                            </div>

                            <!-- Row 2: Account Settings -->
                            <div class="col-12 mt-4 mb-0 pb-0">
                                <h2 class="h6 fw-bold text-uppercase text-secondary tracking-wider" style="font-size: 0.75rem; border-bottom: 1px solid #eaeaea; padding-bottom: 0.5rem;">
                                    Security Credentials
                                </h2>
                            </div>

                            <!-- Email Address -->
                            <div class="col-12">
                                <label for="email">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="juan.delacruz@example.com" value="<?php echo htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <label for="password">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="At least 8 characters" required>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <label for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       placeholder="Verify password" required>
                            </div>
                        </div>

                        <!-- Action Submit Button -->
                        <div class="d-grid mt-4">
                            <button class="btn btn-dark py-2" type="submit">
                                Create My Account
                            </button>
                            <p class="text-center mt-3 mb-0 small text-muted">
                                Already registered? <a href="<?php echo BASE_URL; ?>/login" class="text-dark fw-semibold text-decoration-none border-bottom border-dark">Log In here</a>
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