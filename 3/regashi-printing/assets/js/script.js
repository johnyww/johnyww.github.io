/**
 * Main JavaScript
 * Regashi Printing Website
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Mobile navigation toggle
    const navbarToggler = document.querySelector('.navbar-toggler');
    if (navbarToggler) {
        navbarToggler.addEventListener('click', function() {
            document.body.classList.toggle('mobile-nav-open');
        });
    }
    
    // File input preview
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileLabel = this.nextElementSibling;
            if (fileLabel && fileLabel.classList.contains('custom-file-label')) {
                if (this.files.length > 0) {
                    fileLabel.textContent = this.files[0].name;
                } else {
                    fileLabel.textContent = 'Choose file';
                }
            }
            
            // Show file preview if available
            const previewContainerId = this.getAttribute('data-preview');
            if (previewContainerId) {
                const previewContainer = document.getElementById(previewContainerId);
                if (previewContainer && this.files.length > 0) {
                    const file = this.files[0];
                    
                    if (file.type.match('image.*')) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            previewContainer.innerHTML = `<img src="${e.target.result}" class="img-fluid preview-image" alt="File Preview">`;
                        };
                        
                        reader.readAsDataURL(file);
                    } else {
                        previewContainer.innerHTML = `
                            <div class="file-preview-icon">
                                <i class="fas fa-file fa-3x"></i>
                                <p class="mt-2">${file.name}</p>
                            </div>
                        `;
                    }
                }
            }
        });
    });
    
    // Quantity input controls
    const quantityControls = document.querySelectorAll('.quantity-control');
    quantityControls.forEach(control => {
        const input = control.querySelector('input');
        const decreaseBtn = control.querySelector('.decrease');
        const increaseBtn = control.querySelector('.increase');
        
        if (input && decreaseBtn && increaseBtn) {
            decreaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(input.value);
                if (currentValue > parseInt(input.min || 1)) {
                    input.value = currentValue - 1;
                    input.dispatchEvent(new Event('change'));
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(input.value);
                const max = parseInt(input.max || 100);
                if (currentValue < max) {
                    input.value = currentValue + 1;
                    input.dispatchEvent(new Event('change'));
                }
            });
        }
    });
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
    
    // Back to top button
    const backToTopBtn = document.getElementById('back-to-top');
    if (backToTopBtn) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });
        
        backToTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({top: 0, behavior: 'smooth'});
        });
    }
    
    // Cart item quantity update
    const cartQuantityInputs = document.querySelectorAll('.cart-quantity-input');
    cartQuantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    });
    
    // Service tab navigation
    const serviceTabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    serviceTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(event) {
            // Update URL hash
            if (history.pushState) {
                history.pushState(null, null, event.target.getAttribute('href'));
            } else {
                location.hash = event.target.getAttribute('href');
            }
        });
    });
    
    // Initialize from URL hash if available
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector(`[data-bs-toggle="tab"][href="${hash}"]`);
        if (tab) {
            new bootstrap.Tab(tab).show();
        }
    }
    
    // Material options selection for t-shirt page
    const materialOptions = document.querySelectorAll('.material-option');
    if (materialOptions.length > 0) {
        materialOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                materialOptions.forEach(opt => opt.classList.remove('active'));
                
                // Add active class to clicked option
                this.classList.add('active');
                
                // Check the associated radio button
                const radioId = this.getAttribute('for');
                if (radioId) {
                    const radio = document.getElementById(radioId);
                    if (radio) {
                        radio.checked = true;
                        radio.dispatchEvent(new Event('change'));
                    }
                }
            });
        });
    }
    
    // Color options selection for t-shirt page
    const colorOptions = document.querySelectorAll('.color-option');
    if (colorOptions.length > 0) {
        colorOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                colorOptions.forEach(opt => opt.classList.remove('active'));
                
                // Add active class to clicked option
                this.classList.add('active');
                
                // Find the parent label and get the 'for' attribute
                const label = this.closest('label');
                if (label) {
                    const radioId = label.getAttribute('for');
                    if (radioId) {
                        const radio = document.getElementById(radioId);
                        if (radio) {
                            radio.checked = true;
                            radio.dispatchEvent(new Event('change'));
                        }
                    }
                }
            });
        });
    }
    
    // Delivery method selection in checkout
    const deliveryMethodInputs = document.querySelectorAll('input[name="delivery_method"]');
    const deliveryAddressFields = document.getElementById('delivery_address_fields');
    
    if (deliveryMethodInputs.length > 0 && deliveryAddressFields) {
        deliveryMethodInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value === 'delivery') {
                    deliveryAddressFields.style.display = 'block';
                } else {
                    deliveryAddressFields.style.display = 'none';
                }
            });
        });
        
        // Initialize based on current selection
        const selectedDeliveryMethod = document.querySelector('input[name="delivery_method"]:checked');
        if (selectedDeliveryMethod) {
            if (selectedDeliveryMethod.value === 'delivery') {
                deliveryAddressFields.style.display = 'block';
            } else {
                deliveryAddressFields.style.display = 'none';
            }
        }
    }
    
    // Save design checkbox functionality
    const saveDesignCheckbox = document.getElementById('save-design');
    const designNameGroup = document.getElementById('design-name-group');
    
    if (saveDesignCheckbox && designNameGroup) {
        saveDesignCheckbox.addEventListener('change', function() {
            if (this.checked) {
                designNameGroup.style.display = 'block';
            } else {
                designNameGroup.style.display = 'none';
            }
        });
        
        // Initialize based on current state
        designNameGroup.style.display = saveDesignCheckbox.checked ? 'block' : 'none';
    }
});