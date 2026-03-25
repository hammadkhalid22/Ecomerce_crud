<?php
require_once 'db.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vouchers - Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Vouchers</h1>
        </div>
        
        <div class="card">
            <div style="padding: 3rem; text-align: center; color: #64748b;">
                <i class="fas fa-ticket-alt fa-3x" style="color: #cbd5e1; margin-bottom: 1rem; display:block;"></i>
                <h2>Coming Soon</h2>
                <p>The Vouchers module is currently under development.</p>
            </div>
        </div>
    </div>

</body>
</html>
