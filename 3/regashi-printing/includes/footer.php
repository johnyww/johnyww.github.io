<?php
/**
 * Footer Template
 * Regashi Printing Website
 */
?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">About Regashi Printing</h5>
                    <p class="text-muted">Professional printing services for all your needs. We provide high-quality printing solutions for businesses and individuals.</p>
                    <div class="d-flex mt-3">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>" class="text-muted">Home</a></li>
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/services.php" class="text-muted">Services</a></li>
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/about.php" class="text-muted">About Us</a></li>
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/contact.php" class="text-muted">Contact</a></li>
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/faq.php" class="text-muted">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Services</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/services/paper-printing.php" class="text-muted">Paper Printing</a></li>
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/services/banner-printing.php" class="text-muted">Banner Printing</a></li>
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/services/tshirt-printing.php" class="text-muted">Custom T-Shirts</a></li>
                        <li class="mb-2"><a href="<?php echo SITE_URL; ?>/services/bag-printing.php" class="text-muted">Custom Bags</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="mb-3">Contact Us</h5>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 Printing Street, Design City</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +1 234 567 8901</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@regashiprinting.com</li>
                        <li class="mb-2"><i class="fas fa-clock me-2"></i> Mon - Fri: 9:00 AM - 6:00 PM</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="small text-muted mb-md-0">© <?php echo date('Y'); ?> Regashi Printing. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline small mb-0">
                        <li class="list-inline-item"><a href="<?php echo SITE_URL; ?>/terms.php" class="text-muted">Terms of Service</a></li>
                        <li class="list-inline-item"><span class="text-muted">•</span></li>
                        <li class="list-inline-item"><a href="<?php echo SITE_URL; ?>/privacy.php" class="text-muted">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="<?php echo SITE_URL; ?>/assets/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
    
    <?php if (isset($extra_js)): ?>
        <?php echo $extra_js; ?>
    <?php endif; ?>
</body>
</html>
