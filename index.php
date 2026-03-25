<?php
require_once 'db.php';
requireLogin();

// Fetch all products with their categories and companies
try {
    $stmt = $pdo->query("
        SELECT p.*, c.name as category_name, co.name as company_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN companies co ON p.company_id = co.id
        ORDER BY p.id DESC
    ");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}

$success_msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Products Overview</h1>
            <a href="add.php" class="btn"><i class="fas fa-plus"></i>&nbsp; Add New Product</a>
        </div>
        
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Product Details</th>
                        <th>Classification</th>
                        <th>Price</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($products) && count($products) > 0): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><span style="color: #64748b; font-weight: 500;">#<?= htmlspecialchars($product['id']) ?></span></td>
                                <td>
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background-color: #e2e8f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #94a3b8;"><i class="fas fa-image"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column;">
                                        <strong><?= htmlspecialchars($product['name']) ?></strong>
                                        <small style="color: #64748b; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; margin-top: 4px;">
                                            <?= htmlspecialchars($product['description']) ?>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start;">
                                        <?php if (!empty($product['category_name'])): ?>
                                            <span class="badge badge-category"><i class="fas fa-tag"></i> <?= htmlspecialchars($product['category_name']) ?></span>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-size: 0.75rem;">No Category</span>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($product['company_name'])): ?>
                                            <span class="badge badge-company"><i class="fas fa-building"></i> <?= htmlspecialchars($product['company_name']) ?></span>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-size: 0.75rem;">No Brand</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><strong style="color: #0f172a;">$<?= number_format($product['price'], 2) ?></strong></td>
                                <td><?= date('M d, Y', strtotime($product['created_at'])) ?></td>
                                <td class="actions">
                                    <a href="edit.php?id=<?= $product['id'] ?>" class="btn-icon btn-edit"><i class="fas fa-pen"></i> Edit</a>
                                    <a href="delete.php?id=<?= $product['id'] ?>" class="btn-icon btn-del" onclick="return confirm('Are you certain you wish to delete this product?');"><i class="fas fa-trash-alt"></i> Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #64748b;">
                                <i class="fas fa-inbox fa-3x" style="color: #cbd5e1; margin-bottom: 1rem; display:block;"></i>
                                No products found. Click "Add New Product" to get started!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
