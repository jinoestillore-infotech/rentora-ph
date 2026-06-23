<?php
/**
 * File Location: views/owner/edit_room.php
 * File Name: edit_room.php
 * Description: Standalone, modern, and minimalist edit page to adjust existing room characteristics.
 */
$title = "Edit Room";
require_once dirname(__DIR__) . '/templates/header.php';

// Safe variables lookup
$house = $house ?? [];
$room = $room ?? [];
$houseId = (int)($house['id'] ?? 0);
$roomId = (int)($room['id'] ?? 0);

$error = $_SESSION['error'] ?? null;
$old = $_SESSION['old_input'] ?? [];

// Clean flash scope
unset($_SESSION['error'], $_SESSION['old_input']);
?>

<div class="container my-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9 col-12">
            <!-- Top Return Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="<?php echo BASE_URL; ?>/owner/rooms/<?php echo $houseId; ?>" class="text-decoration-none text-dark small fw-semibold">
                    <i class="fa-solid fa-arrow-left me-2"></i>Back to House
                </a>
                <span class="badge bg-white text-dark border border-light-subtle py-2 px-3 rounded-1 small fw-semibold shadow-sm font-monospace">
                    Adjust Configuration
                </span>
            </div>

            <!-- Page Header Title -->
            <div class="pb-3 mb-2">
                <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Modify Room Details</span>
                <h1 class="h2 fw-bold text-dark mb-1">Edit Room Configurations</h1>
                <p class="text-muted mb-0 small">Modifying specifications for: <strong><?php echo htmlspecialchars($room['room_name'] ?? 'Loading...', ENT_QUOTES, 'UTF-8'); ?></strong></p>
            </div>

            <!-- Error Alerts -->
            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #fff5f5; color: #c53030;" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-3"></i>
                    <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border rounded-3 bg-white">
                <div class="bg-transparent border-bottom border-light-subtle p-4">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Room Specifications
                    </h5>
                    <p class="text-muted mb-0 small">You are free to alter rental parameters, amenities lists, or operational statuses here.</p>
                </div>

                <form action="<?php echo BASE_URL; ?>/owner/room/edit" method="POST" enctype="multipart/form-data">
                    <!-- Anti-forgery verification -->
                    <?php echo \App\Core\Security::csrfField(); ?>
                    <input type="hidden" name="boarding_house_id" value="<?php echo $houseId; ?>">
                    <input type="hidden" name="room_id" value="<?php echo $roomId; ?>">

                    <div class="card-body p-4">
                        <div class="row g-3">
                            
                            <!-- Room Name / Identifier -->
                            <div class="col-12">
                                <label for="room_name" class="form-label small fw-semibold text-secondary">Room Name or Identifier <span class="text-danger">*</span></label>
                                <input type="text" id="room_name" name="room_name" class="form-control" 
                                    value="<?php echo htmlspecialchars($old['room_name'] ?? $room['room_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>

                            <!-- Monthly Rental Price -->
                            <div class="col-md-6">
                                <label for="price" class="form-label small fw-semibold text-secondary">Monthly Rental Price (₱) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" id="price" name="price" class="form-control" 
                                    value="<?php echo htmlspecialchars($old['price'] ?? $room['price'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" min="1" required>
                            </div>

                            <!-- Operational Status -->
                            <div class="col-md-6">
                                <label for="status" class="form-label small fw-semibold text-secondary">Operational Status</label>
                                <select id="status" name="status" class="form-select">
                                    <?php 
                                    $activeStatus = $old['status'] ?? $room['status'] ?? 'Available';
                                    ?>
                                    <option value="Available" <?php echo ($activeStatus === 'Available') ? 'selected' : ''; ?>>Available</option>
                                    <option value="Fully Booked" <?php echo ($activeStatus === 'Fully Booked') ? 'selected' : ''; ?>>Fully Booked</option>
                                    <option value="Maintenance" <?php echo ($activeStatus === 'Maintenance') ? 'selected' : ''; ?>>Under Maintenance</option>
                                </select>
                            </div>

                            <!-- Maximum Bed Capacity -->
                            <div class="col-md-6">
                                <label for="capacity" class="form-label small fw-semibold text-secondary">Maximum Bed Capacity <span class="text-danger">*</span></label>
                                <input type="number" id="capacity" name="capacity" class="form-control" 
                                    value="<?php echo htmlspecialchars($old['capacity'] ?? $room['capacity'] ?? '1', ENT_QUOTES, 'UTF-8'); ?>" min="1" required>
                            </div>

                            <!-- Available Free Beds -->
                            <div class="col-md-6">
                                <label for="available_beds" class="form-label small fw-semibold text-secondary">Available Free Beds <span class="text-danger">*</span></label>
                                <input type="number" id="available_beds" name="available_beds" class="form-control" 
                                    value="<?php echo htmlspecialchars($old['available_beds'] ?? $room['available_beds'] ?? '1', ENT_QUOTES, 'UTF-8'); ?>" min="0" required>
                            </div>

                            <!-- Room Specific Amenities -->
                            <div class="col-12">
                                <label for="amenities" class="form-label small fw-semibold text-secondary">Room Specific Amenities</label>
                                <input type="text" id="amenities" name="amenities" class="form-control" 
                                    value="<?php echo htmlspecialchars($old['amenities'] ?? $room['amenities'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                <span class="form-text text-muted small" style="font-size: 0.7rem;">Separate multiple items with commas.</span>
                            </div>

                            <!-- Room Image Preview File Upload -->
                            <div class="col-12">
                                <label for="image_path" class="form-label small fw-semibold text-secondary">Room Interior Image Preview</label>
                                <input type="file" id="image_path" name="image_path" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
                                <span class="form-text text-muted small" style="font-size: 0.7rem;">Leave empty to keep your existing interior thumbnail image.</span>
                                
                                <!-- Current Image Preview Panel -->
                                <?php if (!empty($room['image_path'])): ?>
                                    <div class="mt-3 p-2 border rounded-1 bg-light d-inline-block">
                                        <span class="text-muted d-block small mb-1 fw-semibold">Current Image:</span>
                                        <img src="<?php echo BASE_URL . '/public/' . htmlspecialchars($room['image_path'], ENT_QUOTES, 'UTF-8'); ?>" 
                                            style="max-width: 150px; max-height: 100px; object-fit: cover;" 
                                            alt="Current Room Facade">
                                    </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>

                    <!-- Footer Action Triggers -->
                    <div class="card-footer bg-transparent border-top border-light-subtle py-3 px-4 d-flex justify-content-end gap-2">
                        <a href="<?php echo BASE_URL; ?>/owner/rooms/<?php echo $houseId; ?>" class="btn btn-light btn-sm px-3 rounded-1 pt-2">Cancel</a>
                        <button type="submit" class="btn btn-dark btn-sm px-4 rounded-1">Save Configurations</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>