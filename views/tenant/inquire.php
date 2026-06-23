<?php
/**
 * File Location: views/tenant/inquire.php
 * File Name: inquire.php
 * Description: Clean, premium, minimalist landlord direct inquiry view utilizing our grayscale theme.
 */

$title = "Inquire";

require_once dirname(__DIR__) . '/templates/header.php';
$house = $house ?? [];
?>

<style>
    .inquire-card {
        border: 1px solid #eaeaea !important;
        background-color: #ffffff;
        transition: transform 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    }
    .inquire-card:hover {
        transform: translateY(-2px);
        border-color: #1a1a1a !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04) !important;
    }
    .action-badge {
        font-size: 0.7rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        font-weight: 700;
    }
    .contact-accent-icon {
        width: 50px;
        height: 50px;
        background-color: #fafafa;
        border: 1px solid #eaeaea;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1a1a1a;
        transition: all 0.2s ease;
    }
    .inquire-card:hover .contact-accent-icon {
        background-color: #1a1a1a;
        border-color: #1a1a1a;
        color: #ffffff;
    }
    .hover-text:hover {
        text-decoration: underline !important;
    }
</style>

<div class="container my-5 mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            
            <!-- Top Return Header Link -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="<?php echo BASE_URL; ?>/tenant/house/view/<?php echo (int)$house['id']; ?>" class="text-decoration-none text-dark small fw-semibold">
                    <i class="fa-solid fa-arrow-left me-2"></i>Return to Property
                </a>
                <span class="badge bg-white text-dark border border-light-subtle py-2 px-3 rounded-1 small fw-semibold shadow-sm font-monospace">
                    Direct Inquiry
                </span>
            </div>

            <!-- Page Title Block -->
            <div class="pb-3 mb-4 border-bottom border-light-subtle">
                <span class="text-uppercase text-muted fw-bold small tracking-wider" style="font-size: 0.75rem;">Contact Information Portal</span>
                <h1 class="h2 fw-bold text-dark mb-1">Inquire About This Property</h1>
                <p class="text-muted mb-0 small">Getting in touch for: <strong><?php echo htmlspecialchars($house['name'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
            </div>

            <div class="row g-4 mb-4">
                
                <!-- Direct Phone and SMS Card -->
                <div class="col-md-6 col-12">
                    <div class="card inquire-card rounded-4 p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="contact-accent-icon">
                                    <i class="fa-solid fa-phone fs-5"></i>
                                </div>
                                <span class="badge bg-light text-dark border action-badge">Phone & SMS</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Call or Text</h5>
                            <p class="text-muted small mb-4">Feel free to dial directly or send a short text message to ask about room viewing schedules, available slots, or custom amenities.</p>
                        </div>
                        
                        <div class="border-top border-light-subtle pt-3 mt-auto">
                            <span class="text-muted small d-block mb-1">Landlord Mobile Line <span class="fst-italic small">(Press/ Click the Number)</span></span>
                            <a href="tel:<?php echo htmlspecialchars($house['contact_number'], ENT_QUOTES, 'UTF-8'); ?>" class="fs-5 fw-bold text-dark text-decoration-none font-monospace hover-text">
                                <?php echo htmlspecialchars($house['contact_number'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Support Email Card -->
                <div class="col-md-6 col-12">
                    <div class="card inquire-card rounded-4 p-4 h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="contact-accent-icon">
                                    <i class="fa-solid fa-envelope fs-5"></i>
                                </div>
                                <span class="badge bg-light text-dark border action-badge">Email</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Write an Email</h5>
                            <p class="text-muted small mb-4">Send a formal message detailing your prospective lease dates, occupant information, or any documentation questions you have for the owner.</p>
                        </div>
                        
                        <div class="border-top border-light-subtle pt-3 mt-auto">
                            <span class="text-muted small d-block mb-1">Send Messages <span class="fst-italic small">(Press/ Click the Email)</span></span>
                            <a href="mailto:<?php echo htmlspecialchars($house['owner_email'], ENT_QUOTES, 'UTF-8'); ?>" class="fs-5 fw-bold text-dark text-decoration-none text-truncate d-block hover-text" title="<?php echo htmlspecialchars($house['owner_email'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($house['owner_email'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Friendly Guidance & Safety Card -->
            <div class="card border border-light-subtle bg-light rounded-3 p-4 mb-4">
                <div class="d-flex align-items-start">
                    <div class="text-dark fs-3 me-3 mt-1">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Tips for Communicating Safely</h6>
                        <p class="text-muted small mb-0 leading-relaxed">
                            When inquiring with the partner landlord, kindly introduce yourself as a <strong>RENTORA PH</strong> user, specify the room type you are interested in, and ask if any reservation deposits are required. Always verify lease agreements before making financial transactions.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Direct Owner Identification Profile -->
            <div class="card border border-light-subtle rounded-3 bg-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-dark text-white rounded-circle p-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-user-tie fs-5"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Verified Property Owner</span>
                        <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($house['owner_firstname'] . ' ' . $house['owner_lastname'], ENT_QUOTES, 'UTF-8'); ?></h6>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/templates/footer.php'; ?>