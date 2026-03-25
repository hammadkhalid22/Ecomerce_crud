<?php
require_once 'db.php';
requireLogin();

// Delete company if requested
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM companies WHERE id = :id");
        $stmt->bindParam(':id', $_GET['delete']);
        $stmt->execute();
        $success_msg = "Company deleted successfully.";
    } catch (PDOException $e) {
        $error = "Error deleting company: " . $e->getMessage();
    }
}

// Fetch all companies
try {
    $stmt = $pdo->query("SELECT * FROM companies ORDER BY name ASC");
    $companies = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}

$success_msg = isset($_GET['msg']) ? $_GET['msg'] : (isset($success_msg) ? $success_msg : '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Companies - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>Manage Companies / Brands</h1>
            <a href="add_company.php" class="btn"><i class="fas fa-plus"></i>&nbsp; Add Company</a>
        </div>
        
        <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Company Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($companies) && count($companies) > 0): ?>
                        <?php foreach ($companies as $comp): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($comp['id']) ?></td>
                                <td><strong><?= htmlspecialchars($comp['name']) ?></strong></td>
                                <td class="actions">
                                    <a href="companies.php?delete=<?= $comp['id'] ?>" class="btn-icon btn-del" onclick="return confirm('Delete this company?');"><i class="fas fa-trash-alt"></i> Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 2rem;">No companies found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
