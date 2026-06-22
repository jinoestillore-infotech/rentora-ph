<?php

use App\Core\Security;

// Include standard clean header template
require_once dirname(__DIR__) . '/templates/header.php';

// Fetch old input values and session error alerts if present
$error = $_SESSION['error'] ?? null;
$old = $_SESSION['old_input'] ?? [];

// Clear the flash session data
unset($_SESSION['error'], $_SESSION['old_input']);
?>

<div class="container my-5 mt-3">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-12">
            
            <!-- Breadcrumb Navigation -->
            <div class="mb-3">
                <a href="<?php echo BASE_URL; ?>/owner/dashboard" class="text-decoration-none text-dark small fw-semibold">
                    <i class="fa-solid fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #fff5f5; color: #c53030;" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-3"></i>
                    <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border rounded-5 bg-white">
                <div class="bg-transparent border-bottom border-light-subtle p-4">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-house-chimney me-2"></i>Register Boarding House
                    </h5>
                    <p class="text-muted mb-0 small">Please fill out the details below. This property will require admin approval before you can add rooms.</p>
                </div>
                
                <form action="<?php echo BASE_URL; ?>/owner/add-house" method="POST">
                    <!-- Security Verification Field -->
                    <?php echo Security::csrfField(); ?>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            
                            <!-- Boarding House Name -->
                            <div class="col-12">
                                <label for="name">Boarding House Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="e.g. Dela Cruz Cozy Living Space" 
                                       value="<?php echo htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>

                            <!-- Town selection localized to Bohol municipalities -->
                            <div class="col-md-6">
                                <label for="town">Town / Municipality <span class="text-danger">*</span></label>
                                <select class="form-select" id="town" name="town" required>
                                    <option value="" disabled <?php echo empty($old['town']) ? 'selected' : ''; ?>>-- Select Town --</option>
                                    <option value="Inabanga" <?php echo (isset($old['town']) && $old['town'] === 'Inabanga') ? 'selected' : ''; ?>>Inabanga</option>
                                    <option value="Tubigon" <?php echo (isset($old['town']) && $old['town'] === 'Tubigon') ? 'selected' : ''; ?>>Tubigon</option>
                                    <option value="Clarin" <?php echo (isset($old['town']) && $old['town'] === 'Clarin') ? 'selected' : ''; ?>>Clarin</option>
                                    <option value="Sagbayan" <?php echo (isset($old['town']) && $old['town'] === 'Sagbayan') ? 'selected' : ''; ?>>Sagbayan</option>
                                    <option value="Loon" <?php echo (isset($old['town']) && $old['town'] === 'Loon') ? 'selected' : ''; ?>>Loon</option>
                                    <option value="Tagbilaran" <?php echo (isset($old['town']) && $old['town'] === 'Tagbilaran') ? 'selected' : ''; ?>>Tagbilaran</option>
                                </select>
                            </div>

                            <!-- Contact Number -->
                            <div class="col-md-6">
                                <label for="contact_number">Contact Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                       placeholder="e.g. 09223456789" 
                                       value="<?php echo htmlspecialchars($old['contact_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>

                            <!-- Physical Street Address -->
                            <div class="col-12">
                                <label for="address">Complete Street Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       placeholder="e.g. 123 Rizal Street, Barangay Central" 
                                       value="<?php echo htmlspecialchars($old['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description">Property Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" 
                                          placeholder="Provide an overview of your boarding house facilities, nearby locations, or terms..."><?php echo htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>

                            <!-- Amenities -->
                            <div class="col-12">
                                <label for="amenities">Property Amenities</label>
                                <input type="text" class="form-control" id="amenities" name="amenities" 
                                       placeholder="e.g. WiFi, Kitchen Access, CCTV Security, Shared Lounge"
                                       value="<?php echo htmlspecialchars($old['amenities'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="form-text text-muted small" style="font-size: 0.75rem;">Separate with commas.</div>
                            </div>

                            <!-- House Rules -->
                            <div class="col-12">
                                <label for="house_rules">House Rules</label>
                                <textarea class="form-control" id="house_rules" name="house_rules" rows="2" 
                                          placeholder="e.g. No smoking, Curfew at 10 PM, Visitors allowed until 8 PM only"><?php echo htmlspecialchars($old['house_rules'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>

                        </div>
                    </div>

                    <!-- Footer Submission Buttons -->
                    <div class="card-footer bg-transparent border-top border-light-subtle py-3 px-4 d-flex justify-content-end gap-2">
                        <a href="<?php echo BASE_URL; ?>/owner/dashboard" class="btn pt-2">Cancel</a>
                        <button type="submit" class="btn btn-dark btn-sm rounded-4">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<?php
// Include the standard footer template
require_once dirname(__DIR__) . '/templates/footer.php';
?>