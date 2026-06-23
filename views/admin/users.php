<?php
/**
 * File Location: views/admin/users.php
 * File Name: users.php
 * Description: Clean, minimal table dashboard for managing and monitoring system user and owner accounts.
 */

// Include standard header template
require_once __DIR__ . '/../templates/header.php';
$users = $users ?? [];
$currentUserId = $_SESSION['user_id'] ?? 0;
?>

<style>
    .role-badge-admin {
        background-color: #1a1a1a;
        color: #ffffff;
    }
    .role-badge-owner {
        background-color: #f8f9fa;
        color: #1a1a1a;
        border: 1px solid #dcdcdc;
    }
    .role-badge-tenant {
        background-color: #e9ecef;
        color: #495057;
    }
</style>

<div class="container my-5 mt-4">
    
    <!-- Top Navigation Links -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="text-decoration-none text-dark small fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Dashboard Console
        </a>
        <span class="badge bg-light text-dark border py-1.5 px-3 rounded-1 small font-monospace">
            System Accounts
        </span>
    </div>

    <!-- Header Block -->
    <div class="border-bottom border-light-subtle pb-4 mb-5">
        <h1 class="h2 fw-bold text-dark mb-1">User & Owner Registry</h1>
        <p class="text-muted mb-0 small">Monitor registered partner accounts, review owner registration volumes, or suspend accounts violating platform guidelines.</p>
    </div>

    <!-- Feedback Alerts -->
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

    <!-- Search Tool Panel & Filters -->
    <div class="card shadow-sm border border-light-subtle rounded-1 bg-white mb-4">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-8 col-12">
                    <div class="input-group">
                        <input type="text" id="user-search" class="form-control" placeholder="Type to search name, email, or contact number...">
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <select id="role-filter" class="form-select">
                        <option value="ALL">All Roles</option>
                        <option value="Owner">Owners Only</option>
                        <option value="Tenant">Tenants Only</option>
                        <option value="Admin">Administrators Only</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Accounts Table Card -->
    <div class="card shadow-sm border border-light-subtle rounded-1 bg-white">
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light text-secondary" style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa; box-shadow: inset 0 -1px 0 rgba(0,0,0,.1);">
                    <tr>
                        <th class="py-3 px-4 border-0">Full Name</th>
                        <th class="py-3 border-0">Email Profile</th>
                        <th class="py-3 border-0">Role</th>
                        <th class="py-3 border-0 text-center">Properties</th>
                        <th class="py-3 border-0 text-center">Status</th>
                        <th class="py-3 px-4 border-0 text-end">Action</th>
                    </tr>
                </thead>
                <tbody id="users-tbody">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-users-slash fa-2x d-block mb-3 opacity-50"></i>
                                <span class="small">No user records found in database directory.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="user-row" data-role="<?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?>">
                                <td class="py-3 px-4">
                                    <div class="fw-bold text-dark search-target"><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <span class="text-muted small" style="font-size: 0.75rem;"><i class="fa-solid fa-phone me-1"></i><?php echo htmlspecialchars($user['contact'] ?: 'No phone logged', ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td>
                                    <span class="text-dark search-target"><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $roleClass = 'role-badge-tenant';
                                    if ($user['role'] === 'Admin') $roleClass = 'role-badge-admin';
                                    if ($user['role'] === 'Owner') $roleClass = 'role-badge-owner';
                                    ?>
                                    <span class="badge <?php echo $roleClass; ?> py-1.5 px-2.5 rounded-1 small">
                                        <?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($user['role'] === 'Owner'): ?>
                                        <span class="badge bg-light text-dark border font-monospace px-3 py-1.5 rounded-pill" title="Boarding Houses registered by this Owner">
                                            <?php echo (int)$user['property_count']; ?> Listings
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($user['status'] === 'Active'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-3 rounded-pill small">
                                            Active
                                        </span>
                                    <?php elseif ($user['status'] === 'Suspended'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1 px-3 rounded-pill small">
                                            Suspended
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle py-1 px-3 rounded-pill small">
                                            <?php echo htmlspecialchars($user['status'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <?php if ($user['id'] === $currentUserId): ?>
                                        <span class="text-muted small font-monospace" style="font-size: 0.75rem;"><i class="fa-solid fa-user-shield me-1"></i>You (Self)</span>
                                    <?php else: ?>
                                        <?php if ($user['status'] === 'Suspended'): ?>
                                            <button type="button" class="btn btn-outline-success btn-sm py-1 px-3 rounded-1 fw-bold" 
                                                    data-bs-toggle="modal" data-bs-target="#confirmStatusModal" 
                                                    data-user-id="<?php echo (int)$user['id']; ?>" 
                                                    data-user-name="<?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-action-label="Reactivate"
                                                    data-action-class="btn-success">
                                                Reactivate
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-outline-danger btn-sm py-1 px-3 rounded-1 fw-bold" 
                                                    data-bs-toggle="modal" data-bs-target="#confirmStatusModal" 
                                                    data-user-id="<?php echo (int)$user['id']; ?>" 
                                                    data-user-name="<?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-action-label="Suspend"
                                                    data-action-class="btn-danger">
                                                Suspend
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <div class="card-footer bg-white py-3 border-top border-light-subtle d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3">
            <div class="text-muted small" id="pagination-info">
                Showing <span id="start-idx" class="fw-semibold">0</span> to <span id="end-idx" class="fw-semibold">0</span> of <span id="total-idx" class="fw-semibold">0</span> entries
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0" id="pagination-buttons"></ul>
            </nav>
        </div>
    </div>
</div>

<!-- Modal 1: Confirmation Overlay for Status Toggling -->
<div class="modal fade" id="confirmStatusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-1 border border-light-subtle">
            <div class="modal-header border-bottom border-light-subtle py-3">
                <h6 class="modal-title fw-bold text-dark" id="statusModalLabel"><i class="fa-solid fa-user-shield me-2 text-dark"></i>Confirm Account Action</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo BASE_URL; ?>/admin/user/toggle-status" method="POST">
                <?php echo \App\Core\Security::csrfField(); ?>
                <input type="hidden" id="modal-user-id" name="user_id" value="">
                
                <div class="modal-body py-4">
                    <p class="text-dark small mb-2">
                        Are you sure you want to <strong id="modal-action-text" class="text-lowercase">action</strong> the user account for <strong id="modal-user-name">User Name</strong>?
                    </p>
                    <p class="text-muted small mb-0">
                        Suspended users are immediately blocked from logging into the portal or managing listing entries. Reactivating a profile instantly restores full system privileges.
                    </p>
                </div>
                <div class="modal-footer border-top border-light-subtle py-2">
                    <button type="button" class="btn btn-light btn-sm rounded-1" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="modal-submit-btn" class="btn btn-sm rounded-1 px-4 text-white">Proceed</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Search, Filter & Pagination Client Side Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rowsPerPage = 5;
    const tbody = document.getElementById('users-tbody');
    const searchInput = document.getElementById('user-search');
    const roleFilter = document.getElementById('role-filter');
    if (!tbody) return;

    let rows = Array.from(tbody.getElementsByClassName('user-row'));
    let filteredRows = [...rows];
    let currentPage = 1;

    /**
     * Sifts and paginates items depending on criteria settings
     */
    function applyFilterAndPagination() {
        const query = searchInput.value.toLowerCase().trim();
        const roleSelected = roleFilter.value;

        // Apply filters
        filteredRows = rows.filter(row => {
            const targets = Array.from(row.getElementsByClassName('search-target'));
            const matchesSearch = targets.some(target => target.textContent.toLowerCase().includes(query));
            
            const matchesRole = (roleSelected === 'ALL' || row.getAttribute('data-role') === roleSelected);

            return matchesSearch && matchesRole;
        });

        const totalRows = filteredRows.length;
        if (totalRows === 0) {
            document.getElementById('pagination-info').parentElement.style.setProperty('display', 'none', 'important');
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted"><i class="fa-solid fa-magnifying-glass-minus fa-2x mb-3 d-block opacity-50"></i>No active user accounts match your search parameters.</td></tr>`;
            return;
        }

        document.getElementById('pagination-info').parentElement.style.removeProperty('display');
        const totalPages = Math.ceil(totalRows / rowsPerPage);
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        tbody.innerHTML = '';
        filteredRows.slice(start, end).forEach(row => tbody.appendChild(row));

        document.getElementById('start-idx').textContent = start + 1;
        document.getElementById('end-idx').textContent = Math.min(end, totalRows);
        document.getElementById('total-idx').textContent = totalRows;

        renderButtons(totalPages);
    }

    function renderButtons(totalPages) {
        const container = document.getElementById('pagination-buttons');
        if (!container) return;
        container.innerHTML = '';

        // Previous Button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<button class="page-link text-dark" ${currentPage === 1 ? 'disabled' : ''}>Previous</button>`;
        prevLi.addEventListener('click', () => { if (currentPage > 1) { currentPage--; applyFilterAndPagination(); } });
        container.appendChild(prevLi);

        // Numerics
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${currentPage === i ? 'active' : ''}`;
            const customStyle = currentPage === i ? 'background-color: #1a1a1a; border-color: #1a1a1a; color: #ffffff !important;' : 'color: #1a1a1a;';
            li.innerHTML = `<button class="page-link" style="box-shadow: none; ${customStyle}">${i}</button>`;
            li.addEventListener('click', () => { currentPage = i; applyFilterAndPagination(); });
            container.appendChild(li);
        }

        // Next Button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<button class="page-link text-dark" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>`;
        nextLi.addEventListener('click', () => { if (currentPage < totalPages) { currentPage++; applyFilterAndPagination(); } });
        container.appendChild(nextLi);
    }

    // Attach interaction callbacks
    searchInput.addEventListener('input', () => { currentPage = 1; applyFilterAndPagination(); });
    roleFilter.addEventListener('change', () => { currentPage = 1; applyFilterAndPagination(); });

    // Initialize display list
    applyFilterAndPagination();

    // Modal data bindings listener
    const confirmModal = document.getElementById('confirmStatusModal');
    if (confirmModal) {
        confirmModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            const actionLabel = button.getAttribute('data-action-label');
            const actionClass = button.getAttribute('data-action-class');

            // Update modal text fields
            document.getElementById('modal-user-id').value = userId;
            document.getElementById('modal-user-name').textContent = userName;
            document.getElementById('modal-action-text').textContent = actionLabel;

            // Adjust submit button styles
            const submitBtn = document.getElementById('modal-submit-btn');
            submitBtn.textContent = actionLabel;
            submitBtn.className = `btn btn-sm rounded-1 px-4 ${actionClass}`;
        });
    }
});
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>