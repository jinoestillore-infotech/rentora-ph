<?php
/**
 * File Location: views/owner/dashboard.php
 * File Name: dashboard.php
 * Description: Clean, professional, and modern card-based Owner Dashboard with integrated property image previews.
 */

// Include the standard clean header template
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
</style>

<div class="container my-5">
    <!-- Welcome Header banner -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center border-bottom border-light-subtle pb-4 mb-5">
        <div>
            <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Dashboard Portal</span>
            <h1 class="h2 fw-bold text-dark mb-1">Welcome, <?php echo htmlspecialchars($_SESSION['firstname'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
            <p class="text-muted mb-0 small">Manage your boarding house properties, view approval records, and configure rooms.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/owner/add-house" class="btn btn-dark btn-sm mt-3 mt-md-0 px-4 py-2">
            <i class="fa-solid fa-plus me-2"></i>Register New House
        </a>
    </div>

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
    <div class="row g-4 mb-5">
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
                            <h5 class="fw-bold text-dark mb-2 text-truncate" title="<?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?>">
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
                                        <span class="text-muted fw-normal d-block small mt-0.5 text-truncate"><?php echo htmlspecialchars($house['address'], ENT_QUOTES, 'UTF-8'); ?></span>
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
                                <a href="<?php echo BASE_URL; ?>/owner/rooms?house_id=<?php echo (int)$house['id']; ?>" class="btn btn-dark btn-sm py-2 rounded-3">
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