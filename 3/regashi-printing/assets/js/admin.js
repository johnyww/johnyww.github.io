/**
 * Admin JavaScript
 * Regashi Printing Website
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Toggle sidebar on mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
        });
    }
    
    // Admin order filter form
    const orderFilterForm = document.getElementById('orderFilterForm');
    if (orderFilterForm) {
        const resetBtn = orderFilterForm.querySelector('.reset-filter');
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Clear form fields
                const inputs = orderFilterForm.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.type === 'text' || input.type === 'number' || input.type === 'date' || input.tagName === 'SELECT') {
                        input.value = '';
                    } else if (input.type === 'checkbox' || input.type === 'radio') {
                        input.checked = false;
                    }
                });
                
                // Submit the form
                orderFilterForm.submit();
            });
        }
    }
    
    // Bulk actions handler
    const bulkActionForm = document.getElementById('bulkActionForm');
    if (bulkActionForm) {
        const bulkActionSelect = document.getElementById('bulkAction');
        const applyBtn = document.getElementById('applyBulkAction');
        
        if (bulkActionSelect && applyBtn) {
            applyBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Check if any items are selected
                const checkedItems = bulkActionForm.querySelectorAll('input[name="selected_items[]"]:checked');
                if (checkedItems.length === 0) {
                    alert('Please select at least one item to apply the action.');
                    return;
                }
                
                // Check if an action is selected
                if (bulkActionSelect.value === '') {
                    alert('Please select an action to apply.');
                    return;
                }
                
                // Confirm action if it's delete
                if (bulkActionSelect.value === 'delete' && !confirm('Are you sure you want to delete the selected items? This action cannot be undone.')) {
                    return;
                }
                
                // Submit the form
                bulkActionForm.submit();
            });
        }
        
        // Select all checkbox
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const checkboxes = bulkActionForm.querySelectorAll('input[name="selected_items[]"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        }
    }
    
    // Order status update
    const orderStatusForm = document.getElementById('orderStatusForm');
    if (orderStatusForm) {
        const statusSelect = document.getElementById('orderStatus');
        const statusSubmitBtn = document.getElementById('updateStatusBtn');
        
        if (statusSelect && statusSubmitBtn) {
            statusSelect.addEventListener('change', function() {
                if (this.value === '') {
                    statusSubmitBtn.disabled = true;
                } else {
                    statusSubmitBtn.disabled = false;
                }
            });
            
            statusSubmitBtn.addEventListener('click', function(e) {
                if (statusSelect.value === '') {
                    e.preventDefault();
                    alert('Please select a status to update.');
                    return;
                }
                
                // Confirm action if it's cancel
                if (statusSelect.value === 'cancelled' && !confirm('Are you sure you want to cancel this order?')) {
                    e.preventDefault();
                    return;
                }
            });
        }
    }
    
    // Charts initialization
    if (typeof Chart !== 'undefined') {
        try {
            // Define default data if not available
            const defaultLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            const defaultValues = [0, 0, 0, 0, 0, 0];
            
            // Get the chart data
            const salesData = window.salesData || { labels: defaultLabels, values: defaultValues };
            const ordersData = window.ordersData || { labels: defaultLabels, values: defaultValues };
            const statusData = window.statusData || { 
                labels: ['Pending', 'Processing', 'Printing', 'Out for Delivery', 'Delivered', 'Cancelled'],
                values: [0, 0, 0, 0, 0, 0]
            };
            
            // Initialize charts
            initializeCharts(salesData, ordersData, statusData);
            
        } catch (error) {
            console.error('Error initializing charts:', error);
        }
    }
    
    // Function to initialize charts
    function initializeCharts(salesData, ordersData, statusData) {
        // Sales Chart
        const salesChartCanvas = document.getElementById('salesChart');
        if (salesChartCanvas) {
            new Chart(salesChartCanvas, {
                type: 'line',
                data: {
                    labels: salesData.labels,
                    datasets: [{
                        label: 'Sales',
                        data: salesData.values,
                        borderColor: '#4263eb',
                        backgroundColor: 'rgba(66, 99, 235, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Orders Chart
        const ordersChartCanvas = document.getElementById('ordersChart');
        if (ordersChartCanvas) {
            new Chart(ordersChartCanvas, {
                type: 'bar',
                data: {
                    labels: ordersData.labels,
                    datasets: [{
                        label: 'Orders',
                        data: ordersData.values,
                        backgroundColor: '#4263eb'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
        
        // Status Chart
        const statusChartCanvas = document.getElementById('statusChart');
        if (statusChartCanvas) {
            new Chart(statusChartCanvas, {
                type: 'doughnut',
                data: {
                    labels: statusData.labels,
                    datasets: [{
                        data: statusData.values,
                        backgroundColor: [
                            '#f6c23e', // pending
                            '#36b9cc', // processing
                            '#4e73df', // printing
                            '#1cc88a', // out for delivery
                            '#1cc88a', // delivered
                            '#e74a3b'  // cancelled
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
        }
    }
    
    // Image preview for product management
    const productImageInput = document.getElementById('productImage');
    if (productImageInput) {
        productImageInput.addEventListener('change', function() {
            const previewContainer = document.getElementById('productImagePreview');
            if (previewContainer && this.files.length > 0) {
                const file = this.files[0];
                
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        previewContainer.innerHTML = `<img src="${e.target.result}" class="img-fluid preview-image" alt="Product Image">`;
                    };
                    
                    reader.readAsDataURL(file);
                }
            }
        });
    }
});