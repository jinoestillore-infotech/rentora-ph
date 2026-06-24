<?php
/**
 * File Location: views/tenant/applications.php
 * File Name: applications.php
 * Description: Clean, minimalist overview panel displaying the active, approved, or rejected applications submitted by a tenant. Fully optimized for mobile screens.
 */

$title = "Tenancy Application";
require_once dirname(__DIR__) . '/templates/header.php';
$applications = $applications ?? [];
?>

<style>
    /* Monochrome premium hover cards matching Rentora Ph style values */
    .application-status-card {
        border: 1px solid #eaeaea !important;
        background-color: #ffffff;
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    }
    .application-status-card:hover {
        transform: translateY(-3px);
        border-color: #1a1a1a !important;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04) !important;
    }
    .status-accent-badge {
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        font-weight: 700;
        text-transform: uppercase;
    }
    .meta-detail-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #888888;
        font-weight: 600;
    }
    
    /* Custom responsive borders and adjustments for compact mobile screens */
    @media (min-width: 576px) {
        .border-sm-start {
            border-left: 1px solid #dee2e6 !important;
        }
    }
    @media (max-width: 575.98px) {
        .mobile-gap-fix {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #dee2e6 !important;
        }
    }
</style>

<div class="container my-5 mt-4">
    
    <!-- Top return breadcrumb link -->
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <a href="<?php echo BASE_URL; ?>/tenant/dashboard" class="text-decoration-none text-dark small fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Return to Dashboard
        </a>
        <span class="badge bg-white text-dark border border-light-subtle py-2 px-3 rounded-1 small fw-semibold shadow-sm font-monospace align-self-start align-self-sm-auto">
            My Bookings Track
        </span>
    </div>

    <!-- Page Title Header block -->
    <div class="pb-3 mb-4 border-bottom border-light-subtle">
        <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Applications History Status</span>
        <h1 class="fs-3 fs-md-2 fw-bold text-dark mb-1">Track My Space Applications</h1>
        <p class="text-muted mb-0 small">Review the progress of your pending tenancy requests, verify room choices, and inspect direct feedback flags.</p>
    </div>

    <!-- Session Feedback Alerts -->
    <?php if (isset($success)): ?>
        <div class="alert alert-success d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #f0fff4; color: #22543d;" role="alert">
            <i class="fa-solid fa-circle-check me-3"></i>
            <span class="small"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #fff5f5; color: #c53030;" role="alert">
            <i class="fa-solid fa-circle-exclamation me-3"></i>
            <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Applications Grid layout -->
    <?php if (empty($applications)): ?>
        <div class="card border border-light-subtle rounded-3 bg-white text-center py-5 shadow-sm px-3">
            <div class="card-body">
                <i class="fa-solid fa-file-signature text-muted fs-1 mb-3 opacity-40"></i>
                <h6 class="fw-semibold text-dark">No Active Applications Found</h6>
                <p class="text-muted small mb-4 mx-auto" style="max-width: 450px;">
                    You have not submitted any boarding house requests yet. Head over to our catalog page to find approved vacancies!
                </p>
                <a href="<?php echo BASE_URL; ?>/tenant/browse" class="btn btn-dark btn-sm px-4 rounded-3 py-2">
                    <i class="fa-solid fa-magnifying-glass me-2"></i>Browse Space Catalog
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($applications as $app): ?>
                <div class="col-lg-6 col-12">
                    <div class="card application-status-card rounded-4 p-3 p-sm-4 shadow-sm h-100 d-flex flex-column justify-content-between">
                        
                        <div>
                            <!-- Header Frame with responsive layout stack -->
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start mb-3 gap-3">
                                <div class="pe-0 pe-sm-2">
                                    <h5 class="fw-bold text-dark mb-1 text-wrap" title="<?php echo htmlspecialchars($app['house_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($app['house_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </h5>
                                    <span class="text-muted small d-block">
                                        <i class="fa-solid fa-location-dot text-danger me-1"></i><?php echo htmlspecialchars($app['house_town'] . ' - ' . $app['house_address'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>
                                
                                <!-- Dynamic Status State Badges -->
                                <div class="align-self-start align-self-sm-auto">
                                    <?php if ($app['status'] === 'Approved'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle py-2 px-3 rounded-pill status-accent-badge d-inline-block">
                                            <i class="fa-solid fa-check-circle me-1"></i>Approved
                                        </span>
                                    <?php elseif ($app['status'] === 'Rejected'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-2 px-3 rounded-pill status-accent-badge d-inline-block">
                                            <i class="fa-solid fa-ban me-1"></i>Rejected
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle py-2 px-3 rounded-pill status-accent-badge d-inline-block" style="color: #856404 !important; border-color: #ffeeba !important; background-color: #fff3cd !important;">
                                            <i class="fa-solid fa-hourglass-half me-1"></i>Pending
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Room Configuration Details -->
                            <div class="p-3 border rounded-3 bg-light mb-4 mt-3">
                                <div class="row align-items-center g-2">
                                    <div class="col-12 col-sm-8">
                                        <span class="meta-detail-label d-block mb-1">Requested Accommodation Layout</span>
                                        <span class="fw-bold text-dark d-block text-wrap" style="font-size: 0.95rem;"><?php echo htmlspecialchars($app['room_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                    <div class="col-12 col-sm-4 text-start text-sm-end border-sm-start border-light-subtle ps-0 ps-sm-3 mobile-gap-fix">
                                        <span class="meta-detail-label d-block mb-1">Monthly Rent</span>
                                        <span class="fw-bold text-dark fs-5 font-monospace">₱<?php echo number_format($app['room_price'], 2); ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Applicant Identity coordinates -->
                            <div class="row g-3">
                                <div class="col-sm-6 col-12">
                                    <span class="meta-detail-label d-block">Applicant Name</span>
                                    <span class="text-dark small fw-semibold"><?php echo htmlspecialchars($app['firstname'] . ' ' . $app['lastname'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="col-sm-6 col-12">
                                    <span class="meta-detail-label d-block">Submission Date</span>
                                    <span class="text-muted small font-monospace"><?php echo date('M d, Y h:i A', strtotime($app['created_at'])); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Guides or Explanations -->
                        <div class="border-top border-light-subtle pt-3 mt-4">
                            <?php if ($app['status'] === 'Approved'): ?>
                                <div class="small d-flex align-items-start text-success" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-circle-check me-2 mt-0.5"></i>
                                    <span>Congrats! The owner has verified your dossier. Please prepare payment credentials.</span>
                                </div>
                            <?php elseif ($app['status'] === 'Rejected'): ?>
                                <div class="small d-flex align-items-start text-danger" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-circle-exclamation me-2 mt-0.5"></i>
                                    <span>Validation rejected. Please submit correct identity and verification logs next time.</span>
                                </div>
                            <?php else: ?>
                                <div class="small d-flex align-items-start text-muted" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-hourglass-half me-2 mt-0.5"></i>
                                    <span>Your profile dossier is currently sitting in the landlord’s pending evaluation console.</span>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>