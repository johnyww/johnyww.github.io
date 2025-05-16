<?php
/**
 * Order Confirmation Page
 * Regashi Printing Website
 */

// Set page title
$page_title = "Order Confirmation";

// Include config and functions
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

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

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to homepage
    header("Location: " . SITE_URL);
    exit;
}

$orderId = intval($_GET['id']);

// Get order details
$orderDetails = getOrderDetails($pdo, $orderId);

// Check if order exists and belongs to the current user
if (!$orderDetails || $orderDetails['order']['user_id'] != $_SESSION['user_id']) {
    // Redirect to homepage
    header("Location: " . SITE_URL);
    exit;
}

// Handle payment receipt upload
$uploadSuccess = false;
$uploadError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_receipt'])) {
    // Check if file is uploaded
    if ($_FILES['payment_receipt']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Upload receipt
        $uploadDir = 'assets/uploads/receipts/';
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
include_once 'includes/header.php';
?>

<!-- Order Confirmation Header -->
<section class="page-header bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1 class="fw-bold mb-3">Order Confirmation</h1>
                <p class="lead mb-0">Thank you for your order!</p>
            </div>
        </div>
    </div>
</section>

<!-- Order Confirmation Content -->
<section class="order-confirmation-section py-5">
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
        
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 text-center">
                <div class="mb-4">
                    <div class="confirmation-icon bg-success text-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-check fa-3x"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-2">Your Order Has Been Received</h3>
                <p class="text-muted mb-0">Order #<?php echo $orderDetails['order']['order_number']; ?></p>
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
                    <a href="<?php echo SITE_URL; ?>/customer/order-details.php?id=<?php echo $orderId; ?>" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i> View Order Details
                    </a>
                    <a href="<?php echo SITE_URL; ?>/customer/dashboard.php" class="btn btn-outline-primary">
                        <i class="fas fa-user me-2"></i> Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Order Details</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Options</th>
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
                                                <?php echo $item['quantity']; ?>
                                            </td>
                                            <td class="align-middle text-end">
                                                <div class="fw-medium"><?php echo formatCurrency($item['price']); ?></div>
                                                <small class="text-muted">Total: <?php echo formatCurrency($item['price'] * $item['quantity']); ?></small>
                                            </td>
                                        </tr>
                                        <?php if (!empty($item['special_instructions'])): ?>
                                            <tr class="table-light">
                                                <td colspan="4" class="py-2">
                                                    <small class="text-muted"><strong>Special Instructions:</strong> <?php echo $item['special_instructions']; ?></small>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total:</td>
                                        <td class="text-end fw-bold"><?php echo formatCurrency($orderDetails['order']['total_amount']); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Delivery Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4 text-md-end fw-medium">Delivery Method:</div>
                            <div class="col-md-8"><?php echo ucfirst($orderDetails['order']['delivery_method']); ?></div>
                        </div>
                        
                        <?php if ($orderDetails['order']['delivery_method'] === 'delivery'): ?>
                            <div class="row mb-3">
                                <div class="col-md-4 text-md-end fw-medium">Delivery Address:</div>
                                <div class="col-md-8">
                                    <?php echo $orderDetails['order']['delivery_address']; ?>,<br>
                                    <?php echo $orderDetails['order']['delivery_city']; ?>, 
                                    <?php echo $orderDetails['order']['delivery_postal_code']; ?>,<br>
                                    <?php echo $orderDetails['order']['delivery_country']; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="row mb-3">
                                <div class="col-md-4 text-md-end fw-medium">Pickup Location:</div>
                                <div class="col-md-8">
                                    Regashi Printing Store<br>
                                    123 Printing Street, Design City<br>
                                    Business Hours: Mon-Fri 9:00 AM - 6:00 PM
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($orderDetails['order']['order_notes'])): ?>
                            <div class="row mb-0">
                                <div class="col-md-4 text-md-end fw-medium">Order Notes:</div>
                                <div class="col-md-8"><?php echo $orderDetails['order']['order_notes']; ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="row mb-2">
                                <div class="col-6 text-muted">Order Number:</div>
                                <div class="col-6 text-end fw-medium"><?php echo $orderDetails['order']['order_number']; ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 text-muted">Date:</div>
                                <div class="col-6 text-end fw-medium"><?php echo date('M d, Y', strtotime($orderDetails['order']['created_at'])); ?></div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-6 text-muted">Status:</div>
                                <div class="col-6 text-end">
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
                                <div class="col-6 text-muted">Payment:</div>
                                <div class="col-6 text-end">
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
                            <div class="row mb-0">
                                <div class="col-6 text-muted">Total:</div>
                                <div class="col-6 text-end fw-bold"><?php echo formatCurrency($orderDetails['order']['total_amount']); ?></div>
                            </div>
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
                            <div class="alert alert-info mb-3">
                                <h6 class="alert-heading fw-bold"><i class="fas fa-info-circle me-2"></i> Payment Instructions</h6>
                                <p class="mb-0">Please make your payment using the following details:</p>
                                <hr>
                                <div class="mb-2"><strong>Bank:</strong> Example Bank</div>
                                <div class="mb-2"><strong>Account Name:</strong> Regashi Printing</div>
                                <div class="mb-2"><strong>Account Number:</strong> 1234567890</div>
                                <div class="mb-0"><strong>Reference:</strong> <?php echo $orderDetails['order']['order_number']; ?></div>
                            </div>
                            
                            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $orderId); ?>" enctype="multipart/form-data">
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
    .confirmation-icon {
        font-size: 24px;
    }
</style>

<?php
// Include footer
include_once 'includes/footer.php';
?>