<?php
/**
 * Admin Orders Management
 * Regashi Printing Website
 */

// Set page title
$page_title = "Orders Management";

// Include config and functions
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    // Redirect to login page
    header("Location: " . SITE_URL . "/auth/login.php?redirect=admin");
    exit;
}

// Get admin info
$admin = getUserById($pdo, $_SESSION['user_id']);

// Set default values
$ordersPerPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $ordersPerPage;

// Handle filter parameters
$filters = [];

if (!empty($_GET['status'])) {
    $filters['status'] = sanitize($_GET['status']);
}

if (!empty($_GET['payment_status'])) {
    $filters['payment_status'] = sanitize($_GET['payment_status']);
}

if (!empty($_GET['order_number'])) {
    $filters['order_number'] = sanitize($_GET['order_number']);
}

if (!empty($_GET['customer_name'])) {
    $filters['customer_name'] = sanitize($_GET['customer_name']);
}

if (!empty($_GET['date_from'])) {
    $filters['date_from'] = sanitize($_GET['date_from']);
}

if (!empty($_GET['date_to'])) {
    $filters['date_to'] = sanitize($_GET['date_to']);
}

// Check if viewing a specific order
$viewOrder = isset($_GET['id']) ? intval($_GET['id']) : null;

// Check if updating order status
$updateSuccess = false;
$updateError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = intval($_POST['order_id'] ?? 0);
    $newStatus = sanitize($_POST['new_status'] ?? '');
    $statusMessage = sanitize($_POST['status_message'] ?? '');
    
    // Validate inputs
    if ($orderId > 0 && !empty($newStatus) && in_array($newStatus, array_keys(ORDER_STATUS))) {
        // Update order status
        $result = updateOrderStatus($pdo, $orderId, $newStatus, $_SESSION['user_id'], $statusMessage);
        
        if ($result) {
            $updateSuccess = true;
        } else {
            $updateError = "Failed to update order status. Please try again.";
        }
    } else {
        $updateError = "Invalid input. Please check your data and try again.";
    }
}

// Get orders based on filters or specific order
if ($viewOrder) {
    // Get specific order details
    $orderDetails = getOrderDetails($pdo, $viewOrder);
    
    if (!$orderDetails) {
        // Order not found, redirect to orders page
        header("Location: " . SITE_URL . "/admin/orders.php");
        exit;
    }
} else {
    // Get filtered orders
    $orders = getAllOrders($pdo, $filters, $ordersPerPage, $offset);
    
    // Get total number of orders for pagination
    try {
        $sql = "SELECT COUNT(*) as total FROM orders o JOIN users u ON o.user_id = u.user_id WHERE 1=1";
        $params = [];
        
        // Apply filters
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['payment_status'])) {
            $sql .= " AND o.payment_status = :payment_status";
            $params[':payment_status'] = $filters['payment_status'];
        }
        
        if (!empty($filters['order_number'])) {
            $sql .= " AND o.order_number LIKE :order_number";
            $params[':order_number'] = '%' . $filters['order_number'] . '%';
        }
        
        if (!empty($filters['customer_name'])) {
            $sql .= " AND (u.first_name LIKE :customer_name OR u.last_name LIKE :customer_name)";
            $params[':customer_name'] = '%' . $filters['customer_name'] . '%';
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND o.created_at >= :date_from";
            $params[':date_from'] = $filters['date_from'] . ' 00:00:00';
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND o.created_at <= :date_to";
            $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
        }
        
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $totalOrders = $stmt->fetch()['total'];
        $totalPages = ceil($totalOrders / $ordersPerPage);
    } catch(PDOException $e) {
        error_log("Error counting filtered orders: " . $e->getMessage());
        $totalOrders = 0;
        $totalPages = 1;
    }
}

// Include header
include_once 'includes/admin-header.php';
?>

<!-- Orders Management Content -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include_once 'includes/admin-sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <?php if ($viewOrder): ?>
                <!-- Order Details View -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
                    <h1 class="h2">Order Details</h1>
                    <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Orders
                    </a>
                </div>
                
                <?php if ($updateSuccess): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> Order status updated successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($updateError)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> <?php echo $updateError; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Order Summary Card -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Order Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Order Number:</div>
                                    <div class="col-sm-8"><?php echo $orderDetails['order']['order_number']; ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Date:</div>
                                    <div class="col-sm-8"><?php echo formatDate($orderDetails['order']['created_at']); ?></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Status:</div>
                                    <div class="col-sm-8">
                                        <span class="badge 
                                            <?php 
                                            switch ($orderDetails['order']['status']) {
                                                case 'pending': echo 'bg-warning'; break;
                                                case 'processing': echo 'bg-info'; break;
                                                case 'printing': echo 'bg-primary'; break;
                                                case 'out for delivery': echo 'bg-info'; break;
                                                case 'delivered': echo 'bg-success'; break;
                                                case 'cancelled': echo 'bg-danger'; break;
                                                default: echo 'bg-secondary';
                                            }
                                            ?>">
                                            <?php echo ucfirst($orderDetails['order']['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Payment Status:</div>
                                    <div class="col-sm-8">
                                        <span class="badge 
                                            <?php 
                                            switch ($orderDetails['order']['payment_status']) {
                                                case 'paid': echo 'bg-success'; break;
                                                case 'pending': echo 'bg-warning'; break;
                                                case 'refunded': echo 'bg-danger'; break;
                                                default: echo 'bg-secondary';
                                            }
                                            ?>">
                                            <?php echo ucfirst($orderDetails['order']['payment_status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Payment Receipt:</div>
                                    <div class="col-sm-8">
                                        <?php if (!empty($orderDetails['order']['payment_receipt'])): ?>
                                            <a href="<?php echo SITE_URL; ?>/assets/uploads/receipts/<?php echo $orderDetails['order']['payment_receipt']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-invoice-dollar me-1"></i> View Receipt
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No receipt uploaded</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Total Amount:</div>
                                    <div class="col-sm-8 fw-bold text-primary"><?php echo formatCurrency($orderDetails['order']['total_amount']); ?></div>
                                </div>
                                
                                <hr>
                                
                                <div class="row mb-2">
                                    <div class="col-sm-4 fw-bold">Delivery Method:</div>
                                    <div class="col-sm-8"><?php echo ucfirst($orderDetails['order']['delivery_method']); ?></div>
                                </div>
                                <?php if ($orderDetails['order']['delivery_method'] === 'delivery'): ?>
                                    <div class="row mb-2">
                                        <div class="col-sm-4 fw-bold">Delivery Address:</div>
                                        <div class="col-sm-8">
                                            <?php echo $orderDetails['order']['delivery_address']; ?>,<br>
                                            <?php echo $orderDetails['order']['delivery_city']; ?>, 
                                            <?php echo $orderDetails['order']['delivery_postal_code']; ?>,<br>
                                            <?php echo $orderDetails['order']['delivery_country']; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($orderDetails['order']['order_notes'])): ?>
                                    <div class="row mb-2">
                                        <div class="col-sm-4 fw-bold">Order Notes:</div>
                                        <div class="col-sm-8"><?php echo $orderDetails['order']['order_notes']; ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Customer Information</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                // Get customer details
                                $customer = getUserById($pdo, $orderDetails['order']['user_id']);
                                ?>
                                <?php if ($customer): ?>
                                    <div class="row mb-2">
                                        <div class="col-sm-4 fw-bold">Name:</div>
                                        <div class="col-sm-8"><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4 fw-bold">Email:</div>
                                        <div class="col-sm-8"><?php echo $customer['email']; ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4 fw-bold">Phone:</div>
                                        <div class="col-sm-8"><?php echo $customer['phone'] ?: 'N/A'; ?></div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4 fw-bold">Customer Since:</div>
                                        <div class="col-sm-8"><?php echo formatDate($customer['created_at']); ?></div>
                                    </div>
                                    <hr>
                                    <div class="text-end">
                                        <a href="users.php?id=<?php echo $customer['user_id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-user me-1"></i> View Customer Profile
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">Customer information not available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Options</th>
                                        <th>Design</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderDetails['items'] as $item): ?>
                                        <tr>
                                            <td><?php echo $item['product_name']; ?></td>
                                            <td>
                                                <?php
                                                $options = json_decode($item['options'], true);
                                                if ($options && count($options) > 0):
                                                    echo '<ul class="mb-0 ps-3">';
                                                    foreach ($options as $optionId => $valueId):
                                                        // Get option name and value
                                                        try {
                                                            $stmt = $pdo->prepare("
                                                                SELECT po.option_name, ov.value_name
                                                                FROM product_options po
                                                                JOIN option_values ov ON po.option_id = ov.option_id
                                                                WHERE po.option_id = :option_id AND ov.value_id = :value_id
                                                            ");
                                                            $stmt->bindParam(':option_id', $optionId, PDO::PARAM_INT);
                                                            $stmt->bindParam(':value_id', $valueId, PDO::PARAM_INT);
                                                            $stmt->execute();
                                                            
                                                            $optionData = $stmt->fetch();
                                                            if ($optionData):
                                                                echo '<li><strong>' . $optionData['option_name'] . ':</strong> ' . $optionData['value_name'] . '</li>';
                                                            endif;
                                                        } catch(PDOException $e) {
                                                            error_log("Error fetching option data: " . $e->getMessage());
                                                        }
                                                    endforeach;
                                                    echo '</ul>';
                                                else:
                                                    echo '<span class="text-muted">No options selected</span>';
                                                endif;
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($item['design_file'])): ?>
                                                    <a href="<?php echo SITE_URL; ?>/assets/uploads/designs/<?php echo $item['design_file']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-image me-1"></i> View Design
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">No design file</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo formatCurrency($item['price']); ?></td>
                                            <td><?php echo formatCurrency($item['price'] * $item['quantity']); ?></td>
                                        </tr>
                                        <?php if (!empty($item['special_instructions'])): ?>
                                            <tr>
                                                <td colspan="6" class="bg-light">
                                                    <strong>Special Instructions:</strong> <?php echo $item['special_instructions']; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Total:</td>
                                        <td class="fw-bold"><?php echo formatCurrency($orderDetails['order']['total_amount']); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Order Status Timeline -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Order Status History</h5>
                            </div>
                            <div class="card-body">
                                <div class="order-timeline">
                                    <?php if (!empty($orderDetails['status_history'])): ?>
                                        <?php foreach ($orderDetails['status_history'] as $status): ?>
                                            <div class="timeline-item">
                                                <div class="timeline-marker <?php echo ($status['status'] === $orderDetails['order']['status']) ? 'active' : ''; ?>">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                                <div class="timeline-content">
                                                    <h6 class="mb-1"><?php echo ucfirst($status['status']); ?></h6>
                                                    <p class="small text-muted mb-1">
                                                        <?php echo formatDate($status['created_at']); ?> 
                                                        <?php echo !empty($status['updated_by_username']) ? 'by ' . $status['updated_by_username'] : ''; ?>
                                                    </p>
                                                    <?php if (!empty($status['status_message'])): ?>
                                                        <p class="mb-0"><?php echo $status['status_message']; ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted">No status history available.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Update Order Status</h5>
                            </div>
                            <div class="card-body">
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $viewOrder); ?>" method="post">
                                    <input type="hidden" name="order_id" value="<?php echo $orderDetails['order']['order_id']; ?>">
                                    
                                    <div class="mb-3">
                                        <label for="new_status" class="form-label">New Status</label>
                                        <select class="form-select" id="new_status" name="new_status" required>
                                            <option value="">Select Status</option>
                                            <?php foreach (ORDER_STATUS as $key => $value): ?>
                                                <option value="<?php echo $key; ?>" <?php echo ($key === $orderDetails['order']['status']) ? 'selected' : ''; ?>>
                                                    <?php echo $value; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="status_message" class="form-label">Status Message (Optional)</label>
                                        <textarea class="form-control" id="status_message" name="status_message" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Orders List View -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Orders Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print();">
                                <i class="fas fa-print me-1"></i> Print
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-file-export me-1"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Filters Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Filter Orders</h5>
                        <button class="btn btn-sm btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="true" aria-controls="filtersCollapse">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="collapse show" id="filtersCollapse">
                        <div class="card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="row g-3">
                                <div class="col-md-4">
                                    <label for="order_number" class="form-label">Order Number</label>
                                    <input type="text" class="form-control" id="order_number" name="order_number" value="<?php echo $filters['order_number'] ?? ''; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="customer_name" class="form-label">Customer Name</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo $filters['customer_name'] ?? ''; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="status" class="form-label">Order Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Statuses</option>
                                        <?php foreach (ORDER_STATUS as $key => $value): ?>
                                            <option value="<?php echo $key; ?>" <?php echo (isset($filters['status']) && $filters['status'] === $key) ? 'selected' : ''; ?>>
                                                <?php echo $value; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="payment_status" class="form-label">Payment Status</label>
                                    <select class="form-select" id="payment_status" name="payment_status">
                                        <option value="">All Payment Statuses</option>
                                        <?php foreach (PAYMENT_STATUS as $key => $value): ?>
                                            <option value="<?php echo $key; ?>" <?php echo (isset($filters['payment_status']) && $filters['payment_status'] === $key) ? 'selected' : ''; ?>>
                                                <?php echo $value; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="date_from" class="form-label">Date From</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo $filters['date_from'] ?? ''; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="date_to" class="form-label">Date To</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo $filters['date_to'] ?? ''; ?>">
                                </div>
                                <div class="col-12 text-end">
                                    <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="btn btn-outline-secondary me-2">Reset</a>
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Orders Table -->
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($orders) > 0): ?>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?php echo SITE_URL; ?>/admin/orders.php?id=<?php echo $order['order_id']; ?>" class="fw-bold text-decoration-none">
                                                        <?php echo $order['order_number']; ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php echo $order['first_name'] . ' ' . $order['last_name']; ?><br>
                                                    <small class="text-muted"><?php echo $order['email']; ?></small>
                                                </td>
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
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                            Actions
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                            <li>
                                                                <a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/orders.php?id=<?php echo $order['order_id']; ?>">
                                                                    <i class="fas fa-eye me-2"></i> View Details
                                                                </a>
                                                            </li>
                                                            <?php if ($order['payment_status'] === 'pending'): ?>
                                                                <li>
                                                                    <a class="dropdown-item" href="javascript:void(0);" onclick="updatePaymentStatus(<?php echo $order['order_id']; ?>, 'paid')">
                                                                        <i class="fas fa-check-circle me-2"></i> Mark as Paid
                                                                    </a>
                                                                </li>
                                                            <?php endif; ?>
                                                            <?php if ($order['status'] !== 'cancelled'): ?>
                                                                <li>
                                                                    <a class="dropdown-item" href="javascript:void(0);" onclick="updateOrderStatus(<?php echo $order['order_id']; ?>, 'cancelled')">
                                                                        <i class="fas fa-times-circle me-2"></i> Cancel Order
                                                                    </a>
                                                                </li>
                                                            <?php endif; ?>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item" href="javascript:void(0);" onclick="printOrderInvoice(<?php echo $order['order_id']; ?>)">
                                                                    <i class="fas fa-print me-2"></i> Print Invoice
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <p class="mb-0 text-muted">No orders found</p>
                                                <small class="text-muted">Try adjusting your filters or check back later</small>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if (!$viewOrder && $totalPages > 1): ?>
                        <div class="card-footer bg-white py-3">
                            <nav aria-label="Page navigation">
                                <ul class="pagination mb-0 justify-content-center">
                                    <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="<?php echo SITE_URL; ?>/admin/orders.php?page=<?php echo $currentPage - 1; ?><?php echo (!empty($filters)) ? '&' . http_build_query($filters) : ''; ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                    
                                    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                        <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
                                            <a class="page-link" href="<?php echo SITE_URL; ?>/admin/orders.php?page=<?php echo $i; ?><?php echo (!empty($filters)) ? '&' . http_build_query($filters) : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="<?php echo SITE_URL; ?>/admin/orders.php?page=<?php echo $currentPage + 1; ?><?php echo (!empty($filters)) ? '&' . http_build_query($filters) : ''; ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script>
    // Function to update order status
    function updateOrderStatus(orderId, status) {
        if (confirm("Are you sure you want to change the order status to " + status + "?")) {
            document.getElementById("new_status").value = status;
            document.getElementById("status_message").value = "Status updated by admin";
            document.querySelector("form").submit();
        }
    }
    
    // Function to update payment status
    function updatePaymentStatus(orderId, status) {
        // This would be implemented with an AJAX call or form submission
        alert("This functionality would update the payment status to: " + status);
    }
    
    // Function to print order invoice
    function printOrderInvoice(orderId) {
        // This would be implemented to print an invoice
        alert("This functionality would print an invoice for order ID: " + orderId);
    }
</script>

<?php
// Include footer
include_once 'includes/admin-footer.php';
?>
