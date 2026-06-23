<?php
/**
 * File Location: views/owner/rooms.php
 * File Name: rooms.php
 * Description: Clean, modern, and professional Boarding House Dashboard for managing individual rooms.
 */
$title = "Manage Rooms";
require_once dirname(__DIR__) . '/templates/header.php';

$rooms = $rooms ?? [];
$house = $house ?? [];
?>

<style>
    .room-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #eaeaea !important;
        background-color: #ffffff;
    }
    .room-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.05) !important;
        border-color: #948e8e !important;
    }
    .room-card-image-wrapper {
        position: relative;
        height: 160px;
        background-color: #fafafa;
        overflow: hidden;
        border-bottom: 1px solid #eaeaea;
    }
    .room-card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .badge-available {
        background-color: #e6fffa;
        color: #0d9488;
        border: 1px solid #b2f5ea;
    }
    .badge-booked {
        background-color: #fef2f2;
        color: #e11d48;
        border: 1px solid #fecaca;
    }
    .badge-maintenance {
        background-color: #fffbeb;
        color: #d97706;
        border: 1px solid #fef3c7;
    }
</style>

<div class="container my-5 mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?php echo BASE_URL; ?>/owner/dashboard" class="text-decoration-none text-dark small fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Return to Properties
        </a>
        <span class="badge bg-white text-dark border border-light-subtle py-2 px-3 rounded-1 small fw-semibold shadow-sm font-monospace">
            House Dashboard
        </span>
    </div>

    <div class="card border border-light-subtle rounded-3 bg-white p-4 mb-5 shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-4">
            <div>
                <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Currently Managing Rooms For</span>
                <h1 class="h2 fw-bold text-dark mb-1"><?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <p class="text-muted mb-0 small">
                    <i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($house['town'] . ', ' . $house['address'], ENT_QUOTES, 'UTF-8'); ?>
                    <br />
                    <i class="fa-solid fa-phone me-1"></i><?php echo htmlspecialchars($house['contact_number'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>/owner/room/add/<?php echo (int)$house['id']; ?>" class="btn btn-dark btn-sm px-4 py-2 text-nowrap">
                <i class="fa-solid fa-plus me-2"></i>Add New Room
            </a>
        </div>
    </div>

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

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="input-group">
                <input type="text" id="room-search" class="form-control" placeholder="Search rooms by name, status, or amenities keywords...">
            </div>
        </div>
        <div class="col-md-4">
            <select id="status-filter" class="form-select">
                <option value="ALL">All Statuses</option>
                <option value="Available">Available Only</option>
                <option value="Fully Booked">Fully Booked</option>
                <option value="Maintenance">Under Maintenance</option>
            </select>
        </div>
    </div>

    <div class="row g-4" id="rooms-grid">
        <?php if (empty($rooms)): ?>
            <div class="col-12" id="no-rooms-placeholder">
                <div class="card shadow-sm border border-light-subtle rounded-1 bg-white text-center py-5">
                    <div class="card-body">
                        <i class="fa-solid fa-door-closed text-muted fs-1 mb-3 opacity-50"></i>
                        <h6 class="fw-semibold text-dark">No Rooms Found</h6>
                        <p class="text-muted small mb-3">You haven't configured any rooms for this property yet.</p>
                        <button type="button" class="btn btn-dark btn-sm px-4" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                            Add Your First Room
                        </button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($rooms as $room): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-12 room-card-item" 
                     data-name="<?php echo htmlspecialchars($room['room_name'], ENT_QUOTES, 'UTF-8'); ?>"
                     data-status="<?php echo htmlspecialchars($room['status'], ENT_QUOTES, 'UTF-8'); ?>"
                     data-amenities="<?php echo htmlspecialchars($room['amenities'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <div class="card room-card h-100 shadow-sm rounded-3 overflow-hidden d-flex flex-column">
                        
                        <!-- Room Thumbnail -->
                        <div class="room-card-image-wrapper">
                            <?php if (!empty($room['image_path'])): ?>
                                <img src="<?php echo BASE_URL . '/public/' . htmlspecialchars($room['image_path'], ENT_QUOTES, 'UTF-8'); ?>" 
                                     class="room-card-image" 
                                     alt="<?php echo htmlspecialchars($room['room_name'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                    <i class="fa-solid fa-door-open fa-2x mb-2 opacity-30"></i>
                                    <span class="small opacity-50" style="font-size: 0.75rem;">No Thumbnail Attached</span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Status Badges inside the frame -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <?php if ($room['status'] === 'Available'): ?>
                                    <span class="badge badge-available py-1 px-2.5 rounded-pill shadow-sm small font-monospace">Available</span>
                                <?php elseif ($room['status'] === 'Fully Booked'): ?>
                                    <span class="badge badge-booked py-1 px-2.5 rounded-pill shadow-sm small font-monospace">Fully Booked</span>
                                <?php else: ?>
                                    <span class="badge badge-maintenance py-1 px-2.5 rounded-pill shadow-sm small font-monospace">Maintenance</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-4 flex-grow-1 d-flex flex-column">
                            <h5 class="fw-bold text-dark mb-1 text-truncate" title="<?php echo htmlspecialchars($room['room_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($room['room_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </h5>
                            
                            <!-- Monthly Price Rent tag -->
                            <h6 class="fw-bold text-dark mb-3">
                                ₱<?php echo number_format($room['price'], 2); ?> <span class="text-muted fw-normal small">/ month</span>
                            </h6>

                            <!-- Room Capacity statistics -->
                            <div class="p-2 border border-light-subtle rounded-1 bg-light mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small">Max Capacity</span>
                                    <span class="fw-bold text-dark small"><?php echo (int)$room['capacity']; ?> beds</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Available Free</span>
                                    <span class="fw-bold text-dark small"><?php echo (int)$room['available_beds']; ?> beds free</span>
                                </div>
                            </div>

                            <!-- Amenities List -->
                            <div class="mb-3">
                                <span class="text-muted d-block small mb-1 fw-semibold text-uppercase tracking-wider" style="font-size: 0.65rem;">Room Amenities</span>
                                <?php if (!empty($room['amenities'])): ?>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach (explode(',', $room['amenities']) as $amenity): ?>
                                            <span class="badge bg-light text-dark border rounded-1 py-1 px-2 font-monospace" style="font-size: 0.65rem;">
                                                <?php echo htmlspecialchars(trim($amenity), ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small italic" style="font-size: 0.75rem;">No room-specific amenities logged.</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Action Hooks -->
                        <div class="bg-transparent border-top border-light-subtle p-3 mt-auto d-flex gap-2">
                            <a href="<?php echo BASE_URL; ?>/owner/room/edit/<?php echo (int)$house['id']; ?>/<?php echo (int)$room['id']; ?>" class="btn btn-dark btn-sm w-100">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Room
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm px-3 delete-room-btn" 
                                    data-id="<?php echo (int)$room['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($room['room_name'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-bs-toggle="modal" data-bs-target="#deleteRoomModal">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
            <div class="col-12 text-center py-4 d-none" id="no-search-results">
                <i class="fa-solid fa-magnifying-glass-minus text-muted fs-2 d-block mb-2 opacity-50"></i>
                <span class="text-muted small">No rooms match your filter parameters.</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="deleteRoomModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-1 border border-light-subtle">
            <div class="modal-header border-bottom border-light-subtle py-3">
                <h6 class="modal-title fw-bold text-dark" id="deleteModalTitle"><i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Confirm Specifications Deletion</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-dark small mb-2">Are you sure you want to permanently delete <strong id="delete-room-name-label">this room</strong> from your boarding house roster?</p>
                <p class="text-muted small mb-0">This operational action is permanent. Any uploaded interior media files will be instantly unlinked and permanently deleted from disk storage.</p>
            </div>
            <div class="modal-footer border-top border-light-subtle py-2">
                <button type="button" class="btn btn-light btn-sm rounded-1" data-bs-dismiss="modal">Cancel</button>
                <form action="<?php echo BASE_URL; ?>/owner/room/delete" method="POST" class="d-inline">
                    <?php echo \App\Core\Security::csrfField(); ?>
                    <input type="hidden" name="boarding_house_id" value="<?php echo (int)$house['id']; ?>">
                    <input type="hidden" id="delete-room-id" name="room_id" value="">
                    <button type="submit" class="btn btn-danger btn-sm rounded-1 px-4">Permanently Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('room-search');
    const statusFilter = document.getElementById('status-filter');
    const gridItems = Array.from(document.querySelectorAll('.room-card-item'));
    const placeholder = document.getElementById('no-rooms-placeholder');
    const noResults = document.getElementById('no-search-results');

    // Real-time client-side listing search and filtering
    function applyFilters() {
        if (!gridItems.length) return;

        const query = searchInput.value.toLowerCase().trim();
        const selectedStatus = statusFilter.value;
        let matchCount = 0;

        gridItems.forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            const status = item.getAttribute('data-status');
            const amenities = item.getAttribute('data-amenities').toLowerCase();

            const matchesQuery = name.includes(query) || amenities.includes(query);
            const matchesStatus = (selectedStatus === 'ALL' || status === selectedStatus);

            if (matchesQuery && matchesStatus) {
                item.style.setProperty('display', 'block', 'important');
                matchCount++;
            } else {
                item.style.setProperty('display', 'none', 'important');
            }
        });

        if (noResults) {
            if (matchCount === 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        }
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);

    // Dynamic Delete Modal parameters assignment
    const deleteRoomModal = document.getElementById('deleteRoomModal');
    if (deleteRoomModal) {
        deleteRoomModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');

            document.getElementById('delete-room-id').value = id;
            document.getElementById('delete-room-name-label').textContent = name;
        });
    }
});
</script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>