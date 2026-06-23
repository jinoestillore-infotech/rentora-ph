<?php
/**
 * File Location: views/tenant/browse.php
 * File Name: browse.php
 * Description: Clean, modern monochrome layout for searching, filtering, and exploring boarding houses.
 */
$title = "Browse House";
require_once dirname(__DIR__) . '/templates/header.php';

$boholTowns = [
    'Alburquerque', 'Alicia', 'Anda', 'Antequera', 'Baclayon', 'Balilihan', 'Batuan', 'Bien Unido',
    'Bilar', 'Buenavista', 'Calape', 'Candijay', 'Carmen', 'Catigbian', 'Clarin', 'Corella', 'Cortes',
    'Dagohoy', 'Danao', 'Dauis', 'Dimiao', 'Duero', 'Garcia Hernandez', 'Getafe', 'Guindulman',
    'Inabanga', 'Jagna', 'Lila', 'Loay', 'Loboc', 'Loon', 'Mabini', 'Maribojoc', 'Panglao', 'Pilar',
    'President Carlos P. Garcia', 'Sagbayan', 'San Isidro', 'San Miguel', 'Sevilla', 'Sierra Bullones',
    'Sikatuna', 'Talibon', 'Trinidad', 'Tubigon', 'Ubay', 'Valencia', 'Tagbilaran City'
];

$activeTown = $_GET['town'] ?? 'ALL';
$activeSearch = $_GET['search'] ?? '';
$activeBeds = $_GET['min_beds'] ?? 0;
$activePrice = $_GET['max_price'] ?? '';
?>

<style>
    .browse-property-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border: 1px solid #eaeaea !important;
        background-color: #ffffff;
    }
    .browse-property-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.05) !important;
        border-color: #1a1a1a !important;
    }
    .browse-card-img-wrapper {
        position: relative;
        height: 190px;
        background-color: #fafafa;
        overflow: hidden;
        border-bottom: 1px solid #eaeaea;
    }
    .browse-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<div class="container my-5 mt-4">
    
    <!-- Title Section -->
    <div class="pb-3 mb-2">
        <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Explore Accommodations</span>
        <h1 class="h2 fw-bold text-dark mb-1">Find Your Next Room</h1>
        <p class="text-muted mb-0 small">Browse approved boarding houses, compare room pricing, and connect directly with local landlords.</p>
    </div>

    <!-- Dynamic Search and Filters Tool Card -->
    <div class="card shadow-sm border border-light-subtle rounded-3 bg-white p-4 mb-5">
        <form action="<?php echo BASE_URL; ?>/tenant/browse" method="GET" id="filterForm">
            <div class="row g-3">
                
                <!-- Query Text Search -->
                <div class="col-lg-4 col-md-6 col-12">
                    <label class="form-label small fw-semibold text-secondary">Search Keyword</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="e.g. WiFi, Cozy Suite, Rizal Street..." value="<?php echo htmlspecialchars($activeSearch, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>

                <!-- Town Municipality Select dropdown -->
                <div class="col-lg-3 col-md-6 col-12">
                    <label class="form-label small fw-semibold text-secondary">Town Location</label>
                    <select name="town" class="form-select">
                        <option value="ALL">All Towns (Bohol)</option>
                        <?php foreach ($boholTowns as $town): ?>
                            <option value="<?php echo htmlspecialchars($town, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $activeTown === $town ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($town, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Max pricing limits select dropdown -->
                <div class="col-lg-3 col-md-6 col-12">
                    <label class="form-label small fw-semibold text-secondary">Maximum Price (₱)</label>
                    <select name="max_price" class="form-select">
                        <option value="">Any Price Limit</option>
                        <option value="1500" <?php echo $activePrice === '1500' ? 'selected' : ''; ?>>₱1,500 and under</option>
                        <option value="2500" <?php echo $activePrice === '2500' ? 'selected' : ''; ?>>₱2,500 and under</option>
                        <option value="4000" <?php echo $activePrice === '4000' ? 'selected' : ''; ?>>₱4,000 and under</option>
                        <option value="6000" <?php echo $activePrice === '6000' ? 'selected' : ''; ?>>₱6,000 and under</option>
                    </select>
                </div>

                <!-- Minimum available beds selection -->
                <div class="col-lg-2 col-md-6 col-12">
                    <label class="form-label small fw-semibold text-secondary">Required Vacancies</label>
                    <select name="min_beds" class="form-select">
                        <option value="0" <?php echo (int)$activeBeds === 0 ? 'selected' : ''; ?>>Any Availability</option>
                        <option value="1" <?php echo (int)$activeBeds === 1 ? 'selected' : ''; ?>>At least 1 bed free</option>
                        <option value="2" <?php echo (int)$activeBeds === 2 ? 'selected' : ''; ?>>At least 2 beds free</option>
                        <option value="3" <?php echo (int)$activeBeds === 3 ? 'selected' : ''; ?>>3+ beds free</option>
                    </select>
                </div>

                <!-- Action Button submission triggers -->
                <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                    <a href="<?php echo BASE_URL; ?>/tenant/browse" class="btn btn-light btn-sm px-4 py-2 border">Reset Filters</a>
                    <button type="submit" class="btn btn-dark btn-sm px-4 py-2">Apply Filters</button>
                </div>

            </div>
        </form>
    </div>

    <!-- Grid Layout of Properties -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark mb-0">Listings</h5>
        <span class="badge bg-white text-dark border border-light-subtle py-2 px-3 rounded-1 small fw-semibold shadow-sm font-monospace">
            <?php echo count($properties); ?> Properties Found
        </span>
    </div>

    <?php if (empty($properties)): ?>
        <div class="card shadow-sm border border-light-subtle rounded-3 bg-white text-center py-5">
            <div class="card-body">
                <i class="fa-solid fa-magnifying-glass-minus text-muted fs-1 mb-3 opacity-50"></i>
                <h6 class="fw-semibold text-dark">No Matching Properties Found</h6>
                <p class="text-muted small mb-0">Try clearing your filters or testing other search terms.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($properties as $house): ?>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card browse-property-card h-100 shadow-sm rounded-4 overflow-hidden d-flex flex-column">
                        
                        <!-- Header image container with visual overlays -->
                        <div class="browse-card-img-wrapper">
                            <?php if (!empty($house['image_path'])): ?>
                                <img src="<?php echo BASE_URL . '/public/' . htmlspecialchars($house['image_path'], ENT_QUOTES, 'UTF-8'); ?>" class="browse-card-img" alt="<?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                    <i class="fa-solid fa-house-chimney fa-2x mb-2 opacity-50"></i>
                                    <span class="small opacity-75">No image uploaded</span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Bed vacancy count dynamic tag -->
                            <div class="position-absolute bottom-0 start-0 m-3">
                                <?php if ((int)$house['total_available_beds'] > 0): ?>
                                    <span class="badge bg-white text-dark border border-light-subtle shadow-sm py-1.5 px-3 rounded-pill small font-monospace fw-bold">
                                        <i class="fa-solid fa-bed text-success me-1"></i><?php echo (int)$house['total_available_beds']; ?> beds vacant
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-white text-danger border border-danger-subtle shadow-sm py-1.5 px-3 rounded-pill small font-monospace fw-bold">
                                        <i class="fa-solid fa-ban me-1"></i>Fully Occupied
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card metadata details -->
                        <div class="card-body p-4 flex-grow-1 d-flex flex-column">
                            
                            <div class="mb-2">
                                <span class="text-uppercase text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 0.05em;">
                                    <i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($house['town'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </div>

                            <h5 class="fw-bold text-dark mb-2 text-truncate" title="<?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </h5>

                            <p class="text-muted small mb-4 text-truncate-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 38px;">
                                <?php echo htmlspecialchars($house['description'] ?: 'No structural description logged for this property.', ENT_QUOTES, 'UTF-8'); ?>
                            </p>

                            <!-- Pricing summary meta tags -->
                            <div class="mt-auto border-top border-light-subtle pt-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted d-block small" style="font-size: 0.65rem;">Price Range</span>
                                    <?php if ($house['room_count'] > 0 && isset($house['min_price'])): ?>
                                        <span class="fw-bold text-dark">
                                            ₱<?php echo number_format($house['min_price'], 0); ?> - ₱<?php echo number_format($house['max_price'], 0); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small italic">No rooms listed</span>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-light text-dark border rounded-pill py-1.5 px-2.5 font-monospace" style="font-size: 0.7rem;">
                                    <?php echo (int)$house['room_count']; ?> rooms
                                </span>
                            </div>

                        </div>

                        <!-- View detail navigation hook -->
                        <div class="bg-transparent border-top border-light-subtle p-3 d-grid">
                            <a href="<?php echo BASE_URL; ?>/tenant/house/view/<?php echo (int)$house['id']; ?>" class="btn btn-dark btn-sm py-2 rounded-3">
                                View Rooms & Landlord
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>