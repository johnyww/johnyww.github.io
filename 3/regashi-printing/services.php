<?php
/**
 * Services Overview Page
 * Regashi Printing Website
 */

// Set page title
$page_title = "Our Services";

// Include config and functions
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get all categories and products
try {
    // Get categories
    $categories = getAllCategories($pdo);
    
    // Get products for each category
    $categoryProducts = [];
    foreach ($categories as $category) {
        $categoryProducts[$category['category_id']] = getProductsByCategory($pdo, $category['category_id']);
    }
} catch(PDOException $e) {
    error_log("Error fetching categories and products: " . $e->getMessage());
    $categories = [];
    $categoryProducts = [];
}

// Include header
include_once 'includes/header.php';
?>

<!-- Services Page Header -->
<section class="page-header bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <h1 class="fw-bold mb-3">Our Services</h1>
                <p class="lead mb-0">Explore our range of high-quality printing services</p>
            </div>
            <div class="col-md-6">
                <img src="<?php echo SITE_URL; ?>/assets/images/services/services-header.jpg" alt="Printing Services" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Services Overview -->
<section class="services-overview py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4 mb-lg-0">
                <!-- Services Navigation -->
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 10;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Services</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($categories as $index => $category): ?>
                                <a href="#category-<?php echo $category['category_id']; ?>" class="list-group-item list-group-item-action <?php echo ($index === 0) ? 'active' : ''; ?>">
                                    <?php echo $category['name']; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-9">
                <!-- Services Content -->
                <?php foreach ($categories as $index => $category): ?>
                    <div id="category-<?php echo $category['category_id']; ?>" class="mb-5">
                        <h2 class="fw-bold mb-4"><?php echo $category['name']; ?></h2>
                        <p class="lead mb-4"><?php echo $category['description']; ?></p>
                        
                        <div class="row g-4">
                            <?php 
                            if (isset($categoryProducts[$category['category_id']])):
                                foreach ($categoryProducts[$category['category_id']] as $product):
                                    // Determine service page URL based on category name
                                    $serviceUrl = '#';
                                    if (stripos($category['name'], 'paper') !== false) {
                                        $serviceUrl = SITE_URL . '/services/paper-printing.php';
                                    } elseif (stripos($category['name'], 'banner') !== false) {
                                        $serviceUrl = SITE_URL . '/services/banner-printing.php';
                                    } elseif (stripos($category['name'], 't-shirt') !== false) {
                                        $serviceUrl = SITE_URL . '/services/tshirt-printing.php';
                                    } elseif (stripos($category['name'], 'bag') !== false) {
                                        $serviceUrl = SITE_URL . '/services/bag-printing.php';
                                    }
                            ?>
                                <div class="col-md-6">
                                    <div class="card service-card h-100 border-0 shadow-sm">
                                        <div class="card-body p-4">
                                            <h5 class="card-title fw-bold mb-3"><?php echo $product['name']; ?></h5>
                                            <p class="card-text text-muted mb-4"><?php echo $product['description'] ?: 'High-quality printing service tailored to your needs.'; ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold text-primary">From <?php echo formatCurrency($product['base_price']); ?></span>
                                                <a href="<?php echo $serviceUrl; ?>" class="btn btn-outline-primary">Learn More</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($categories)): ?>
                    <div class="text-center py-5">
                        <p class="text-muted mb-0">No services found. Please check back later.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-us py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">Why Choose Us</h2>
            <p class="text-muted">Reasons to trust Regashi Printing for all your printing needs</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon bg-primary text-white rounded-circle mx-auto mb-4">
                            <i class="fas fa-print"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">High-Quality Printing</h5>
                        <p class="card-text text-muted">We use state-of-the-art printing technology to ensure the highest quality results for all your printing needs.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon bg-primary text-white rounded-circle mx-auto mb-4">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">Fast Delivery</h5>
                        <p class="card-text text-muted">We offer quick turnaround times and reliable delivery services to ensure you get your orders when you need them.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon bg-primary text-white rounded-circle mx-auto mb-4">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">Competitive Pricing</h5>
                        <p class="card-text text-muted">We offer affordable pricing without compromising on quality, providing you with the best value for your money.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon bg-primary text-white rounded-circle mx-auto mb-4">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">Customer Support</h5>
                        <p class="card-text text-muted">Our dedicated team is always ready to assist you with any questions or concerns you may have about your orders.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon bg-primary text-white rounded-circle mx-auto mb-4">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">Customization Options</h5>
                        <p class="card-text text-muted">We offer a wide range of customization options to ensure your printed materials meet your exact specifications.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon bg-primary text-white rounded-circle mx-auto mb-4">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">Quality Guarantee</h5>
                        <p class="card-text text-muted">We stand behind the quality of our products with a satisfaction guarantee to ensure your complete happiness.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h2 class="fw-bold mb-3">Ready to Start Your Printing Project?</h2>
                <p class="lead mb-0">Get in touch with us today and let us help you bring your ideas to life!</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-light btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<style>
    .feature-icon {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .list-group-item.active {
        background-color: #4263eb;
        border-color: #4263eb;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll to category when clicking on navigation links
        const categoryLinks = document.querySelectorAll('.list-group-item');
        
        categoryLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all links
                categoryLinks.forEach(item => {
                    item.classList.remove('active');
                });
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Get target category id
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                // Scroll to target category
                if (targetElement) {
                    const headerOffset = 100;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                    
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Update active link based on scroll position
        window.addEventListener('scroll', function() {
            const categories = document.querySelectorAll('[id^="category-"]');
            const scrollPosition = window.scrollY;
            
            categories.forEach((category, index) => {
                const topOffset = category.offsetTop - 120;
                const bottomOffset = topOffset + category.offsetHeight;
                
                if (scrollPosition >= topOffset && scrollPosition < bottomOffset) {
                    categoryLinks.forEach(link => {
                        link.classList.remove('active');
                    });
                    
                    const activeLink = document.querySelector(`.list-group-item[href="#${category.id}"]`);
                    if (activeLink) {
                        activeLink.classList.add('active');
                    }
                }
            });
        });
    });
</script>

<?php
// Include footer
include_once 'includes/footer.php';
?>