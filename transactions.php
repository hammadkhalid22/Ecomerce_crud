<?php
require_once 'db.php';
requireLogin();

try {
    $stmt = $pdo->query("
        SELECT t.*, c.name as customer_name
        FROM transactions t
        JOIN orders o ON t.order_id = o.id
        LEFT JOIN customers c ON o.customer_id = c.id
        ORDER BY t.transaction_date DESC
    ");
    $transactions = $stmt->fetchAll();
} catch (PDOException $e) {
    if ($e->getCode() == '42S02') { // Table not found
        $error = "Transactions table doesn't exist yet! Ensure the DB schema is updated.";
    } else {
        $error = "Error fetching transactions: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Transactions Record</h1>
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
                        <th>Transaction ID</th>
                        <th>Order Ref</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($transactions) && count($transactions) > 0): ?>
                        <?php foreach ($transactions as $txn): ?>
                            <tr>
                                <td><span style="color: #64748b; font-weight: 500;">#TXN-<?= htmlspecialchars($txn['id']) ?></span></td>
                                <td><a href="view_order.php?id=<?= $txn['order_id'] ?>" style="color: var(--primary); text-decoration: none; font-weight: 500;">#ORD-<?= htmlspecialchars($txn['order_id']) ?></a></td>
                                <td>
                                    <strong><?= htmlspecialchars($txn['customer_name'] ?? 'Guest') ?></strong>
                                </td>
                                <td><strong style="color: #0f172a;">$<?= number_format($txn['amount'], 2) ?></strong></td>
                                <td>
                                    <span style="color: #475569;"><i class="fas fa-credit-card" style="color: #cbd5e1; margin-right:4px;"></i> <?= htmlspecialchars($txn['payment_method']) ?></span>
                                </td>
                                <td>
                                    <?php
                                        $statusBg = '#f59e0b'; // Pending
                                        if ($txn['status'] == 'Success') $statusBg = '#10b981';
                                        if ($txn['status'] == 'Failed') $statusBg = '#ef4444';
                                    ?>
                                    <span class="badge" style="background-color: <?= $statusBg ?>; color: white; border: none;">
                                        <?= htmlspecialchars($txn['status']) ?>
                                    </span>
                                </td>
                                <td style="color: #64748b;"><?= date('M d, Y h:i A', strtotime($txn['transaction_date'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 4rem; color: #64748b;">
                                <i class="fas fa-file-invoice-dollar fa-4x" style="color: #cbd5e1; margin-bottom: 1.5rem; display:block;"></i>
                                <h3 style="color: #334155; margin-bottom: 0.5rem;">No transactions yet</h3>
                                <p>Customer payments and refunds will appear here.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
