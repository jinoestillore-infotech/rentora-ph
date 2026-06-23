<?php
/**
 * File Location: views/admin/view_approved.php
 * File Name: view_approved.php
 * Description: Dynamic detailed specifications view of an approved asset with direct deletion hooks.
 */

use App\Core\Security;

require_once dirname(__DIR__) . '/templates/header.php';
$house = $house ?? [];
?>

<div class="container my-5 mt-4">
    
    <!-- Navigation Link -->
    <div class="mb-4">
        <a href="<?php echo BASE_URL; ?>/admin/approved-houses" class="text-decoration-none text-dark small fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Approved Properties Registry
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #fff5f5; color: #c53030;" role="alert">
            <i class="fa-solid fa-circle-exclamation me-3"></i>
            <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Header Frame -->
    <div class="border-bottom border-light-subtle pb-4 mb-5">
        <div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row gap-3">
            <div>
                <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Verified Platform Asset</span>
                <h1 class="h2 fw-bold text-dark mb-1"><?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="text-muted mb-0 small">Partner Owner: <strong><?php echo htmlspecialchars($house['firstname'] . ' ' . $house['lastname'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
            </div>
            
            <!-- Quick Deletion Trigger -->
            <button type="button" class="btn btn-outline-danger btn-sm py-2 px-4 rounded-1 fw-bold" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                <i class="fa-solid fa-trash-can me-2"></i>Delete Listing
            </button>
        </div>
    </div>

    <!-- Specifications Content Grid -->
    <div class="row g-5">
        
        <!-- Metadata Info -->
        <div class="col-lg-5 col-12">
            <h5 class="fw-bold text-dark mb-3">Listing Profile Parameters</h5>
            
            <div class="card border border-light-subtle rounded-1 bg-white shadow-sm p-4 mb-4">
                <h6 class="fw-bold text-uppercase text-muted tracking-wider mb-3" style="font-size: 0.75rem;">1. Location & Communication</h6>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <span class="text-muted small d-block">Town Municipality</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['town'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Physical Coordinates Address</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['address'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Listing Contact Number</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['contact_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>

                <h6 class="fw-bold text-uppercase text-muted tracking-wider mb-3" style="font-size: 0.75rem; border-top: 1px solid #eaeaea; padding-top: 1.5rem;">2. Rules & Structural Facilities</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <span class="text-muted small d-block">Amenities List</span>
                        <span class="text-dark small fw-medium"><?php echo htmlspecialchars($house['amenities'] ?: 'No amenities defined', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Standard House Rules</span>
                        <span class="text-dark small fw-medium"><?php echo htmlspecialchars($house['house_rules'] ?: 'No rules specified', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Owner Direct Information -->
            <div class="card border border-light-subtle rounded-1 bg-white shadow-sm p-4">
                <h6 class="fw-bold text-uppercase text-muted tracking-wider mb-3" style="font-size: 0.75rem;">3. Owner Credentials Details</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <span class="text-muted small d-block">Account Registered Name</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['firstname'] . ' ' . $house['lastname'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Primary Email Profile</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['owner_email'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Direct Contact Line</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['owner_contact'] ?: 'No alternate number', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Previews -->
        <div class="col-lg-7 col-12">
            <h5 class="fw-bold text-dark mb-3">Attached Media Inspector</h5>
            
            <!-- Facade Image -->
            <div class="card border border-light-subtle rounded-1 bg-white shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-bottom border-light-subtle">
                    <span class="fw-bold text-dark small"><i class="fa-solid fa-image me-2"></i>1. Primary Exterior Facade Preview</span>
                </div>
                <div class="card-body bg-light p-3 text-center">
                    <?php if (!empty($house['image_path'])): ?>
                        <img src="<?php echo BASE_URL . '/public/' . htmlspecialchars($house['image_path'], ENT_QUOTES, 'UTF-8'); ?>" 
                             class="img-fluid border rounded shadow-sm" 
                             style="max-height: 350px; object-fit: contain;" 
                             alt="Exterior Facade Image">
                    <?php else: ?>
                        <div class="py-5 text-muted">
                            <i class="fa-solid fa-image-slash fa-3x d-block mb-2 opacity-50"></i>
                            <span class="small d-block">No exterior image attached.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Business Permit Documents -->
            <div class="card border border-light-subtle rounded-1 bg-white shadow-sm overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom border-light-subtle d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark small"><i class="fa-solid fa-file-shield me-2"></i>2. Business Permit or Ownership Document</span>
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
                            <div class="ratio ratio-4x3 border rounded bg-white shadow-sm" style="height: 400px;">
                                <iframe src="<?php echo BASE_URL . '/public/' . htmlspecialchars($house['legality_proof'], ENT_QUOTES, 'UTF-8'); ?>" allowfullscreen></iframe>
                            </div>
                        <?php else: ?>
                            <img src="<?php echo BASE_URL . '/public/' . htmlspecialchars($house['legality_proof'], ENT_QUOTES, 'UTF-8'); ?>" 
                                 class="img-fluid border rounded shadow-sm" 
                                 style="max-height: 350px; object-fit: contain;" 
                                 alt="Business Permit Document">
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="py-5 text-muted">
                            <i class="fa-solid fa-file-circle-exclamation fa-3x d-block mb-2 opacity-50"></i>
                            <span class="small d-block">No legal proof documents provided.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deletion Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-1 border border-light-subtle">
            <div class="modal-header border-bottom border-light-subtle py-3">
                <h6 class="modal-title fw-bold text-dark" id="deleteModalLabel"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Confirm Deletion</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-dark small mb-2">Are you sure you want to permanently delete <strong><?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?></strong> from the platform?</p>
                <p class="text-muted small mb-0">This cascading action cannot be undone. All linked room configurations, photos, and verification documents will be deleted from the database and disk storage immediately.</p>
            </div>
            <div class="modal-footer border-top border-light-subtle py-2">
                <button type="button" class="btn btn-light btn-sm rounded-1" data-bs-dismiss="modal">Cancel</button>
                <form action="<?php echo BASE_URL; ?>/admin/approved-house/delete" method="POST" class="d-inline">
                    <?php echo Security::csrfField(); ?>
                    <input type="hidden" name="house_id" value="<?php echo (int)$house['id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm rounded-1 px-4">
                        Permanently Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>