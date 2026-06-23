<?php
/**
 * File Location: views/admin/rejected_houses.php
 * File Name: rejected_houses.php
 * Description: Grayscale listing layout displaying all rejected applications with searchable reasons.
 */

$title = "Rejected Houses";
require_once dirname(__DIR__) . '/templates/header.php';
$properties = $properties ?? [];
?>

<div class="container my-5 mt-4">
    
    <!-- Top Nav Links -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?php echo BASE_URL; ?>/admin/dashboard" class="text-decoration-none text-dark small fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Dashboard Console
        </a>
        <span class="badge bg-light text-danger border border-danger-subtle py-1.5 px-3 rounded-1 small font-monospace">
            Rejected Applications Logs
        </span>
    </div>

    <!-- Header Frame -->
    <div class="border-bottom border-light-subtle pb-4 mb-5">
        <h1 class="h2 fw-bold text-dark mb-1">Rejected Boarding Houses</h1>
        <p class="text-muted mb-0 small">Review rejection logs, write/update structural failure descriptions, or permanently delete directories.</p>
    </div>

    <!-- Alerts -->
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

    <!-- Search Tool Panel -->
    <div class="card shadow-sm border border-light-subtle rounded-1 bg-white mb-4">
        <div class="card-body p-3">
            <div class="input-group">
                <input type="text" id="property-search" class="form-control" placeholder="Search by name, owner, municipality, or rejection reason keyword...">
            </div>
        </div>
    </div>

    <!-- Registry Card -->
    <div class="card shadow-sm border border-light-subtle rounded-1 bg-white">
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light text-secondary" style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa; box-shadow: inset 0 -1px 0 rgba(0,0,0,.1);">
                    <tr>
                        <th class="py-3 px-4 border-0">Property Name</th>
                        <th class="py-3 border-0">Owner Partner</th>
                        <th class="py-3 border-0">Town / Address</th>
                        <th class="py-3 border-0">Rejection Reason logged</th>
                        <th class="py-3 px-4 border-0 text-end">Action</th>
                    </tr>
                </thead>
                <tbody id="rejected-tbody">
                    <?php if (empty($properties)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-open fa-2x d-block mb-3 opacity-50"></i>
                                <span class="small">No rejected properties found in database registry.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($properties as $house): ?>
                            <tr class="property-row">
                                <td class="py-3 px-4">
                                    <div class="fw-bold text-dark search-target"><?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <span class="text-muted small" style="font-size: 0.75rem;"><?php echo htmlspecialchars($house['contact_number'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium search-target"><?php echo htmlspecialchars($house['firstname'] . ' ' . $house['lastname'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <span class="text-muted small d-block search-target"><?php echo htmlspecialchars($house['owner_email'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td>
                                    <div class="text-dark search-target"><?php echo htmlspecialchars($house['town'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <span class="text-muted small text-truncate d-block search-target" style="max-width: 200px;" title="<?php echo htmlspecialchars($house['address'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($house['address'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                <td class="search-target">
                                    <?php if (!empty($house['rejection_reason'])): ?>
                                        <span class="text-danger small fw-semibold text-truncate d-block" style="max-width: 280px;" title="<?php echo htmlspecialchars($house['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i><?php echo htmlspecialchars($house['rejection_reason'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small italic">No reason explanation logged.</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <a href="<?php echo BASE_URL; ?>/admin/rejected-house/view/<?php echo (int)$house['id']; ?>" class="btn btn btn-dark btn-sm py-2 px-3">
                                        <i class="fa-solid fa-eye me-1"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controller segment -->
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

<!-- Search & Pagination Logic -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rowsPerPage = 5;
    const tbody = document.getElementById('rejected-tbody');
    const searchInput = document.getElementById('property-search');
    if (!tbody) return;

    let rows = Array.from(tbody.getElementsByClassName('property-row'));
    let filteredRows = [...rows];
    let currentPage = 1;

    function applyPagination() {
        const totalRows = filteredRows.length;
        if (totalRows === 0) {
            const infoContainer = document.getElementById('pagination-info');
            if (infoContainer && infoContainer.parentElement) {
                infoContainer.parentElement.style.setProperty('display', 'none', 'important');
            }
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-5 text-muted"><i class="fa-solid fa-magnifying-glass-minus fa-2x mb-3 d-block opacity-50"></i>No rejected properties match your keywords.</td></tr>`;
            return;
        }

        const infoContainer = document.getElementById('pagination-info');
        if (infoContainer && infoContainer.parentElement) {
            infoContainer.parentElement.style.removeProperty('display');
        }

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

        // Previous link
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

        // Next link
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextLi.innerHTML = `<button class="page-link text-dark" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>`;
        nextLi.addEventListener('click', () => { if (currentPage < totalPages) { currentPage++; applyPagination(); } });
        container.appendChild(nextLi);
    }

    searchInput.addEventListener('input', function() {
        const query = searchInput.value.toLowerCase().trim();
        filteredRows = rows.filter(row => {
            const targets = Array.from(row.getElementsByClassName('search-target'));
            return targets.some(target => target.textContent.toLowerCase().includes(query));
        });
        currentPage = 1;
        applyPagination();
    });

    applyPagination();
});
</script>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>