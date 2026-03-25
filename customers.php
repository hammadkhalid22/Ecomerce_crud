<?php
require_once 'db.php';
requireLogin();

try {
    // Get all customers and count their orders
    $stmt = $pdo->query("
        SELECT c.*, 
               COUNT(o.id) as total_orders,
               SUM(o.total_amount) as total_spent
        FROM customers c
        LEFT JOIN orders o ON c.id = o.customer_id
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ");
    $customers = $stmt->fetchAll();
} catch (PDOException $e) {
    if ($e->getCode() == '42S02') { // Table not found
        $error = "Customers table doesn't exist yet! Ensure the DB schema is updated.";
    } else {
        $error = "Error fetching customers: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Customers List</h1>
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
                        <th>Customer</th>
                        <th>Contact Details</th>
                        <th>Total Orders</th>
                        <th>Total Spent</th>
                        <th>Registered On</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($customers) && count($customers) > 0): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 1rem;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #64748b; font-weight: bold;">
                                            <?= strtoupper(substr($customer['name'], 0, 1)) ?>
                                        </div>
                                        <strong style="color: #0f172a; font-size: 1.05rem;"><?= htmlspecialchars($customer['name']) ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <span style="color: #475569;"><i class="fas fa-envelope" style="color: #cbd5e1; width: 16px;"></i> <?= htmlspecialchars($customer['email'] ?: 'No email') ?></span>
                                        <span style="color: #475569;"><i class="fas fa-phone" style="color: #cbd5e1; width: 16px;"></i> <?= htmlspecialchars($customer['phone'] ?: 'No phone') ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge" style="background: #f1f5f9; color: #334155; border: 1px solid #e2e8f0;">
                                        <?= htmlspecialchars($customer['total_orders']) ?> orders
                                    </span>
                                </td>
                                <td><strong style="color: var(--primary);">$<?= number_format($customer['total_spent'] ?: 0, 2) ?></strong></td>
                                <td style="color: #64748b;"><?= date('M d, Y', strtotime($customer['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 4rem; color: #64748b;">
                                <i class="fas fa-users fa-4x" style="color: #cbd5e1; margin-bottom: 1.5rem; display:block;"></i>
                                <h3 style="color: #334155; margin-bottom: 0.5rem;">No customers found</h3>
                                <p>Customers will appear here once they register or place orders.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
