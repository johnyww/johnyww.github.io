<?php
/**
 * Customer Dashboard
 * Regashi Printing Website
 */

// Set page title
$page_title = "My Dashboard";

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

// Get recent orders
$recentOrders = getUserOrders($pdo, $user['user_id'], 5, 0);

// Get saved designs
$savedDesigns = getUserSavedDesigns($pdo, $user['user_id']);

// Include header
include_once '../includes/header.php';
?>

<!-- Dashboard Header -->
<section class="dashboard-header py-4 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="fw-bold mb-1">My Dashboard</h1>
                <p class="text-muted mb-0">Welcome back, <?php echo $user['first_name']; ?>!</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="btn-group">
                    <a href="<?php echo SITE_URL; ?>/customer/orders.php" class="btn btn-outline-primary">
                        <i class="fas fa-shopping-bag me-2"></i> My Orders
                    </a>
                    <a href="<?php echo SITE_URL; ?>/customer/saved-designs.php" class="btn btn-outline-primary">
                        <i class="fas fa-palette me-2"></i> Saved Designs
                    </a>
                    <a href="<?php echo SITE_URL; ?>/customer/profile.php" class="btn btn-outline-primary">
                        <i class="fas fa-user-edit me-2"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dashboard Content -->
<section class="dashboard-content py-5">
    <div class="container">
        <div class="row">
            <!-- Account Overview -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Account Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="avatar-circle mx-auto mb-3">
                                <span class="avatar-initials"><?php echo substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1); ?></span>
                            </div>
                            <h5 class="fw-bold mb-1"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h5>
                            <p class="text-muted mb-0"><?php echo $user['email']; ?></p>
                        </div>
                        
                        <div class="account-info">
                            <div class="row mb-3">
                                <div class="col-5 text-muted">Username:</div>
                                <div class="col-7 fw-medium"><?php echo $user['username']; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">Member Since:</div>
                                <div class="col-7 fw-medium"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">Phone:</div>
                                <div class="col-7 fw-medium"><?php echo $user['phone'] ?: 'Not provided'; ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">Address:</div>
                                <div class="col-7 fw-medium">
                                    <?php if ($user['address']): ?>
                                        <?php echo $user['address']; ?>,<br>
                                        <?php echo $user['city']; ?>, <?php echo $user['postal_code']; ?>,<br>
                                        <?php echo $user['country']; ?>
                                    <?php else: ?>
                                        Not provided
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="<?php echo SITE_URL; ?>/customer/profile.php" class="btn btn-outline-primary">
                                <i class="fas fa-user-edit me-2"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold">Recent Orders</h5>
                        <a href="<?php echo SITE_URL; ?>/customer/orders.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($recentOrders) > 0): ?>
                                        <?php foreach ($recentOrders as $order): ?>
                                            <tr>
                                                <td><strong><?php echo $order['order_number']; ?></strong></td>
                                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                <td><?php echo formatCurrency($order['total_amount']); ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?php 
                                                        switch ($order['status']) {
                                                            case 'pending': echo 'bg-warning'; break;
                                                            case 'processing': echo 'bg-info'; break;
                                                            case 'printing': echo 'bg-primary'; break;
                                                            case 'out for delivery': echo 'bg-info'; break;
                                                            case 'delivered': echo 'bg-success'; break;
                                                            case 'cancelled': echo 'bg-danger'; break;
                                                            default: echo 'bg-secondary';
                                                        }
                                                        ?>">
                                                        <?php echo ucfirst($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo SITE_URL; ?>/customer/order-details.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <p class="mb-0 text-muted">You don't have any orders yet</p>
                                                <a href="<?php echo SITE_URL; ?>/services.php" class="btn btn-sm btn-primary mt-2">Shop Now</a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Saved Designs -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 fw-bold">Saved Designs</h5>
                        <a href="<?php echo SITE_URL; ?>/customer/saved-designs.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (count($savedDesigns) > 0): ?>
                            <div class="row g-4">
                                <?php foreach (array_slice($savedDesigns, 0, 3) as $design): ?>
                                    <div class="col-md-4">
                                        <div class="card saved-design-card h-100 border shadow-sm">
                                            <div class="card-img-top saved-design-img">
                                                <?php 
                                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                                                $fileExtension = pathinfo($design['design_file'], PATHINFO_EXTENSION);
                                                ?>
                                                
                                                <?php if (in_array(strtolower($fileExtension), $imageExtensions)): ?>
                                                    <img src="<?php echo SITE_URL; ?>/assets/uploads/designs/<?php echo $design['design_file']; ?>" class="img-fluid" alt="<?php echo $design['design_name']; ?>">
                                                <?php else: ?>
                                                    <div class="file-icon">
                                                        <i class="fas fa-file-alt fa-3x"></i>
                                                        <span class="file-ext">.<?php echo $fileExtension; ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-body">
                                                <h6 class="card-title fw-bold mb-1"><?php echo $design['design_name']; ?></h6>
                                                <p class="card-text small text-muted mb-2">
                                                    <i class="fas fa-tag me-1"></i> <?php echo $design['product_name']; ?>
                                                </p>
                                                <p class="card-text small text-muted">
                                                    <i class="far fa-calendar-alt me-1"></i> Saved on <?php echo date('M d, Y', strtotime($design['created_at'])); ?>
                                                </p>
                                            </div>
                                            <div class="card-footer bg-white border-top-0">
                                                <div class="d-flex justify-content-between">
                                                    <a href="<?php echo SITE_URL; ?>/assets/uploads/designs/<?php echo $design['design_file']; ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                                                        <i class="fas fa-eye me-1"></i> View
                                                    </a>
                                                    <a href="<?php echo SITE_URL; ?>/services/tshirt-printing.php" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-shopping-cart me-1"></i> Order
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="fas fa-palette fa-3x text-muted"></i>
                                </div>
                                <h6 class="text-muted mb-2">You don't have any saved designs yet</h6>
                                <p class="text-muted mb-3">Save your designs for quick access when placing future orders</p>
                                <a href="<?php echo SITE_URL; ?>/services.php" class="btn btn-primary">Create a Design</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .avatar-circle {
        width: 80px;
        height: 80px;
        background-color: #4263eb;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .avatar-initials {
        color: white;
        font-size: 24px;
        font-weight: 600;
    }
    
    .saved-design-img {
        height: 180px;
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
    
    .file-icon {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #6c757d;
    }
    
    .file-ext {
        font-size: 14px;
        font-weight: 600;
        margin-top: 5px;
    }
</style>

<?php
// Include footer
include_once '../includes/footer.php';
?>
