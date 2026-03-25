<?php
require_once 'db.php';
requireLogin();

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $id = trim($_GET['id']);
    
    try {
        // Fetch image path to delete file
        $stmtImg = $pdo->prepare("SELECT image FROM products WHERE id = :id");
        $stmtImg->bindParam(":id", $id);
        $stmtImg->execute();
        $product = $stmtImg->fetch();
        
        if ($product && !empty($product['image']) && file_exists($product['image'])) {
            unlink($product['image']);
        }

        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            if ($stmt->rowCount() > 0) {
                header("Location: index.php?msg=" . urlencode("Product securely deleted."));
            } else {
                header("Location: index.php?msg=" . urlencode("Could not delete. Product might not exist."));
            }
        } else {
            header("Location: index.php?msg=" . urlencode("Error processing database request."));
        }
    } catch (PDOException $e) {
        header("Location: index.php?msg=" . urlencode("Error: " . $e->getMessage()));
    }
} else {
    header("Location: index.php");
}
exit();
?>
