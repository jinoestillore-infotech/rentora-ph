<?php
/**
 * File Location: views/admin/dashboard.php
 * File Name: dashboard.php
 * Description: Professional and minimalist black-and-white Administrator verification console.
 */

// Include standard dynamic header
require_once dirname(__DIR__) . '/templates/header.php';
// Use the controller-provided pending properties, defaulting to an empty array if not set
$pendingProperties = $pendingProperties ?? [];
?>

<style>
    .hover-card:hover {
        border: 1px solid black !important;
    }
</style>
<div class="container my-5">
    
    <!-- Title banner header -->
    <div class="border-bottom border-light-subtle pb-4 mb-5">
        <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Verification Control Center</span>
        <h1 class="h2 fw-bold text-dark mb-1">Administrative Console</h1>
        <p class="text-muted mb-0 small">Verify boarding house legality documents, review business facade images, and grant portal approval.</p>
    </div>

    <!-- Alert banners -->
    <?php if ($success): ?>
        <div class="alert alert-success d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #f0fff4; color: #22543d;" role="alert">
            <i class="fa-solid fa-circle-check me-3"></i>
            <span class="small"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show p-3 border-0 rounded-1 mb-4" style="background-color: #fff5f5; color: #c53030;" role="alert">
            <i class="fa-solid fa-circle-exclamation me-3"></i>
            <span class="small"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- System-wide statistics panel -->
    <div class="row g-4 mb-5">
        <!-- Stat panel 1 -->
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border border-light-subtle rounded-1 bg-white h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase text-muted fw-semibold small" style="font-size: 0.7rem;">Total Listings</span>
                            <h3 class="fw-bold mt-1 mb-0 text-dark"><?php echo (int)($stats['total'] ?? 0); ?></h3>
                        </div>
                        <div class="bg-light rounded-1 p-2 border border-light-subtle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-house-chimney text-secondary fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stat panel 2 -->
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border border-light-subtle rounded-1 bg-white h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase text-muted fw-semibold small" style="font-size: 0.7rem;">Pending Verification</span>
                            <h3 class="fw-bold mt-1 mb-0 text-secondary"><?php echo (int)($stats['pending'] ?? 0); ?></h3>
                        </div>
                        <div class="bg-light rounded-1 p-2 border border-light-subtle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-hourglass-half text-secondary fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stat panel 3 -->
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border border-light-subtle rounded-1 bg-white h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase text-muted fw-semibold small" style="font-size: 0.7rem;">Approved Properties</span>
                            <h3 class="fw-bold mt-1 mb-0 text-success"><?php echo (int)($stats['approved'] ?? 0); ?></h3>
                        </div>
                        <div class="bg-success-subtle rounded-1 p-2 border border-success-subtle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-circle-check text-success fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Stat panel 4 -->
        <div class="col-xl-3 col-sm-6">
            <div class="card shadow-sm border border-light-subtle rounded-1 bg-white h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-uppercase text-muted fw-semibold small" style="font-size: 0.7rem;">Rejected Submissions</span>
                            <h3 class="fw-bold mt-1 mb-0 text-danger"><?php echo (int)($stats['rejected'] ?? 0); ?></h3>
                        </div>
                        <div class="bg-danger-subtle rounded-1 p-2 border border-danger-subtle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-circle-xmark text-danger fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Launchpad Block -->
    <div class="card shadow-sm border border-light-subtle rounded-1 bg-white mb-5">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-dark text-white rounded-1 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="fa-solid fa-rocket fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">System Control Launchpad</h5>
                    <p class="text-muted small mb-0">Quick navigation actions to manage live boarding houses on Rentora PH.</p>
                </div>
            </div>
            
            <div class="row g-2">
                <div class="col-md-6 col-12">
                    <a href="<?php echo BASE_URL; ?>/admin/approved-houses" class="card text-decoration-none border border-light-subtle rounded-1 p-3 bg-light-subtle hover-card transition-all h-100" style="transition: all 0.2s ease-in-out;">
                        <div class="d-flex align-items-center">
                            <div class="bg-white border rounded-1 p-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-building-shield text-dark fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Approved Listings Registry</h6>
                                <p class="text-muted small mb-0">Manage verified live properties, inspect documents, or revoke access credentials.</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-12">
                    <a href="<?php echo BASE_URL; ?>/admin/rejected-houses" class="card text-decoration-none border border-light-subtle rounded-1 p-3 bg-light-subtle hover-card transition-all h-100" style="transition: all 0.2s ease-in-out;">
                        <div class="d-flex align-items-center">
                            <div class="bg-white border rounded-1 p-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-building-circle-xmark text-dark fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Rejected Submissions Listings Registry</h6>
                                <p class="text-muted small mb-0">View profiles and logged rejection reasons.</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 col-12">
                    <div class="card border border-light-subtle rounded-1 p-3 bg-light-subtle h-100 opacity-75">
                        <div class="d-flex align-items-center">
                            <div class="bg-white border rounded-1 p-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-users-gear text-secondary fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-secondary mb-1">User & Owner Accounts (Locked)</h6>
                                <p class="text-muted small mb-0">Monitor platform partner accounts, review registration limits, or suspend users.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Listings Verification List -->
    <div class="card shadow-sm border border-light-subtle rounded-1 bg-white mb-5">
        <div class="card-header bg-white py-3 border-bottom border-light-subtle d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0" style="font-size: 1rem;">
                <i class="fa-solid fa-list-check me-2"></i>Registration Log Registry
            </h5>
            <span class="badge bg-light text-dark border py-1.5 px-3 rounded-1 small font-monospace">
                <?php echo count($pendingProperties); ?> Pending Entries
            </span>
        </div>

        <!-- Wrapped inside a scrollable container with sticky header styling -->
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light text-secondary" style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa; box-shadow: inset 0 -1px 0 rgba(0,0,0,.1);">
                    <tr>
                        <th class="py-3 px-4 border-0">Property / House</th>
                        <th class="py-3 border-0">Location</th>
                        <th class="py-3 border-0">Owner Partner</th>
                        <th class="py-3 border-0">Date Submitted</th>
                        <th class="py-3 border-0 text-center">Status</th>
                        <th class="py-3 px-4 border-0 text-end">Action</th>
                    </tr>
                </thead>
                <tbody id="listings-tbody">
                    <?php if (empty($pendingProperties)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-open fa-2x d-block mb-3 opacity-50"></i>
                                <span class="small">No pending onboarding files registered. All clear!</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pendingProperties as $house): ?>
                            <tr class="listing-row">
                                <td class="py-3 px-4">
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <span class="text-muted small" style="font-size: 0.75rem;"><?php echo htmlspecialchars($house['contact_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td>
                                    <div class="text-dark"><?php echo htmlspecialchars($house['town'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <span class="text-muted small" style="font-size: 0.75rem;"><?php echo htmlspecialchars($house['address'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium"><?php echo htmlspecialchars($house['firstname'] . ' ' . $house['lastname'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <span class="text-muted small" style="font-size: 0.75rem;"><?php echo htmlspecialchars($house['owner_email'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="text-muted">
                                    <?php echo date('M d, Y h:i A', strtotime($house['created_at'])); ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($house['status'] === 'Approved'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle py-1.5 px-3 rounded-pill small">
                                            Approved
                                        </span>
                                    <?php elseif ($house['status'] === 'Pending'): ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle py-1.5 px-3 rounded-pill small">
                                            Pending
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1.5 px-3 rounded-pill small">
                                            Rejected
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <a href="<?php echo BASE_URL; ?>/admin/verify-house/<?php echo (int)$house['id']; ?>" class="btn btn-dark btn-sm py-1.5 px-3">
                                        <i class="fa-solid fa-shield-halved me-1"></i> Verify File
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Dynamic Page Navigation footer segment -->
        <div class="card-footer bg-white py-3 border-top border-light-subtle d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
            <div class="text-muted small" id="pagination-info">
                Showing <span id="start-idx" class="fw-semibold">0</span> to <span id="end-idx" class="fw-semibold">0</span> of <span id="total-idx" class="fw-semibold">0</span> entries
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0" id="pagination-buttons">
                    <!-- Nav links will render dynamically via javascript -->
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rowsPerPage = 5; // Adjust maximum listing row limit here
    const tbody = document.getElementById('listings-tbody');
    if (!tbody) return;

    const rows = Array.from(tbody.getElementsByClassName('listing-row'));
    const totalRows = rows.length;

    // Handle initial state if list is empty
    if (totalRows === 0) {
        const footer = document.getElementById('pagination-info');
        if (footer && footer.parentElement) {
            footer.parentElement.style.setProperty('display', 'none', 'important');
        }
        return;
    }

    let currentPage = 1;
    const totalPages = Math.ceil(totalRows / rowsPerPage);

    /**
     * Display a specific page number, hiding off-index entries
     */
    function showPage(page) {
        currentPage = page;
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.forEach((row, idx) => {
            if (idx >= start && idx < end) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Update entries label variables
        document.getElementById('start-idx').textContent = start + 1;
        document.getElementById('end-idx').textContent = Math.min(end, totalRows);
        document.getElementById('total-idx').textContent = totalRows;

        renderButtons();
    }

    /**
     * Generate modular pagination controls dynamically
     */
    function renderButtons() {
        const container = document.getElementById('pagination-buttons');
        if (!container) return;
        container.innerHTML = '';

        // "Previous" Nav Trigger link
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<button class="page-link text-dark" ${currentPage === 1 ? 'disabled' : ''} style="box-shadow: none;">Previous</button>`;
        prevLi.addEventListener('click', function() {
            if (currentPage > 1) showPage(currentPage - 1);
        });
        container.appendChild(prevLi);

        // Numeric button items logic
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${currentPage === i ? 'active' : ''}`;
            
            // Modern, minimalist dark theme styles for the active page button
            const customStyle = currentPage === i 
                ? 'background-color: #1a1a1a; border-color: #1a1a1a; color: #ffffff !important;' 
                : 'color: #1a1a1a;';
                
            li.innerHTML = `<button class="page-link" style="box-shadow: none; ${customStyle}">${i}</button>`;
            li.addEventListener('click', function() {
                showPage(i);
            });
            container.appendChild(li);
        }

        // "Next" Nav Trigger link
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<button class="page-link text-dark" ${currentPage === totalPages ? 'disabled' : ''} style="box-shadow: none;">Next</button>`;
        nextLi.addEventListener('click', function() {
            if (currentPage < totalPages) showPage(currentPage + 1);
        });
        container.appendChild(nextLi);
    }

    // Default initialization page
    showPage(1);
});
</script>

<?php
// Include standard dynamic footer
require_once dirname(__DIR__) . '/templates/footer.php';
?>