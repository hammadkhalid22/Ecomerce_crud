<?php
require_once 'db.php';
requireLogin();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);

    if (empty($name)) {
        $error = "Company name is required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO companies (name) VALUES (:name)");
            $stmt->bindParam(':name', $name);
            
            if ($stmt->execute()) {
                header("Location: companies.php?msg=" . urlencode("Company added successfully."));
                exit();
            } else {
                $error = "Failed to add company.";
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
    <title>Add Company - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>Add New Company / Brand</h1>
            <a href="companies.php" class="btn" style="background-color: var(--text-muted);"><i class="fas fa-arrow-left"></i>&nbsp; Back</a>
        </div>

        <div class="card form-container">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label class="form-label">Company Name *</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Apple, Samsung">
                </div>
                <button type="submit" class="btn"><i class="fas fa-save"></i>&nbsp; Save Company</button>
            </form>
        </div>
    </div>
</body>
</html>
