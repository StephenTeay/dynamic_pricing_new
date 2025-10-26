<?php
$pageTitle = APP_NAME . ' - Pricing';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/seller_nav.php';
?>

<div class="container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="page-title">Pricing Management</h1>
        <p class="page-subtitle">Manage product prices and profit margins</p>
    </div>

    <!-- Action Buttons -->
    <?php if (!empty($products)): ?>
    <div class="pricing-actions mb-4">
        <button onclick="bulkPriceUpdate()" class="btn btn-secondary">
            <span>💰 Bulk Price Update</span>
        </button>
        <button onclick="exportPricing()" class="btn btn-secondary">
            <span>📊 Export Pricing</span>
        </button>
    </div>
    <?php endif; ?>

    <!-- Pricing Stats -->
    <?php if (!empty($products)): ?>
    <?php
        $avgMargin = array_sum(array_column($products, 'margin')) / count($products);
        $highMarginProducts = count(array_filter($products, function($p) { return ($p['margin'] ?? 0) > 30; }));
        $lowMarginProducts = count(array_filter($products, function($p) { return ($p['margin'] ?? 0) < 15; }));
        $totalRevenue = array_sum(array_map(function($p) { return $p['current_price'] * ($p['stock_quantity'] ?? 0); }, $products));
    ?>
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Average Margin</h3>
            <div class="stat-value"><?php echo number_format($avgMargin, 1); ?>%</div>
        </div>
        
        <div class="stat-card">
            <h3>High Margin Products</h3>
            <div class="stat-value text-success"><?php echo $highMarginProducts; ?></div>
            <small class="text-secondary">Above 30%</small>
        </div>
        
        <div class="stat-card <?php echo $lowMarginProducts > 0 ? 'alert' : ''; ?>">
            <h3>Low Margin Products</h3>
            <div class="stat-value"><?php echo $lowMarginProducts; ?></div>
            <small class="text-secondary">Below 15%</small>
        </div>
        
        <div class="stat-card">
            <h3>Potential Revenue</h3>
            <div class="stat-value">$<?php echo number_format($totalRevenue, 0); ?></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Pricing Table -->
    <div class="section">
        <h2>Product Pricing</h2>
        
        <?php if (empty($products)): ?>
        <div class="no-products">
            <div class="empty-state-icon">💰</div>
            <h2>No Products to Manage</h2>
            <p>You haven't added any products yet.</p>
            <p>Create your first product to start managing prices.</p>
            <a href="<?php echo url('seller/product/create'); ?>" class="btn btn-primary mt-3">
                <span>Add Your First Product</span>
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Current Price</th>
                        <th>Base Cost</th>
                        <th>Profit Margin</th>
                        <th>Profit/Unit</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <?php 
                        $price = $product['current_price'] ?? 0;
                        $cost = $product['base_cost'] ?? 0;
                        $margin = $product['margin'] ?? 0;
                        $profit = $price - $cost;
                        $marginClass = $margin > 30 ? 'high-margin' : ($margin < 15 ? 'low-margin' : 'good-margin');
                    ?>
                    <tr class="pricing-row">
                        <td>
                            <strong><?php echo htmlspecialchars($product['product_name'] ?? 'Unnamed Product'); ?></strong>
                            <br>
                            <small class="text-secondary"><?php echo htmlspecialchars($product['sku'] ?? 'N/A'); ?></small>
                        </td>
                        <td>
                            <span class="price-display">$<?php echo number_format($price, 2); ?></span>
                        </td>
                        <td>
                            <span class="cost-display">$<?php echo number_format($cost, 2); ?></span>
                        </td>
                        <td>
                            <div class="margin-indicator <?php echo $marginClass; ?>">
                                <span class="margin-value"><?php echo number_format($margin, 1); ?>%</span>
                                <div class="margin-bar">
                                    <div class="margin-fill <?php echo $marginClass; ?>" 
                                         style="width: <?php echo min(100, $margin); ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="profit-display <?php echo $profit > 0 ? 'text-success' : 'text-danger'; ?>">
                                $<?php echo number_format($profit, 2); ?>
                            </span>
                        </td>
                        <td>
                            <span class="text-secondary">
                                <?php echo $product['last_price_update'] ? date('M j, Y', strtotime($product['last_price_update'])) : 'Never'; ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary update-price" 
                                    data-product-id="<?php echo $product['product_id']; ?>"
                                    data-product-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                    data-current-price="<?php echo $price; ?>"
                                    data-base-cost="<?php echo $cost; ?>">
                                <span>Update Price</span>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Price Update Modal -->
<div class="modal" id="price-update-modal">
    <div class="modal-content">
        <div class="modal-close" onclick="PricingManager.closeModal()">&times;</div>
        <h2 class="mb-4">Update Product Price</h2>
        
        <div id="modal-product-info" class="mb-4">
            <p class="text-secondary">Product: <strong id="modal-product-name"></strong></p>
            <p class="text-secondary">Current Price: <strong id="modal-current-price"></strong></p>
            <p class="text-secondary">Base Cost: <strong id="modal-base-cost"></strong></p>
        </div>
        
        <form id="price-update-form">
            <input type="hidden" id="modal-product-id">
            
            <div class="form-group">
                <label for="new-price" class="form-label">New Price ($) *</label>
                <input type="number" 
                       id="new-price" 
                       class="form-control" 
                       step="0.01"
                       min="0" 
                       placeholder="Enter new price"
                       required>
            </div>
            
            <div class="form-group">
                <label for="price-reason" class="form-label">Reason for Change (Optional)</label>
                <textarea id="price-reason" 
                          class="form-control" 
                          rows="3" 
                          placeholder="Note the reason for this price change..."></textarea>
            </div>
            
            <!-- Price Preview -->
            <div class="price-preview" id="price-preview" style="display: none;">
                <div class="price-preview-row">
                    <span>New Price:</span>
                    <strong id="preview-new-price">$0.00</strong>
                </div>
                <div class="price-preview-row">
                    <span>Profit per Unit:</span>
                    <strong id="preview-profit" class="text-success">$0.00</strong>
                </div>
                <div class="price-preview-row">
                    <span>Profit Margin:</span>
                    <strong id="preview-margin">0%</strong>
                </div>
                <div class="price-preview-row">
                    <span>Change:</span>
                    <strong id="preview-change">$0.00 (0%)</strong>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="PricingManager.closeModal()">
                    <span>Cancel</span>
                </button>
                <button type="submit" class="btn btn-primary">
                    <span>Update Price</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Update Modal -->
<div class="modal" id="bulk-update-modal">
    <div class="modal-content">
        <div class="modal-close" onclick="PricingManager.closeBulkModal()">&times;</div>
        <h2 class="mb-4">Bulk Price Update</h2>
        
        <p class="text-secondary mb-4">Apply a price adjustment to multiple products at once.</p>
        
        <form id="bulk-update-form">
            <div class="form-group">
                <label class="form-label">Adjustment Type</label>
                <select id="bulk-type" class="form-control">
                    <option value="percentage">Percentage Increase/Decrease</option>
                    <option value="fixed">Fixed Amount Increase/Decrease</option>
                    <option value="margin">Set Target Margin</option>
                </select>
            </div>
            
            <div class="form-group" id="percentage-group">
                <label for="bulk-percentage" class="form-label">Percentage (%)</label>
                <input type="number" 
                       id="bulk-percentage" 
                       class="form-control" 
                       step="0.1"
                       placeholder="e.g., 10 for +10% or -5 for -5%">
                <small class="text-secondary">Use positive for increase, negative for decrease</small>
            </div>
            
            <div class="form-group" id="fixed-group" style="display: none;">
                <label for="bulk-fixed" class="form-label">Fixed Amount ($)</label>
                <input type="number" 
                       id="bulk-fixed" 
                       class="form-control" 
                       step="0.01"
                       placeholder="e.g., 5.00 for +$5 or -2.50 for -$2.50">
                <small class="text-secondary">Use positive for increase, negative for decrease</small>
            </div>
            
            <div class="form-group" id="margin-group" style="display: none;">
                <label for="bulk-margin" class="form-label">Target Margin (%)</label>
                <input type="number" 
                       id="bulk-margin" 
                       class="form-control" 
                       step="0.1"
                       min="0"
                       max="100"
                       placeholder="e.g., 25 for 25% margin">
            </div>
            
            <div class="alert alert-info">
                <strong>Note:</strong> This will update prices for all products. Review carefully before confirming.
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="PricingManager.closeBulkModal()">
                    <span>Cancel</span>
                </button>
                <button type="submit" class="btn btn-primary">
                    <span>Apply to All Products</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Modal Styles - Complete */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(8px);
    z-index: 10000;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal.active {
    display: flex !important;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    background: white;
    padding: 2.5rem;
    border-radius: 1.25rem;
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 25px 50px rgba(15, 23, 42, 0.35);
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-content h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 1.5rem;
}

.modal-close {
    position: absolute;
    top: 1.25rem;
    right: 1.25rem;
    font-size: 2rem;
    line-height: 1;
    cursor: pointer;
    color: #64748b;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    background: transparent;
    border: none;
}

.modal-close:hover {
    color: #0f172a;
    background: #f1f5f9;
    transform: rotate(90deg);
}

#modal-product-info {
    padding: 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 0.5rem;
    border-left: 4px solid #3b82f6;
    margin-bottom: 1.5rem;
}

#modal-product-info p {
    margin-bottom: 0.5rem;
}

#modal-product-info p:last-child {
    margin-bottom: 0;
}

.form-group {
    margin-bottom: 1.75rem;
}

.form-label {
    display: block;
    margin-bottom: 0.625rem;
    font-weight: 600;
    color: #0f172a;
    font-size: 0.9375rem;
}

.form-control {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: all 0.2s ease;
    background: white;
    font-family: inherit;
}

.form-control:hover {
    border-color: #cbd5e1;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
    line-height: 1.6;
}

select.form-control {
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 16px 12px;
    padding-right: 3rem;
    cursor: pointer;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
    border-top: 2px solid #e2e8f0;
}

/* Pricing-specific styles */
.pricing-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    align-items: center;
}

.pricing-row {
    transition: all 0.2s ease;
}

.pricing-row:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.price-display {
    font-size: 1.25rem;
    font-weight: 700;
    color: #3b82f6;
}

.cost-display {
    font-size: 1.125rem;
    font-weight: 600;
    color: #64748b;
}

.profit-display {
    font-size: 1.125rem;
    font-weight: 700;
}

.margin-indicator {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.margin-value {
    font-size: 1.125rem;
    font-weight: 700;
}

.margin-indicator.high-margin .margin-value {
    color: #10b981;
}

.margin-indicator.good-margin .margin-value {
    color: #3b82f6;
}

.margin-indicator.low-margin .margin-value {
    color: #ef4444;
}

.margin-bar {
    width: 100px;
    height: 8px;
    background: #e2e8f0;
    border-radius: 1rem;
    overflow: hidden;
}

.margin-fill {
    height: 100%;
    transition: width 0.3s ease;
}

.margin-fill.high-margin {
    background: linear-gradient(90deg, #10b981, #059669);
}

.margin-fill.good-margin {
    background: linear-gradient(90deg, #3b82f6, #2563eb);
}

.margin-fill.low-margin {
    background: linear-gradient(90deg, #f59e0b, #d97706);
}

.price-preview {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem;
    border-radius: 0.75rem;
    border-left: 4px solid #3b82f6;
    margin-bottom: 1.5rem;
}

.price-preview-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    font-size: 1rem;
}

.price-preview-row:last-child {
    margin-bottom: 0;
    padding-top: 0.75rem;
    border-top: 2px solid #cbd5e1;
}

.price-preview-row strong {
    font-size: 1.125rem;
}

@media (max-width: 768px) {
    .modal-content {
        padding: 2rem;
        max-width: 95%;
    }

    .pricing-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .pricing-actions .btn {
        width: 100%;
    }
    
    .margin-bar {
        width: 80px;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .form-actions .btn {
        width: 100%;
    }
}
</style>

<script>
(function() {
    'use strict';

    // Pricing Manager
    window.PricingManager = {
        modal: null,
        bulkModal: null,
        form: null,
        bulkForm: null,
        baseUrl: window.BASE_URL || '<?php echo BASE_URL; ?>',

        init: function() {
            this.modal = document.getElementById('price-update-modal');
            this.bulkModal = document.getElementById('bulk-update-modal');
            this.form = document.getElementById('price-update-form');
            this.bulkForm = document.getElementById('bulk-update-form');
            
            if (!this.modal || !this.form) {
                console.error('Pricing UI elements not found');
                return;
            }

            this.attachEventListeners();
            console.log('Pricing Manager initialized');
        },

        attachEventListeners: function() {
            // Handle form submission
            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handlePriceUpdate();
            });

            // Handle bulk form submission
            if (this.bulkForm) {
                this.bulkForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleBulkUpdate();
                });

                // Handle bulk type change
                const bulkType = document.getElementById('bulk-type');
                bulkType.addEventListener('change', () => this.updateBulkFields());
            }

            // Handle update price buttons
            const updateButtons = document.querySelectorAll('.update-price');
            updateButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    this.openModal(e.currentTarget);
                });
            });

            // Close modal on background click
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) this.closeModal();
            });

            if (this.bulkModal) {
                this.bulkModal.addEventListener('click', (e) => {
                    if (e.target === this.bulkModal) this.closeBulkModal();
                });
            }

            // Close modal on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (this.modal.classList.contains('active')) this.closeModal();
                    if (this.bulkModal && this.bulkModal.classList.contains('active')) this.closeBulkModal();
                }
            });

            // Real-time price preview
            const newPriceInput = document.getElementById('new-price');
            if (newPriceInput) {
                newPriceInput.addEventListener('input', () => this.updatePricePreview());
            }
        },

        openModal: function(button) {
            const productId = button.dataset.productId;
            const productName = button.dataset.productName;
            const currentPrice = parseFloat(button.dataset.currentPrice) || 0;
            const baseCost = parseFloat(button.dataset.baseCost) || 0;

            // Populate modal
            document.getElementById('modal-product-id').value = productId;
            document.getElementById('modal-product-name').textContent = productName;
            document.getElementById('modal-current-price').textContent = '$' + currentPrice.toFixed(2);
            document.getElementById('modal-base-cost').textContent = '$' + baseCost.toFixed(2);

            // Store for preview calculation
            this.currentPrice = currentPrice;
            this.baseCost = baseCost;

            // Reset form
            this.form.reset();
            document.getElementById('price-preview').style.display = 'none';

            // Show modal
            this.modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Focus on price input
            setTimeout(() => {
                document.getElementById('new-price').focus();
            }, 100);
        },

        closeModal: function() {
            this.modal.classList.remove('active');
            document.body.style.overflow = '';
            this.form.reset();
        },

        closeBulkModal: function() {
            if (this.bulkModal) {
                this.bulkModal.classList.remove('active');
                document.body.style.overflow = '';
                this.bulkForm.reset();
            }
        },

        updateBulkFields: function() {
            const type = document.getElementById('bulk-type').value;
            document.getElementById('percentage-group').style.display = type === 'percentage' ? 'block' : 'none';
            document.getElementById('fixed-group').style.display = type === 'fixed' ? 'block' : 'none';
            document.getElementById('margin-group').style.display = type === 'margin' ? 'block' : 'none';
        },

        updatePricePreview: function() {
            const newPrice = parseFloat(document.getElementById('new-price').value) || 0;
            const preview = document.getElementById('price-preview');
            
            if (newPrice <= 0) {
                preview.style.display = 'none';
                return;
            }

            const profit = newPrice - this.baseCost;
            const margin = this.baseCost > 0 ? ((profit / newPrice) * 100) : 0;
            const change = newPrice - this.currentPrice;
            const changePercent = this.currentPrice > 0 ? ((change / this.currentPrice) * 100) : 0;

            document.getElementById('preview-new-price').textContent = '$' + newPrice.toFixed(2);
            document.getElementById('preview-profit').textContent = '$' + profit.toFixed(2);
            document.getElementById('preview-profit').className = profit >= 0 ? 'text-success' : 'text-danger';
            document.getElementById('preview-margin').textContent = margin.toFixed(1) + '%';
            document.getElementById('preview-margin').className = margin > 20 ? 'text-success' : (margin > 10 ? '' : 'text-danger');
            
            const changeText = (change >= 0 ? '+' : '') + '$' + change.toFixed(2) + ' (' + (changePercent >= 0 ? '+' : '') + changePercent.toFixed(1) + '%)';
            document.getElementById('preview-change').textContent = changeText;
            document.getElementById('preview-change').className = change >= 0 ? 'text-success' : 'text-danger';

            preview.style.display = 'block';
        },

        async handlePriceUpdate() {
            const productId = document.getElementById('modal-product-id').value;
            const newPrice = parseFloat(document.getElementById('new-price').value);
            const reason = document.getElementById('price-reason').value;

            if (!newPrice || newPrice < 0) {
                this.showToast('Please enter a valid price', 'error');
                return;
            }

            const submitButton = this.form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<span>Updating...</span>';
            submitButton.disabled = true;

            try {
                const response = await fetch(`${this.baseUrl}/api/v1/pricing/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        new_price: newPrice,
                        reason: reason || null
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showToast('Price updated successfully!', 'success');
                    this.closeModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error(result.message || 'Failed to update price');
                }
            } catch (error) {
                console.error('Error updating price:', error);
                this.showToast(error.message || 'Failed to update price. Please try again.', 'error');
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        },

        async handleBulkUpdate() {
            const type = document.getElementById('bulk-type').value;
            let value;

            switch(type) {
                case 'percentage':
                    value = parseFloat(document.getElementById('bulk-percentage').value);
                    break;
                case 'fixed':
                    value = parseFloat(document.getElementById('bulk-fixed').value);
                    break;
                case 'margin':
                    value = parseFloat(document.getElementById('bulk-margin').value);
                    break;
            }

            if (isNaN(value)) {
                this.showToast('Please enter a valid value', 'error');
                return;
            }

            if (!confirm('This will update prices for ALL products. Are you sure?')) {
                return;
            }

            const submitButton = this.bulkForm.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<span>Updating...</span>';
            submitButton.disabled = true;

            try {
                const response = await fetch(`${this.baseUrl}/api/v1/pricing/bulk-update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        type: type,
                        value: value
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showToast('Bulk price update completed!', 'success');
                    this.closeBulkModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error(result.message || 'Failed to update prices');
                }
            } catch (error) {
                console.error('Error in bulk update:', error);
                this.showToast(error.message || 'Failed to update prices. Please try again.', 'error');
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        },

        showToast: function(message, type = 'info') {
            const existingToasts = document.querySelectorAll('.toast');
            existingToasts.forEach(toast => toast.remove());

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.3s ease-out forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    };

    // Export to CSV
    window.exportPricing = function() {
        const table = document.querySelector('.table');
        if (!table) {
            PricingManager.showToast('No pricing data to export', 'error');
            return;
        }

        let csv = [];
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            const cols = row.querySelectorAll('td, th');
            const rowData = [];
            
            cols.forEach((col, index) => {
                if (index < cols.length - 1) {
                    let text = col.textContent.trim().replace(/\s+/g, ' ');
                    rowData.push('"' + text.replace(/"/g, '""') + '"');
                }
            });
            
            if (rowData.length > 0) {
                csv.push(rowData.join(','));
            }
        });

        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        
        link.setAttribute('hidden', '');
        link.setAttribute('href', url);
        link.setAttribute('download', `pricing_${new Date().toISOString().split('T')[0]}.csv`);
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        PricingManager.showToast('Pricing data exported successfully!', 'success');
    };

    // Bulk price update modal
    window.bulkPriceUpdate = function() {
        if (PricingManager.bulkModal) {
            PricingManager.bulkModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            PricingManager.init();
        });
    } else {
        PricingManager.init();
    }

})();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>