<?php
/**
 * FAQ Page
 * Regashi Printing Website
 */

// Set page title
$page_title = "Frequently Asked Questions";

// Include config and functions
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include header
include_once 'includes/header.php';
?>

<!-- FAQ Page Header -->
<section class="page-header bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="fw-bold mb-0">Frequently Asked Questions</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">FAQ</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Content -->
<section class="faq-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 mb-4 mb-lg-0">
                <!-- FAQ Categories -->
                <div class="card border-0 shadow-sm sticky-top" style="top: 100px; z-index: 10;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Categories</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <a href="#general" class="list-group-item list-group-item-action active">General Questions</a>
                            <a href="#orders" class="list-group-item list-group-item-action">Ordering Process</a>
                            <a href="#payment" class="list-group-item list-group-item-action">Payment & Pricing</a>
                            <a href="#delivery" class="list-group-item list-group-item-action">Delivery & Shipping</a>
                            <a href="#design" class="list-group-item list-group-item-action">Design & File Setup</a>
                            <a href="#products" class="list-group-item list-group-item-action">Products & Services</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-9">
                <!-- General Questions -->
                <div id="general" class="faq-category mb-5">
                    <h3 class="fw-bold mb-4">General Questions</h3>
                    
                    <div class="accordion" id="generalFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="general1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#generalCollapse1" aria-expanded="true" aria-controls="generalCollapse1">
                                    What is Regashi Printing?
                                </button>
                            </h2>
                            <div id="generalCollapse1" class="accordion-collapse collapse show" aria-labelledby="general1" data-bs-parent="#generalFAQ">
                                <div class="accordion-body">
                                    Regashi Printing is a professional printing service provider offering a wide range of printing solutions, including paper printing, banner printing, custom t-shirts, and custom bags. We cater to both businesses and individuals, providing high-quality printing services for various needs.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="general2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#generalCollapse2" aria-expanded="false" aria-controls="generalCollapse2">
                                    How do I contact customer support?
                                </button>
                            </h2>
                            <div id="generalCollapse2" class="accordion-collapse collapse" aria-labelledby="general2" data-bs-parent="#generalFAQ">
                                <div class="accordion-body">
                                    You can contact our customer support team through multiple channels:
                                    <ul>
                                        <li>Phone: +1 (234) 567-8901</li>
                                        <li>Email: support@regashiprinting.com</li>
                                        <li>Contact Form: Use the form on our <a href="<?php echo SITE_URL; ?>/contact.php">Contact Us</a> page</li>
                                        <li>Visit Us: Come to our store during business hours</li>
                                    </ul>
                                    Our customer support team is available Monday to Friday, 9:00 AM to 6:00 PM.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="general3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#generalCollapse3" aria-expanded="false" aria-controls="generalCollapse3">
                                    What are your business hours?
                                </button>
                            </h2>
                            <div id="generalCollapse3" class="accordion-collapse collapse" aria-labelledby="general3" data-bs-parent="#generalFAQ">
                                <div class="accordion-body">
                                    Our business hours are:
                                    <ul>
                                        <li>Monday to Friday: 9:00 AM - 6:00 PM</li>
                                        <li>Saturday: 10:00 AM - 4:00 PM</li>
                                        <li>Sunday: Closed</li>
                                    </ul>
                                    Please note that our online ordering system is available 24/7, allowing you to place orders at your convenience.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ordering Process -->
                <div id="orders" class="faq-category mb-5">
                    <h3 class="fw-bold mb-4">Ordering Process</h3>
                    
                    <div class="accordion" id="ordersFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="orders1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#ordersCollapse1" aria-expanded="true" aria-controls="ordersCollapse1">
                                    How do I place an order?
                                </button>
                            </h2>
                            <div id="ordersCollapse1" class="accordion-collapse collapse show" aria-labelledby="orders1" data-bs-parent="#ordersFAQ">
                                <div class="accordion-body">
                                    Placing an order with Regashi Printing is simple:
                                    <ol>
                                        <li>Browse our services and select the product you want</li>
                                        <li>Customize your product by selecting size, material, color, and other options</li>
                                        <li>Upload your design file</li>
                                        <li>Review your order details and pricing</li>
                                        <li>Add the item to your cart</li>
                                        <li>Proceed to checkout and provide delivery information</li>
                                        <li>Make payment by following the provided instructions</li>
                                        <li>Submit your order</li>
                                    </ol>
                                    After placing your order, you'll receive an order confirmation email with details about your purchase.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="orders2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ordersCollapse2" aria-expanded="false" aria-controls="ordersCollapse2">
                                    Do I need to create an account to place an order?
                                </button>
                            </h2>
                            <div id="ordersCollapse2" class="accordion-collapse collapse" aria-labelledby="orders2" data-bs-parent="#ordersFAQ">
                                <div class="accordion-body">
                                    Yes, you need to create an account to place an order with us. Creating an account offers several benefits:
                                    <ul>
                                        <li>Track your order status</li>
                                        <li>View your order history</li>
                                        <li>Save your delivery information for future orders</li>
                                        <li>Save your designs for future use</li>
                                        <li>Easier reordering process</li>
                                    </ul>
                                    Account creation is free and only takes a minute. You'll just need to provide your name, email address, and create a password.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="orders3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ordersCollapse3" aria-expanded="false" aria-controls="ordersCollapse3">
                                    Can I modify or cancel my order after it's placed?
                                </button>
                            </h2>
                            <div id="ordersCollapse3" class="accordion-collapse collapse" aria-labelledby="orders3" data-bs-parent="#ordersFAQ">
                                <div class="accordion-body">
                                    Order modifications or cancellations are possible, but they depend on the order status:
                                    <ul>
                                        <li><strong>Pending:</strong> Orders can be modified or cancelled without any charges</li>
                                        <li><strong>Processing:</strong> Limited modifications may be possible, but cancellations may incur a fee</li>
                                        <li><strong>Printing:</strong> No modifications or cancellations are possible once production has started</li>
                                    </ul>
                                    To request an order modification or cancellation, please contact our customer support team as soon as possible with your order number.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment & Pricing -->
                <div id="payment" class="faq-category mb-5">
                    <h3 class="fw-bold mb-4">Payment & Pricing</h3>
                    
                    <div class="accordion" id="paymentFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="payment1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#paymentCollapse1" aria-expanded="true" aria-controls="paymentCollapse1">
                                    What payment methods do you accept?
                                </button>
                            </h2>
                            <div id="paymentCollapse1" class="accordion-collapse collapse show" aria-labelledby="payment1" data-bs-parent="#paymentFAQ">
                                <div class="accordion-body">
                                    Currently, we accept bank transfers as our payment method. After placing your order, you'll receive bank transfer instructions. Once you've made the payment, you'll need to upload the payment receipt through your account dashboard.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="payment2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#paymentCollapse2" aria-expanded="false" aria-controls="paymentCollapse2">
                                    How do your pricing calculators work?
                                </button>
                            </h2>
                            <div id="paymentCollapse2" class="accordion-collapse collapse" aria-labelledby="payment2" data-bs-parent="#paymentFAQ">
                                <div class="accordion-body">
                                    Our pricing calculators provide instant quotes based on your specific requirements. The price is calculated dynamically as you select different options:
                                    <ul>
                                        <li>Base price for the selected product</li>
                                        <li>Additional costs for premium options (material, size, color, etc.)</li>
                                        <li>Quantity (bulk discounts may apply)</li>
                                    </ul>
                                    You can see the price update in real-time as you make selections, allowing you to adjust your choices to fit your budget.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="payment3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#paymentCollapse3" aria-expanded="false" aria-controls="paymentCollapse3">
                                    Do you offer bulk discounts?
                                </button>
                            </h2>
                            <div id="paymentCollapse3" class="accordion-collapse collapse" aria-labelledby="payment3" data-bs-parent="#paymentFAQ">
                                <div class="accordion-body">
                                    Yes, we offer discounts for bulk orders. The discount rate depends on the order quantity and type of products. Our pricing calculator automatically applies these discounts when you input larger quantities.
                                    
                                    For large corporate orders or special projects, please contact our sales team for custom quotes and additional discount options.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Delivery & Shipping -->
                <div id="delivery" class="faq-category mb-5">
                    <h3 class="fw-bold mb-4">Delivery & Shipping</h3>
                    
                    <div class="accordion" id="deliveryFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="delivery1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#deliveryCollapse1" aria-expanded="true" aria-controls="deliveryCollapse1">
                                    What are your delivery options?
                                </button>
                            </h2>
                            <div id="deliveryCollapse1" class="accordion-collapse collapse show" aria-labelledby="delivery1" data-bs-parent="#deliveryFAQ">
                                <div class="accordion-body">
                                    We offer two delivery options:
                                    <ul>
                                        <li><strong>Home Delivery:</strong> We deliver your order directly to your specified address</li>
                                        <li><strong>Store Pickup:</strong> You can pick up your order from our store location</li>
                                    </ul>
                                    You can select your preferred delivery method during checkout.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="delivery2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#deliveryCollapse2" aria-expanded="false" aria-controls="deliveryCollapse2">
                                    How long does delivery take?
                                </button>
                            </h2>
                            <div id="deliveryCollapse2" class="accordion-collapse collapse" aria-labelledby="delivery2" data-bs-parent="#deliveryFAQ">
                                <div class="accordion-body">
                                    Our standard delivery timeframe is 3-5 business days from the day your order is processed, depending on your location and the complexity of your order. The total time from order placement to delivery typically follows this timeline:
                                    <ul>
                                        <li>Order Processing: 1-2 business days</li>
                                        <li>Printing Production: 1-3 business days</li>
                                        <li>Delivery: 1-2 business days</li>
                                    </ul>
                                    You can track your order status through your account dashboard at any time.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="delivery3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#deliveryCollapse3" aria-expanded="false" aria-controls="deliveryCollapse3">
                                    Is there a delivery fee?
                                </button>
                            </h2>
                            <div id="deliveryCollapse3" class="accordion-collapse collapse" aria-labelledby="delivery3" data-bs-parent="#deliveryFAQ">
                                <div class="accordion-body">
                                    We offer free delivery on all orders as part of our service. There are no additional delivery fees regardless of your location or order size.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Design & File Setup -->
                <div id="design" class="faq-category mb-5">
                    <h3 class="fw-bold mb-4">Design & File Setup</h3>
                    
                    <div class="accordion" id="designFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="design1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#designCollapse1" aria-expanded="true" aria-controls="designCollapse1">
                                    What file formats do you accept?
                                </button>
                            </h2>
                            <div id="designCollapse1" class="accordion-collapse collapse show" aria-labelledby="design1" data-bs-parent="#designFAQ">
                                <div class="accordion-body">
                                    We accept most common file formats for your designs, including:
                                    <ul>
                                        <li>JPEG (.jpg, .jpeg)</li>
                                        <li>PNG (.png)</li>
                                        <li>GIF (.gif)</li>
                                        <li>PDF (.pdf)</li>
                                        <li>Adobe Illustrator (.ai)</li>
                                        <li>Photoshop (.psd)</li>
                                    </ul>
                                    For best results, we recommend using high-resolution files (300 DPI or higher) in PDF or vector formats (AI, EPS) whenever possible.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="design2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#designCollapse2" aria-expanded="false" aria-controls="designCollapse2">
                                    What is the maximum file size for uploads?
                                </button>
                            </h2>
                            <div id="designCollapse2" class="accordion-collapse collapse" aria-labelledby="design2" data-bs-parent="#designFAQ">
                                <div class="accordion-body">
                                    The maximum file size for uploads is 256MB. This generous limit allows you to upload high-resolution, detailed designs without compression. If your file exceeds this limit, you may need to compress it or contact our customer support for assistance.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="design3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#designCollapse3" aria-expanded="false" aria-controls="designCollapse3">
                                    Do you offer design services?
                                </button>
                            </h2>
                            <div id="designCollapse3" class="accordion-collapse collapse" aria-labelledby="design3" data-bs-parent="#designFAQ">
                                <div class="accordion-body">
                                    Yes, we offer professional design services for customers who don't have their own designs or need help refining their ideas. Our experienced designers can create custom designs for your printing needs.
                                    
                                    For design service inquiries, please contact our customer support team with details about your project, and we'll provide you with pricing and timeline information.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Products & Services -->
                <div id="products" class="faq-category mb-5">
                    <h3 class="fw-bold mb-4">Products & Services</h3>
                    
                    <div class="accordion" id="productsFAQ">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="products1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#productsCollapse1" aria-expanded="true" aria-controls="productsCollapse1">
                                    What printing services do you offer?
                                </button>
                            </h2>
                            <div id="productsCollapse1" class="accordion-collapse collapse show" aria-labelledby="products1" data-bs-parent="#productsFAQ">
                                <div class="accordion-body">
                                    We offer a comprehensive range of printing services, including:
                                    <ul>
                                        <li><strong>Paper Printing:</strong> Documents, business cards, flyers, brochures, etc.</li>
                                        <li><strong>Banner Printing:</strong> Indoor and outdoor banners in various sizes</li>
                                        <li><strong>Custom T-Shirts:</strong> Personalized t-shirts in multiple sizes, materials, and colors</li>
                                        <li><strong>Custom Bags:</strong> Tote bags and canvas bags with custom designs</li>
                                    </ul>
                                    Each service comes with multiple customization options to meet your specific needs.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="products2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#productsCollapse2" aria-expanded="false" aria-controls="productsCollapse2">
                                    What's the difference between the t-shirt materials?
                                </button>
                            </h2>
                            <div id="productsCollapse2" class="accordion-collapse collapse" aria-labelledby="products2" data-bs-parent="#productsFAQ">
                                <div class="accordion-body">
                                    We offer three main t-shirt materials, each with different properties:
                                    <ul>
                                        <li><strong>Cotton:</strong> Soft, breathable, and comfortable. Ideal for everyday wear and casual events.</li>
                                        <li><strong>Polyester:</strong> Durable, wrinkle-resistant, and quick-drying. Better for maintaining color vibrancy over time. Good for sports and outdoor activities.</li>
                                        <li><strong>Dry-fit:</strong> Moisture-wicking fabric that keeps you cool and dry during physical activities. Perfect for sports teams, gym wear, and active lifestyles.</li>
                                    </ul>
                                    The best choice depends on your specific needs and how the shirts will be used.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="products3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#productsCollapse3" aria-expanded="false" aria-controls="productsCollapse3">
                                    What's the minimum order quantity?
                                </button>
                            </h2>
                            <div id="productsCollapse3" class="accordion-collapse collapse" aria-labelledby="products3" data-bs-parent="#productsFAQ">
                                <div class="accordion-body">
                                    There is no minimum order quantity for any of our services. You can order as little as one item or as many as you need. This makes our services suitable for both individual projects and large business orders.
                                    
                                    For bulk orders, we offer quantity discounts that are automatically applied during the ordering process.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Section -->
                <div class="text-center mt-5">
                    <h4 class="fw-bold mb-3">Still Have Questions?</h4>
                    <p class="text-muted mb-4">If you couldn't find the answer to your question, please feel free to contact us.</p>
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">Contact Us</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scrolling to FAQ categories
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
            const categories = document.querySelectorAll('.faq-category');
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