<?php
/**
 * File Location: views/tenant/view_house.php
 * File Name: view_house.php
 * Description: Clean split visual detailed inspector panel for Tenants to explore a property.
 */
$title = "View House";
require_once dirname(__DIR__) . '/templates/header.php';
?>

<div class="container my-5 mt-4">
    
    <!-- Top breadcrumb -->
    <div class="mb-4">
        <a href="<?php echo BASE_URL; ?>/tenant/browse" class="text-decoration-none text-dark small fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Return to Browsing Catalog
        </a>
    </div>

    <!-- Main visual brand header block -->
    <div class="border-bottom border-light-subtle pb-4 mb-3">
        <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Property Portfolio Inspector</span>
        <h1 class="h2 fw-bold text-dark mb-1"><?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="text-muted mb-0 small">
            <i class="fa-solid fa-location-dot me-1 text-danger"></i><?php echo htmlspecialchars($house['town'] . ' - ' . $house['address'], ENT_QUOTES, 'UTF-8'); ?>
        </p>
    </div>

    <div class="row mb-3 pb-3 border-bottom border-white-subtle">
        <div class="col-md-5 px-0 m-0">
            <!-- Landlord contact parameters -->
            <div class="card border-0 bg-transparent p-4">
                <h6 class="fw-bold text-uppercase tracking-wider mb-3" style="font-size: 0.75rem;"><i class="fa-solid fa-user-tie me-2"></i>Direct Landlord Contact Details</h6>
                <div class="row g-3">
                    <div class="col-12">
                        <span class="text-muted small d-block">Authorized Landlord</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['owner_firstname'] . ' ' . $house['owner_lastname'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Primary Contact Number</span>
                        <span class="fw-semibold text-dark font-monospace"><?php echo htmlspecialchars($house['contact_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="col-12">
                        <span class="text-muted small d-block">Support Mail Profile</span>
                        <span class="fw-semibold text-dark"><?php echo htmlspecialchars($house['owner_email'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 bg-light d-flex align-items-center justify-content-center px-3 m-0" style="min-height: 180px;">
            <?php if (!empty($house['image_path'])): ?>
                <img src="<?php echo BASE_URL . '/public/' . htmlspecialchars($house['image_path'], ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid w-100" style="height: 250px;" alt="Room Photo Layout">
            <?php else: ?>
                <div class="text-center text-muted p-3">
                    <i class="fa-solid fa-bed fs-1 d-block mb-2 opacity-30"></i>
                    <span class="small opacity-50" style="font-size: 0.7rem;">No Image Provided</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <!-- COLUMN 1: Photo Previews and dynamic Room Offerings cards -->
        <div class="col-lg-7 col-12 py-md-3">

            <!-- Rooms catalog table list -->
            <h5 class="fw-bold text-dark mb-3">Room Accommodations Catalog</h5>
            
            <?php if (empty($rooms)): ?>
                <div class="card border border-light-subtle rounded-3 bg-white shadow-sm py-5 text-center">
                    <div class="card-body">
                        <i class="fa-solid fa-door-closed text-muted fs-1 mb-3 opacity-50"></i>
                        <h6 class="fw-semibold text-dark">No Active Rooms Found</h6>
                        <p class="text-muted small mb-0">The landlord has not configured room layouts for this property yet.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-4 mb-3">
                    <?php foreach ($rooms as $room): ?>
                        <div class="card border border-light-subtle rounded-3 bg-white shadow-sm overflow-hidden" style="transition: all 0.2s ease-in-out;">
                            <div class="row g-0">
                                
                                <!-- Room Thumbnail Visual preview layout -->
                                <div class="col-md-4 bg-light d-flex align-items-center justify-content-center border-end border-light-subtle" style="min-height: 180px;">
                                    <?php if (!empty($room['image_path'])): ?>
                                        <img src="<?php echo BASE_URL . '/public/' . htmlspecialchars($room['image_path'], ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid h-100 w-100" style="object-fit: cover;" alt="Room Photo Layout">
                                    <?php else: ?>
                                        <div class="text-center text-muted p-3">
                                            <i class="fa-solid fa-bed fs-1 d-block mb-2 opacity-30"></i>
                                            <span class="small opacity-50" style="font-size: 0.7rem;">No Image Provided</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Room description parameters -->
                                <div class="col-md-8">
                                    <div class="card-body p-4 h-100 d-flex flex-column">
                                        
                                        <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($room['room_name'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                                <span class="badge bg-light text-dark border font-monospace mt-1" style="font-size: 0.70em;">
                                                    Max Capacity: <?php echo (int)$room['capacity']; ?> occupants
                                                </span>
                                            </div>
                                            
                                            <!-- Availability state metrics tags -->
                                            <div>
                                                <?php if ($room['status'] === 'Maintenance'): ?>
                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle py-1 px-2 rounded-1 font-monospace" style="font-size: 0.69rem;">
                                                        Maintenance
                                                    </span>
                                                <?php elseif ((int)$room['available_beds'] > 0): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2 rounded-1 font-monospace" style="font-size: 0.69rem;">
                                                        <?php echo (int)$room['available_beds']; ?> bed/s vacant
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1 px-2 rounded-1 font-monospace" style="font-size: 0.69rem;">
                                                        Fully Occupied
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Room-specific amenities -->
                                        <div class="my-3">
                                            <span class="d-block small fw-semibold" style="font-size: 0.70rem;">Room Amenities</span>
                                            <?php if (!empty($room['amenities'])): ?>
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    <?php foreach (explode(',', $room['amenities']) as $amenity): ?>
                                                        <span class="badge bg-white text-dark border rounded-1 py-1 px-2 font-monospace" style="font-size: 0.70em;">
                                                            <?php echo htmlspecialchars(trim($amenity), ENT_QUOTES, 'UTF-8'); ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small italic" style="font-size: 0.7rem;">No room-specific amenities logged.</span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Bottom Pricing Rate Tag -->
                                        <div class="mt-auto pt-3 border-top border-light-subtle d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="fw-bold d-block" style="font-size: 0.70rem;">Monthly Rent</span>
                                                <span class="fw-bold text-dark fs-5">₱<?php echo number_format($room['price'], 2); ?></span>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        </div>

        <!-- COLUMN 2: Details, Amenities, Rules and Landlord details -->
        <div class="col-lg-5 col-12">
            <div class="sticky-md-top py-md-3">
                <!-- Description panel -->
                <h5 class="fw-bold text-dark mb-3">Accommodations Overview</h5>
                <div class="card border border-light-subtle rounded-3 bg-white shadow-sm p-4 mb-3">
                    <p class="text-dark small leading-relaxed mb-0">
                        <?php echo nl2br(htmlspecialchars($house['description'] ?: 'No primary overview coordinates provided for this boarding house.', ENT_QUOTES, 'UTF-8')); ?>
                    </p>
                </div>

                <!-- Rules & Amenities logs -->
                <div class="card border border-light-subtle rounded-3 bg-white shadow-sm p-4">
                    <h6 class="fw-bold text-uppercase tracking-wider mb-3" style="font-size: 0.75rem;"><i class="fa-solid fa-list-check me-2"></i>Rules & Building Facilities</h6>
                    <div class="mb-4">
                        <span class="text-muted fw-semibold small d-block mb-2">Building Amenities</span>
                        <?php if (!empty($house['amenities'])): ?>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach (explode(',', $house['amenities']) as $amenity): ?>
                                    <span class="badge bg-light text-dark border rounded-1 py-1 px-2 font-monospace" style="font-size: 0.7rem;">
                                        <?php echo htmlspecialchars(trim($amenity), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <span class="text-muted small italic">No standard building facilities defined.</span>
                        <?php endif; ?>
                    </div>

                    <div class="border-top border-light-subtle pt-3">
                        <span class="fw-semibold small d-block mb-1">Boarding House Rules</span>
                        <p class="text-dark small leading-relaxed mb-0">
                            <?php echo nl2br(htmlspecialchars($house['house_rules'] ?: 'No boarding restrictions compiled.', ENT_QUOTES, 'UTF-8')); ?>
                        </p>
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="d-flex justify-content-center d-block mt-4 gap-2">
                    <a href="<?php echo BASE_URL; ?>/tenant/house/inquire/<?php echo (int)$house['id']; ?>" class="btn btn-dark btn-lg w-100 text-decoration-none d-flex align-items-center justify-content-center">Inquire</a>
                    <a href="<?php echo BASE_URL; ?>/tenant/house/apply/<?php echo (int)$house['id']; ?>" class="btn btn-dark btn-lg w-100 text-decoration-none d-flex align-items-center justify-content-center">Apply</a>
                </div>  
            </div>  
        </div>

    </div>

</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>