<?php
/**
 * File Location: views/owner/dashboard.php
 * File Name: dashboard.php
 * Description: Clean, professional, and modern card-based Owner Dashboard optimized for mobile.
 */

// Include the standard clean header template
$title = "Dashboard";
require_once dirname(__DIR__) . '/templates/header.php';
?>

<style>
    /* Premium Hover Animation for Property Cards */
    .property-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border: 1px solid #eaeaea !important;
        background-color: #ffffff;
    }
    .property-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.04) !important;
        border-color: #948e8e !important;
    }
    .property-meta-icon {
        width: 24px;
        color: #6c757d;
    }
    /* Fixed Aspect Ratio Image Area */
    .property-card-image-wrapper {
        position: relative;
        height: 180px;
        background-color: #fafafa;
        overflow: hidden;
        border-bottom: 1px solid #eaeaea;
    }
    .property-card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Mobile Responsive Optimizations */
    @media (max-width: 575.98px) {
        .property-card-image-wrapper {
            height: 150px;
        }
        .dashboard-header-title {
            font-size: 1.5rem !important;
        }
        .property-card-title {
            font-size: 1.1rem !important;
        }
    }
</style>

<div class="container my-5">
    <!-- Welcome Header banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center border-bottom border-light-subtle pb-4 mb-4">
        <div>
            <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Dashboard Portal</span>
            <h1 class="h2 fw-bold text-dark mb-1 dashboard-header-title">Welcome, <?php echo htmlspecialchars($_SESSION['firstname'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
            <p class="text-muted mb-0 small">Manage your boarding house properties, view approval records, and configure rooms.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/owner/add-house" class="btn btn-dark btn-sm mt-3 mt-md-0 px-4 py-2">
            <i class="fa-solid fa-plus me-2"></i>Register New House
        </a>
    </div>

    <!-- Dynamic Rejection Action Required Alert Notification -->
    <?php 
    // Fall back to inline counting if $rejectedCount is not explicitly passed from the controller
    $rejectedCount = $rejectedCount ?? count(array_filter($properties ?? [], function($house) {
        return $house['status'] === 'Rejected';
    }));
    if ($rejectedCount > 0): 
    ?>
        <div class="col-12 mt-3 mb-4">
            <div class="card bg-danger-subtle border-0 rounded-3 shadow-sm">
                <div class="card-body p-3 p-sm-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-warning fs-3 mt-1">
                            <i class="fa-solid fa-circle-exclamation text-danger"></i>
                        </div>
                        <div>
                            <span class="text-uppercase text-danger fw-bold d-block small" style="letter-spacing: 0.05em; font-size: 0.75rem;">Action Required</span>
                            <h4 class="h6 fw-semibold text-dark mb-0 mt-1">You have <?php echo $rejectedCount; ?> rejected boarding house registration<?php echo $rejectedCount > 1 ? 's' : ''; ?>.</h4>
                            <p class="text-muted small mb-0 mt-1">Rejected files must be reviewed immediately. Uncorrected listings are automatically deleted from server storage 3 days after rejection.</p>
                        </div>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/owner/rejected-houses" class="btn btn-danger rounded-2 fw-bold py-2 px-4 shadow-sm text-nowrap align-self-stretch align-self-md-center text-light">
                        View Logs <i class="fa-solid fa-chevron-right ms-1 small"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Dynamic Pending Tenant Applications Alert Notification -->
    <?php 
    $pendingAppsCount = $pendingAppsCount ?? 0;
    if ($pendingAppsCount > 0): 
    ?>
        <div class="col-12 mt-3 mb-4">
            <div class="card bg-warning-subtle border-0 rounded-3 shadow-sm" style="background-color: #fff3cd !important; border: 1px solid #ffeeba !important;">
                <div class="card-body p-3 p-sm-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="text-warning fs-3 mt-1">
                            <i class="fa-solid fa-circle-exclamation text-warning" style="color: #856404 !important;"></i>
                        </div>
                        <div>
                            <span class="text-uppercase fw-bold d-block small" style="letter-spacing: 0.05em; font-size: 0.75rem; color: #856404;">Action Required</span>
                            <h4 class="h6 fw-semibold text-dark mb-0 mt-1">You have <?php echo $pendingAppsCount; ?> pending tenant application<?php echo $pendingAppsCount > 1 ? 's' : ''; ?> awaiting review.</h4>
                            <p class="text-muted small mb-0 mt-1">Inspect tenant identity verifications and assign room vacancies immediately to secure bookings.</p>
                        </div>
                    </div>
                    <a href="<?php echo BASE_URL; ?>/owner/applications" class="btn btn-warning rounded-2 fw-bold py-2 px-4 shadow-sm text-nowrap align-self-stretch align-self-md-center text-dark" style="background-color: #ffc107 !important; border-color: #ffc107 !important;">
                        Review Inbox <i class="fa-solid fa-chevron-right ms-1 small"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Alert Notifications -->
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

    <!-- Analytics / Summary Cards -->
    <div class="row g-3 mb-5">
        <!-- Stat Item 1 -->
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card shadow-sm border border-light-subtle rounded-1 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase text-muted fw-semibold small" style="font-size: 0.7rem;">My Properties</span>
                            <h3 class="fw-bold mt-1 mb-0 text-dark"><?php echo (int)($stats['total_properties'] ?? 0); ?></h3>
                        </div>
                        <div class="bg-light rounded-1 p-2 border border-light-subtle">
                            <i class="fa-solid fa-hotel text-muted fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stat Item 2 -->
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card shadow-sm border border-light-subtle rounded-1 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase text-muted fw-semibold small" style="font-size: 0.7rem;">Approved Listings</span>
                            <h3 class="fw-bold mt-1 mb-0 text-success"><?php echo (int)($stats['approved_properties'] ?? 0); ?></h3>
                        </div>
                        <div class="bg-success-subtle rounded-1 p-2 border border-success-subtle">
                            <i class="fa-solid fa-circle-check text-success fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stat Item 3 -->
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card shadow-sm border border-light-subtle rounded-1 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase text-muted fw-semibold small" style="font-size: 0.7rem;">Pending Verification</span>
                            <h3 class="fw-bold mt-1 mb-0 text-secondary"><?php echo (int)($stats['pending_properties'] ?? 0); ?></h3>
                        </div>
                        <div class="bg-light rounded-1 p-2 border border-light-subtle">
                            <i class="fa-solid fa-hourglass-half text-secondary fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stat Item 4 -->
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card shadow-sm border border-light-subtle rounded-1 h-100 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase text-muted fw-semibold small" style="font-size: 0.7rem;">Total Rooms Managed</span>
                            <h3 class="fw-bold mt-1 mb-0 text-dark"><?php echo (int)($stats['total_rooms'] ?? 0); ?></h3>
                        </div>
                        <div class="bg-light rounded-1 p-2 border border-light-subtle">
                            <i class="fa-solid fa-door-open text-muted fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Boarding Houses Grid Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark mb-0">
            Registered Properties
        </h5>
        <span class="badge bg-white text-dark border border-light-subtle py-2 px-3 rounded-1 small fw-semibold shadow-sm">
            <?php echo count($properties); ?> Properties
        </span>
    </div>

    <?php if (empty($properties)): ?>
        <div class="card shadow-sm border border-light-subtle rounded-1 bg-white text-center py-5">
            <div class="card-body">
                <i class="fa-solid fa-house-chimney text-muted fs-1 mb-3"></i>
                <h6 class="fw-semibold text-dark">No Properties Found</h6>
                <p class="text-muted small mb-3">You haven't registered any boarding houses with RENTORA PH yet.</p>
                <a href="<?php echo BASE_URL; ?>/owner/add-house" class="btn btn-dark btn-sm px-4">
                    Register Your First House
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4 mb-5">
            <?php foreach ($properties as $house): ?>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card property-card h-100 shadow-sm rounded-4 bg-white d-flex flex-column overflow-hidden">
                        
                        <!-- Visual Image Wrapper featuring uploads -->
                        <div class="property-card-image-wrapper">
                            <?php if (!empty($house['image_path'])): ?>
                                <img src="<?php echo BASE_URL . '/public/' . htmlspecialchars($house['image_path'], ENT_QUOTES, 'UTF-8'); ?>" 
                                     class="property-card-image" 
                                     alt="<?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                    <i class="fa-solid fa-house-chimney fa-2x mb-2 opacity-50"></i>
                                    <span class="small opacity-75">No Image Provided</span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Floating Status Badge inside Image frame -->
                            <div class="position-absolute top-0 end-0 m-3">
                                <?php if ($house['status'] === 'Approved'): ?>
                                    <span class="badge bg-white text-success border border-success-subtle py-1.5 px-3 rounded-pill shadow-sm small font-monospace">
                                        Approved
                                    </span>
                                <?php elseif ($house['status'] === 'Pending'): ?>
                                    <span class="badge bg-white text-secondary border border-secondary-subtle py-1.5 px-3 rounded-pill shadow-sm small font-monospace">
                                        Pending
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-white text-danger border border-danger-subtle py-1.5 px-3 rounded-pill shadow-sm small font-monospace">
                                        Rejected
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Body Details -->
                        <div class="card-body p-4 flex-grow-1">
                            <h5 class="fw-bold text-dark mb-2 text-truncate property-card-title" title="<?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </h5>

                            <!-- Property Short Description -->
                            <p class="text-muted small mb-4 text-truncate-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 38px;">
                                <?php echo htmlspecialchars($house['description'] ?: 'No description provided for this property.', ENT_QUOTES, 'UTF-8'); ?>
                            </p>

                            <!-- Property Metadata Info List -->
                            <div class="d-flex flex-column gap-2 mb-2">
                                <div class="d-flex align-items-start small">
                                    <span class="property-meta-icon"><i class="fa-solid fa-location-dot"></i></span>
                                    <span class="text-dark fw-medium text-truncate">
                                        <?php echo htmlspecialchars($house['town'], ENT_QUOTES, 'UTF-8'); ?> 
                                        <span class="text-muted fw-normal d-block small mt-1 text-truncate"><?php echo htmlspecialchars($house['address'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center small text-muted">
                                    <span class="property-meta-icon"><i class="fa-solid fa-phone"></i></span>
                                    <span><?php echo htmlspecialchars($house['contact_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="d-flex align-items-center small text-muted">
                                    <span class="property-meta-icon"><i class="fa-solid fa-door-open"></i></span>
                                    <span class="fw-semibold text-dark">
                                        <?php echo (int)$house['total_rooms']; ?> Rooms 
                                        <span class="text-muted fw-normal font-monospace">(<?php echo (int)($house['total_available_beds'] ?: 0); ?> beds free)</span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer actions -->
                        <div class="bg-transparent border-top border-light-subtle p-3 mt-auto d-grid">
                            <?php if ($house['status'] === 'Approved'): ?>
                                <a href="<?php echo BASE_URL; ?>/owner/rooms/<?php echo (int)$house['id']; ?>" class="btn btn-dark btn-sm py-2 rounded-3">
                                    <i class="fa-solid fa-door-open me-2"></i>Manage Rooms
                                </a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary btn-sm py-2 rounded-3" disabled title="Awaiting Administrator activation">
                                    <i class="fa-solid fa-lock me-2"></i>Locked (Review Pending)
                                </button>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
// Include the standard footer template
require_once dirname(__DIR__) . '/templates/footer.php';
?>