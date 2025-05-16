<?php
/**
 * About Us Page
 * Regashi Printing Website
 */

// Set page title
$page_title = "About Us";

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

<!-- About Page Header -->
<section class="page-header bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="fw-bold mb-0">About Us</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">About Us</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="about-section py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="<?php echo SITE_URL; ?>/assets/images/about-image.jpg" alt="About Regashi Printing" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Our Story</h2>
                <p class="mb-4">Regashi Printing was founded with a simple mission: to provide high-quality printing services that help businesses and individuals bring their ideas to life. What started as a small printing shop has grown into a comprehensive printing service provider offering a wide range of solutions.</p>
                <p class="mb-4">Our journey began when we identified a need for reliable, high-quality printing services that combined excellent customer service with competitive pricing. Over the years, we've expanded our offerings to include paper printing, banner printing, custom t-shirts, and custom bags.</p>
                <p class="mb-0">Today, we're proud to serve a diverse range of clients, from small businesses to large corporations, and individuals looking for personalized printing solutions. Our commitment to quality, innovation, and customer satisfaction remains at the heart of everything we do.</p>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-12 text-center mb-5">
                <h2 class="fw-bold">Our Values</h2>
                <p class="lead text-muted">The principles that guide our business</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="value-icon bg-primary text-white rounded-circle mx-auto mb-4">
                            <i class="fas fa-medal"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">Quality</h5>
                        <p class="card-text text-muted">We are committed to delivering the highest quality printing services. We use premium materials and state-of-the-art technology to ensure exceptional results every time.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="value-icon bg-primary text-white rounded-circle mx-auto mb-4">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">Reliability</h5>
                        <p class="card-text text-muted">We understand the importance of deadlines. You can count on us to deliver your orders on time, every time, without compromising on quality or attention to detail.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="value-icon bg-primary text-white rounded-circle mx-auto mb-4">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">Customer Focus</h5>
                        <p class="card-text text-muted">Our customers are at the center of everything we do. We listen to your needs, provide personalized solutions, and ensure a seamless experience from order to delivery.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="value-icon bg-primary text-white rounded-circle mx-auto mb-4">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">Innovation</h5>
                        <p class="card-text text-muted">We continuously strive to improve our processes, adopt new technologies, and expand our offerings to provide you with the best printing solutions available.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="value-icon bg-primary text-white rounded-circle mx-auto mb-4">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">Sustainability</h5>
                        <p class="card-text text-muted">We are committed to environmentally responsible practices. We use eco-friendly materials whenever possible and continuously work to reduce our environmental footprint.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="value-icon bg-primary text-white rounded-circle mx-auto mb-4">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h5 class="card-title fw-bold mb-3">Passion</h5>
                        <p class="card-text text-muted">We are passionate about printing and helping our customers achieve their goals. This passion drives us to go the extra mile in everything we do.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-5">
                <h2 class="fw-bold">Our Team</h2>
                <p class="lead text-muted">Meet the people behind Regashi Printing</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card team-card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="team-avatar mb-3">
                            <img src="<?php echo SITE_URL; ?>/assets/images/team/team-1.jpg" alt="Team Member" class="img-fluid rounded-circle">
                        </div>
                        <h5 class="card-title fw-bold mb-1">John Smith</h5>
                        <p class="text-muted mb-3">Founder & CEO</p>
                        <p class="card-text text-muted mb-3">John founded Regashi Printing with over 15 years of experience in the printing industry. His vision and leadership have been instrumental in the company's growth.</p>
                        <div class="social-links">
                            <a href="#" class="text-muted me-2"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="text-muted me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-muted"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card team-card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="team-avatar mb-3">
                            <img src="<?php echo SITE_URL; ?>/assets/images/team/team-2.jpg" alt="Team Member" class="img-fluid rounded-circle">
                        </div>
                        <h5 class="card-title fw-bold mb-1">Jane Doe</h5>
                        <p class="text-muted mb-3">Design Director</p>
                        <p class="card-text text-muted mb-3">Jane leads our design team with her creative expertise and attention to detail. She ensures that every project meets the highest design standards.</p>
                        <div class="social-links">
                            <a href="#" class="text-muted me-2"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="text-muted me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-muted"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card team-card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="team-avatar mb-3">
                            <img src="<?php echo SITE_URL; ?>/assets/images/team/team-3.jpg" alt="Team Member" class="img-fluid rounded-circle">
                        </div>
                        <h5 class="card-title fw-bold mb-1">Mike Johnson</h5>
                        <p class="text-muted mb-3">Production Manager</p>
                        <p class="card-text text-muted mb-3">Mike oversees our production processes, ensuring efficiency, quality, and timely delivery of all orders. His expertise keeps our operations running smoothly.</p>
                        <div class="social-links">
                            <a href="#" class="text-muted me-2"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="text-muted me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-muted"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-5">
                <h2 class="fw-bold">What Our Customers Say</h2>
                <p class="lead text-muted">Hear from our satisfied clients</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card testimonial-card h-100 p-4 border rounded shadow-sm">
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
                <div class="card testimonial-card h-100 p-4 border rounded shadow-sm">
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
                <div class="card testimonial-card h-100 p-4 border rounded shadow-sm">
                    <div class="testimonial-rating text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="testimonial-text mb-4">"The banners we ordered for our trade show were a hit! The printing quality was excellent, and they were delivered on time. Great customer service throughout the ordering process."</p>
                    <div class="testimonial-author d-flex align-items-center">
                        <div class="testimonial-avatar me-3 bg-primary text-white rounded-circle">RJ</div>
                        <div>
                            <h6 class="mb-1">Robert Johnson</h6>
                            <p class="small text-muted mb-0">Business Owner</p>
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
                <p class="lead mb-0">Get in touch with us today and let us help you bring your ideas to life!</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-light btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<style>
    .value-icon, .testimonial-avatar {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .team-avatar img {
        width: 150px;
        height: 150px;
        object-fit: cover;
    }
    
    .testimonial-avatar {
        width: 50px;
        height: 50px;
        font-weight: 600;
    }
</style>

<?php
// Include footer
include_once 'includes/footer.php';
?>