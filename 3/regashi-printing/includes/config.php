<?php
/**
 * Database Configuration File
 * Regashi Printing Website
 */

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'john123');
define('DB_NAME', 'regashi_printing');

// File upload paths
define('UPLOAD_DESIGNS_DIR', '../assets/uploads/designs/');
define('UPLOAD_RECEIPTS_DIR', '../assets/uploads/receipts/');
define('MAX_FILE_SIZE', 256 * 1024 * 1024); // 256MB in bytes

// Website settings
define('SITE_NAME', 'Regashi Printing');
define('SITE_URL', 'http://localhost/3/regashi-printing');
define('ADMIN_EMAIL', 'admin@regashi.com');

// Session timeout (in seconds)
define('SESSION_TIMEOUT', 1800); // 30 minutes

// Order status
define('ORDER_STATUS', [
    'pending' => 'Pending',
    'processing' => 'Processing',
    'printing' => 'Printing',
    'out for delivery' => 'Out for Delivery',
    'delivered' => 'Delivered',
    'cancelled' => 'Cancelled'
]);

// Payment status
define('PAYMENT_STATUS', [
    'pending' => 'Pending',
    'paid' => 'Paid',
    'refunded' => 'Refunded'
]);

// Accepted file formats for design uploads
define('ACCEPTED_FILE_FORMATS', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/pdf',
    'application/postscript',
    'application/illustrator',
    'application/x-photoshop',
    'application/vnd.adobe.photoshop'
]);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
