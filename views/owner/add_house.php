<?php

use App\Core\Security;

// Include standard clean header template
require_once dirname(__DIR__) . '/templates/header.php';

$boholTowns = [
    'Alburquerque',
    'Alicia',
    'Anda',
    'Antequera',
    'Baclayon',
    'Balilihan',
    'Batuan',
    'Bien Unido',
    'Bilar',
    'Buenavista',
    'Calape',
    'Candijay',
    'Carmen',
    'Catigbian',
    'Clarin',
    'Corella',
    'Cortes',
    'Dagohoy',
    'Danao',
    'Dauis',
    'Dimiao',
    'Duero',
    'Garcia Hernandez',
    'Getafe',
    'Guindulman',
    'Inabanga',
    'Jagna',
    'Lila',
    'Loay',
    'Loboc',
    'Loon',
    'Mabini',
    'Maribojoc',
    'Panglao',
    'Pilar',
    'President Carlos P. Garcia',
    'Sagbayan',
    'San Isidro',
    'San Miguel',
    'Sevilla',
    'Sierra Bullones',
    'Sikatuna',
    'Talibon',
    'Trinidad',
    'Tubigon',
    'Ubay',
    'Valencia',
    'Tagbilaran City'
];

// Fetch old input values and session error alerts if present
$error = $_SESSION['error'] ?? null;
$old = $_SESSION['old_input'] ?? [];

// Clear the flash session data
unset($_SESSION['error'], $_SESSION['old_input']);
?>

<div class="container my-5 mt-3">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9 col-12">
            
            <!-- Breadcrumb Navigation -->
            <div class="mb-3">
                <a href="<?php echo BASE_URL; ?>/owner/dashboard" class="text-decoration-none text-dark small fw-semibold">
                    <i class="fa-solid fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #fff5f5; color: #c53030;" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-3"></i>
                    <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border rounded-1 bg-white">
                <div class="bg-transparent border-bottom border-light-subtle p-4">
                    <h5 class="card-title fw-bold text-dark mb-0">
                        <i class="fa-solid fa-house-chimney me-2"></i>Register Boarding House
                    </h5>
                    <p class="text-muted mb-0 small">Please fill out the details below. This property will require admin approval before you can add rooms.</p>
                </div>
                
                <!-- Updated with enctype attribute to handle file uploads -->
                <form action="<?php echo BASE_URL; ?>/owner/add-house" method="POST" enctype="multipart/form-data">
                    <!-- Security Verification Field -->
                    <?php echo Security::csrfField(); ?>

                    <div class="card-body p-4">
                        <div class="row g-3">
                            
                            <!-- Boarding House Name -->
                            <div class="col-12">
                                <label for="name">Boarding House Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="e.g. Dela Cruz Cozy Living Space" 
                                       value="<?php echo htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>

                            <!-- Searchable Town Selection Input Group -->
                            <div class="col-md-6 position-relative" id="town-dropdown-container">
                                <label for="town_search">Town / Municipality <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="town_search" 
                                           placeholder="Type to search town..." 
                                           value="<?php echo htmlspecialchars($old['town'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                                           autocomplete="off" required>
                                </div>
                                
                                <!-- Hidden input for securing actual POST request variable -->
                                <input type="hidden" id="town" name="town" value="<?php echo htmlspecialchars($old['town'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                
                                <!-- Floating Dropdown Selection Menu -->
                                <ul class="dropdown-menu w-100 shadow-sm overflow-auto" id="town-list" style="max-height: 250px; display: none; position: absolute; z-index: 1000; margin-top: 2px;">
                                    <?php foreach ($boholTowns as $town): ?>
                                        <li>
                                            <button type="button" class="dropdown-item py-2 small" data-value="<?php echo htmlspecialchars($town, ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php echo htmlspecialchars($town, ENT_QUOTES, 'UTF-8'); ?>
                                            </button>
                                        </li>
                                    <?php endforeach; ?>
                                    <li id="no-results" style="display: none;">
                                        <span class="dropdown-item text-muted small py-2">No matching towns found</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Contact Number -->
                            <div class="col-md-6">
                                <label for="contact_number">Contact Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                       placeholder="e.g. 09223456789" 
                                       value="<?php echo htmlspecialchars($old['contact_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>

                            <!-- Physical Street Address -->
                            <div class="col-12">
                                <label for="address">Complete Street Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       placeholder="e.g. 123 Rizal Street, Barangay Central" 
                                       value="<?php echo htmlspecialchars($old['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description">Property Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" 
                                          placeholder="Provide an overview of your boarding house facilities, nearby locations, or terms..."><?php echo htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>

                            <!-- Amenities -->
                            <div class="col-12">
                                <label for="amenities">Property Amenities</label>
                                <input type="text" class="form-control" id="amenities" name="amenities" 
                                       placeholder="e.g. WiFi, Kitchen Access, CCTV Security, Shared Lounge"
                                       value="<?php echo htmlspecialchars($old['amenities'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="form-text text-muted small" style="font-size: 0.75rem;">Separate with commas.</div>
                            </div>

                            <!-- House Rules -->
                            <div class="col-12">
                                <label for="house_rules">House Rules</label>
                                <textarea class="form-control" id="house_rules" name="house_rules" rows="2" 
                                          placeholder="e.g. No smoking, Curfew at 10 PM, Visitors allowed until 8 PM only"><?php echo htmlspecialchars($old['house_rules'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>

                            <!-- Verification & Media Header -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-uppercase tracking-wider text-muted mb-1" style="font-size: 0.8rem;">Media & Legal Verification</h6>
                                <hr class="my-2 border-light-subtle">
                            </div>

                            <!-- Featured Property Image Upload -->
                            <div class="col-12">
                                <label for="image_path" class="form-label">Featured Property Image <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="image_path" name="image_path" accept="image/png, image/jpeg, image/jpg, image/webp" required>
                                <div class="form-text text-muted small" style="font-size: 0.75rem;">
                                    Please upload a clear picture of your boarding house exterior or facade. Only JPG, JPEG, PNG, and WEBP formats are accepted.
                                </div>
                            </div>

                            <!-- Verification Permit / Proof of Ownership Upload -->
                            <div class="col-12">
                                <label for="legality_proof" class="form-label">Proof of Ownership / Business Permit <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="legality_proof" name="legality_proof" accept="image/png, image/jpeg, image/jpg, image/webp, application/pdf" required>
                                <div class="form-text text-muted small" style="font-size: 0.75rem;">
                                    Upload a copy of your Business Permit, Barangay Clearance, or Land Title to verify legality. PDF and image formats are allowed.
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Footer Submission Buttons -->
                    <div class="card-footer bg-transparent border-top border-light-subtle py-3 px-4 d-flex justify-content-end gap-2">
                        <a href="<?php echo BASE_URL; ?>/owner/dashboard" class="btn btn-light btn-sm px-3 pt-2 text-decoration-none">Cancel</a>
                        <button type="submit" class="btn btn-dark btn-sm px-4">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- Searchable Town Selector UI Interaction Logic -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('town_search');
    const hiddenInput = document.getElementById('town');
    const dropdownMenu = document.getElementById('town-list');
    const listItems = dropdownMenu.querySelectorAll('.dropdown-item:not(#no-results *)');
    const noResultsItem = document.getElementById('no-results');

    // Show dropdown when focused
    searchInput.addEventListener('focus', function() {
        dropdownMenu.style.display = 'block';
        filterTowns();
    });

    // Handle character entries dynamically
    searchInput.addEventListener('input', function() {
        dropdownMenu.style.display = 'block';
        filterTowns();
        // Clear hidden output if the typed search does not match any valid selection
        hiddenInput.value = '';
    });

    // Simple search filtering utility
    function filterTowns() {
        const query = searchInput.value.toLowerCase().trim();
        let matchFound = false;

        listItems.forEach(item => {
            const cityName = item.textContent.toLowerCase();
            if (cityName.includes(query)) {
                item.closest('li').style.display = 'block';
                matchFound = true;
            } else {
                item.closest('li').style.display = 'none';
            }
        });

        // Toggle "No Matching Towns" notification helper
        noResultsItem.style.display = matchFound ? 'none' : 'block';
    }

    // Set form parameters on click
    dropdownMenu.addEventListener('click', function(e) {
        const clickedBtn = e.target.closest('.dropdown-item');
        if (clickedBtn && clickedBtn.id !== 'no-results') {
            const selectedVal = clickedBtn.getAttribute('data-value');
            searchInput.value = selectedVal;
            hiddenInput.value = selectedVal;
            dropdownMenu.style.display = 'none';
        }
    });

    // Close the dropdown cleanly when clicking outside the input container
    document.addEventListener('click', function(e) {
        const container = document.getElementById('town-dropdown-container');
        if (!container.contains(e.target)) {
            dropdownMenu.style.display = 'none';
            
            // Revert search box text to active value if user clicked away without completing selection
            if (hiddenInput.value) {
                searchInput.value = hiddenInput.value;
            } else {
                searchInput.value = '';
            }
        }
    });
});
</script>

<?php
// Include the standard footer template
require_once dirname(__DIR__) . '/templates/footer.php';
?>