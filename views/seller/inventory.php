<?php
$pageTitle = APP_NAME . ' - Inventory';
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/seller_nav.php';
?>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    @keyframes fillBar {
        from { width: 0; }
    }
    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 2rem;
        margin: -2.5rem -1.5rem 3rem;
        border-radius: 0 0 2rem 2rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }
    
    .page-header-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 2rem;
    }
    
    .page-title {
        font-size: 2.5rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -1px;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .page-subtitle {
        font-size: 1.0625rem;
        opacity: 0.95;
        margin-top: 0.5rem;
    }
    
    .inventory-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .inventory-actions .btn {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: white;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .inventory-actions .btn:hover {
        background: white;
        color: #667eea;
        transform: translateY(-2px);
    }
    
    .alert {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-left: 5px solid #f59e0b;
        border-radius: 1rem;
        padding: 1.75rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        animation: slideInRight 0.5s ease-out;
    }
    
    .alert-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    
    .alert h5 {
        color: #92400e;
        font-size: 1.25rem;
        font-weight: 800;
        margin-bottom: 0.75rem;
    }
    
    .alert p {
        color: #92400e;
        margin-bottom: 1rem;
    }
    
    .low-stock-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        gap: 0.75rem;
    }
    
    .low-stock-item {
        background: rgba(255, 255, 255, 0.6);
        padding: 1rem 1.25rem;
        border-radius: 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        border: 2px solid rgba(245, 158, 11, 0.3);
    }
    
    .low-stock-item strong {
        color: #92400e;
        font-weight: 700;
    }
    
    .badge {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 700;
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge-warning {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #78350f;
    }
    
    .badge-success {
        background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        color: #065f46;
    }
    
    .badge-danger {
        background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
        color: #7f1d1d;
        animation: pulse 2s infinite;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .stat-card {
        background: white;
        padding: 2rem;
        border-radius: 1.25rem;
        box-shadow: 0 4px 6px rgba(15, 23, 42, 0.07), 0 2px 4px rgba(15, 23, 42, 0.06);
        border: 2px solid #f1f5f9;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out backwards;
    }
    
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
        border-color: rgba(102, 126, 234, 0.3);
    }
    
    .stat-card.alert {
        border-left: 5px solid #f59e0b;
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    }
    
    .stat-card h3 {
        font-size: 0.875rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.75rem;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }
    
    .section {
        background: white;
        padding: 2.5rem;
        border-radius: 1.5rem;
        box-shadow: 0 4px 6px rgba(15, 23, 42, 0.07), 0 2px 4px rgba(15, 23, 42, 0.06);
        border: 2px solid #f1f5f9;
        margin-bottom: 3rem;
    }
    
    .section h2 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .no-products {
        text-align: center;
        padding: 5rem 2rem;
    }
    
    .empty-state-icon {
        font-size: 6rem;
        margin-bottom: 1.5rem;
        opacity: 0.3;
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }
    
    .no-products h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
    }
    
    .no-products p {
        color: #64748b;
        font-size: 1.0625rem;
        margin-bottom: 0.5rem;
    }
    
    .table-responsive {
        overflow-x: auto;
        border-radius: 1rem;
        border: 2px solid #e2e8f0;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table thead {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    
    .table th {
        padding: 1.25rem 1.5rem;
        text-align: left;
        font-weight: 800;
        color: #1e293b;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .table td {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    
    .table tbody tr.critical-stock {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    }
    
    .table tbody tr.critical-stock:hover {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    }
    
    .sku-badge {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        font-family: 'Courier New', monospace;
        font-weight: 700;
        font-size: 0.8125rem;
        color: #475569;
    }
    
    .stock-quantity {
        font-weight: 700;
        color: #10b981;
        font-size: 1rem;
    }
    
    .stock-quantity.low {
        color: #ef4444;
        animation: pulse 2s infinite;
    }
    
    .stock-level-bar-container {
        width: 100%;
        max-width: 150px;
    }
    
    .stock-level-bar {
        width: 100%;
        height: 12px;
        background: #e2e8f0;
        border-radius: 1rem;
        overflow: hidden;
    }
    
    .stock-level-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #34d399);
        border-radius: 1rem;
        transition: width 0.5s ease;
        animation: fillBar 1s ease-out;
    }
    
    .stock-level-fill.low {
        background: linear-gradient(90deg, #f59e0b, #fbbf24);
    }
    
    .stock-level-fill.critical {
        background: linear-gradient(90deg, #ef4444, #f87171);
    }
    
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(8px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        animation: fadeIn 0.3s ease-out;
    }
    
    .modal.active {
        display: flex;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .modal-content {
        background: white;
        border-radius: 1.5rem;
        padding: 2.5rem;
        max-width: 600px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        position: relative;
        animation: slideUp 0.3s ease-out;
    }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .modal-close {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border-radius: 50%;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        color: #64748b;
        font-weight: 700;
    }
    
    .modal-close:hover {
        background: #ef4444;
        color: white;
        transform: rotate(90deg);
    }
    
    .modal-content h2 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 1.5rem;
    }
    
    #modal-product-info {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.25rem 1.5rem;
        border-radius: 0.875rem;
        border-left: 4px solid #667eea;
    }
    
    #modal-product-info p {
        margin: 0.5rem 0;
        color: #64748b;
        font-weight: 500;
    }
    
    #modal-product-info strong {
        color: #1e293b;
        font-weight: 700;
    }
    
    .form-group {
        margin-bottom: 1.75rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.625rem;
        font-weight: 700;
        color: #1e293b;
        font-size: 0.9375rem;
    }
    
    .form-control {
        width: 100%;
        padding: 0.875rem 1.25rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.875rem;
        font-size: 1rem;
        transition: all 0.2s ease;
        font-family: inherit;
    }
    
    .form-control:hover {
        border-color: #cbd5e1;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }
    
    .toast {
        position: fixed;
        top: 2rem;
        right: 2rem;
        background: white;
        padding: 1.25rem 1.75rem;
        border-radius: 0.875rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        z-index: 2000;
        animation: slideInRight 0.3s ease-out;
        border-left: 5px solid #10b981;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .toast.error {
        border-left-color: #ef4444;
        color: #991b1b;
    }
    
    .toast.success {
        border-left-color: #10b981;
        color: #065f46;
    }
    
    @keyframes fadeOut {
        to { opacity: 0; transform: translateX(100%); }
    }
    
    @media print {
        .btn, .inventory-actions, .page-header, nav, footer, .alert { display: none !important; }
        .section { box-shadow: none; border: 1px solid #000; page-break-inside: avoid; }
        .table { font-size: 10px; }
        .badge { border: 1px solid #000; }
        body { background: white; }
    }
    
    @media (max-width: 768px) {
        .page-header { padding: 2rem 1.5rem; margin: -2.5rem -1rem 2rem; }
        .page-title { font-size: 1.75rem; }
        .inventory-actions { flex-direction: column; width: 100%; }
        .inventory-actions .btn { width: 100%; justify-content: center; }
        .stats-grid { grid-template-columns: 1fr; }
        .modal-content { padding: 2rem 1.5rem; }
        .form-actions { flex-direction: column-reverse; }
        .form-actions .btn { width: 100%; }
    }
</style>

<div class="container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="page-header-content">
            <div>
                <h1 class="page-title">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    Inventory Management
                </h1>
                <p class="page-subtitle">Monitor and manage your product stock levels</p>
            </div>
            
            <?php if (!empty($products)): ?>
            <div class="inventory-actions">
                <button onclick="exportInventory()" class="btn btn-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Export CSV
                </button>
                <button onclick="printInventory()" class="btn btn-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Print Report
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <?php if (!empty($lowStock)): ?>
    <div class="alert alert-warning">
        <div class="alert-icon">⚠️</div>
        <div>
            <h5><strong>Low Stock Alert!</strong></h5>
            <p>The following products are running low on inventory and need restocking:</p>
            <ul class="low-stock-list">
                <?php foreach ($lowStock as $product): ?>
                <li class="low-stock-item">
                    <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                    <span class="badge badge-warning"><?php echo $product['stock_quantity']; ?> units remaining</span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stats Overview -->
    <?php if (!empty($products)): ?>
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Products</h3>
            <div class="stat-value"><?php echo count($products); ?></div>
        </div>
        
        <div class="stat-card">
            <h3>In Stock</h3>
            <div class="stat-value text-success">
                <?php echo count(array_filter($products, function($p) { 
                    return $p['stock_quantity'] > $p['min_stock_quantity']; 
                })); ?>
            </div>
        </div>
        
        <div class="stat-card alert">
            <h3>Low Stock</h3>
            <div class="stat-value">
                <?php echo count(array_filter($products, function($p) { 
                    return $p['stock_quantity'] <= $p['min_stock_quantity']; 
                })); ?>
            </div>
        </div>
        
        <div class="stat-card">
            <h3>Total Units</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($products, 'stock_quantity')); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Inventory Table -->
    <div class="section">
        <h2>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#667eea" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                <line x1="12" y1="22.08" x2="12" y2="12"></line>
            </svg>
            Product Inventory
        </h2>
        
        <?php if (empty($products)): ?>
        <div class="no-products">
            <div class="empty-state-icon">📦</div>
            <h2>No Products in Inventory</h2>
            <p>You haven't added any products yet.</p>
            <p>Start by creating your first product listing.</p>
            <a href="<?php echo url('seller/product/create'); ?>" class="btn btn-primary mt-3">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Your First Product
            </a>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Current Stock</th>
                        <th>Min Stock Level</th>
                        <th>Stock Health</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <?php 
                        $isLowStock = $product['stock_quantity'] <= $product['min_stock_quantity'];
                        $isCritical = $product['stock_quantity'] <= ($product['min_stock_quantity'] * 0.5);
                        $stockPercentage = min(100, ($product['stock_quantity'] / max($product['min_stock_quantity'] * 2, 1)) * 100);
                    ?>
                    <tr class="<?php echo $isCritical ? 'critical-stock' : ''; ?>">
                        <td>
                            <strong><?php echo htmlspecialchars($product['product_name']); ?></strong>
                        </td>
                        <td>
                            <code class="sku-badge"><?php echo htmlspecialchars($product['sku']); ?></code>
                        </td>
                        <td>
                            <span class="stock-quantity <?php echo $isLowStock ? 'low' : ''; ?>">
                                <?php echo $product['stock_quantity']; ?> units
                            </span>
                        </td>
                        <td><?php echo $product['min_stock_quantity']; ?> units</td>
                        <td>
                            <div class="stock-level-bar-container">
                                <div class="stock-level-bar">
                                    <div class="stock-level-fill <?php echo $isCritical ? 'critical' : ($isLowStock ? 'low' : ''); ?>" 
                                         style="width: <?php echo $stockPercentage; ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($isCritical): ?>
                                <span class="badge badge-danger">Critical</span>
                            <?php elseif ($isLowStock): ?>
                                <span class="badge badge-warning">Low Stock</span>
                            <?php else: ?>
                                <span class="badge badge-success">In Stock</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary update-stock" 
                                    data-product-id="<?php echo $product['product_id']; ?>" 
                                    data-product-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                                    data-current-stock="<?php echo $product['stock_quantity']; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                Update
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

<!-- Stock Update Modal -->
<div class="modal" id="stock-update-modal">
    <div class="modal-content">
        <div class="modal-close" onclick="InventoryManager.closeModal()">&times;</div>
        <h2 class="mb-4">Update Stock Level</h2>
        
        <div id="modal-product-info" class="mb-4">
            <p>Product: <strong id="modal-product-name"></strong></p>
            <p>Current Stock: <strong id="modal-current-stock"></strong> units</p>
        </div>
        
        <form id="stock-update-form">
            <input type="hidden" id="modal-product-id">
            
            <div class="form-group">
                <label for="stock-operation" class="form-label">Operation</label>
                <select id="stock-operation" class="form-control">
                    <option value="add">Add Stock (Restock)</option>
                    <option value="set">Set Stock (Override)</option>
                    <option value="remove">Remove Stock (Adjustment)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="stock-quantity" class="form-label">Quantity</label>
                <input type="number" 
                       id="stock-quantity" 
                       class="form-control" 
                       min="0" 
                       placeholder="Enter quantity"
                       required>
            </div>
            
            <div class="form-group">
                <label for="stock-reason" class="form-label">Reason (Optional)</label>
                <textarea id="stock-reason" 
                          class="form-control" 
                          rows="3" 
                          placeholder="Note the reason for this stock change..."></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="InventoryManager.closeModal()">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Update Stock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    'use strict';

    // Stock Update Manager
    window.InventoryManager = {
        modal: null,
        form: null,
        baseUrl: window.BASE_URL || '<?php echo BASE_URL; ?>',

        init: function() {
            this.modal = document.getElementById('stock-update-modal');
            this.form = document.getElementById('stock-update-form');
            
            if (!this.modal || !this.form) {
                console.error('Inventory UI elements not found');
                return;
            }

            this.attachEventListeners();
            this.animateStats();
            console.log('Inventory Manager initialized');
        },

        attachEventListeners: function() {
            // Handle form submission
            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleStockUpdate();
            });

            // Handle update stock buttons
            const updateButtons = document.querySelectorAll('.update-stock');
            updateButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    this.openModal(e.currentTarget);
                });
            });

            // Close modal on background click
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) {
                    this.closeModal();
                }
            });

            // Close modal on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.modal.classList.contains('active')) {
                    this.closeModal();
                }
            });

            // Update quantity preview on operation change
            const operationSelect = document.getElementById('stock-operation');
            const quantityInput = document.getElementById('stock-quantity');
            
            if (operationSelect && quantityInput) {
                operationSelect.addEventListener('change', () => {
                    this.updateQuantityPreview();
                });
                
                quantityInput.addEventListener('input', () => {
                    this.updateQuantityPreview();
                });
            }
        },

        animateStats: function() {
            const statValues = document.querySelectorAll('.stat-value');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        const text = element.textContent.trim();
                        const number = parseInt(text.replace(/[^0-9]/g, '')) || 0;
                        
                        if (number > 0) {
                            this.animateCounter(element, 0, number, 1500);
                        }
                        observer.unobserve(element);
                    }
                });
            }, { threshold: 0.5 });
            
            statValues.forEach(stat => observer.observe(stat));
        },

        animateCounter: function(element, start, end, duration) {
            const range = end - start;
            const increment = range / (duration / 16);
            let current = start;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= end) {
                    current = end;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 16);
        },

        openModal: function(button) {
            const productId = button.dataset.productId;
            const productName = button.dataset.productName;
            const currentStock = parseInt(button.dataset.currentStock) || 0;

            // Populate modal
            document.getElementById('modal-product-id').value = productId;
            document.getElementById('modal-product-name').textContent = productName;
            document.getElementById('modal-current-stock').textContent = currentStock;

            // Reset form
            this.form.reset();
            document.getElementById('stock-operation').value = 'add';
            document.getElementById('stock-quantity').value = '';

            // Show modal
            this.modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Focus on quantity input
            setTimeout(() => {
                document.getElementById('stock-quantity').focus();
            }, 100);
        },

        closeModal: function() {
            this.modal.classList.remove('active');
            document.body.style.overflow = '';
            this.form.reset();
        },

        updateQuantityPreview: function() {
            const operation = document.getElementById('stock-operation').value;
            const quantity = parseInt(document.getElementById('stock-quantity').value) || 0;
            const currentStock = parseInt(document.getElementById('modal-current-stock').textContent) || 0;
            
            let newStock = currentStock;
            
            switch(operation) {
                case 'add':
                    newStock = currentStock + quantity;
                    break;
                case 'set':
                    newStock = quantity;
                    break;
                case 'remove':
                    newStock = Math.max(0, currentStock - quantity);
                    break;
            }

            console.log('Preview:', { operation, currentStock, quantity, newStock });
        },

        async handleStockUpdate() {
            const productId = document.getElementById('modal-product-id').value;
            const operation = document.getElementById('stock-operation').value;
            const quantity = parseInt(document.getElementById('stock-quantity').value);
            const reason = document.getElementById('stock-reason').value;
            const currentStock = parseInt(document.getElementById('modal-current-stock').textContent) || 0;

            if (!quantity || quantity < 0) {
                this.showToast('Please enter a valid quantity', 'error');
                return;
            }

            // Calculate new stock based on operation
            let newStock = currentStock;
            switch(operation) {
                case 'add':
                    newStock = currentStock + quantity;
                    break;
                case 'set':
                    newStock = quantity;
                    break;
                case 'remove':
                    newStock = Math.max(0, currentStock - quantity);
                    break;
            }

            // Show loading state
            const submitButton = this.form.querySelector('button[type="submit"]');
            const originalHTML = submitButton.innerHTML;
            submitButton.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite;"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg> Updating...';
            submitButton.disabled = true;

            try {
                // Make API call to update stock
                const response = await fetch(`${this.baseUrl}/api/v1/inventory/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        operation: operation,
                        quantity: quantity,
                        new_stock: newStock,
                        reason: reason || null
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    this.showToast('Stock updated successfully!', 'success');
                    this.closeModal();
                    
                    // Reload page to show updated stock
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error(result.message || 'Failed to update stock');
                }
            } catch (error) {
                console.error('Error updating stock:', error);
                this.showToast(error.message || 'Failed to update stock. Please try again.', 'error');
                
                // Restore button
                submitButton.innerHTML = originalHTML;
                submitButton.disabled = false;
            }
        },

        showToast: function(message, type = 'info') {
            // Remove existing toasts
            const existingToasts = document.querySelectorAll('.toast');
            existingToasts.forEach(toast => toast.remove());

            // Create toast icon
            let icon = '';
            if (type === 'success') {
                icon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
            } else if (type === 'error') {
                icon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
            }

            // Create toast
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = icon + '<span>' + message + '</span>';
            
            document.body.appendChild(toast);

            // Auto remove after 3 seconds
            setTimeout(() => {
                toast.style.animation = 'fadeOut 0.3s ease-out forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    };

    // Export to CSV
    window.exportInventory = function() {
        const table = document.querySelector('.table');
        if (!table) {
            InventoryManager.showToast('No inventory data to export', 'error');
            return;
        }

        let csv = [];
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            const cols = row.querySelectorAll('td, th');
            const rowData = [];
            
            cols.forEach((col, index) => {
                // Skip the actions column (last column)
                if (index < cols.length - 1) {
                    // Clean up the text content
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
        link.setAttribute('download', `inventory_${new Date().toISOString().split('T')[0]}.csv`);
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        InventoryManager.showToast('Inventory exported successfully!', 'success');
    };

    // Print inventory
    window.printInventory = function() {
        window.print();
    };

    // Add spin animation for loading spinner
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            InventoryManager.init();
        });
    } else {
        InventoryManager.init();
    }

})();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>