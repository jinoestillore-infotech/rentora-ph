<?php
/**
 * File Location: views/admin/verify_house.php
 * File Name: verify_house.php
 * Description: Dynamic split comparison verification display page designed for inspecting legality certifications.
 */

use App\Core\Security;

// Include standard header template
require_once dirname(__DIR__) . '/templates/header.php';
?>

<div class="container my-5 mt-4">
    
    <!-- Navigation Back Link -->
    <div class="mb-4">
        <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="text-decoration-none text-dark small fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Return to Verification Center
        </a>
    </div>

    <!-- Alert error notifications -->
    <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #fff5f5; color: #c53030;" role="alert">
            <i class="fa-solid fa-circle-exclamation me-3"></i>
            <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Title Section -->
    <div class="border-bottom border-light-subtle pb-4 mb-5">
        <div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row gap-3">
            <div>
                <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Onboarding File Review</span>
                <h1 class="h2 fw-bold text-dark mb-1 mt-3"><?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="text-muted mb-0 small">Submitted by Partner: <strong><?php echo htmlspecialchars($house['firstname'] . ' ' . $house['lastname'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
            </div>
            
            <!-- Floating Status Header Indicator -->
            <div>
                <?php if ($house['status'] === 'Approved'): ?>
                    <span class="badge bg-success text-white py-2 px-4 rounded-pill font-monospace shadow-sm">
                        Approved Asset
                    </span>
                <?php elseif ($house['status'] === 'Pending'): ?>
                    <span class="badge bg-secondary text-white py-2 px-4 rounded-pill font-monospace shadow-sm">
                        Verification Pending
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger text-white py-2 px-4 rounded-pill font-monospace shadow-sm">
                        Rejected Submission
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Responsive Split Review Layout Grid -->
    <div class="row g-5">
        
        <!-- COLUMN 1: Metadata Verification Form -->
        <div class="col-lg-5 col-12">
            <h5 class="fw-bold text-dark mb-">Onboarding Specifications</h5>
            
            <div class="card border border-light-subtle rounded-1 bg-white shadow-sm p-4 mb-4">
                
                <!-- Section 1: Physical Parameters -->
                <h6 class="fw-bold text-uppercase text-muted tracking-wider mb-3" style="font-size: 0.75rem;">1. Property Physical Profile</h6>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <span class="text-muted small d-block">Municipality / Location</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['town'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Street Physical Address</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['address'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Contact Phone Line</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['contact_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Amenities Registered</span>
                        <span class="text-dark small"><?php echo htmlspecialchars($house['amenities'] ?: 'None specified', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">House Rules Profile</span>
                        <span class="text-dark small"><?php echo htmlspecialchars($house['house_rules'] ?: 'None specified', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>

                <!-- Section 2: Owner Contact Parameters -->
                <h6 class="fw-bold text-uppercase text-muted tracking-wider mb-3" style="font-size: 0.75rem; border-top: 1px solid #eaeaea; padding-top: 1.5rem;">2. Property Owner Credentials</h6>
                <div class="row g-3 mb-2">
                    <div class="col-12">
                        <span class="text-muted small d-block">Account Full Name</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['firstname'] . ' ' . $house['lastname'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Portal Email Profile</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['owner_email'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Direct Mobile Contact</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['owner_contact'] ?: 'No alternate number', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>

            </div>

            <!-- Verification Action Form Block (Rendered if Status is Pending or to Allow Reevaluation) -->
            <div class="card border border-light-subtle rounded-1 bg-white shadow-sm p-4">
                <h6 class="fw-bold text-uppercase text-muted tracking-wider mb-3" style="font-size: 0.75rem;">3. Administrator Adjudication Gate</h6>
                <p class="text-muted small">Perform a strict inspection of the featured facade images and legality permits. Choose the appropriate action below.</p>
                
                <form action="<?php echo BASE_URL; ?>/admin/verify-house" method="POST" class="mt-4">
                    <!-- Strict anti-forgery layer -->
                    <?php echo Security::csrfField(); ?>
                    
                    <input type="hidden" name="house_id" value="<?php echo (int)$house['id']; ?>">
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <button type="submit" name="status_action" value="Rejected" class="btn btn-outline-danger w-100 py-2 rounded-1 small fw-bold">
                                Reject Listing
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="submit" name="status_action" value="Approved" class="btn btn-dark w-100 py-2 border border-dark border-2 rounded-1 small fw-bold">
                                Approve Listing
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        <!-- COLUMN 2: Verification Media Inspector -->
        <div class="col-lg-7 col-12">
            <h5 class="fw-bold text-dark mb-3">Media Document Verification Inspector</h5>
            
            <!-- Tab 1: Featured Facade Image Preview -->
            <div class="card border border-light-subtle rounded-1 bg-white shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-bottom border-light-subtle">
                    <span class="fw-bold text-dark small">1. Featured Facade Display Image</span>
                </div>
                <div class="card-body bg-light p-3 text-center">
                    <?php if (!empty($house['image_path'])): ?>
                        <img src="<?php echo BASE_URL . '/public/' . htmlspecialchars($house['image_path'], ENT_QUOTES, 'UTF-8'); ?>" 
                             class="img-fluid border rounded shadow-sm" 
                             style="max-height: 400px; object-fit: contain;" 
                             alt="Featured Facade Image Preview">
                    <?php else: ?>
                        <div class="py-5 text-muted">
                            <i class="fa-solid fa-house-chimney-crack fa-3x d-block mb-2 opacity-50"></i>
                            <span class="small d-block">No physical facade photo submitted.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tab 2: Legality Document Proof Preview -->
            <div class="card border border-light-subtle rounded-1 bg-white shadow-sm overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom border-light-subtle d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark small">2. Proof of Legality / Business Permit</span>
                    <?php if (!empty($house['legality_proof'])): ?>
                        <a href="<?php echo BASE_URL . '/public/' . htmlspecialchars($house['legality_proof'], ENT_QUOTES, 'UTF-8'); ?>" 
                           target="_blank" 
                           class="btn btn-light btn-sm rounded-1 border small">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Fullscreen
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body bg-light p-3 text-center">
                    <?php if (!empty($house['legality_proof'])): ?>
                        <?php 
                        $fileExtension = strtolower(pathinfo($house['legality_proof'], PATHINFO_EXTENSION));
                        if ($fileExtension === 'pdf'): 
                        ?>
                            <!-- PDF iframe embed renderer -->
                            <div class="ratio ratio-4x3 border rounded bg-white shadow-sm" style="height: 450px;">
                                <iframe src="<?php echo BASE_URL . '/public/' . htmlspecialchars($house['legality_proof'], ENT_QUOTES, 'UTF-8'); ?>" allowfullscreen></iframe>
                            </div>
                        <?php else: ?>
                            <!-- Image document render -->
                            <img src="<?php echo BASE_URL . '/public/' . htmlspecialchars($house['legality_proof'], ENT_QUOTES, 'UTF-8'); ?>" 
                                 class="img-fluid border rounded shadow-sm" 
                                 style="max-height: 400px; object-fit: contain;" 
                                 alt="Legality Verification Certificate Document">
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="py-5 text-muted">
                            <i class="fa-solid fa-file-circle-exclamation fa-3x d-block mb-2 opacity-50"></i>
                            <span class="small d-block">No legality permits submitted.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        
    </div>
</div>

<?php
// Include standard footer template
require_once dirname(__DIR__) . '/templates/footer.php';
?>