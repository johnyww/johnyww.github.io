<?php
/**
 * Homepage
 * Regashi Printing Website
 */

// Set page title
$page_title = "Home";

// Include header
include_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section position-relative">
    <div class="hero-slider">
        <div class="hero-slide" style="background-image: url('assets/images/hero-image.jpg');">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="hero-content text-white py-5">
                            <h1 class="display-4 fw-bold mb-3">Quality Printing Solutions</h1>
                            <p class="lead mb-4">We provide professional printing services for all your needs. From paper printing to custom t-shirts and bags.</p>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="services.php" class="btn btn-primary btn-lg">Our Services</a>
                                <a href="contact.php" class="btn btn-outline-light btn-lg">Contact Us</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">Our Services</h2>
            <p class="text-muted">Explore our wide range of printing services</p>
        </div>
        
        <div class="row g-4">
            <!-- Paper Printing -->
            <div class="col-md-6 col-lg-3">
                <div class="card service-card h-100 border-0 shadow-sm">
                    <div class="card-image-container">
                        <img src="assets/images/services/paper-printing.jpg" class="card-img-top" alt="Paper Printing">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Paper Printing</h5>
                        <p class="card-text">High-quality prints for documents, business cards, flyers, and more.</p>
                        <ul class="list-unstyled mb-4">
                            <li><i class="fas fa-check-circle text-success me-2"></i>Multiple sizes (A1-A4)</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Various paper thickness</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Color and grayscale options</li>
                        </ul>
                        <a href="services/paper-printing.php" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
            
            <!-- Banner Printing -->
            <div class="col-md-6 col-lg-3">
                <div class="card service-card h-100 border-0 shadow-sm">
                    <div class="card-image-container">
                        <img src="assets/images/services/banner-printing.jpg" class="card-img-top" alt="Banner Printing">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Banner Printing</h5>
                        <p class="card-text">Eye-catching banners for promotions, events, and advertisements.</p>
                        <ul class="list-unstyled mb-4">
                            <li><i class="fas fa-check-circle text-success me-2"></i>Custom sizes available</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Durable materials</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Indoor and outdoor options</li>
                        </ul>
                        <a href="services/banner-printing.php" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
            
            <!-- T-Shirt Printing -->
            <div class="col-md-6 col-lg-3">
                <div class="card service-card h-100 border-0 shadow-sm">
                    <div class="card-image-container">
                        <img src="assets/images/services/tshirt-printing.jpg" class="card-img-top" alt="T-Shirt Printing">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Custom T-Shirts</h5>
                        <p class="card-text">Personalized t-shirts for teams, events, promotions, or personal use.</p>
                        <ul class="list-unstyled mb-4">
                            <li><i class="fas fa-check-circle text-success me-2"></i>Multiple sizes (XS-XXL)</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Various materials</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Different colors available</li>
                        </ul>
                        <a href="services/tshirt-printing.php" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
            
            <!-- Bag Printing -->
            <div class="col-md-6 col-lg-3">
                <div class="card service-card h-100 border-0 shadow-sm">
                    <div class="card-image-container">
                        <img src="assets/images/services/bag-printing.jpg" class="card-img-top" alt="Bag Printing">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Custom Bags</h5>
                        <p class="card-text">Personalized bags for branding, promotions, or personal use.</p>
                        <ul class="list-unstyled mb-4">
                            <li><i class="fas fa-check-circle text-success me-2"></i>Tote bags & canvas bags</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Multiple sizes available</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i>Various colors to choose</li>
                        </ul>
                        <a href="services/bag-printing.php" class="btn btn-outline-primary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="how-it-works-section py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">How It Works</h2>
            <p class="text-muted">Simple process, professional results</p>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="process-card text-center">
                    <div class="process-icon bg-primary text-white mb-3">
                        <i class="fas fa-upload"></i>
                    </div>
                    <h5>Upload Your Design</h5>
                    <p class="text-muted">Upload your design files or choose from our templates.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="process-card text-center">
                    <div class="process-icon bg-primary text-white mb-3">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <h5>Customize Options</h5>
                    <p class="text-muted">Select size, material, quantity, and other specifications.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <div class="process-card text-center">
                    <div class="process-icon bg-primary text-white mb-3">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h5>Place Your Order</h5>
                    <p class="text-muted">Review your design, make payment, and confirm your order.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="process-card text-center">
                    <div class="process-icon bg-primary text-white mb-3">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h5>Delivery</h5>
                    <p class="text-muted">We'll print your order and deliver it to your doorstep.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">What Our Customers Say</h2>
            <p class="text-muted">Trusted by businesses and individuals</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card h-100 p-4 border rounded shadow-sm">
                    <div class="testimonial-rating text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text mb-4">"The quality of the business cards exceeded my expectations. The colors are vibrant, and the paper quality is excellent. Fast delivery too!"</p>
                    <div class="testimonial-author d-flex align-items-center">
                        <div class="testimonial-avatar me-3 bg-primary text-white rounded-circle">JD</div>
                        <div>
                            <h6 class="mb-1">John Doe</h6>
                            <p class="small text-muted mb-0">Marketing Manager</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card h-100 p-4 border rounded shadow-sm">
                    <div class="testimonial-rating text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text mb-4">"We ordered custom t-shirts for our company event, and they turned out amazing! The printing was crisp, and the shirts were comfortable. Will definitely order again."</p>
                    <div class="testimonial-author d-flex align-items-center">
                        <div class="testimonial-avatar me-3 bg-primary text-white rounded-circle">JS</div>
                        <div>
                            <h6 class="mb-1">Jane Smith</h6>
                            <p class="small text-muted mb-0">Event Coordinator</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card h-100 p-4 border rounded shadow-sm">
                    <div class="testimonial-rating text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="testimonial-text mb-4">"The tote bags we ordered for our conference were a hit! The printing quality was excellent, and the bags were sturdy. Great customer service throughout the ordering process."</p>
                    <div class="testimonial-author d-flex align-items-center">
                        <div class="testimonial-avatar me-3 bg-primary text-white rounded-circle">RJ</div>
                        <div>
                            <h6 class="mb-1">Robert Johnson</h6>
                            <p class="small text-muted mb-0">Conference Organizer</p>
                        </div>
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
                <p class="lead mb-0">Get high-quality printing services for your business or personal needs.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="services.php" class="btn btn-light btn-lg">Get Started</a>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include_once 'includes/footer.php';
?>
