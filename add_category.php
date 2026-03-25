<?php
require_once 'db.php';
requireLogin();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);

    if (empty($name)) {
        $error = "Category name is required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
            $stmt->bindParam(':name', $name);
            
            if ($stmt->execute()) {
                header("Location: categories.php?msg=" . urlencode("Category added successfully."));
                exit();
            } else {
                $error = "Failed to add category.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>Add New Category</h1>
            <a href="categories.php" class="btn" style="background-color: var(--text-muted);"><i class="fas fa-arrow-left"></i>&nbsp; Back</a>
        </div>

        <div class="card form-container">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Electronics, Clothing">
                </div>
                <button type="submit" class="btn"><i class="fas fa-save"></i>&nbsp; Save Category</button>
            </form>
        </div>
    </div>
</body>
</html>
