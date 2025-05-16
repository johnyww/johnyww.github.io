<?php
/**
 * Customer Saved Designs Page
 * Regashi Printing Website
 */

// Set page title
$page_title = "Saved Designs";

// Include config and functions
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isLoggedIn()) {
    // Redirect to login page
    header("Location: " . SITE_URL . "/auth/login.php");
    exit;
}

// Check if user is admin
if (isAdmin()) {
    // Redirect to admin dashboard
    header("Location: " . SITE_URL . "/admin/index.php");
    exit;
}

// Get user information
$user = getUserById($pdo, $_SESSION['user_id']);

if (!$user) {
    // If user not found, log out and redirect to login page
    session_destroy();
    header("Location: " . SITE_URL . "/auth/login.php");
    exit;
}

// Process design deletion
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_design'])) {
    $designId = intval($_POST['design_id']);
    
    try {
        // Get design details to delete file
        $stmt = $pdo->prepare("SELECT design_file FROM saved_designs WHERE design_id = :design_id AND user_id = :user_id");
        $stmt->bindParam(':design_id', $designId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        
        $design = $stmt->fetch();
        
        if ($design) {
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM saved_designs WHERE design_id = :design_id AND user_id = :user_id");
            $stmt->bindParam(':design_id', $designId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            
            // Delete file from server (if exists)
            $filePath = '../assets/uploads/designs/' . $design['design_file'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $success = "Design has been deleted successfully.";
        } else {
            $error = "Design not found or you don't have permission to delete it.";
        }
    } catch(PDOException $e) {
        $error = "An error occurred while deleting the design. Please try again.";
        error_log("Design deletion error: " . $e->getMessage());
    }
}

// Get saved designs
$savedDesigns = getUserSavedDesigns($pdo, $_SESSION['user_id']);

// Include header
include_once '../includes/header.php';
?>

<!-- Saved Designs Page Header -->
<section class="page-header bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="fw-bold mb-0">Saved Designs</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/customer/dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Saved Designs</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="<?php echo SITE_URL; ?>/services.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Create New Design
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Saved Designs Content -->
<section class="saved-designs-section py-5">
    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (count($savedDesigns) > 0): ?>
            <div class="row g-4">
                <?php foreach ($savedDesigns as $design): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card saved-design-card h-100 border shadow-sm">
                            <div class="card-img-top saved-design-img position-relative">
                                <?php 
                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                                $fileExtension = pathinfo($design['design_file'], PATHINFO_EXTENSION);
                                ?>
                                
                                <?php if (in_array(strtolower($fileExtension), $imageExtensions)): ?>
                                    <img src="<?php echo SITE_URL; ?>/assets/uploads/designs/<?php echo $design['design_file']; ?>" class="img-fluid" alt="<?php echo $design['design_name']; ?>">
                                <?php else: ?>
                                    <div class="file-icon p-4 text-center">
                                        <i class="fas fa-file-alt fa-4x text-primary"></i>
                                        <div class="mt-2">.<?php echo $fileExtension; ?> file</div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="design-actions position-absolute top-0 end-0 p-2">
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" id="designMenuButton-<?php echo $design['design_id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="designMenuButton-<?php echo $design['design_id']; ?>">
                                            <li>
                                                <a class="dropdown-item" href="<?php echo SITE_URL; ?>/assets/uploads/designs/<?php echo $design['design_file']; ?>" target="_blank">
                                                    <i class="fas fa-eye me-2"></i> View Design
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="<?php echo SITE_URL; ?>/services/<?php echo getServiceUrlByProductId($design['product_id']); ?>?design_id=<?php echo $design['design_id']; ?>">
                                                    <i class="fas fa-shopping-cart me-2"></i> Order Now
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return confirm('Are you sure you want to delete this design? This action cannot be undone.');">
                                                    <input type="hidden" name="design_id" value="<?php echo $design['design_id']; ?>">
                                                    <button type="submit" name="delete_design" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash-alt me-2"></i> Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-2"><?php echo $design['design_name']; ?></h5>
                                <p class="card-text small text-muted mb-2">
                                    <i class="fas fa-tag me-1"></i> <?php echo $design['product_name']; ?>
                                </p>
                                <p class="card-text small text-muted mb-0">
                                    <i class="far fa-calendar-alt me-1"></i> Saved on <?php echo formatDate($design['created_at']); ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                <a href="<?php echo SITE_URL; ?>/assets/uploads/designs/<?php echo $design['design_file']; ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                                <a href="<?php echo SITE_URL; ?>/services/<?php echo getServiceUrlByProductId($design['product_id']); ?>?design_id=<?php echo $design['design_id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-shopping-cart me-1"></i> Order Now
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-palette fa-4x text-muted"></i>
                    </div>
                    <h3 class="fw-bold mb-3">No Saved Designs Yet</h3>
                    <p class="text-muted mb-4">You haven't saved any designs yet. Start creating designs and save them for future use.</p>
                    <a href="<?php echo SITE_URL; ?>/services.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Create New Design
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Helper function to determine service URL from product ID
function getServiceUrlByProductId($productId) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT c.name as category_name
            FROM products p
            JOIN categories c ON p.category_id = c.category_id
            WHERE p.product_id = :product_id
        ");
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        
        if ($result) {
            $categoryName = strtolower($result['category_name']);
            
            if (strpos($categoryName, 'paper') !== false) {
                return 'paper-printing.php';
            } elseif (strpos($categoryName, 'banner') !== false) {
                return 'banner-printing.php';
            } elseif (strpos($categoryName, 't-shirt') !== false) {
                return 'tshirt-printing.php';
            } elseif (strpos($categoryName, 'bag') !== false) {
                return 'bag-printing.php';
            }
        }
    } catch(PDOException $e) {
        error_log("Error getting service URL: " . $e->getMessage());
    }
    
    return 'tshirt-printing.php'; // Default to T-shirt printing if no match
}
?>

<style>
    .saved-design-img {
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .saved-design-img img {
        max-height: 100%;
        object-fit: contain;
    }
    
    .design-actions .dropdown-toggle::after {
        display: none;
    }
    
    .design-actions .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<?php
// Include footer
include_once '../includes/footer.php';
?>