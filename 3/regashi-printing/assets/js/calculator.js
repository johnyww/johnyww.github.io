/**
 * Pricing Calculator
 * Regashi Printing Website
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get the calculator form
    const calculatorForm = document.getElementById('calculator-form');
    
    // If calculator form exists on the page
    if (calculatorForm) {
        // Get all form elements that affect price
        const priceElements = document.querySelectorAll('[data-price-factor]');
        
        // Get the price display elements
        const priceAmount = document.getElementById('price-amount');
        const basePrice = document.getElementById('base-price').value;
        
        // Attach event listeners to all price affecting elements
        priceElements.forEach(element => {
            element.addEventListener('change', updatePrice);
        });
        
        // Initial price calculation
        updatePrice();
        
        // Update price based on selected options
        function updatePrice() {
            let totalPrice = parseFloat(basePrice);
            
            // Loop through all price affecting elements and get their values
            priceElements.forEach(element => {
                if (element.type === 'checkbox') {
                    // If it's a checkbox, add the price only if checked
                    if (element.checked) {
                        totalPrice += parseFloat(element.dataset.priceValue || 0);
                    }
                } else if (element.type === 'radio') {
                    // If it's a radio button, add the price only if selected
                    if (element.checked) {
                        totalPrice += parseFloat(element.dataset.priceValue || 0);
                    }
                } else if (element.type === 'select-one') {
                    // If it's a select element, get the selected option's price
                    const selectedOption = element.options[element.selectedIndex];
                    totalPrice += parseFloat(selectedOption.dataset.priceValue || 0);
                } else if (element.type === 'number') {
                    // If it's a quantity field, multiply the item price by the quantity
                    const quantity = parseInt(element.value) || 1;
                    
                    // If minimum quantity is 1
                    if (quantity < 1) {
                        element.value = 1;
                    }
                    
                    if (element.id === 'quantity') {
                        // Base price already added once, so multiply by (quantity - 1)
                        totalPrice += basePrice * (quantity - 1);
                    }
                }
            });
            
            // Update the price display
            if (priceAmount) {
                priceAmount.textContent = formatCurrency(totalPrice);
            }
            
            // Update hidden total price input field
            const totalPriceInput = document.getElementById('total-price');
            if (totalPriceInput) {
                totalPriceInput.value = totalPrice.toFixed(2);
            }
            
            return totalPrice;
        }
        
        // Format currency
        function formatCurrency(amount) {
            return '$' + amount.toFixed(2);
        }
        
        // File upload preview
        const designFileInput = document.getElementById('design-file');
        const filePreviewContainer = document.getElementById('file-preview-container');
        
        if (designFileInput && filePreviewContainer) {
            designFileInput.addEventListener('change', function() {
                const file = this.files[0];
                
                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        let previewElement;
                        
                        // Check if it's an image file
                        if (file.type.match('image.*')) {
                            previewElement = document.createElement('img');
                            previewElement.classList.add('file-preview', 'img-fluid', 'mt-3');
                            previewElement.src = e.target.result;
                        } else {
                            // For non-image files, just show the file name
                            previewElement = document.createElement('div');
                            previewElement.classList.add('alert', 'alert-success', 'mt-3');
                            previewElement.innerHTML = `<i class="fas fa-file me-2"></i>File uploaded: ${file.name}`;
                        }
                        
                        // Clear previous preview
                        filePreviewContainer.innerHTML = '';
                        
                        // Add new preview
                        filePreviewContainer.appendChild(previewElement);
                    };
                    
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Show save design option only if logged in
        const saveDesignCheckbox = document.getElementById('save-design');
        const designNameGroup = document.getElementById('design-name-group');
        
        if (saveDesignCheckbox && designNameGroup) {
            // Initially hide design name field if checkbox is unchecked
            if (!saveDesignCheckbox.checked) {
                designNameGroup.style.display = 'none';
            }
            
            saveDesignCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    designNameGroup.style.display = 'block';
                } else {
                    designNameGroup.style.display = 'none';
                }
            });
        }
        
        // Form validation
        calculatorForm.addEventListener('submit', function(e) {
            const designFile = document.getElementById('design-file');
            const designError = document.getElementById('design-error');
            
            // Check if design file is selected
            if (designFile && designFile.required && designFile.files.length === 0) {
                e.preventDefault();
                designError.style.display = 'block';
                designFile.classList.add('is-invalid');
            } else if (designError) {
                designError.style.display = 'none';
                if (designFile) {
                    designFile.classList.remove('is-invalid');
                }
            }
            
            // Validate save design name if checkbox is checked
            const saveDesign = document.getElementById('save-design');
            const designName = document.getElementById('design-name');
            const designNameError = document.getElementById('design-name-error');
            
            if (saveDesign && saveDesign.checked && designName && designName.value.trim() === '') {
                e.preventDefault();
                designNameError.style.display = 'block';
                designName.classList.add('is-invalid');
            } else if (designNameError) {
                designNameError.style.display = 'none';
                if (designName) {
                    designName.classList.remove('is-invalid');
                }
            }
        });
    }
});
