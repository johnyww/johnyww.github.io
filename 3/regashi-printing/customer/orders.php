<?php
/**
 * Customer Orders Page
 * Regashi Printing Website
 */

// Set page title
$page_title = "My Orders";

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

// Pagination settings
$ordersPerPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $ordersPerPage;

// Get user's orders
$orders = getUserOrders($pdo, $user['user_id'], $ordersPerPage, $offset);

// Get total number of orders for pagination
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $totalOrders = $stmt->fetch()['total'];
    $totalPages = ceil($totalOrders / $ordersPerPage);
} catch(PDOException $e) {
    error_log("Error counting user orders: " . $e->getMessage());
    $totalOrders = 0;
    $totalPages = 1;
}

// Include header
include_once '../includes/header.php';
?>

<!-- Orders Page Header -->
<section class="page-header bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="fw-bold mb-0">My Orders</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/customer/dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Orders</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="<?php echo SITE_URL; ?>/services.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i> Place New Order
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Orders Content -->
<section class="orders-section py-5">
    <div class="container">
        <div class="card border-0 shadow-sm">
            <?php if (count($orders) > 0): ?>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order Number</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td class="fw-medium"><?php echo $order['order_number']; ?></td>
                                        <td><?php echo formatDate($order['created_at']); ?></td>
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
                                            <span class="badge 
                                                <?php 
                                                switch ($order['payment_status']) {
                                                    case 'paid': echo 'bg-success'; break;
                                                    case 'pending': echo 'bg-warning'; break;
                                                    case 'refunded': echo 'bg-danger'; break;
                                                    default: echo 'bg-secondary';
                                                }
                                                ?>">
                                                <?php echo ucfirst($order['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo SITE_URL; ?>/customer/order-details.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="card-footer bg-white py-3">
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0 justify-content-center">
                                <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo SITE_URL; ?>/customer/orders.php?page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                
                                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                    <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo SITE_URL; ?>/customer/orders.php?page=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo SITE_URL; ?>/customer/orders.php?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-shopping-bag fa-4x text-muted"></i>
                    </div>
                    <h3 class="fw-bold mb-3">No Orders Yet</h3>
                    <p class="text-muted mb-4">You haven't placed any orders yet. Start shopping to see your orders here.</p>
                    <a href="<?php echo SITE_URL; ?>/services.php" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i> Start Shopping
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
// Include footer
include_once '../includes/footer.php';
?>