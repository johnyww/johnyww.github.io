<?php
/**
 * Customer Order Details
 * Regashi Printing Website
 */

// Set page title
$page_title = "Order Details";

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

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to orders page
    header("Location: " . SITE_URL . "/customer/orders.php");
    exit;
}

$orderId = intval($_GET['id']);

// Get order details
$orderDetails = getOrderDetails($pdo, $orderId);

// Check if order exists and belongs to the current user
if (!$orderDetails || $orderDetails['order']['user_id'] != $_SESSION['user_id']) {
    // Redirect to orders page
    header("Location: " . SITE_URL . "/customer/orders.php");
    exit;
}

// Handle payment receipt upload
$uploadSuccess = false;
$uploadError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_receipt'])) {
    // Check if file is uploaded
    if ($_FILES['payment_receipt']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Upload receipt
        $uploadDir = '../assets/uploads/receipts/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
        
        $fileName = handleFileUpload($_FILES['payment_receipt'], $uploadDir, $allowedTypes);
        
        if ($fileName) {
            // Update order with receipt
            $result = uploadPaymentReceipt($pdo, $orderId, $_FILES['payment_receipt']);
            
            if ($result) {
                $uploadSuccess = true;
                
                // Refresh order details
                $orderDetails = getOrderDetails($pdo, $orderId);
            } else {
                $uploadError = "Failed to update order with receipt. Please try again.";
            }
        } else {
            $uploadError = "Failed to upload receipt. Please check file type and size.";
        }
    } else {
        $uploadError = "Please select a file to upload.";
    }
}

// Include header
include_once '../includes/header.php';
?>

<!-- Order Details Header -->
<section class="order-details-header py-4 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="fw-bold mb-1">Order Details</h1>
                <p class="text-muted mb-0">Order #<?php echo $orderDetails['order']['order_number']; ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="<?php echo SITE_URL; ?>/customer/orders.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Order Details Content -->
<section class="order-details-content py-5">
    <div class="container">
        <?php if ($uploadSuccess): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> Payment receipt uploaded successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($uploadError)): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $uploadError; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Order Summary -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="row mb-2">
                                <div class="col-5 text-muted">Order Number:</div>
                                <div class="col-7 fw-medium"><?php echo $orderDetails['order']['order_number']; ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 text-muted">Date:</div>
                                <div class="col-7 fw-medium"><?php echo date('M d, Y', strtotime($orderDetails['order']['created_at'])); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-5 text-muted">Status:</div>
                                <div class="col-7">
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
                                <div class="col-5 text-muted">Payment:</div>
                                <div class="col-7">
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
                                <div class="col-5 text-muted">Total:</div>
                                <div class="col-7 fw-bold text-primary"><?php echo formatCurrency($orderDetails['order']['total_amount']); ?></div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Delivery Information</h6>
                            <div class="row mb-2">
                                <div class="col-5 text-muted">Method:</div>
                                <div class="col-7 fw-medium"><?php echo ucfirst($orderDetails['order']['delivery_method']); ?></div>
                            </div>
                            <?php if ($orderDetails['order']['delivery_method'] === 'delivery'): ?>
                                <div class="row mb-2">
                                    <div class="col-5 text-muted">Address:</div>
                                    <div class="col-7 fw-medium">
                                        <?php echo $orderDetails['order']['delivery_address']; ?>,<br>
                                        <?php echo $orderDetails['order']['delivery_city']; ?>, 
                                        <?php echo $orderDetails['order']['delivery_postal_code']; ?>,<br>
                                        <?php echo $orderDetails['order']['delivery_country']; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="row mb-2">
                                    <div class="col-12 fw-medium">Pick up from store</div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($orderDetails['order']['order_notes'])): ?>
                            <hr>
                            <div class="mb-0">
                                <h6 class="fw-bold mb-2">Order Notes</h6>
                                <p class="text-muted mb-0"><?php echo $orderDetails['order']['order_notes']; ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Order Items</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Options</th>
                                        <th>Design</th>
                                        <th>Quantity</th>
                                        <th class="text-end">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderDetails['items'] as $item): ?>
                                        <tr>
                                            <td class="align-middle">
                                                <strong><?php echo $item['product_name']; ?></strong>
                                            </td>
                                            <td class="align-middle">
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
                                                                echo '<li><small>' . $optionData['option_name'] . ': ' . $optionData['value_name'] . '</small></li>';
                                                            endif;
                                                        } catch(PDOException $e) {
                                                            error_log("Error fetching option data: " . $e->getMessage());
                                                        }
                                                    endforeach;
                                                    echo '</ul>';
                                                else:
                                                    echo '<small class="text-muted">No options selected</small>';
                                                endif;
                                                ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if (!empty($item['design_file'])): ?>
                                                    <a href="<?php echo SITE_URL; ?>/assets/uploads/designs/<?php echo $item['design_file']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i> View
                                                    </a>
                                                <?php else: ?>
                                                    <small class="text-muted">No design</small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php echo $item['quantity']; ?>
                                            </td>
                                            <td class="align-middle text-end">
                                                <div class="fw-medium"><?php echo formatCurrency($item['price']); ?></div>
                                                <small class="text-muted">Total: <?php echo formatCurrency($item['price'] * $item['quantity']); ?></small>
                                            </td>
                                        </tr>
                                        <?php if (!empty($item['special_instructions'])): ?>
                                            <tr class="table-light">
                                                <td colspan="5" class="py-2">
                                                    <small class="text-muted"><strong>Special Instructions:</strong> <?php echo $item['special_instructions']; ?></small>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total:</td>
                                        <td class="text-end fw-bold"><?php echo formatCurrency($orderDetails['order']['total_amount']); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Order Tracking -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Order Status Tracking</h5>
                    </div>
                    <div class="card-body">
                        <div class="order-tracker">
                            <div class="order-tracker-line"></div>
                            <div class="row gx-0">
                                <!-- Pending -->
                                <div class="col">
                                    <div class="order-step <?php echo in_array($orderDetails['order']['status'], ['pending', 'processing', 'printing', 'out for delivery', 'delivered']) ? 'completed' : ''; ?>">
                                        <div class="order-step-icon">
                                            <i class="fas fa-clipboard-list"></i>
                                        </div>
                                        <div class="order-step-text">
                                            <h6 class="mb-1">Pending</h6>
                                            <p class="small text-muted mb-0">Order received</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Processing -->
                                <div class="col">
                                    <div class="order-step <?php echo in_array($orderDetails['order']['status'], ['processing', 'printing', 'out for delivery', 'delivered']) ? 'completed' : ''; ?>">
                                        <div class="order-step-icon">
                                            <i class="fas fa-cogs"></i>
                                        </div>
                                        <div class="order-step-text">
                                            <h6 class="mb-1">Processing</h6>
                                            <p class="small text-muted mb-0">Order confirmed</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Printing -->
                                <div class="col">
                                    <div class="order-step <?php echo in_array($orderDetails['order']['status'], ['printing', 'out for delivery', 'delivered']) ? 'completed' : ''; ?>">
                                        <div class="order-step-icon">
                                            <i class="fas fa-print"></i>
                                        </div>
                                        <div class="order-step-text">
                                            <h6 class="mb-1">Printing</h6>
                                            <p class="small text-muted mb-0">In production</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Out for Delivery -->
                                <div class="col">
                                    <div class="order-step <?php echo in_array($orderDetails['order']['status'], ['out for delivery', 'delivered']) ? 'completed' : ''; ?>">
                                        <div class="order-step-icon">
                                            <i class="fas fa-truck"></i>
                                        </div>
                                        <div class="order-step-text">
                                            <h6 class="mb-1">Shipping</h6>
                                            <p class="small text-muted mb-0">On the way</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Delivered -->
                                <div class="col">
                                    <div class="order-step <?php echo $orderDetails['order']['status'] === 'delivered' ? 'completed' : ''; ?>">
                                        <div class="order-step-icon">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div class="order-step-text">
                                            <h6 class="mb-1">Delivered</h6>
                                            <p class="small text-muted mb-0">Order complete</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status History Timeline -->
                        <div class="status-timeline mt-5">
                            <h6 class="fw-bold mb-3">Status History</h6>
                            <?php if (!empty($orderDetails['status_history'])): ?>
                                <div class="order-timeline">
                                    <?php foreach ($orderDetails['status_history'] as $status): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-marker <?php echo ($status['status'] === $orderDetails['order']['status']) ? 'active' : ''; ?>">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1"><?php echo ucfirst($status['status']); ?></h6>
                                                <p class="small text-muted mb-1"><?php echo date('M d, Y h:i A', strtotime($status['created_at'])); ?></p>
                                                <?php if (!empty($status['status_message'])): ?>
                                                    <p class="small mb-0"><?php echo $status['status_message']; ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No status history available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Receipt Upload -->
                <?php if ($orderDetails['order']['payment_status'] === 'pending'): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Upload Payment Receipt</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">Please upload your payment receipt to confirm your order. We accept bank transfer receipts, mobile payment screenshots, or other payment confirmations.</p>
                            
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $orderId); ?>" method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="payment_receipt" class="form-label">Payment Receipt</label>
                                    <input class="form-control" type="file" id="payment_receipt" name="payment_receipt" accept=".jpg,.jpeg,.png,.gif,.pdf" required>
                                    <div class="form-text">Accepted formats: JPEG, PNG, GIF, PDF. Max size: <?php echo MAX_FILE_SIZE / (1024 * 1024); ?>MB</div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Upload Receipt</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php elseif (!empty($orderDetails['order']['payment_receipt'])): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold">Payment Receipt</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">Your payment receipt has been uploaded successfully.</p>
                            
                            <div class="text-center">
                                <a href="<?php echo SITE_URL; ?>/assets/uploads/receipts/<?php echo $orderDetails['order']['payment_receipt']; ?>" target="_blank" class="btn btn-outline-primary">
                                    <i class="fas fa-file-invoice-dollar me-2"></i> View Receipt
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
    /* Order tracker styles */
    .order-tracker {
        position: relative;
        padding: 30px 0;
    }
    
    .order-tracker-line {
        position: absolute;
        top: 55px;
        left: 10%;
        width: 80%;
        height: 4px;
        background-color: #dee2e6;
        z-index: 1;
    }
    
    .order-step {
        position: relative;
        z-index: 2;
        text-align: center;
    }
    
    .order-step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #f8f9fa;
        border: 3px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 18px;
        color: #adb5bd;
        transition: all 0.3s ease;
    }
    
    .order-step.completed .order-step-icon {
        background-color: #4263eb;
        border-color: #4263eb;
        color: #fff;
    }
    
    /* Timeline styles */
    .order-timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .order-timeline::before {
        content: '';
        position: absolute;
        top: 0;
        left: 15px;
        width: 2px;
        height: 100%;
        background-color: #dee2e6;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }
    
    .timeline-marker {
        position: absolute;
        top: 5px;
        left: -30px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }
    
    .timeline-marker.active {
        background-color: #4263eb;
        color: #fff;
    }
</style>

<?php
// Include footer
include_once '../includes/footer.php';
?>
