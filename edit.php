<?php
require_once 'db.php';
requireLogin();

$error = '';
$product = null;

// Fetch categories and companies for dropdowns
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$companies = $pdo->query("SELECT * FROM companies ORDER BY name ASC")->fetchAll();

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $id = trim($_GET['id']);
    
    // Fetch product details
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    
    if ($stmt->rowCount() == 1) {
        $product = $stmt->fetch();
    } else {
        header("Location: index.php?msg=" . urlencode("Product not found."));
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = trim($_POST["price"]);
    $category_id = !empty($_POST["category_id"]) ? $_POST["category_id"] : null;
    $company_id = !empty($_POST["company_id"]) ? $_POST["company_id"] : null;

    // Handle image upload if a new image is provided
    $imagePath = $product['image']; // Default to existing image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFilePath = $uploadDir . $fileName;
        
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
        
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                // Delete old image if it exists and we're replacing it
                if (!empty($product['image']) && file_exists($product['image'])) {
                    unlink($product['image']);
                }
                $imagePath = $targetFilePath;
            } else {
                $error = "Error uploading image.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, WEBP & GIF files are allowed.";
        }
    }

    if (empty($name) || empty($price)) {
        $error = "Product name and price are required.";
    } elseif (!is_numeric($price)) {
        $error = "Price must be a valid number.";
    } elseif (empty($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = :name, description = :description, price = :price, image = :image, category_id = :category_id, company_id = :company_id WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':image', $imagePath);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':company_id', $company_id);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                header("Location: index.php?msg=" . urlencode("Product updated successfully."));
                exit();
            } else {
                $error = "Something went wrong. Please try again.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
    
    // Refresh product details for view after post
    $product['name'] = $name;
    $product['description'] = $description;
    $product['price'] = $price;
    $product['image'] = $imagePath;
    $product['category_id'] = $category_id;
    $product['company_id'] = $company_id;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header">
            <h1>Edit Product</h1>
            <a href="index.php" class="btn" style="background-color: var(--text-muted);"><i class="fas fa-arrow-left"></i>&nbsp; Back</a>
        </div>

        <div class="card form-container">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($product['name'] ?? '') ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group flex-1">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-control">
                            <option value="">-- Select Category --</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group flex-1">
                        <label class="form-label">Brand / Company</label>
                        <select name="company_id" class="form-control">
                            <option value="">-- Select Company --</option>
                            <?php foreach($companies as $comp): ?>
                                <option value="<?= $comp['id'] ?>" <?= (isset($product['company_id']) && $product['company_id'] == $comp['id']) ? 'selected' : '' ?>><?= htmlspecialchars($comp['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group flex-1">
                        <label class="form-label">Price ($) *</label>
                        <input type="number" step="0.01" name="price" class="form-control" required value="<?= htmlspecialchars($product['price'] ?? '') ?>">
                    </div>
                    <div class="form-group flex-1">
                        <label class="form-label">Product Image</label>
                        <?php if (!empty($product['image'])): ?>
                            <div style="margin-bottom: 10px;">
                                <img src="<?= htmlspecialchars($product['image']) ?>" alt="Current Image" style="max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid #e2e8f0;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small style="color: #64748b; margin-top: 4px; display: block;">Leave blank to keep current</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>
                
                <button type="submit" class="btn"><i class="fas fa-save"></i>&nbsp; Update Product</button>
            </form>
        </div>
    </div>
</body>
</html>
