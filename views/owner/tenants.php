<?php
/**
 * File Location: views/owner/tenants.php
 * File Name: tenants.php
 * Description: Premium monochrome-styled dashboard to manage and list active boarding house occupants.
 * Fully optimized with search features, paginations, and modern responsive stack grids.
 */

use App\Core\Security;

$title = "Manage Occupants";
require_once dirname(__DIR__) . '/templates/header.php';

$tenants = $tenants ?? [];
$selectedRoom = $selectedRoom ?? null;
?>

<style>
    /* Premium monochrome visual styling standards */
    .tenant-card {
        border: 1px solid #eaeaea !important;
        background-color: #ffffff;
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .tenant-card:hover {
        transform: translateY(-2px);
        border-color: #1a1a1a !important;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04) !important;
    }

    /* Interactive mobile adjustments for layout scaling */
    @media (max-width: 767.98px) {
        .table-view-desktop {
            display: none !important;
        }
        .cards-view-mobile {
            display: block !important;
        }
    }
    @media (min-width: 768px) {
        .table-view-desktop {
            display: block !important;
        }
        .cards-view-mobile {
            display: none !important;
        }
    }
</style>

<div class="container my-5 mt-4">
    
    <!-- Top Nav links -->
    <div class="mb-4">
        <a href="<?php echo BASE_URL; ?>/owner/rooms/<?php echo (int)$house['id']; ?>" class="text-decoration-none text-dark small fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Return to Rooms
        </a>
    </div>

    <!-- Page Title Header Block -->
    <div class="border-bottom border-light-subtle pb-4 mb-5">
        <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Occupant Directory Portal</span>
        <h1 class="h2 fw-bold text-dark mb-1">Active Room Occupants</h1>
        <p class="text-muted mb-0 small">Manage currently checked-in tenants, monitor check-in dates, and execute check-out actions to update vacancies.</p>
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

    <!-- Search Tool Panel -->
    <div class="card shadow-sm border border-light-subtle rounded-3 bg-white mb-4">
        <div class="card-body p-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" id="tenant-search" class="form-control border-start-0 ps-0" placeholder="Type to search occupant name, room, or boarding house...">
            </div>
        </div>
    </div>

    <!-- Occupant Grid Layout -->
    <?php if (empty($tenants)): ?>
        <div class="card shadow-sm border border-light-subtle rounded-3 bg-white text-center py-5">
            <div class="card-body py-5">
                <i class="fa-solid fa-users-slash text-muted fs-1 mb-3 opacity-40"></i>
                <h5 class="fw-semibold text-dark">No Active Occupants Found</h5>
                <p class="text-muted small mb-0">No active tenant bookings found for your filter or query.</p>
            </div>
        </div>
    <?php else: ?>

        <!-- DESKTOP MODE: Elegant Structured Tabular View -->
        <div class="card shadow-sm border border-light-subtle rounded-3 bg-white overflow-hidden table-view-desktop">
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light text-secondary" style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa; box-shadow: inset 0 -1px 0 rgba(0,0,0,.1);">
                        <tr>
                            <th class="py-3 px-4 border-0">Tenant Occupant Name</th>
                            <th class="py-3 border-0">Boarding House</th>
                            <th class="py-3 border-0">Room Assigned</th>
                            <th class="py-3 border-0">Check-in Date</th>
                            <th class="py-3 px-4 border-0 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tenants-tbody">
                        <?php foreach ($tenants as $tenant): ?>
                            <tr class="tenant-row">
                                <td class="py-3 px-4">
                                    <div class="fw-bold text-dark search-target"><?php echo htmlspecialchars($tenant['firstname'] . ' ' . $tenant['lastname'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <span class="text-muted small" style="font-size: 0.75rem;"><i class="fa-solid fa-phone me-1"></i><?php echo htmlspecialchars($tenant['contact_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium search-target"><?php echo htmlspecialchars($tenant['house_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </td>
                                <td>
                                    <div class="text-dark search-target"><?php echo htmlspecialchars($tenant['room_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <span class="text-muted small" style="font-size: 0.75rem;">Rent Rate: ₱<?php echo number_format($tenant['room_price'], 2); ?></span>
                                </td>
                                <td class="text-muted">
                                    <?php echo date('M d, Y h:i A', strtotime($tenant['checkin_date'])); ?>
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <button type="button" class="btn btn-outline-danger btn-sm py-1.5 px-3 rounded-1 fw-bold checkout-btn"
                                            data-bs-toggle="modal" data-bs-target="#checkoutConfirmModal"
                                            data-id="<?php echo (int)$tenant['application_id']; ?>"
                                            data-name="<?php echo htmlspecialchars($tenant['firstname'] . ' ' . $tenant['lastname'], ENT_QUOTES, 'UTF-8'); ?>"
                                            data-room="<?php echo htmlspecialchars($tenant['room_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <i class="fa-solid fa-right-from-bracket me-1"></i> Check Out
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Dynamic Page Navigation footer segment -->
            <div class="card-footer bg-white py-3 border-top border-light-subtle d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
                <div class="text-muted small" id="pagination-info">
                    Showing <span id="start-idx" class="fw-semibold">0</span> to <span id="end-idx" class="fw-semibold">0</span> of <span id="total-idx" class="fw-semibold">0</span> entries
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0" id="pagination-buttons"></ul>
                </nav>
            </div>
        </div>

        <!-- MOBILE MODE: Responsive visual cards view -->
        <div class="row g-3 cards-view-mobile" style="display: none;" id="mobile-tenants-list">
            <?php foreach ($tenants as $tenant): ?>
                <div class="col-12 mobile-tenant-card-item" 
                     data-search-name="<?php echo htmlspecialchars(strtolower($tenant['firstname'] . ' ' . $tenant['lastname'] . ' ' . $tenant['house_name'] . ' ' . $tenant['room_name']), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="card tenant-card rounded-3 p-3 shadow-sm">
                        
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="fw-bold text-dark fs-6 d-block"><?php echo htmlspecialchars($tenant['firstname'] . ' ' . $tenant['lastname'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="text-muted small" style="font-size: 0.75rem;"><i class="fa-solid fa-phone me-1"></i><?php echo htmlspecialchars($tenant['contact_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <span class="badge bg-light text-dark border py-1 px-2 font-monospace" style="font-size: 0.65rem;">
                                Occupant
                            </span>
                        </div>

                        <div class="py-2 border-top border-bottom border-light-subtle my-2">
                            <span class="text-muted small d-block mb-1" style="font-size: 0.7rem; text-transform: uppercase;">Room Details</span>
                            <span class="text-dark fw-bold d-block" style="font-size: 0.85rem;"><?php echo htmlspecialchars($tenant['house_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="text-muted small d-block" style="font-size: 0.8rem;">Layout: <?php echo htmlspecialchars($tenant['room_name'], ENT_QUOTES, 'UTF-8'); ?> (₱<?php echo number_format($tenant['room_price'], 2); ?>)</span>
                            <span class="text-muted small d-block mt-1" style="font-size: 0.75rem;">Checked-in: <?php echo date('M d, Y', strtotime($tenant['checkin_date'])); ?></span>
                        </div>

                        <div class="d-grid mt-2">
                            <button type="button" class="btn btn-outline-danger btn-sm py-2 rounded-2 fw-bold checkout-btn"
                                    data-bs-toggle="modal" data-bs-target="#checkoutConfirmModal"
                                    data-id="<?php echo (int)$tenant['application_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($tenant['firstname'] . ' ' . $tenant['lastname'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-room="<?php echo htmlspecialchars($tenant['room_name'], ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="fa-solid fa-right-from-bracket me-2"></i>Execute Check Out
                            </button>
                        </div>
                        
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="col-12 text-center py-4 d-none" id="no-mobile-results">
                <i class="fa-solid fa-magnifying-glass-minus text-muted fs-3 mb-2 d-block opacity-50"></i>
                <span class="text-muted small">No active occupants match your search keywords.</span>
            </div>
        </div>

    <?php endif; ?>

</div>

<!-- Check Out Confirmation Modal -->
<div class="modal fade" id="checkoutConfirmModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-header border-bottom border-light-subtle py-3 px-4">
                <h6 class="modal-title fw-bold text-dark" id="checkoutModalLabel">
                    <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>Confirm Occupant Checkout
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="<?php echo BASE_URL; ?>/owner/tenant/checkout" method="POST">
                <?php echo Security::csrfField(); ?>
                <input type="hidden" id="modal-application-id" name="application_id" value="">
                
                <div class="modal-body p-4">
                    <div class="alert border border-danger-subtle rounded-3 p-3 mb-4 bg-white" style="color: #721c24; background-color: #fff5f5 !important;" role="alert">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-info mt-0.5 flex-shrink-0 text-danger"></i>
                            <div class="small">
                                <strong class="d-block mb-1">Room Vacancy Restoration Notice:</strong>
                                <span class="leading-relaxed">
                                    Executing checkout will automatically update the tenant’s status to <strong>Checked Out</strong> and increment the available bed vacancy count in <strong><span id="modal-room-name-bold">the room</span></strong> back to the availability pool.
                                </span>
                            </div>
                        </div>
                    </div>

                    <p class="small text-muted mb-0">
                        Are you sure you want to log checkout procedures for occupant <strong id="modal-tenant-name-bold">User Name</strong>? This action updates occupancy logs immediately for platform accounting.
                    </p>
                </div>
                
                <div class="modal-footer border-top border-light-subtle py-2 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light btn-sm rounded-1 px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-1 px-4 fw-bold">Confirm Checkout</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkoutModal = document.getElementById('checkoutConfirmModal');
    if (checkoutModal) {
        checkoutModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const applicationId = button.getAttribute('data-id');
            const tenantName = button.getAttribute('data-name');
            const roomName = button.getAttribute('data-room');

            document.getElementById('modal-application-id').value = applicationId;
            document.getElementById('modal-tenant-name-bold').textContent = tenantName;
            document.getElementById('modal-room-name-bold').textContent = roomName;
        });
    }

    // Interactive Client-side filter mechanisms for both Desktop & Mobile indexes
    const searchInput = document.getElementById('tenant-search');
    const tbody = document.getElementById('tenants-tbody');
    const mobileContainer = document.getElementById('mobile-tenants-list');

    // Setup desktop pagination
    const rowsPerPage = 5;
    let desktopRows = tbody ? Array.from(tbody.getElementsByClassName('tenant-row')) : [];
    let filteredDesktopRows = [...desktopRows];
    let currentPage = 1;

    function applyPagination() {
        if (!tbody) return;

        const totalRows = filteredDesktopRows.length;
        if (totalRows === 0) {
            document.getElementById('pagination-info').parentElement.style.setProperty('display', 'none', 'important');
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted"><i class="fa-solid fa-magnifying-glass-minus fa-2x mb-3 d-block opacity-50"></i>No active occupants match your search keywords.</td></tr>`;
            return;
        }

        document.getElementById('pagination-info').parentElement.style.removeProperty('display');
        const totalPages = Math.ceil(totalRows / rowsPerPage);
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        tbody.innerHTML = '';
        filteredDesktopRows.slice(start, end).forEach(row => tbody.appendChild(row));

        document.getElementById('start-idx').textContent = start + 1;
        document.getElementById('end-idx').textContent = Math.min(end, totalRows);
        document.getElementById('total-idx').textContent = totalRows;

        renderButtons(totalPages);
    }

    function renderButtons(totalPages) {
        const container = document.getElementById('pagination-buttons');
        if (!container) return;
        container.innerHTML = '';

        // Previous button link
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<button class="page-link text-dark" ${currentPage === 1 ? 'disabled' : ''}>Previous</button>`;
        prevLi.addEventListener('click', () => { if (currentPage > 1) { currentPage--; applyPagination(); } });
        container.appendChild(prevLi);

        // Numerics links
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${currentPage === i ? 'active' : ''}`;
            const customStyle = currentPage === i ? 'background-color: #1a1a1a; border-color: #1a1a1a; color: #ffffff !important;' : 'color: #1a1a1a;';
            li.innerHTML = `<button class="page-link" style="box-shadow: none; ${customStyle}">${i}</button>`;
            li.addEventListener('click', () => { currentPage = i; applyPagination(); });
            container.appendChild(li);
        }

        // Next button link
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<button class="page-link text-dark" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>`;
        nextLi.addEventListener('click', () => { if (currentPage < totalPages) { currentPage++; applyPagination(); } });
        container.appendChild(nextLi);
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = searchInput.value.toLowerCase().trim();

            // 1. Desktop Search Filter
            filteredDesktopRows = desktopRows.filter(row => {
                const targets = Array.from(row.getElementsByClassName('search-target'));
                return targets.some(target => target.textContent.toLowerCase().includes(query));
            });
            currentPage = 1;
            applyPagination();

            // 2. Mobile Cards Search Filter
            if (mobileContainer) {
                const cards = Array.from(mobileContainer.getElementsByClassName('mobile-tenant-card-item'));
                let visibleMobileCount = 0;

                cards.forEach(card => {
                    const text = card.getAttribute('data-search-name');
                    if (text.includes(query)) {
                        card.style.setProperty('display', 'block', 'important');
                        visibleMobileCount++;
                    } else {
                        card.style.setProperty('display', 'none', 'important');
                    }
                });

                const noMobileMsg = document.getElementById('no-mobile-results');
                if (noMobileMsg) {
                    if (visibleMobileCount === 0) noMobileMsg.classList.remove('d-none');
                    else noMobileMsg.classList.add('d-none');
                }
            }
        });
    }

    // Load initial pagination state
    applyPagination();
});
</script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>