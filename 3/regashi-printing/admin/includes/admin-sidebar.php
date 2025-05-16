<?php
/**
 * Admin Sidebar Template
 * Regashi Printing Website
 */

// Get the current page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/index.php">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'orders.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/orders.php">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/users.php">
                    <i class="fas fa-users me-2"></i>
                    Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'products.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/products.php">
                    <i class="fas fa-box me-2"></i>
                    Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'categories.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/categories.php">
                    <i class="fas fa-tags me-2"></i>
                    Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'settings.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/settings.php">
                    <i class="fas fa-cog me-2"></i>
                    Settings
                </a>
            </li>
        </ul>
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Reports</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'sales-report.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/sales-report.php">
                    <i class="fas fa-chart-line me-2"></i>
                    Sales Report
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $current_page == 'customer-report.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/customer-report.php">
                    <i class="fas fa-user-check me-2"></i>
                    Customer Report
                </a>
            </li>
        </ul>
    </div>
</nav>
