<?php
/**
 * Contact Page
 * Regashi Printing Website
 */

// Set page title
$page_title = "Contact Us";

// Include config and functions
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Handle contact form submission
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    // Validate form data
    if (empty($name)) {
        $error = "Please enter your name";
    } elseif (empty($email)) {
        $error = "Please enter your email address";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } elseif (empty($subject)) {
        $error = "Please enter a subject";
    } elseif (empty($message)) {
        $error = "Please enter your message";
    } else {
        // In a real-world scenario, we would process the form here
        // For demonstration purposes, we'll just set success to true
        $success = true;
        
        // Clear form data
        $name = $email = $subject = $message = '';
    }
}

// Include header
include_once 'includes/header.php';
?>

<!-- Contact Page Header -->
<section class="page-header bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="fw-bold mb-0">Contact Us</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Contact Content -->
<section class="contact-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="fw-bold mb-4">Get In Touch</h2>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success mb-4">
                                <i class="fas fa-check-circle me-2"></i> Thank you for your message! We'll get back to you as soon as possible.
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger mb-4">
                                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="contact-form needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $name ?? ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email ?? ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" value="<?php echo $subject ?? ''; ?>" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?php echo $message ?? ''; ?></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="fw-bold mb-4">Contact Information</h2>
                        
                        <div class="contact-info mb-4">
                            <div class="d-flex mb-3">
                                <div class="contact-icon bg-primary text-white rounded-circle me-3">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Address</h5>
                                    <p class="text-muted mb-0">123 Printing Street, Design City, 12345</p>
                                </div>
                            </div>
                            
                            <div class="d-flex mb-3">
                                <div class="contact-icon bg-primary text-white rounded-circle me-3">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Phone</h5>
                                    <p class="text-muted mb-0">+1 (234) 567-8901</p>
                                </div>
                            </div>
                            
                            <div class="d-flex mb-3">
                                <div class="contact-icon bg-primary text-white rounded-circle me-3">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Email</h5>
                                    <p class="text-muted mb-0">info@regashiprinting.com</p>
                                </div>
                            </div>
                            
                            <div class="d-flex">
                                <div class="contact-icon bg-primary text-white rounded-circle me-3">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">Business Hours</h5>
                                    <p class="text-muted mb-0">Monday - Friday: 9:00 AM - 6:00 PM</p>
                                    <p class="text-muted mb-0">Saturday: 10:00 AM - 4:00 PM</p>
                                    <p class="text-muted mb-0">Sunday: Closed</p>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="fw-bold mb-3">Follow Us</h5>
                        <div class="social-links">
                            <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="btn btn-outline-primary"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3 class="fw-bold mb-4 text-center">Our Location</h3>
                <div class="map-container shadow-sm rounded overflow-hidden">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12345.678901234567!2d-73.98765432109876!3d40.12345678901234!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDA3JzI0LjQiTiA3M8KwNTknMTUuNiJX!5e0!3m2!1sen!2sus!4v1234567890123!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-5">
                <h2 class="fw-bold">Frequently Asked Questions</h2>
                <p class="lead text-muted">Find answers to common questions about our services</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="accordion" id="contactFAQ">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                What are your delivery timeframes?
                            </button>
                        </h2>
                        <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#contactFAQ">
                            <div class="accordion-body">
                                Our standard delivery timeframe is 3-5 business days, depending on the order size and complexity. For rush orders, please contact us directly to discuss expedited options.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                Do you offer bulk discounts?
                            </button>
                        </h2>
                        <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#contactFAQ">
                            <div class="accordion-body">
                                Yes, we offer discounts for bulk orders. The discount rate depends on the order quantity and type of products. Please contact our sales team for specific pricing information.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                What file formats do you accept for printing?
                            </button>
                        </h2>
                        <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#contactFAQ">
                            <div class="accordion-body">
                                We accept most common file formats, including JPEG, PNG, PDF, AI, and PSD. For best results, we recommend using high-resolution files (300 DPI or higher) in PDF format.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                Do you offer design services?
                            </button>
                        </h2>
                        <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#contactFAQ">
                            <div class="accordion-body">
                                Yes, we offer professional design services for an additional fee. Our experienced designers can help create custom designs for your printing needs. Please contact us for more information about our design services and pricing.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="faq5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                What is your refund policy?
                            </button>
                        </h2>
                        <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faq5" data-bs-parent="#contactFAQ">
                            <div class="accordion-body">
                                We stand behind the quality of our products. If you're not satisfied with your order due to a printing error or defect, please contact us within 7 days of receiving your order. We'll either reprint your order or provide a refund, depending on the situation.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .contact-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
</style>

<?php
// Include footer
include_once 'includes/footer.php';
?>