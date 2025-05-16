<?php
/**
 * 404 Error Page
 * Regashi Printing Website
 */

// Set page title
$page_title = "Page Not Found";

// Include config
require_once 'includes/config.php';

// Include header
include_once 'includes/header.php';
?>

<section class="error-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-center">
                    <div class="error-code display-1 fw-bold text-primary mb-4">404</div>
                    <h1 class="fw-bold mb-3">Page Not Found</h1>
                    <p class="lead text-muted mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i> Back to Home
                        </a>
                        <a href="<?php echo SITE_URL; ?>/services.php" class="btn btn-outline-primary">
                            <i class="fas fa-shopping-bag me-2"></i> Browse Services
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include_once 'includes/footer.php';
?>