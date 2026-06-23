<?php
/**
 * File Location: views/tenant/apply.php
 * File Name: apply.php
 * Description: Premium, minimalist, multi-step styling application view for Tenants matching RENTORA PH monochrome branding.
 */
use App\Core\Security;

$title = "Apply as Tenant";
require_once dirname(__DIR__) . '/templates/header.php';
$house = $house ?? [];
$rooms = $rooms ?? [];
?>

<div class="container my-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            
            <!-- Top return links -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="<?php echo BASE_URL; ?>/tenant/house/view/<?php echo (int)$house['id']; ?>" class="text-decoration-none text-dark small fw-semibold">
                    <i class="fa-solid fa-arrow-left me-2"></i>Return to Property Profile
                </a>
                <span class="badge bg-white text-dark border border-light-subtle py-2 px-3 rounded-1 small fw-semibold shadow-sm font-monospace">
                    Booking Request Form
                </span>
            </div>

            <!-- Page Title Header block -->
            <div class="pb-3 mb-4 border-bottom border-light-subtle">
                <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Accommodations Entry Form</span>
                <h1 class="h2 fw-bold text-dark mb-1">Apply for Tenancy</h1>
                <p class="text-muted mb-0 small">Property: <strong><?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?></strong> <br />Located in: <strong><?php echo htmlspecialchars($house['town'] . ' - ' . $house['address'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
            </div>

            <!-- Error Alerts -->
            <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #fff5f5; color: #c53030;" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-3"></i>
                    <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card border border-light-subtle rounded-3 bg-white shadow-sm overflow-hidden">
                <div class="bg-light border-bottom border-light-subtle p-4 pb-3">
                    <h5 class="fw-bold text-dark mb-1">Tenant Application Dossier</h5>
                    <p class="text-muted mb-0 small">Please fill in your current, real-world profile coordinates and upload valid identity verification cards to secure reservation processing.</p>
                </div>

                <form action="<?php echo BASE_URL; ?>/tenant/house/apply" method="POST" enctype="multipart/form-data">
                    <!-- Anti-forgery validation token injection -->
                    <?php echo Security::csrfField(); ?>
                    <input type="hidden" name="boarding_house_id" value="<?php echo (int)$house['id']; ?>">

                    <div class="card-body p-4 p-md-5 pt-md-4">
                        
                        <!-- SECTION 1: Accommodations Target Layout -->
                        <div class="mb-5">
                            <h6 class="fw-bold text-uppercase text-secondary tracking-wider mb-3" style="font-size: 0.75rem; border-bottom: 1px solid #eaeaea; padding-bottom: 0.5rem;"><i class="fa-solid fa-bed me-2"></i>1. Accommodation Space Target Selection</h6>
                            <div class="row">
                                <div class="col-12">
                                    <label for="room_id" class="form-label small fw-semibold text-secondary">Choose Room layout <span class="text-danger">*</span></label>
                                    <select id="room_id" name="room_id" class="form-select" required>
                                        <option value="" disabled <?php echo empty($old['room_id']) ? 'selected' : ''; ?>>-- Please Select from Available Vacancies --</option>
                                        <?php foreach ($rooms as $room): ?>
                                            <option value="<?php echo (int)$room['id']; ?>" <?php echo (isset($old['room_id']) && (int)$old['room_id'] === (int)$room['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($room['room_name'], ENT_QUOTES, 'UTF-8'); ?> - ₱<?php echo number_format($room['price'], 2); ?> - ( <?php echo (int)$room['available_beds']; ?> beds free )
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text text-muted small" style="font-size: 0.7rem;">Only rooms currently listed as "Available" with vacant bed spaces are shown.</div>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: Applicant Personal Details -->
                        <div class="mb-5">
                            <h6 class="fw-bold text-uppercase text-secondary tracking-wider mb-3" style="font-size: 0.75rem; border-bottom: 1px solid #eaeaea; padding-bottom: 0.5rem;"><i class="fa-solid fa-address-card me-2"></i>2. Applicant Identification Dossier</h6>
                            
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label for="firstname" class="form-label small fw-semibold text-secondary">Given First Name <span class="text-danger">*</span></label>
                                    <input type="text" id="firstname" name="firstname" class="form-control" 
                                           value="<?php echo htmlspecialchars($old['firstname'] ?? $_SESSION['firstname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="col-md-5">
                                    <label for="lastname" class="form-label small fw-semibold text-secondary">Family Last Name <span class="text-danger">*</span></label>
                                    <input type="text" id="lastname" name="lastname" class="form-control" 
                                           value="<?php echo htmlspecialchars($old['lastname'] ?? $_SESSION['lastname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="middlename" class="form-label small fw-semibold text-secondary">Middle Initial</label>
                                    <input type="text" id="middlename" name="middlename" class="form-control" maxlength="10"
                                           value="<?php echo htmlspecialchars($old['middlename'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="col-md-9">
                                    <label for="permanent_address" class="form-label small fw-semibold text-secondary">Home Permanent Address <span class="text-danger">*</span></label>
                                    <input type="text" id="permanent_address" name="permanent_address" class="form-control" 
                                           placeholder="Street Address, Town, City, Province"
                                           value="<?php echo htmlspecialchars($old['permanent_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="age" class="form-label small fw-semibold text-secondary">Current Age <span class="text-danger">*</span></label>
                                    <input type="number" id="age" name="age" class="form-control" min="15" max="100"
                                           value="<?php echo htmlspecialchars($old['age'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="contact_number" class="form-label small fw-semibold text-secondary">Mobile contact Number <span class="text-danger">*</span></label>
                                    <input type="tel" id="contact_number" name="contact_number" class="form-control" 
                                           placeholder="e.g. 09123456789"
                                           value="<?php echo htmlspecialchars($old['contact_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label small fw-semibold text-secondary">Email Profile <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" class="form-control" 
                                           value="<?php echo htmlspecialchars($old['email'] ?? $_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>

                                <div class="col-12 mt-4">
                                    <label for="verification_id" class="form-label small fw-semibold text-dark">Tenant Verification Identity Card (Government ID / Student ID) <span class="text-danger">*</span></label>
                                    <input type="file" id="verification_id" name="verification_id" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp, application/pdf" required>
                                    <span class="form-text text-muted small" style="font-size: 0.7rem;">Please upload a clear, legible picture scan of your ID. Accepted formats: JPG, PNG, WEBP, or PDF (Max Size: 3MB).</span>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: Emergency Contact Details -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-uppercase text-secondary tracking-wider mb-3" style="font-size: 0.75rem; border-bottom: 1px solid #eaeaea; padding-bottom: 0.5rem;"><i class="fa-solid fa-house-medical me-2"></i>3. Emergency Contact Protocol Logs</h6>
                            
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <label for="emergency_fullname" class="form-label small fw-semibold text-secondary">Full Name of Emergency Contact Person <span class="text-danger">*</span></label>
                                    <input type="text" id="emergency_fullname" name="emergency_fullname" class="form-control" 
                                           placeholder="Firstname Middlename Lastname"
                                           value="<?php echo htmlspecialchars($old['emergency_fullname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                                <div class="col-md-5">
                                    <label for="emergency_contact_number" class="form-label small fw-semibold text-secondary">Emergency Mobile Line <span class="text-danger">*</span></label>
                                    <input type="tel" id="emergency_contact_number" name="emergency_contact_number" class="form-control" 
                                           placeholder="e.g. 09123456789"
                                           value="<?php echo htmlspecialchars($old['emergency_contact_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>

                                <div class="col-12 mt-4">
                                    <label for="emergency_verification_id" class="form-label small fw-semibold text-dark">Emergency Person Verification Identity Card (Government ID / Baranggay ID) <span class="text-danger">*</span></label>
                                    <input type="file" id="emergency_verification_id" name="emergency_verification_id" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp, application/pdf" required>
                                    <span class="form-text text-muted small" style="font-size: 0.7rem;">Verification ID upload is required for administrative security. Max file size: 3MB (JPG, PNG, PDF formats allowed).</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Card Form Action Footer -->
                    <div class="card-footer bg-light border-top border-light-subtle py-3 px-4 d-flex justify-content-end gap-2">
                        <a href="<?php echo BASE_URL; ?>/tenant/house/view/<?php echo (int)$house['id']; ?>" class="btn btn-light btn-sm px-4 pt-2">Cancel</a>
                        <button type="submit" class="btn btn-dark btn-sm px-4">Submit Booking Request</button>
                    </div>
                </form>

            </div>

            <!-- Safety Policy Info Panel -->
            <div class="card border border-light-subtle bg-light rounded-3 p-4 mt-4">
                <div class="d-flex align-items-start">
                    <div class="text-dark fs-3 me-3 mt-1">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Platform Processing Guarantee</h6>
                        <p class="text-muted small mb-0 leading-relaxed">
                            By submitting this request, your information is routed directly to your landlord's pending evaluation console. No deposit payments should be requested inside the portal prior to active, verified approval flags appearing on your Tenant dashboard screen. Keep transaction receipts for reference.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>