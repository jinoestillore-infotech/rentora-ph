<?php
/**
 * File Location: views/owner/applications.php
 * File Name: applications.php
 * Description: High-contrast, mobile-responsive landlord inbox console to track incoming tenant applications.
 */

// Include standard dynamic header
$title = "Tenancy Bookings";
require_once dirname(__DIR__) . '/templates/header.php';
$applications = $applications ?? [];
?>

<style>
    /* Monochrome visual highlights matching RENTORA PH styling values */
    .inbox-card {
        border: 1px solid #eaeaea !important;
        background-color: #ffffff;
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .inbox-card:hover {
        transform: translateY(-2px);
        border-color: #1a1a1a !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04) !important;
    }
    .status-badge-pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }
    .status-badge-approved {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .status-badge-rejected {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Mobile-first structural custom overrides */
    @media (max-width: 767.98px) {
        .desktop-table-view {
            display: none !important;
        }
        .mobile-cards-view {
            display: block !important;
        }
    }
    @media (min-width: 768px) {
        .desktop-table-view {
            display: block !important;
        }
        .mobile-cards-view {
            display: none !important;
        }
    }
</style>

<div class="container my-5 mt-4">
    
    <!-- Top Nav breadcrumb -->
    <div class="mb-4">
        <a href="<?php echo BASE_URL; ?>/owner/dashboard" class="text-decoration-none text-dark small fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Return to Owner Dashboard
        </a>
    </div>

    <!-- Page Title Header block -->
    <div class="border-bottom border-light-subtle pb-4 mb-5">
        <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Applications Inbox</span>
        <h1 class="h2 fw-bold text-dark mb-1">Tenancy Bookings Inbox</h1>
        <p class="text-muted mb-0 small">Review incoming renter applications, inspect verification ID documents, and manage room allocations.</p>
    </div>

    <!-- Feedback Alerts -->
    <?php if (isset($success) && $success): ?>
        <div class="alert alert-success d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #f0fff4; color: #22543d;" role="alert">
            <i class="fa-solid fa-circle-check me-3"></i>
            <span class="small"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #fff5f5; color: #c53030;" role="alert">
            <i class="fa-solid fa-circle-exclamation me-3"></i>
            <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Empty State Callback -->
    <?php if (empty($applications)): ?>
        <div class="card shadow-sm border border-light-subtle rounded-3 bg-white text-center py-5">
            <div class="card-body py-5">
                <i class="fa-solid fa-folder-open text-muted fs-1 mb-3 opacity-40"></i>
                <h5 class="fw-semibold text-dark">No Applications Found</h5>
                <p class="text-muted small mb-0">You currently have no incoming tenancy applications to review for your properties.</p>
            </div>
        </div>
    <?php else: ?>

        <!-- DESKTOP SCREEN VIEW (Clear structured tabular view) -->
        <div class="card shadow-sm border border-light-subtle rounded-3 bg-white overflow-hidden desktop-table-view">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light text-secondary">
                        <tr>
                            <th class="py-3 px-4 border-0">Applicant Full Name</th>
                            <th class="py-3 border-0">Target Property</th>
                            <th class="py-3 border-0">Requested Room Layout</th>
                            <th class="py-3 border-0">Submitted Date</th>
                            <th class="py-3 border-0 text-center">Status</th>
                            <th class="py-3 px-4 border-0 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td class="py-3 px-4">
                                    <span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($app['firstname'] . ' ' . $app['lastname'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="text-muted small" style="font-size: 0.75rem;">Renter Profile</span>
                                </td>
                                <td>
                                    <span class="text-dark fw-medium"><?php echo htmlspecialchars($app['house_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td>
                                    <span class="text-dark"><?php echo htmlspecialchars($app['room_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="text-muted d-block small" style="font-size: 0.75rem;">Rent: ₱<?php echo number_format($app['room_price'], 2); ?>/mo</span>
                                </td>
                                <td class="text-muted">
                                    <?php echo date('M d, Y h:i A', strtotime($app['created_at'])); ?>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $statusClass = 'status-badge-pending';
                                    if ($app['status'] === 'Approved') $statusClass = 'status-badge-approved';
                                    if ($app['status'] === 'Rejected') $statusClass = 'status-badge-rejected';
                                    ?>
                                    <span class="badge py-1 px-3 rounded-pill small <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($app['status'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <a href="<?php echo BASE_URL; ?>/owner/application/view/<?php echo (int)$app['id']; ?>" class="btn btn-dark btn-sm py-1 px-3">
                                        <i class="fa-solid fa-file-magnifying-glass me-1"></i> Review
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MOBILE SCREEN VIEW (Elegant, stackable card layouts optimized for device tap actions) -->
        <div class="row g-3 mobile-cards-view" style="display: none;">
            <?php foreach ($applications as $app): ?>
                <div class="col-12">
                    <div class="card inbox-card rounded-3 p-3 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="fw-bold text-dark fs-6 d-block"><?php echo htmlspecialchars($app['firstname'] . ' ' . $app['lastname'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="text-muted small" style="font-size: 0.75rem;">Submitted: <?php echo date('M d, Y', strtotime($app['created_at'])); ?></span>
                            </div>
                            
                            <?php 
                            $statusClass = 'status-badge-pending';
                            if ($app['status'] === 'Approved') $statusClass = 'status-badge-approved';
                            if ($app['status'] === 'Rejected') $statusClass = 'status-badge-rejected';
                            ?>
                            <span class="badge py-1 px-2.5 rounded-pill small <?php echo $statusClass; ?>" style="font-size: 0.75rem;">
                                <?php echo htmlspecialchars($app['status'], ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>

                        <div class="py-2 border-top border-bottom border-light-subtle my-2">
                            <span class="text-muted small d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase;">Accommodation Target</span>
                            <span class="text-dark fw-bold d-block" style="font-size: 0.85rem;"><?php echo htmlspecialchars($app['house_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="text-muted small d-block" style="font-size: 0.8rem;">Layout: <?php echo htmlspecialchars($app['room_name'], ENT_QUOTES, 'UTF-8'); ?> (₱<?php echo number_format($app['room_price'], 2); ?>)</span>
                        </div>

                        <div class="d-grid mt-2">
                            <a href="<?php echo BASE_URL; ?>/owner/application/view/<?php echo (int)$app['id']; ?>" class="btn btn-dark btn-sm py-2">
                                <i class="fa-solid fa-file-magnifying-glass me-2"></i>Review Credentials
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>

<?php 
// Include standard footer template
require_once dirname(__DIR__) . '/templates/footer.php'; 
?>