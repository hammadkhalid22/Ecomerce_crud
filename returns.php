<?php
require_once 'db.php';
requireLogin();

try {
    // Fetch returns with order and customer details
    $stmt = $pdo->query("
        SELECT r.*, o.total_amount, c.name as customer_name, c.email as customer_email
        FROM returns r
        JOIN orders o ON r.order_id = o.id
        LEFT JOIN customers c ON o.customer_id = c.id
        ORDER BY r.created_at DESC
    ");
    $returns = $stmt->fetchAll();
} catch (PDOException $e) {
    if ($e->getCode() == '42S02') { // Table not found
        $error = "Returns table doesn't exist yet! Ensure the DB schema is updated.";
    } else {
        $error = "Error fetching returns: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Returns Management</h1>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="background: #fef2f2; color: #b91c1c; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #ef4444;">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Return ID</th>
                        <th>Order Ref</th>
                        <th>Customer</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($returns) && count($returns) > 0): ?>
                        <?php foreach ($returns as $ret): ?>
                            <tr>
                                <td><span style="color: #64748b; font-weight: 500;">#RET-<?= htmlspecialchars($ret['id']) ?></span></td>
                                <td><a href="view_order.php?id=<?= $ret['order_id'] ?>" style="color: var(--primary); text-decoration: none; font-weight: 500;">#ORD-<?= htmlspecialchars($ret['order_id']) ?></a></td>
                                <td>
                                    <div style="display: flex; flex-direction: column;">
                                        <strong><?= htmlspecialchars($ret['customer_name'] ?? 'Guest') ?></strong>
                                    </div>
                                </td>
                                <td><span style="color: #475569; font-size: 0.9rem;"><?= htmlspecialchars(substr($ret['reason'], 0, 50)) ?><?= strlen($ret['reason']) > 50 ? '...' : '' ?></span></td>
                                <td>
                                    <?php
                                        $statusBg = '#f59e0b';
                                        if ($ret['status'] == 'Approved') $statusBg = '#10b981';
                                        if ($ret['status'] == 'Rejected') $statusBg = '#ef4444';
                                    ?>
                                    <span class="badge" style="background-color: <?= $statusBg ?>; color: white; border: none;">
                                        <?= htmlspecialchars($ret['status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($ret['created_at'])) ?></td>
                                <td class="actions">
                                    <a href="#" class="btn-icon" style="background-color: #f1f5f9; color: #334155;"><i class="fas fa-eye"></i> View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 4rem; color: #64748b;">
                                <i class="fas fa-undo fa-4x" style="color: #cbd5e1; margin-bottom: 1.5rem; display:block;"></i>
                                <h3 style="color: #334155; margin-bottom: 0.5rem;">No returns pending</h3>
                                <p>Return requests initiated by customers will appear here.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
