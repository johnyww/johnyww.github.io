<?php
/**
 * T-Shirt Printing Service Page
 * Regashi Printing Website
 */

// Set page title
$page_title = "Custom T-Shirt Printing";

// Include config and functions
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get products for this category
try {
    // Get category ID for t-shirts
    $stmt = $pdo->prepare("SELECT category_id FROM categories WHERE name LIKE :name");
    $tshirtCategory = "%T-Shirt%";
    $stmt->bindParam(':name', $tshirtCategory, PDO::PARAM_STR);
    $stmt->execute();
    $categoryId = $stmt->fetch()['category_id'] ?? 3; // Default to ID 3 if not found
    
    // Get products in this category
    $products = getProductsByCategory($pdo, $categoryId);
    
    // Get the default product (first one)
    $product = $products[0] ?? null;
    
    if ($product) {
        // Get options for this product
        $options = getProductOptions($pdo, $product['product_id']);
    }
} catch(PDOException $e) {
    error_log("Error fetching product data: " . $e->getMessage());
    $products = [];
    $product = null;
    $options = [];
}

// Handle form submission
$success = false;
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in
    if (!isLoggedIn()) {
        // Redirect to login page
        header("Location: " . SITE_URL . "/auth/login.php?redirect=tshirt");
        exit;
    }
    
    // Get form data
    $productId = $_POST['product_id'] ?? null;
    $quantity = $_POST['quantity'] ?? 1;
    $selectedOptions = $_POST['options'] ?? [];
    $specialInstructions = sanitize($_POST['special_instructions'] ?? '');
    $saveDesign = isset($_POST['save_design']) ? true : false;
    $designName = sanitize($_POST['design_name'] ?? '');
    
    // Validate form data
    if (empty($productId)) {
        $errors['product'] = "Please select a product";
    }
    
    if ($quantity < 1) {
        $errors['quantity'] = "Quantity must be at least 1";
    }
    
    // Handle file upload
    if (isset($_FILES['design_file']) && $_FILES['design_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $designFile = $_FILES['design_file'];
        
        // Check if file is valid
        if ($designFile['error'] !== UPLOAD_ERR_OK) {
            $errors['design_file'] = "File upload failed with error code: " . $designFile['error'];
        } elseif ($designFile['size'] > MAX_FILE_SIZE) {
            $errors['design_file'] = "File is too large. Maximum size is " . (MAX_FILE_SIZE / (1024 * 1024)) . "MB";
        } elseif (!in_array($designFile['type'], ACCEPTED_FILE_FORMATS)) {
            $errors['design_file'] = "Invalid file format. Accepted formats: JPEG, PNG, GIF, PDF";
        } else {
            // Upload file
            $uploadDir = '../assets/uploads/designs/';
            $fileName = handleFileUpload($designFile, $uploadDir, ACCEPTED_FILE_FORMATS, MAX_FILE_SIZE);
            
            if (!$fileName) {
                $errors['design_file'] = "Failed to upload file";
            }
        }
    } else {
        $errors['design_file'] = "Please upload a design file";
    }
    
    // If save design is checked, validate design name
    if ($saveDesign && empty($designName)) {
        $errors['design_name'] = "Please enter a name for your design";
    }
    
    // If no errors, add to cart
    if (empty($errors)) {
        // Get product details
        $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch();
        
        // Calculate price
        $price = calculatePrice($pdo, $productId, $selectedOptions, $quantity);
        
        // Create cart item
        $cartItem = [
            'product_id' => $productId,
            'product_name' => $product['name'],
            'quantity' => $quantity,
            'price' => $price / $quantity, // Store unit price
            'options' => $selectedOptions,
            'design_file' => $fileName,
            'special_instructions' => $specialInstructions
        ];
        
        // Add to cart
        addToCart($cartItem);
        
        // Save design if requested
        if ($saveDesign) {
            saveDesign($pdo, $_SESSION['user_id'], $productId, $designName, $fileName);
        }
        
        // Set success message
        $success = true;
    }
}

// Add custom CSS for file upload preview
$extra_css = '
<style>
    .size-chart-modal .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    .size-chart-table th, .size-chart-table td {
        text-align: center;
    }
    
    .color-option {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        display: inline-block;
        border: 2px solid #dee2e6;
        margin-right: 5px;
    }
    
    .color-option.active {
        border-color: #4263eb;
        transform: scale(1.1);
    }
    
    .material-option {
        cursor: pointer;
        padding: 10px;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        text-align: center;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }
    
    .material-option:hover {
        border-color: #4263eb;
    }
    
    .material-option.active {
        border-color: #4263eb;
        background-color: rgba(66, 99, 235, 0.1);
    }
</style>
';

// Add calculator JS
$extra_js = '
<script src="' . SITE_URL . '/assets/js/calculator.js"></script>
';

// Include header
include_once '../includes/header.php';
?>

<!-- Service Header Section -->
<section class="service-header py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="fw-bold mb-3">Custom T-Shirt Printing</h1>
                <p class="lead mb-4">Get high-quality custom t-shirts for your team, event, or personal use. Choose from various materials, sizes, and colors.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#calculator" class="btn btn-primary">Start Designing</a>
                    <a href="#details" class="btn btn-outline-dark">Learn More</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="<?php echo SITE_URL; ?>/assets/images/services/tshirt-printing-header.jpg" alt="Custom T-Shirt Printing" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Service Features Section -->
<section class="service-features py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="feature-item p-4">
                    <div class="feature-icon bg-primary text-white mb-3 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-tshirt fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Multiple Sizes</h5>
                    <p class="text-muted mb-0">From XS to XXL, we've got all sizes covered.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="feature-item p-4">
                    <div class="feature-icon bg-primary text-white mb-3 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-palette fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Various Colors</h5>
                    <p class="text-muted mb-0">Choose from a wide range of colors.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="feature-item p-4">
                    <div class="feature-icon bg-primary text-white mb-3 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-layer-group fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">Premium Materials</h5>
                    <p class="text-muted mb-0">Cotton, polyester, and dry-fit options.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="feature-item p-4">
                    <div class="feature-icon bg-primary text-white mb-3 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-print fa-2x"></i>
                    </div>
                    <h5 class="fw-bold">High-Quality Printing</h5>
                    <p class="text-muted mb-0">Durable prints that won't fade or peel.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Details Section -->
<section id="details" class="service-details py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-4">About Our T-Shirt Printing Service</h3>
                        <p>Our custom t-shirt printing service uses high-quality materials and state-of-the-art printing technology to deliver vibrant, long-lasting designs. Whether you need custom t-shirts for your company, sports team, family reunion, or personal use, we've got you covered.</p>
                        
                        <h5 class="fw-bold mt-4 mb-3">Materials</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><strong>Cotton:</strong> Soft, breathable, and comfortable. Perfect for everyday wear.</li>
                            <li class="mb-2"><strong>Polyester:</strong> Durable, wrinkle-resistant, and quick-drying. Ideal for sports and outdoor activities.</li>
                            <li class="mb-2"><strong>Dry-fit:</strong> Moisture-wicking fabric that keeps you cool and dry during physical activities.</li>
                        </ul>
                        
                        <h5 class="fw-bold mt-4 mb-3">Available Sizes</h5>
                        <p>We offer a wide range of sizes to fit everyone in your group:</p>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-primary">XS</span>
                            <span class="badge bg-primary">S</span>
                            <span class="badge bg-primary">M</span>
                            <span class="badge bg-primary">L</span>
                            <span class="badge bg-primary">XL</span>
                            <span class="badge bg-primary">XXL</span>
                        </div>
                        <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#sizeChartModal">View Size Chart</a>
                        
                        <h5 class="fw-bold mt-4 mb-3">Design Guidelines</h5>
                        <ul>
                            <li>Upload your design in high resolution (300 DPI recommended)</li>
                            <li>Supported file formats: JPEG, PNG, GIF, PDF</li>
                            <li>Maximum file size: <?php echo MAX_FILE_SIZE / (1024 * 1024); ?>MB</li>
                            <li>For best results, use vector files or high-resolution images</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-4">FAQs</h3>
                        
                        <div class="accordion" id="tshirtFAQ">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                        What's the minimum order quantity?
                                    </button>
                                </h2>
                                <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#tshirtFAQ">
                                    <div class="accordion-body">
                                        There is no minimum order quantity. You can order as few as 1 t-shirt or as many as you need.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                        How long does it take to print and deliver?
                                    </button>
                                </h2>
                                <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#tshirtFAQ">
                                    <div class="accordion-body">
                                        Standard production time is 3-5 business days. Delivery time depends on your location, typically 2-3 additional business days.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                        Can I print on both sides of the t-shirt?
                                    </button>
                                </h2>
                                <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#tshirtFAQ">
                                    <div class="accordion-body">
                                        Yes, you can print on both the front and back of the t-shirt. Please upload separate designs for each side and mention in the special instructions.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                        What's the printing technique used?
                                    </button>
                                </h2>
                                <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#tshirtFAQ">
                                    <div class="accordion-body">
                                        We use direct-to-garment (DTG) printing for most orders, which provides high-quality, detailed prints with vibrant colors. For bulk orders, we may use screen printing which is more economical for large quantities.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq5">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                        Can I order different sizes with the same design?
                                    </button>
                                </h2>
                                <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faq5" data-bs-parent="#tshirtFAQ">
                                    <div class="accordion-body">
                                        Yes, you can order multiple sizes with the same design. Please specify the quantities for each size in the special instructions.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Size Chart Modal -->
<div class="modal fade size-chart-modal" id="sizeChartModal" tabindex="-1" aria-labelledby="sizeChartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sizeChartModalLabel">T-Shirt Size Chart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered size-chart-table">
                        <thead>
                            <tr>
                                <th>Size</th>
                                <th>Chest (inches)</th>
                                <th>Length (inches)</th>
                                <th>Sleeve (inches)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>XS</td>
                                <td>34-36</td>
                                <td>27</td>
                                <td>7.5</td>
                            </tr>
                            <tr>
                                <td>S</td>
                                <td>36-38</td>
                                <td>28</td>
                                <td>8</td>
                            </tr>
                            <tr>
                                <td>M</td>
                                <td>38-40</td>
                                <td>29</td>
                                <td>8.5</td>
                            </tr>
                            <tr>
                                <td>L</td>
                                <td>40-42</td>
                                <td>30</td>
                                <td>9</td>
                            </tr>
                            <tr>
                                <td>XL</td>
                                <td>42-44</td>
                                <td>31</td>
                                <td>9.5</td>
                            </tr>
                            <tr>
                                <td>XXL</td>
                                <td>44-46</td>
                                <td>32</td>
                                <td>10</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    <h6>How to Measure</h6>
                    <ul>
                        <li><strong>Chest:</strong> Measure around the fullest part of the chest, keeping the tape horizontal.</li>
                        <li><strong>Length:</strong> Measure from the highest point of the shoulder to the bottom hem.</li>
                        <li><strong>Sleeve:</strong> Measure from the shoulder seam to the end of the sleeve.</li>
                    </ul>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i> These measurements are approximate and may vary slightly between different t-shirt styles.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Calculator Section -->
<section id="calculator" class="calculator-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">Design Your Custom T-Shirt</h2>
            <p class="text-muted">Select options, upload your design, and get an instant quote</p>
        </div>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Success Message -->
                <?php if ($success): ?>
                    <div class="alert alert-success mb-4">
                        <h5 class="alert-heading"><i class="fas fa-check-circle me-2"></i> Added to Cart!</h5>
                        <p>Your custom t-shirt has been added to the cart successfully.</p>
                        <hr>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?php echo SITE_URL; ?>/cart.php" class="btn btn-success">View Cart</a>
                            <a href="<?php echo SITE_URL; ?>/services/tshirt-printing.php" class="btn btn-outline-success">Design Another</a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Calculator Form -->
                <?php if (!$success): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form id="calculator-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data" class="calculator-form">
                                
                                <!-- Product Selection -->
                                <div class="mb-4">
                                    <label for="product_id" class="form-label">Select T-Shirt Type</label>
                                    <select class="form-select <?php echo isset($errors['product']) ? 'is-invalid' : ''; ?>" id="product_id" name="product_id" data-price-factor="product" required>
                                        <?php foreach ($products as $prod): ?>
                                            <option value="<?php echo $prod['product_id']; ?>" data-price-value="0" <?php echo ($product && $product['product_id'] == $prod['product_id']) ? 'selected' : ''; ?>>
                                                <?php echo $prod['name']; ?> - <?php echo formatCurrency($prod['base_price']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['product'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['product']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Options -->
                                <?php if ($options): ?>
                                    <?php foreach ($options as $option): ?>
                                        <div class="mb-4">
                                            <label class="form-label"><?php echo $option['option_name']; ?></label>
                                            
                                            <?php if ($option['option_type'] == 'size'): ?>
                                                <div class="row">
                                                    <?php foreach ($option['values'] as $value): ?>
                                                        <div class="col-4 col-md-2 mb-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="options[<?php echo $option['option_id']; ?>]" id="option_<?php echo $value['value_id']; ?>" value="<?php echo $value['value_id']; ?>" data-price-factor="option" data-price-value="<?php echo $value['additional_price']; ?>" <?php echo ($value['value_name'] == 'M') ? 'checked' : ''; ?> required>
                                                                <label class="form-check-label" for="option_<?php echo $value['value_id']; ?>">
                                                                    <?php echo $value['value_name']; ?>
                                                                    <?php if ($value['additional_price'] > 0): ?>
                                                                        <small class="text-muted">(+<?php echo formatCurrency($value['additional_price']); ?>)</small>
                                                                    <?php endif; ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <div class="mt-2">
                                                    <a href="#" class="small text-primary" data-bs-toggle="modal" data-bs-target="#sizeChartModal">
                                                        <i class="fas fa-ruler me-1"></i> View Size Chart
                                                    </a>
                                                </div>
                                            
                                            <?php elseif ($option['option_type'] == 'color'): ?>
                                                <div class="d-flex flex-wrap mb-2">
                                                    <?php foreach ($option['values'] as $index => $value): ?>
                                                        <?php 
                                                        $colorCode = '';
                                                        switch (strtolower($value['value_name'])) {
                                                            case 'white': $colorCode = '#ffffff'; break;
                                                            case 'black': $colorCode = '#000000'; break;
                                                            case 'red': $colorCode = '#e74c3c'; break;
                                                            case 'blue': $colorCode = '#3498db'; break;
                                                            case 'green': $colorCode = '#2ecc71'; break;
                                                            case 'yellow': $colorCode = '#f1c40f'; break;
                                                            case 'purple': $colorCode = '#9b59b6'; break;
                                                            case 'orange': $colorCode = '#e67e22'; break;
                                                            case 'pink': $colorCode = '#fd79a8'; break;
                                                            case 'gray': $colorCode = '#95a5a6'; break;
                                                            case 'navy': $colorCode = '#2c3e50'; break;
                                                            default: $colorCode = '#cccccc';
                                                        }
                                                        ?>
                                                        <div class="me-3 mb-2">
                                                            <input type="radio" name="options[<?php echo $option['option_id']; ?>]" id="option_<?php echo $value['value_id']; ?>" value="<?php echo $value['value_id']; ?>" data-price-factor="option" data-price-value="<?php echo $value['additional_price']; ?>" class="d-none" <?php echo ($index == 0) ? 'checked' : ''; ?> required>
                                                            <label for="option_<?php echo $value['value_id']; ?>" class="d-flex flex-column align-items-center">
                                                                <span class="color-option <?php echo ($index == 0) ? 'active' : ''; ?>" style="background-color: <?php echo $colorCode; ?>; <?php echo ($colorCode == '#ffffff') ? 'border: 1px solid #dee2e6;' : ''; ?>" onclick="this.classList.add('active'); document.getElementById('option_<?php echo $value['value_id']; ?>').checked = true;"></span>
                                                                <span class="small mt-1"><?php echo $value['value_name']; ?></span>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            
                                            <?php elseif ($option['option_type'] == 'material'): ?>
                                                <div class="row">
                                                    <?php foreach ($option['values'] as $index => $value): ?>
                                                        <div class="col-md-4 mb-2">
                                                            <input type="radio" name="options[<?php echo $option['option_id']; ?>]" id="option_<?php echo $value['value_id']; ?>" value="<?php echo $value['value_id']; ?>" data-price-factor="option" data-price-value="<?php echo $value['additional_price']; ?>" class="d-none" <?php echo ($index == 0) ? 'checked' : ''; ?> required>
                                                            <label for="option_<?php echo $value['value_id']; ?>" class="material-option <?php echo ($index == 0) ? 'active' : ''; ?> d-block h-100">
                                                                <h6 class="mb-1"><?php echo $value['value_name']; ?></h6>
                                                                <?php if ($value['additional_price'] > 0): ?>
                                                                    <small class="text-muted d-block">(+<?php echo formatCurrency($value['additional_price']); ?>)</small>
                                                                <?php endif; ?>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            
                                            <?php else: ?>
                                                <select class="form-select" name="options[<?php echo $option['option_id']; ?>]" data-price-factor="option" required>
                                                    <?php foreach ($option['values'] as $value): ?>
                                                        <option value="<?php echo $value['value_id']; ?>" data-price-value="<?php echo $value['additional_price']; ?>">
                                                            <?php echo $value['value_name']; ?>
                                                            <?php if ($value['additional_price'] > 0): ?>
                                                                (+<?php echo formatCurrency($value['additional_price']); ?>)
                                                            <?php endif; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <!-- Quantity -->
                                <div class="mb-4">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control <?php echo isset($errors['quantity']) ? 'is-invalid' : ''; ?>" id="quantity" name="quantity" min="1" value="1" data-price-factor="quantity" required>
                                    <?php if (isset($errors['quantity'])): ?>
                                        <div class="invalid-feedback">
                                            <?php echo $errors['quantity']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Design Upload -->
                                <div class="mb-4">
                                    <label for="design-file" class="form-label">Upload Your Design</label>
                                    <div class="custom-file-upload" onclick="document.getElementById('design-file').click();">
                                        <input type="file" class="d-none <?php echo isset($errors['design_file']) ? 'is-invalid' : ''; ?>" id="design-file" name="design_file" accept=".jpg,.jpeg,.png,.gif,.pdf" required>
                                        <div class="text-center py-4">
                                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                            <h6>Click to upload your design</h6>
                                            <p class="small text-muted mb-0">Supported formats: JPEG, PNG, GIF, PDF</p>
                                            <p class="small text-muted mb-0">Max size: <?php echo MAX_FILE_SIZE / (1024 * 1024); ?>MB</p>
                                        </div>
                                    </div>
                                    <div id="file-preview-container"></div>
                                    <?php if (isset($errors['design_file'])): ?>
                                        <div class="invalid-feedback d-block" id="design-error">
                                            <?php echo $errors['design_file']; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="invalid-feedback d-none" id="design-error">
                                            Please upload a design file
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Special Instructions -->
                                <div class="mb-4">
                                    <label for="special_instructions" class="form-label">Special Instructions (Optional)</label>
                                    <textarea class="form-control" id="special_instructions" name="special_instructions" rows="3" placeholder="Any specific requirements or instructions for your order..."><?php echo $specialInstructions ?? ''; ?></textarea>
                                </div>
                                
                                <!-- Save Design (Only for logged in users) -->
                                <?php if (isLoggedIn()): ?>
                                    <div class="mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="save-design" name="save_design">
                                            <label class="form-check-label" for="save-design">
                                                Save this design for future use
                                            </label>
                                        </div>
                                        
                                        <div id="design-name-group" class="mt-3">
                                            <label for="design-name" class="form-label">Design Name</label>
                                            <input type="text" class="form-control <?php echo isset($errors['design_name']) ? 'is-invalid' : ''; ?>" id="design-name" name="design_name" placeholder="Enter a name for your design">
                                            <?php if (isset($errors['design_name'])): ?>
                                                <div class="invalid-feedback" id="design-name-error">
                                                    <?php echo $errors['design_name']; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="invalid-feedback d-none" id="design-name-error">
                                                    Please enter a name for your design
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Hidden fields for price calculation -->
                                <input type="hidden" id="base-price" value="<?php echo $product['base_price'] ?? 0; ?>">
                                <input type="hidden" id="total-price" name="total_price" value="0">
                                
                                <!-- Submit Button -->
                                <div class="d-grid mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">Add to Cart</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Price Preview -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 10;">
                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-4">Price Summary</h4>
                        
                        <div class="price-preview p-3 rounded mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="fw-bold">Total Price:</span>
                                <span class="price-amount" id="price-amount">$0.00</span>
                            </div>
                            <p class="small text-muted mb-0">Includes all selected options and quantity</p>
                        </div>
                        
                        <div class="bg-light p-3 rounded">
                            <h6 class="fw-bold mb-3">Why Choose Us?</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> High-quality materials</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Vibrant, long-lasting prints</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Fast turnaround time</li>
                                <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> No minimum order quantity</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i> 100% satisfaction guarantee</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Services Section -->
<section class="related-services py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">Related Services</h2>
            <p class="text-muted">Explore our other printing services</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card service-card h-100 border-0 shadow-sm">
                    <div class="card-image-container">
                        <img src="<?php echo SITE_URL; ?>/assets/images/services/paper-printing.jpg" class="card-img-top" alt="Paper Printing">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Paper Printing</h5>
                        <p class="card-text">High-quality prints for documents, business cards, flyers, and more.</p>
                        <a href="<?php echo SITE_URL; ?>/services/paper-printing.php" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card service-card h-100 border-0 shadow-sm">
                    <div class="card-image-container">
                        <img src="<?php echo SITE_URL; ?>/assets/images/services/banner-printing.jpg" class="card-img-top" alt="Banner Printing">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Banner Printing</h5>
                        <p class="card-text">Eye-catching banners for promotions, events, and advertisements.</p>
                        <a href="<?php echo SITE_URL; ?>/services/banner-printing.php" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card service-card h-100 border-0 shadow-sm">
                    <div class="card-image-container">
                        <img src="<?php echo SITE_URL; ?>/assets/images/services/bag-printing.jpg" class="card-img-top" alt="Bag Printing">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Custom Bags</h5>
                        <p class="card-text">Personalized bags for branding, promotions, or personal use.</p>
                        <a href="<?php echo SITE_URL; ?>/services/bag-printing.php" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Color options selection logic
    document.addEventListener('DOMContentLoaded', function() {
        const colorOptions = document.querySelectorAll('.color-option');
        
        colorOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                colorOptions.forEach(opt => opt.classList.remove('active'));
                
                // Add active class to clicked option
                this.classList.add('active');
            });
        });
        
        // Material options selection logic
        const materialOptions = document.querySelectorAll('.material-option');
        
        materialOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                materialOptions.forEach(opt => opt.classList.remove('active'));
                
                // Add active class to clicked option
                this.classList.add('active');
                
                // Check the associated radio button
                const radioId = this.getAttribute('for');
                document.getElementById(radioId).checked = true;
            });
        });
    });
</script>

<?php
// Include footer
include_once '../includes/footer.php';
?>
