<?php
/**
 * File Location: views/owner/view_application.php
 * File Name: view_application.php
 * Description: Detailed verification preview display for landlords to review tenant profiles and approve/reject bookings.
 * Optimized for flawless mobile screen adaptability and standard Bootstrap 5 layouts.
 */

// Include standard dynamic header
$title = "View Tenancy Application";
require_once dirname(__DIR__) . '/templates/header.php';
$app = $application ?? [];
?>

<style>
    /* Premium, minimalist responsive adjustments */
    .evaluation-header {
        letter-spacing: 0.05em;
    }
    
    .document-preview-frame {
        height: 380px;
        transition: height 0.2s ease-in-out;
    }

    /* Target small and medium mobile viewports dynamically */
    @media (max-width: 767.98px) {
        .document-preview-frame {
            height: 280px !important;
        }
        
        .badge-status-pill {
            width: 100%;
            text-align: center;
            display: block;
        }
        
        .action-button-container {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>

<div class="container my-4">
    
    <!-- Top Nav links -->
    <div class="mb-4">
        <a href="<?php echo BASE_URL; ?>/owner/applications" class="text-decoration-none text-dark small fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Return to Bookings Inbox
        </a>
    </div>

    <!-- Page Title Header block -->
    <div class="border-bottom border-light-subtle pb-4 mb-3">
        <div class="d-flex justify-content-between align-items-start align-items-md-center flex-column flex-md-row gap-3">
            <div>
                <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Tenant Evaluation Portal</span>
                <h1 class="h2 fw-bold text-dark mb-1">Tenancy Booking Inspector</h1>
                <p class="text-muted mb-0 small">Applicant: <strong><?php echo htmlspecialchars($app['firstname'] . ' ' . $app['lastname'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
            </div>
            
            <!-- State Header indicator badge -->
            <div class="w-auto">
                <?php if ($app['status'] === 'Approved'): ?>
                    <span class="badge bg-success text-white py-2 px-4 rounded-pill font-monospace shadow-sm">
                        Approved Tenant
                    </span>
                <?php elseif ($app['status'] === 'Rejected'): ?>
                    <span class="badge bg-danger text-white py-2 px-4 rounded-pill font-monospace shadow-sm">
                        Rejected Application
                    </span>
                <?php else: ?>
                    <span class="badge bg-secondary text-white py-2 px-4 rounded-pill font-monospace shadow-sm">
                        Evaluation Pending
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Alert Notifications inside the page -->
    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #fff5f5; color: #c53030;" role="alert">
            <i class="fa-solid fa-circle-exclamation me-3"></i>
            <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Two-column responsive inspection framework -->
    <div class="row g-4">
        
        <!-- COLUMN 1: Profile Specifications Dossier -->
        <div class="col-lg-5 col-12">
            <h5 class="fw-bold text-dark mb-3">Applicant Specifications</h5>
            
            <div class="card border border-light-subtle rounded-3 bg-white shadow-sm p-4 mb-4">
                
                <!-- Section 1: Chosen Room Layout Parameters -->
                <h6 class="fw-bold text-uppercase text-muted tracking-wider mb-3" style="font-size: 0.75rem;">1. Requested Room Layout</h6>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <span class="text-muted small d-block">Target Boarding House</span>
                        <span class="fw-bold text-dark" style="font-size: 1.05rem;"><?php echo htmlspecialchars($app['house_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="text-muted small d-block" style="font-size: 0.8rem;"><i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($app['house_town'] . ' - ' . $app['house_address'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-md-6 col-12">
                        <span class="text-muted small d-block">Room Layout Selected</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($app['room_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-md-6 col-12">
                        <span class="text-muted small d-block">Monthly Rent Amount</span>
                        <span class="fw-bold text-dark">₱<?php echo number_format($app['room_price'], 2); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Remaining Bed Vacancies left</span>
                        <span class="fw-semibold text-dark"><?php echo (int)$app['available_beds']; ?> vacant bed slots</span>
                    </div>
                </div>

                <!-- Section 2: Personal Dossier Coordinates -->
                <h6 class="fw-bold text-uppercase text-muted tracking-wider mb-3" style="font-size: 0.75rem; border-top: 1px solid #eaeaea; padding-top: 1.5rem;">2. Renter Personal Profile</h6>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <span class="text-muted small d-block">Full Legal Name</span>
                        <span class="fw-semibold text-dark">
                            <?php 
                            echo htmlspecialchars($app['firstname'], ENT_QUOTES, 'UTF-8') . ' ' . 
                                 (!empty($app['middlename']) ? htmlspecialchars($app['middlename'] . ' ', ENT_QUOTES, 'UTF-8') : '') . 
                                 htmlspecialchars($app['lastname'], ENT_QUOTES, 'UTF-8'); 
                            ?>
                        </span>
                    </div>
                    <div class="col-md-4 col-12">
                        <span class="text-muted small d-block">Current Age</span>
                        <span class="fw-semibold text-dark"><?php echo (int)$app['age']; ?> years old</span>
                    </div>
                    <div class="col-md-8 col-12">
                        <span class="text-muted small d-block">Primary Contact Number</span>
                        <span class="fw-semibold text-dark font-monospace"><?php echo htmlspecialchars($app['contact_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Primary Email Address</span>
                        <span class="fw-semibold text-dark text-break"><?php echo htmlspecialchars($app['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Permanent Address Coordinates</span>
                        <span class="fw-semibold text-dark small text-break"><?php echo htmlspecialchars($app['permanent_address'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>

                <!-- Section 3: Emergency Contact Protocols -->
                <h6 class="fw-bold text-uppercase text-muted tracking-wider mb-3" style="font-size: 0.75rem; border-top: 1px solid #eaeaea; padding-top: 1.5rem;">3. Emergency Contact Protocol</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <span class="text-muted small d-block">Contact Full Name</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($app['emergency_fullname'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Contact Phone Line</span>
                        <span class="fw-semibold text-dark font-monospace"><?php echo htmlspecialchars($app['emergency_contact_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>

            </div>

        </div>

        <!-- COLUMN 2: Verification Media Inspector -->
        <div class="col-lg-7 col-12">
            <h5 class="fw-bold text-dark mb-3">Verification ID Inspector</h5>
            
            <!-- Document 1: Tenant Primary Government/Student ID -->
            <div class="card border border-light-subtle rounded-3 bg-white shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-bottom border-light-subtle d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <span class="fw-bold text-dark small"><i class="fa-solid fa-id-card me-2"></i>Tenant Primary Verification Identity ID</span>
                    <a href="<?php echo BASE_URL . '/public/' . htmlspecialchars($app['verification_id_path'], ENT_QUOTES, 'UTF-8'); ?>" 
                       target="_blank" 
                       class="btn btn-light btn-sm border rounded-1 small text-nowrap w-auto w-sm-auto">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Fullscreen
                    </a>
                </div>
                <div class="card-body bg-light p-3 text-center">
                    <?php 
                    $tenantExt = strtolower(pathinfo($app['verification_id_path'], PATHINFO_EXTENSION));
                    if ($tenantExt === 'pdf'): 
                    ?>
                        <!-- Embed iframe document layout -->
                        <div class="ratio ratio-4x3 border rounded bg-white shadow-sm document-preview-frame">
                            <iframe src="<?php echo BASE_URL . '/public/' . htmlspecialchars($app['verification_id_path'], ENT_QUOTES, 'UTF-8'); ?>" allowfullscreen></iframe>
                        </div>
                    <?php else: ?>
                        <!-- Standard image output display -->
                        <img src="<?php echo BASE_URL . '/public/' . htmlspecialchars($app['verification_id_path'], ENT_QUOTES, 'UTF-8'); ?>" 
                             class="img-fluid border rounded shadow-sm" 
                             style="max-height: 350px; object-fit: contain;" 
                             alt="Tenant Primary Verification Document Scan">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Document 2: Emergency Contact Identity ID -->
            <div class="card border border-light-subtle rounded-3 bg-white shadow-sm overflow-hidden mb-4">
                <div class="card-header bg-white py-3 border-bottom border-light-subtle d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <span class="fw-bold text-dark small"><i class="fa-solid fa-id-card-clip me-2"></i>Emergency Person Identity ID</span>
                    <a href="<?php echo BASE_URL . '/public/' . htmlspecialchars($app['emergency_verification_id_path'], ENT_QUOTES, 'UTF-8'); ?>" 
                       target="_blank" 
                       class="btn btn-light btn-sm border rounded-1 small text-nowrap w-sm-100 w-md-auto">
                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Fullscreen
                    </a>
                </div>
                <div class="card-body bg-light p-3 text-center">
                    <?php 
                    $emergencyExt = strtolower(pathinfo($app['emergency_verification_id_path'], PATHINFO_EXTENSION));
                    if ($emergencyExt === 'pdf'): 
                    ?>
                        <!-- Embed iframe document layout -->
                        <div class="ratio ratio-4x3 border rounded bg-white shadow-sm document-preview-frame">
                            <iframe src="<?php echo BASE_URL . '/public/' . htmlspecialchars($app['emergency_verification_id_path'], ENT_QUOTES, 'UTF-8'); ?>" allowfullscreen></iframe>
                        </div>
                    <?php else: ?>
                        <!-- Standard image output display -->
                        <img src="<?php echo BASE_URL . '/public/' . htmlspecialchars($app['emergency_verification_id_path'], ENT_QUOTES, 'UTF-8'); ?>" 
                             class="img-fluid border rounded shadow-sm" 
                             style="max-height: 350px; object-fit: contain;" 
                             alt="Emergency Contact Person Verification Document Scan">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Landlord Actions Area (Shown only if application is currently sitting at 'Pending' status) -->
            <?php if ($app['status'] === 'Pending'): ?>
                <div class="card border border-light-subtle rounded-3 bg-white shadow-sm p-4">
                    <h6 class="fw-bold text-uppercase text-muted tracking-wider mb-2" style="font-size: 0.75rem;">4. Adjudication Decision Panel</h6>
                    <p class="text-muted small mb-4">Please verify the files uploaded by the tenant on the right column. Verify bed capacity before executing approvals.</p>
                    
                    <div class="row g-3">
                        <div class="col-md-6 col-12">
                            <!-- Rejection Form -->
                            <form action="<?php echo BASE_URL; ?>/owner/application/reject" method="POST" onsubmit="return confirm('Are you sure you want to REJECT this tenancy application?');">
                                <?php echo \App\Core\Security::csrfField(); ?>
                                <input type="hidden" name="application_id" value="<?php echo (int)$app['id']; ?>">
                                <button type="submit" class="btn btn-outline-danger w-100 py-2 rounded-1 small fw-bold">
                                    Reject Application
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6 col-12">
                            <!-- Approval Form -->
                            <form action="<?php echo BASE_URL; ?>/owner/application/approve" method="POST" onsubmit="return confirm('Are you sure you want to APPROVE this application? Approving will decrement available beds in this room.');">
                                <?php echo \App\Core\Security::csrfField(); ?>
                                <input type="hidden" name="application_id" value="<?php echo (int)$app['id']; ?>">
                                <button type="submit" class="btn btn-dark w-100 py-2 rounded-1 small fw-bold">
                                    Approve Application
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        
    </div>
</div>

<?php 
// Include standard footer template
require_once dirname(__DIR__) . '/templates/footer.php'; 
?>