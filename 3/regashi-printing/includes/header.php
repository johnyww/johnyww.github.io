<?php
/**
 * Header Template
 * Regashi Printing Website
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include the database and functions files
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Get the current page
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/bootstrap.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <?php if (isset($extra_css)): ?>
        <?php echo $extra_css; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header class="bg-white shadow-sm sticky-top">
        <nav class="navbar navbar-expand-lg navbar-light container">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                    <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" alt="<?php echo SITE_NAME; ?>" height="40">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>">Home</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo strpos($current_page, 'services') !== false ? 'active' : ''; ?>" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Services
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/services/paper-printing.php">Paper/Business Card</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/services/banner-printing.php">Banner Printing</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/services/tshirt-printing.php">Custom T-Shirts</a></li>
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/services/bag-printing.php">Custom Bags</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/about.php">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/contact.php">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page == 'faq.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/faq.php">FAQ</a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <?php if (isLoggedIn()): ?>
                            <div class="dropdown me-3">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-user me-1"></i> <?php echo $_SESSION['username']; ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <?php if (isAdmin()): ?>
                                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/index.php"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                    <?php else: ?>
                                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/customer/dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/customer/orders.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/customer/saved-designs.php"><i class="fas fa-palette me-2"></i>Saved Designs</a></li>
                                        <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/customer/profile.php"><i class="fas fa-user-edit me-2"></i>Edit Profile</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                    <?php endif; ?>
                                    <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/auth/login.php" class="btn btn-outline-primary me-2">Login</a>
                            <a href="<?php echo SITE_URL; ?>/auth/register.php" class="btn btn-primary">Register</a>
                        <?php endif; ?>
                        
                        <a href="<?php echo SITE_URL; ?>/cart.php" class="btn btn-outline-dark ms-2 position-relative">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php echo count($_SESSION['cart']); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Main Content -->
    <main class="py-4">
