<?php
/**
 * Common Functions
 * Regashi Printing Website
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitize user input
 * @param string $data
 * @return string
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Generate a secure password hash
 * @param string $password
 * @return string
 */
function generatePasswordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password against hash
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate a random string
 * @param int $length
 * @return string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

/**
 * Generate a unique order number
 * @return string
 */
function generateOrderNumber() {
    return 'RP' . date('Ymd') . strtoupper(generateRandomString(4));
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirect to a URL
 * @param string $url
 * @return void
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Get user information by ID
 * @param PDO $pdo
 * @param int $userId
 * @return array|bool
 */
function getUserById($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch();
        } else {
            return false;
        }
    } catch(PDOException $e) {
        error_log("Error in getUserById: " . $e->getMessage());
        return false;
    }
}

/**
 * Calculate price based on product options
 * @param PDO $pdo
 * @param int $productId
 * @param array $options
 * @param int $quantity
 * @return float
 */
function calculatePrice($pdo, $productId, $options, $quantity = 1) {
    try {
        // Get base product price
        $stmt = $pdo->prepare("SELECT base_price FROM products WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch();
        
        if (!$product) {
            return 0;
        }
        
        $totalPrice = $product['base_price'];
        
        // Add option prices
        foreach ($options as $optionId => $valueId) {
            $stmt = $pdo->prepare("SELECT additional_price FROM option_values WHERE value_id = :value_id");
            $stmt->bindParam(':value_id', $valueId, PDO::PARAM_INT);
            $stmt->execute();
            $optionValue = $stmt->fetch();
            
            if ($optionValue) {
                $totalPrice += $optionValue['additional_price'];
            }
        }
        
        // Multiply by quantity
        $totalPrice *= $quantity;
        
        return $totalPrice;
    } catch(PDOException $e) {
        error_log("Error in calculatePrice: " . $e->getMessage());
        return 0;
    }
}

/**
 * Handle file upload
 * @param array $file $_FILES array element
 * @param string $uploadDir Directory to upload to
 * @param array $allowedTypes Allowed MIME types
 * @param int $maxFileSize Maximum file size in bytes
 * @return string|bool Filename on success, false on failure
 */
function handleFileUpload($file, $uploadDir, $allowedTypes = [], $maxFileSize = MAX_FILE_SIZE) {
    // Check if file was uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Check file size
    if ($file['size'] > $maxFileSize) {
        return false;
    }
    
    // Check file type if allowed types are specified
    if (!empty($allowedTypes) && !in_array($file['type'], $allowedTypes)) {
        return false;
    }
    
    // Generate a unique filename
    $fileName = uniqid() . '_' . basename($file['name']);
    $uploadPath = $uploadDir . $fileName;
    
    // Attempt to move the uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return $fileName;
    } else {
        return false;
    }
}

/**
 * Format currency
 * @param float $amount
 * @param string $currencySymbol
 * @return string
 */
function formatCurrency($amount, $currencySymbol = '$') {
    return $currencySymbol . number_format($amount, 2);
}

/**
 * Format date
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'd M Y, h:i A') {
    return date($format, strtotime($date));
}

/**
 * Get all product categories
 * @param PDO $pdo
 * @return array
 */
function getAllCategories($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error in getAllCategories: " . $e->getMessage());
        return [];
    }
}

/**
 * Get products by category
 * @param PDO $pdo
 * @param int $categoryId
 * @return array
 */
function getProductsByCategory($pdo, $categoryId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = :category_id ORDER BY name ASC");
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error in getProductsByCategory: " . $e->getMessage());
        return [];
    }
}

/**
 * Get product options
 * @param PDO $pdo
 * @param int $productId
 * @return array
 */
function getProductOptions($pdo, $productId) {
    try {
        $stmt = $pdo->prepare("
            SELECT po.*, ov.value_id, ov.value_name, ov.additional_price 
            FROM product_options po
            JOIN option_values ov ON po.option_id = ov.option_id
            WHERE po.product_id = :product_id
            ORDER BY po.option_name ASC, ov.value_name ASC
        ");
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        
        $options = [];
        while ($row = $stmt->fetch()) {
            if (!isset($options[$row['option_id']])) {
                $options[$row['option_id']] = [
                    'option_id' => $row['option_id'],
                    'option_name' => $row['option_name'],
                    'option_type' => $row['option_type'],
                    'values' => []
                ];
            }
            
            $options[$row['option_id']]['values'][] = [
                'value_id' => $row['value_id'],
                'value_name' => $row['value_name'],
                'additional_price' => $row['additional_price']
            ];
        }
        
        return array_values($options);
    } catch(PDOException $e) {
        error_log("Error in getProductOptions: " . $e->getMessage());
        return [];
    }
}

/**
 * Get user's orders
 * @param PDO $pdo
 * @param int $userId
 * @param int $limit
 * @param int $offset
 * @return array
 */
function getUserOrders($pdo, $userId, $limit = 10, $offset = 0) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM orders 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error in getUserOrders: " . $e->getMessage());
        return [];
    }
}

/**
 * Get order details
 * @param PDO $pdo
 * @param int $orderId
 * @return array|bool
 */
function getOrderDetails($pdo, $orderId) {
    try {
        // Get order information
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        $order = $stmt->fetch();
        
        if (!$order) {
            return false;
        }
        
        // Get order items
        $stmt = $pdo->prepare("
            SELECT oi.*, p.name as product_name 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id = :order_id
        ");
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();
        
        // Get order status history
        $stmt = $pdo->prepare("
            SELECT osh.*, u.username as updated_by_username
            FROM order_status_history osh
            LEFT JOIN users u ON osh.updated_by = u.user_id
            WHERE osh.order_id = :order_id
            ORDER BY osh.created_at ASC
        ");
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        $statusHistory = $stmt->fetchAll();
        
        return [
            'order' => $order,
            'items' => $items,
            'status_history' => $statusHistory
        ];
    } catch(PDOException $e) {
        error_log("Error in getOrderDetails: " . $e->getMessage());
        return false;
    }
}

/**
 * Update order status
 * @param PDO $pdo
 * @param int $orderId
 * @param string $status
 * @param int $updatedBy
 * @param string $statusMessage
 * @return bool
 */
function updateOrderStatus($pdo, $orderId, $status, $updatedBy, $statusMessage = '') {
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Update order status
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET status = :status, updated_at = NOW() 
            WHERE order_id = :order_id
        ");
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Add to status history
        $stmt = $pdo->prepare("
            INSERT INTO order_status_history (order_id, status, status_message, updated_by) 
            VALUES (:order_id, :status, :status_message, :updated_by)
        ");
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':status_message', $statusMessage, PDO::PARAM_STR);
        $stmt->bindParam(':updated_by', $updatedBy, PDO::PARAM_INT);
        $stmt->execute();
        
        // Commit transaction
        $pdo->commit();
        
        return true;
    } catch(PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        error_log("Error in updateOrderStatus: " . $e->getMessage());
        return false;
    }
}

/**
 * Get user's saved designs
 * @param PDO $pdo
 * @param int $userId
 * @return array
 */
function getUserSavedDesigns($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("
            SELECT sd.*, p.name as product_name 
            FROM saved_designs sd
            JOIN products p ON sd.product_id = p.product_id
            WHERE sd.user_id = :user_id
            ORDER BY sd.created_at DESC
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error in getUserSavedDesigns: " . $e->getMessage());
        return [];
    }
}

/**
 * Save a design
 * @param PDO $pdo
 * @param int $userId
 * @param int $productId
 * @param string $designName
 * @param string $designFile
 * @return bool
 */
function saveDesign($pdo, $userId, $productId, $designName, $designFile) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO saved_designs (user_id, product_id, design_name, design_file)
            VALUES (:user_id, :product_id, :design_name, :design_file)
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindParam(':design_name', $designName, PDO::PARAM_STR);
        $stmt->bindParam(':design_file', $designFile, PDO::PARAM_STR);
        
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log("Error in saveDesign: " . $e->getMessage());
        return false;
    }
}

/**
 * Add to cart
 * @param array $item
 * @return void
 */
function addToCart($item) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $_SESSION['cart'][] = $item;
}

/**
 * Get cart contents
 * @return array
 */
function getCart() {
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

/**
 * Get cart total
 * @return float
 */
function getCartTotal() {
    $total = 0;
    $cart = getCart();
    
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    return $total;
}

/**
 * Clear cart
 * @return void
 */
function clearCart() {
    $_SESSION['cart'] = [];
}

/**
 * Remove item from cart
 * @param int $index
 * @return void
 */
function removeFromCart($index) {
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
    }
}

/**
 * Update item quantity in cart
 * @param int $index
 * @param int $quantity
 * @return void
 */
function updateCartItemQuantity($index, $quantity) {
    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['quantity'] = $quantity;
    }
}

/**
 * Create a new order
 * @param PDO $pdo
 * @param array $orderData
 * @return int|bool Order ID on success, false on failure
 */
function createOrder($pdo, $orderData) {
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Generate order number
        $orderNumber = generateOrderNumber();
        
        // Insert order
        $stmt = $pdo->prepare("
            INSERT INTO orders (
                user_id, order_number, total_amount, status, payment_status, 
                delivery_method, delivery_address, delivery_city, 
                delivery_postal_code, delivery_country, order_notes
            ) VALUES (
                :user_id, :order_number, :total_amount, :status, :payment_status, 
                :delivery_method, :delivery_address, :delivery_city, 
                :delivery_postal_code, :delivery_country, :order_notes
            )
        ");
        
        $stmt->bindParam(':user_id', $orderData['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':order_number', $orderNumber, PDO::PARAM_STR);
        $stmt->bindParam(':total_amount', $orderData['total_amount'], PDO::PARAM_STR);
        $stmt->bindValue(':status', 'pending', PDO::PARAM_STR);
        $stmt->bindValue(':payment_status', 'pending', PDO::PARAM_STR);
        $stmt->bindParam(':delivery_method', $orderData['delivery_method'], PDO::PARAM_STR);
        $stmt->bindParam(':delivery_address', $orderData['delivery_address'], PDO::PARAM_STR);
        $stmt->bindParam(':delivery_city', $orderData['delivery_city'], PDO::PARAM_STR);
        $stmt->bindParam(':delivery_postal_code', $orderData['delivery_postal_code'], PDO::PARAM_STR);
        $stmt->bindParam(':delivery_country', $orderData['delivery_country'], PDO::PARAM_STR);
        $stmt->bindParam(':order_notes', $orderData['order_notes'], PDO::PARAM_STR);
        
        $stmt->execute();
        
        // Get the order ID
        $orderId = $pdo->lastInsertId();
        
        // Insert order items
        foreach ($orderData['items'] as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (
                    order_id, product_id, quantity, price, options, design_file, special_instructions
                ) VALUES (
                    :order_id, :product_id, :quantity, :price, :options, :design_file, :special_instructions
                )
            ");
            
            $options = json_encode($item['options']);
            
            $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $item['product_id'], PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
            $stmt->bindParam(':price', $item['price'], PDO::PARAM_STR);
            $stmt->bindParam(':options', $options, PDO::PARAM_STR);
            $stmt->bindParam(':design_file', $item['design_file'], PDO::PARAM_STR);
            $stmt->bindParam(':special_instructions', $item['special_instructions'], PDO::PARAM_STR);
            
            $stmt->execute();
        }
        
        // Add initial status to history
        $stmt = $pdo->prepare("
            INSERT INTO order_status_history (order_id, status, updated_by)
            VALUES (:order_id, 'pending', :user_id)
        ");
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $orderData['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        
        // Commit transaction
        $pdo->commit();
        
        return $orderId;
    } catch(PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        error_log("Error in createOrder: " . $e->getMessage());
        return false;
    }
}

/**
 * Upload payment receipt
 * @param PDO $pdo
 * @param int $orderId
 * @param array $file
 * @return bool
 */
function uploadPaymentReceipt($pdo, $orderId, $file) {
    try {
        // Handle file upload
        $fileName = handleFileUpload($file, UPLOAD_RECEIPTS_DIR);
        
        if (!$fileName) {
            return false;
        }
        
        // Update order with receipt file
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET payment_receipt = :payment_receipt, payment_status = 'paid', updated_at = NOW()
            WHERE order_id = :order_id
        ");
        $stmt->bindParam(':payment_receipt', $fileName, PDO::PARAM_STR);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log("Error in uploadPaymentReceipt: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all orders (for admin)
 * @param PDO $pdo
 * @param array $filters
 * @param int $limit
 * @param int $offset
 * @return array
 */
function getAllOrders($pdo, $filters = [], $limit = 20, $offset = 0) {
    try {
        $sql = "
            SELECT o.*, u.username, u.email, u.first_name, u.last_name
            FROM orders o
            JOIN users u ON o.user_id = u.user_id
            WHERE 1=1
        ";
        
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
        
        $sql .= " ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error in getAllOrders: " . $e->getMessage());
        return [];
    }
}

/**
 * Get all users (for admin)
 * @param PDO $pdo
 * @param array $filters
 * @param int $limit
 * @param int $offset
 * @return array
 */
function getAllUsers($pdo, $filters = [], $limit = 20, $offset = 0) {
    try {
        $sql = "SELECT * FROM users WHERE 1=1";
        
        $params = [];
        
        // Apply filters
        if (!empty($filters['role'])) {
            $sql .= " AND role = :role";
            $params[':role'] = $filters['role'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (username LIKE :search OR email LIKE :search OR first_name LIKE :search OR last_name LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Error in getAllUsers: " . $e->getMessage());
        return [];
    }
}

/**
 * Log error
 * @param string $message
 * @param string $severity
 * @return void
 */
function logError($message, $severity = 'ERROR') {
    $logFile = '../logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$severity] $message" . PHP_EOL;
    
    // Ensure log directory exists
    if (!file_exists('../logs')) {
        mkdir('../logs', 0755, true);
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
