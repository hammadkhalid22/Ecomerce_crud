<?php
require_once 'db.php';
requireLogin();

// Handle new flash sale submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_sale'])) {
    $name = $_POST['name'];
    $discount = $_POST['discount'];
    $start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO flash_sales (name, discount_percent, start_time, end_time, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $discount, $start, $end, $status]);
        $success_msg = "Flash sale created successfully!";
    } catch (PDOException $e) {
        $error = "Error adding flash sale: " . $e->getMessage();
    }
}

// Fetch sales
try {
    $stmt = $pdo->query("SELECT * FROM flash_sales ORDER BY start_time DESC");
    $sales = $stmt->fetchAll();
} catch (PDOException $e) {
    if ($e->getCode() == '42S02') {
        $error = "Flash sales table doesn't exist yet! Ensure the DB schema is updated.";
    } else {
        $error = "Error fetching sales: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flash Sales - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .grid-layout { display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start; }
        @media (max-width: 900px) { .grid-layout { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Flash Sales Manager</h1>
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

        <div class="grid-layout">
            <!-- Add New Form -->
            <div class="card">
                <h3 style="margin-top:0; border-bottom: 1px solid #e2e8f0; padding-bottom:1rem; margin-bottom:1rem;"><i class="fas fa-plus-circle" style="color:var(--primary);"></i> Create Flash Sale</h3>
                <form method="POST" action="">
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Campaign Name</label>
                        <input type="text" name="name" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px;" placeholder="e.g. Summer Blowout">
                    </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Discount Percentage (%)</label>
                        <input type="number" name="discount" min="1" max="99" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px;" placeholder="e.g. 20">
                    </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Start Time</label>
                        <input type="datetime-local" name="start_time" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">End Time</label>
                        <input type="datetime-local" name="end_time" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Status</label>
                        <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px;">
                            <option value="Active">Active (Publish immediately)</option>
                            <option value="Inactive">Inactive (Draft)</option>
                        </select>
                    </div>
                    <button type="submit" name="add_sale" class="btn" style="width: 100%; justify-content: center;"><i class="fas fa-bullhorn"></i> Launch Campaign</button>
                </form>
            </div>

            <!-- List View -->
            <div class="card" style="overflow-x: auto;">
                <h3 style="margin-top:0; border-bottom: 1px solid #e2e8f0; padding-bottom:1rem; margin-bottom:1rem;">Active & Past Sales</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; text-align: left; color: #64748b;">
                            <th style="padding: 0.75rem;">Name</th>
                            <th style="padding: 0.75rem;">Discount</th>
                            <th style="padding: 0.75rem;">Duration</th>
                            <th style="padding: 0.75rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($sales) && count($sales) > 0): ?>
                            <?php foreach ($sales as $sale): ?>
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 1rem 0.75rem;"><strong><?= htmlspecialchars($sale['name']) ?></strong></td>
                                    <td style="padding: 1rem 0.75rem;"><span class="badge" style="background:#fef2f2; color:#ef4444; border:none;">-<?= htmlspecialchars($sale['discount_percent']) ?>%</span></td>
                                    <td style="padding: 1rem 0.75rem;">
                                        <div style="font-size: 0.85rem; color: #475569;">
                                            <div><strong>Starts:</strong> <?= date('M d, H:i', strtotime($sale['start_time'])) ?></div>
                                            <div><strong>Ends:</strong> <?= date('M d, H:i', strtotime($sale['end_time'])) ?></div>
                                        </div>
                                    </td>
                                    <td style="padding: 1rem 0.75rem;">
                                        <?php if ($sale['status'] == 'Active'): ?>
                                            <span class="badge" style="background:#10b981; color:white; border:none;">Active</span>
                                        <?php else: ?>
                                            <span class="badge" style="background:#94a3b8; color:white; border:none;">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem; color: #94a3b8;">No flash sales scheduled yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
