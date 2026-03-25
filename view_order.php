<?php
require_once 'db.php';
requireLogin();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: orders.php?msg=' . urlencode('Invalid order ID.'));
    exit;
}

$order_id = $_GET['id'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $success_msg = "Order status updated successfully!";
    } catch (PDOException $e) {
        $error = "Error updating status: " . $e->getMessage();
    }
}

// Fetch order details
try {
    $stmt = $pdo->prepare("
        SELECT o.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone, c.address as customer_address
        FROM orders o 
        LEFT JOIN customers c ON o.customer_id = c.id 
        WHERE o.id = ?
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        header('Location: orders.php?msg=' . urlencode('Order not found.'));
        exit;
    }

    // Fetch order items
    $stmt_items = $pdo->prepare("
        SELECT oi.*, p.name as product_name, p.image as product_image 
        FROM order_items oi 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?
    ");
    $stmt_items->execute([$order_id]);
    $items = $stmt_items->fetchAll();

} catch (PDOException $e) {
    if (!isset($error)) {
        $error = "Error fetching order details: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order #<?= htmlspecialchars($order['id'] ?? '') ?> - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .order-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start; }
        .info-card { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 1.5rem; }
        .info-card h3 { margin-top: 0; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem; margin-bottom: 1rem; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 0.75rem; border-bottom: 1px dashed #e2e8f0; padding-bottom: 0.5rem; }
        .detail-row:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .detail-label { color: #64748b; font-weight: 500; }
        .detail-value { color: #0f172a; font-weight: 600; text-align: right; }
        
        .item-list { width: 100%; border-collapse: collapse; }
        .item-list th { text-align: left; padding: 0.75rem; border-bottom: 2px solid #e2e8f0; color: #64748b; }
        .item-list td { padding: 1rem 0.75rem; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .item-list tr:last-child td { border-bottom: none; }
        
        @media (max-width: 900px) {
            .order-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <a href="orders.php" class="btn-icon" style="background: transparent; color: #64748b; font-size: 1.25rem; box-shadow: none; padding: 0;"><i class="fas fa-arrow-left"></i></a>
                <h1 style="margin: 0;">Order #ORD-<?= htmlspecialchars($order['id'] ?? '') ?></h1>
                <?php if (isset($order)): ?>
                    <?php
                        $statusBg = '#f59e0b';
                        if ($order['status'] == 'Processing') $statusBg = '#3b82f6';
                        if ($order['status'] == 'Completed') $statusBg = '#10b981';
                        if ($order['status'] == 'Cancelled') $statusBg = '#ef4444';
                    ?>
                    <span class="badge" style="background-color: <?= $statusBg ?>; color: white; border: none; font-size: 0.9rem; margin-left: auto;">
                        <?= htmlspecialchars($order['status']) ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success" style="background: #ecfdf5; color: #047857; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" style="background: #fef2f2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($order) && $order): ?>
        <div class="order-grid">
            <!-- Left Column: Items and Customer Info -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="info-card">
                    <h3>Order Items</h3>
                    <table class="item-list">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($items) && count($items) > 0): ?>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 1rem;">
                                                <?php if (!empty($item['product_image'])): ?>
                                                    <img src="<?= htmlspecialchars($item['product_image']) ?>" alt="<?= htmlspecialchars($item['product_name'] ?? 'Unknown') ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                <?php else: ?>
                                                    <div style="width: 40px; height: 40px; background-color: #e2e8f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #94a3b8;"><i class="fas fa-image"></i></div>
                                                <?php endif; ?>
                                                <strong style="color: #334155;"><?= htmlspecialchars($item['product_name'] ?? 'Unknown Product (ID: '.$item['product_id'].')') ?></strong>
                                            </div>
                                        </td>
                                        <td>$<?= number_format($item['price'], 2) ?></td>
                                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                                        <td style="text-align: right; font-weight: 600;">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #94a3b8; padding: 2rem;">No items found for this order.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="info-card">
                    <h3>Customer Details</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <span class="detail-label">Name</span>
                            <div style="color: #0f172a; font-weight: 500; margin-top: 0.25rem;"><i class="fas fa-user" style="color:#cbd5e1; margin-right:6px;"></i> <?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></div>
                        </div>
                        <div>
                            <span class="detail-label">Email</span>
                            <div style="color: #0f172a; font-weight: 500; margin-top: 0.25rem;"><i class="fas fa-envelope" style="color:#cbd5e1; margin-right:6px;"></i> <?= htmlspecialchars($order['customer_email'] ?? 'Not provided') ?></div>
                        </div>
                        <div>
                            <span class="detail-label">Phone</span>
                            <div style="color: #0f172a; font-weight: 500; margin-top: 0.25rem;"><i class="fas fa-phone" style="color:#cbd5e1; margin-right:6px;"></i> <?= htmlspecialchars($order['customer_phone'] ?? 'Not provided') ?></div>
                        </div>
                        <div style="grid-column: 1 / -1;">
                            <span class="detail-label">Shipping Address</span>
                            <div style="color: #0f172a; font-weight: 500; margin-top: 0.25rem; line-height: 1.5; background: #f8fafc; padding: 1rem; border-radius: 6px; border: 1px solid #e2e8f0;"><i class="fas fa-map-marker-alt" style="color:#cbd5e1; margin-right:6px;"></i> <?= nl2br(htmlspecialchars($order['customer_address'] ?? 'Not provided')) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Summary and Actions -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="info-card">
                    <h3>Order Summary</h3>
                    
                    <div class="detail-row">
                        <span class="detail-label">Order Date</span>
                        <span class="detail-value"><?= date('M d, Y h:i A', strtotime($order['created_at'])) ?></span>
                    </div>
                    
                    <div class="detail-row" style="margin-top: 1rem; border-top: 2px solid #e2e8f0; border-bottom: none; padding-top: 1rem;">
                        <span class="detail-label" style="font-size: 1.1rem; color: #0f172a;">Grand Total</span>
                        <span class="detail-value" style="font-size: 1.25rem; color: var(--primary);">$<?= number_format($order['total_amount'], 2) ?></span>
                    </div>
                </div>

                <div class="info-card">
                    <h3>Update Status</h3>
                    <form method="POST" action="">
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label for="status" style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #475569;">Order Status</label>
                            <select name="status" id="status" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; background: white; font-size: 0.95rem;">
                                <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Processing" <?= $order['status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="Completed" <?= $order['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" name="update_status" class="btn" style="width: 100%; justify-content: center;"><i class="fas fa-save"></i> Save Status</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

</body>
</html>
