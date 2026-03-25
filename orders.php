<?php
require_once 'db.php';
requireLogin();

// Fetch orders from the database
try {
    $stmt = $pdo->query("
        SELECT o.*, c.name as customer_name, c.email as customer_email 
        FROM orders o 
        LEFT JOIN customers c ON o.customer_id = c.id 
        ORDER BY o.created_at DESC
    ");
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    if ($e->getCode() == '42S02') { // Table not found
        $error = "Orders table doesn't exist yet! Ensure the DB schema is updated.";
    } else {
        $error = "Error fetching orders: " . $e->getMessage();
    }
}

// Success messages (e.g., from view_order.php status updates)
$success_msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Orders Management</h1>
        </div>
        
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success" style="background: #ecfdf5; color: #047857; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #10b981;">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="background: #fef2f2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($orders) && count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><span style="color: #64748b; font-weight: 500;">#ORD-<?= htmlspecialchars($order['id']) ?></span></td>
                                <td>
                                    <div style="display: flex; flex-direction: column;">
                                        <strong><?= htmlspecialchars($order['customer_name'] ?? 'Guest (No Name)') ?></strong>
                                        <small style="color: #64748b; font-size: 0.8rem; margin-top: 4px;"><?= htmlspecialchars($order['customer_email'] ?? 'No Email') ?></small>
                                    </div>
                                </td>
                                <td><strong style="color: #0f172a;">$<?= number_format($order['total_amount'], 2) ?></strong></td>
                                <td>
                                    <?php
                                        $statusBg = '#f59e0b'; // Default Pending
                                        if ($order['status'] == 'Processing') $statusBg = '#3b82f6';
                                        if ($order['status'] == 'Completed') $statusBg = '#10b981';
                                        if ($order['status'] == 'Cancelled') $statusBg = '#ef4444';
                                    ?>
                                    <span class="badge" style="background-color: <?= $statusBg ?>; color: white; border: none;">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <span><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
                                        <small style="color: #64748b;"><?= date('h:i A', strtotime($order['created_at'])) ?></small>
                                    </div>
                                </td>
                                <td class="actions">
                                    <a href="view_order.php?id=<?= $order['id'] ?>" class="btn-icon" style="background-color: var(--primary); color: white; display: inline-flex; align-items: center; gap: 4px; padding: 0.5rem 0.75rem; border-radius: 4px; text-decoration: none; font-size: 0.8rem; font-weight: 500;">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 4rem; color: #64748b;">
                                <i class="fas fa-box-open fa-4x" style="color: #cbd5e1; margin-bottom: 1.5rem; display:block;"></i>
                                <h3 style="color: #334155; margin-bottom: 0.5rem;">No orders yet</h3>
                                <p>Once customers place orders, they will appear here.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
