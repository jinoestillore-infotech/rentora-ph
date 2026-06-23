<?php
/**
 * File Location: views/owner/rejected_houses.php
 * File Name: rejected_houses.php
 * Description: Grayscale minimalist owner warning interface displaying rejected properties and countdown parameters.
 */

// Include standard dynamic header
$title = "Rejected House";
require_once __DIR__ . '/../templates/header.php';
$properties = $properties ?? [];
?>

<div class="container my-5 mt-4">
    
    <!-- Top Nav Breadcrumbs -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?php echo BASE_URL; ?>/owner/dashboard" class="text-decoration-none text-dark small fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>My Dashboard
        </a>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1.5 px-3 rounded-1 small font-monospace">
            Safety Warning Registry
        </span>
    </div>

    <!-- Page Header Title -->
    <div class="border-bottom border-light-subtle pb-4 mb-5">
        <h1 class="h2 fw-bold text-dark mb-1">Rejected Applications Logs</h1>
        <p class="text-muted mb-0 small">Review rejection logs compiled by platform verification administrators. Please make note of the required changes.</p>
    </div>

    <!-- Informational Policy Banner -->
    <div class="alert-warning alert-system border border-warning-subtle rounded-1 p-4 mb-5 bg-white shadow-sm" style="color: #664d03;">
        <div class="d-flex align-items-start">
            <div class="bg-warning text-white rounded-1 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                <i class="fa-solid fa-triangle-exclamation fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1" style="color: #664d03;">System Policy: 3-Day Permanent Storage Cleanup</h6>
                <p class="small mb-0 leading-relaxed">
                    To maintain clean hosting directories, all rejected boarding house submissions (including photos, facade files, and business permit images) are automatically deleted <strong>3 days</strong> after registration rejection. If you wish to re-submit, please take note of the reasons below, re-capture the correct files, and file a new registration.
                </p>
            </div>
        </div>
    </div>

    <!-- Grid Items -->
    <?php if (empty($properties)): ?>
        <div class="card shadow-sm border border-light-subtle rounded-1 bg-white text-center py-5">
            <div class="card-body">
                <i class="fa-solid fa-circle-check text-muted fs-1 mb-3 opacity-50"></i>
                <h6 class="fw-semibold text-dark">All Clear!</h6>
                <p class="text-muted small mb-0">You currently have no rejected boarding house records in our system directory.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($properties as $house): ?>
                <div class="col-lg-5 col-12">
                    <div class="card shadow-sm border border-danger-subtle rounded-3 bg-white h-100 overflow-hidden d-flex flex-column" style="transition: all 0.2s ease-in-out;">
                        
                        <div class="card-header bg-danger text-white py-3 border-0 d-flex justify-content-between align-items-center">
                            <span class="small fw-bold text-uppercase tracking-wider"><i class="fa-solid fa-clock-rotate-left me-2"></i>Deletion Schedule</span>
                            <span class="badge bg-white text-danger font-monospace py-1 px-2.5 rounded-1 small shadow-sm fw-bold">
                                <?php echo htmlspecialchars($house['time_remaining_label'], ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>

                        <div class="card-body p-4 flex-grow-1">
                            
                            <!-- House Metadata Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
                                <div>
                                    <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <span class="text-muted small"><i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($house['town'] . ', ' . $house['address'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </div>

                            <!-- Permanent Deletion Warning Indicator -->
                            <div class="p-3 border rounded-1 mb-4 bg-light" style="border-style: dashed !important;">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid fa-trash-can text-danger me-2"></i>
                                    <span class="small text-muted">
                                        Scheduled deletion: <strong class="text-dark"><?php echo htmlspecialchars($house['scheduled_deletion_date'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </span>
                                </div>
                            </div>

                            <!-- Rejection Reason Sub-card layout -->
                            <h6 class="fw-bold text-uppercase tracking-wider text-muted mb-2" style="font-size: 0.75rem;">Administrator Explanatory Log</h6>
                            <div class="p-3 bg-danger-subtle border border-danger-subtle text-dark rounded-1 mb-0 shadow-sm" style="background-color: #fff5f5 !important;">
                                <?php if (!empty($house['rejection_reason'])): ?>
                                    <p class="font-monospace small mb-0 leading-relaxed" style="color: #9b2c2c;">
                                        <i class="fa-solid fa-comment-dots me-2"></i><?php echo nl2br(htmlspecialchars($house['rejection_reason'], ENT_QUOTES, 'UTF-8')); ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-muted small mb-0 italic">
                                        <i class="fa-solid fa-triangle-exclamation me-2"></i>No explanatory rejection details logged. Please contact support.
                                    </p>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all alert notification banners on the page
    const alertElements = document.querySelectorAll('.alert-system');
    
    alertElements.forEach(function(alert) {
        // Automatically trigger dismissal after 5 seconds
        setTimeout(function() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                // Use Bootstrap's native transition effects to safely close the alert
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                if (bsAlert) {
                    bsAlert.close();
                }
            } else {
                // Minimalist visual fallback if Bootstrap is not completely loaded
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }
        }, 50000); // 5000ms = 5 seconds
    });
});
</script>

<?php
// Include standard footer template
require_once __DIR__ . '/../templates/footer.php';
?>