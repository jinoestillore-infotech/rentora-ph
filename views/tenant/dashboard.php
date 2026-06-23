<?php
/**
 * File Location: views/tenant/dashboard.php
 * File Name: dashboard.php
 * Description: Premium, minimalist landing panel for registered Tenants to browse summaries, explore Bohol town shortcuts, and view recent entries.
 */
$title = "Dashboard";
require_once dirname(__DIR__) . '/templates/header.php';

$stats = $stats ?? ['total_houses' => 0, 'total_towns' => 0, 'total_vacant_beds' => 0];
$recent = $recent ?? [];

$shortcutTowns = ['Tagbilaran City', 'Panglao', 'Tubigon', 'Ubay', 'Inabanga', 'Jagna'];
?>

<style>
    /* Monochrome Hover cards and shortcuts */
    .dashboard-shortcut-card {
        border: 1px solid #eaeaea !important;
        background-color: #ffffff;
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .dashboard-shortcut-card:hover {
        transform: translateY(-2px);
        border-color: #1a1a1a !important;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04) !important;
    }
    .recent-property-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border: 1px solid #eaeaea !important;
        background-color: #ffffff;
    }
    .recent-property-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.05) !important;
        border-color: #1a1a1a !important;
    }
    .card-img-wrapper {
        position: relative;
        height: 180px;
        background-color: #fafafa;
        overflow: hidden;
        border-bottom: 1px solid #eaeaea;
    }
    .card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<div class="container my-5">
    
    <!-- Hero Welcoming Area -->
    <div class="card border-0 rounded-4 bg-dark text-white p-4 p-sm-5 mb-5 shadow-sm overflow-hidden position-relative">
        <div style="position: absolute; right: 2%; bottom: 5%; opacity: 0.05; pointer-events: none;">
            <i class="fa-solid fa-house-chimney fa-10x"></i>
        </div>
        <div class="row align-items-center">
            <div class="col-lg-8 col-12">
                <span class="text-uppercase text-white-50 fw-bold small tracking-wider" style="font-size: 0.75rem; letter-spacing: 0.1em;">Find Your Sanctuary</span>
                <h1 class="display-5 fw-bold text-white mt-1 mb-3">Welcome, <?php echo htmlspecialchars($_SESSION['firstname'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
                <p class="lead text-white-50 small mb-4" style="max-width: 600px;">Explore verified boarding houses, review available configurations, and connect directly with partner landlords in Bohol.</p>
                <a href="<?php echo BASE_URL; ?>/tenant/browse" class="btn btn-light btn-lg px-4 fs-6 py-2.5 rounded-3 fw-bold shadow-sm">
                    <i class="fa-solid fa-magnifying-glass me-2"></i>Browse Catalog Space
                </a>
            </div>
        </div>
    </div>

    <!-- Feedback Alerts -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-5" style="background-color: #fff5f5; color: #c53030;" role="alert">
            <i class="fa-solid fa-circle-exclamation me-3"></i>
            <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Platform Stats Counters -->
    <div class="row g-4 mb-5">
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm border border-light-subtle rounded-3 bg-white h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-light border rounded-3 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-hotel text-dark fs-4"></i>
                    </div>
                    <div>
                        <span class="text-uppercase text-muted fw-semibold d-block" style="font-size: 0.65rem; letter-spacing: 0.05em;">Approved Accommodations</span>
                        <h4 class="fw-bold text-dark mb-0 mt-0.5"><?php echo (int)$stats['total_houses']; ?> Active Properties</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card shadow-sm border border-light-subtle rounded-3 bg-white h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-light border rounded-3 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-map-location-dot text-dark fs-4"></i>
                    </div>
                    <div>
                        <span class="text-uppercase text-muted fw-semibold d-block" style="font-size: 0.65rem; letter-spacing: 0.05em;">Municipal Presence</span>
                        <h4 class="fw-bold text-dark mb-0 mt-0.5"><?php echo (int)$stats['total_towns']; ?> Bohol Towns</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="card shadow-sm border border-light-subtle rounded-3 bg-white h-100">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="bg-success-subtle border border-success-subtle rounded-3 p-3 me-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-bed text-success fs-4"></i>
                    </div>
                    <div>
                        <span class="text-uppercase text-muted fw-semibold d-block" style="font-size: 0.65rem; letter-spacing: 0.05em;">Available Vacancies</span>
                        <h4 class="fw-bold text-success mb-0 mt-0.5"><?php echo (int)$stats['total_vacant_beds']; ?> Beds Vacant</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Location Shortcuts -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-dark mb-0">Explore Top Bohol Locations</h5>
            <a href="<?php echo BASE_URL; ?>/tenant/browse" class="text-decoration-none text-dark small fw-semibold">View All Locations <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-3">
            <?php foreach ($shortcutTowns as $town): ?>
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="<?php echo BASE_URL; ?>/tenant/browse?town=<?php echo urlencode($town); ?>" class="card text-decoration-none dashboard-shortcut-card rounded-3 p-3 text-center h-100 d-flex flex-column align-items-center justify-content-center">
                        <i class="fa-solid fa-mountain-city text-muted mb-2 fs-5"></i>
                        <span class="fw-bold text-dark small d-block"><?php echo htmlspecialchars($town, ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="text-muted" style="font-size: 0.65rem;">Explore Properties</span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recently Added Properties -->
    <div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-clock-rotate-left me-2"></i>Recently Added Accommodations</h5>
            <span class="badge bg-white text-dark border border-light-subtle py-2 px-3 rounded-1 small fw-semibold font-monospace">Latest Listings</span>
        </div>

        <?php if (empty($recent)): ?>
            <div class="card shadow-sm border border-light-subtle rounded-3 bg-white text-center py-5">
                <div class="card-body">
                    <i class="fa-solid fa-folder-open text-muted fs-1 mb-3 opacity-50"></i>
                    <h6 class="fw-semibold text-dark">No Listings Available</h6>
                    <p class="text-muted small mb-0">Our landlords are currently setting up layouts. Please check back soon!</p>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($recent as $house): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card recent-property-card h-100 shadow-sm rounded-4 overflow-hidden d-flex flex-column">
                            
                            <!-- Header frame with overlays -->
                            <div class="card-img-wrapper">
                                <?php if (!empty($house['image_path'])): ?>
                                    <img src="<?php echo BASE_URL . '/public/' . htmlspecialchars($house['image_path'], ENT_QUOTES, 'UTF-8'); ?>" class="card-img" alt="Exterior Facade Thumbnail">
                                <?php else: ?>
                                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                        <i class="fa-solid fa-house-chimney fa-2x mb-2 opacity-30"></i>
                                        <span class="small opacity-50" style="font-size: 0.75rem;">No Image Provided</span>
                                    </div>
                                <?php endif; ?>

                                <!-- Bed vacancy indicator badge -->
                                <div class="position-absolute bottom-0 start-0 m-3">
                                    <?php if ((int)$house['total_available_beds'] > 0): ?>
                                        <span class="badge bg-white text-dark border border-light-subtle shadow-sm py-1.5 px-3 rounded-pill small font-monospace fw-bold">
                                            <i class="fa-solid fa-bed text-success me-1"></i><?php echo (int)$house['total_available_beds']; ?> vacant
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-white text-danger border border-danger-subtle shadow-sm py-1.5 px-3 rounded-pill small font-monospace fw-bold">
                                            <i class="fa-solid fa-ban me-1"></i>Fully Occupied
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Context Metadata Details -->
                            <div class="card-body p-4 flex-grow-1 d-flex flex-column">
                                <div class="mb-2">
                                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.05em;">
                                        <i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($house['town'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>

                                <h6 class="fw-bold text-dark mb-2 text-truncate" title="<?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </h6>

                                <p class="text-muted small mb-4 text-truncate-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 38px;">
                                    <?php echo htmlspecialchars($house['description'] ?: 'No structural description has been defined for this property yet.', ENT_QUOTES, 'UTF-8'); ?>
                                </p>

                                <div class="mt-auto border-top border-light-subtle pt-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted d-block small" style="font-size: 0.65rem;">Price Ranges</span>
                                        <?php if ($house['room_count'] > 0 && isset($house['min_price'])): ?>
                                            <span class="fw-bold text-dark small">
                                                ₱<?php echo number_format($house['min_price'], 0); ?> - ₱<?php echo number_format($house['max_price'], 0); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small italic" style="font-size: 0.75rem;">No rooms listed</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="badge bg-light text-dark border rounded-pill py-1.5 px-2.5 font-monospace" style="font-size: 0.65rem;">
                                        <?php echo (int)$house['room_count']; ?> rooms
                                    </span>
                                </div>
                            </div>

                            <!-- Interactive Redirect trigger link -->
                            <div class="bg-transparent border-top border-light-subtle p-3 d-grid">
                                <a href="<?php echo BASE_URL; ?>/tenant/house/view/<?php echo (int)$house['id']; ?>" class="btn btn-dark btn-sm py-2 rounded-3">
                                    <i class="fa-solid fa-eye me-2"></i>Explore Boarding House
                                </a>
                            </div>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>