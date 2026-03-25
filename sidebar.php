<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="logo">
        <i class="fas fa-shopping-bag" style="color: var(--primary);"></i> <span style="color: white; font-weight: 800; letter-spacing: 1px;">StoreAdmin</span>
    </div>
    
    <div class="sidebar-heading">CORE</div>
    <a href="index.php" class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>"><i class="fas fa-chart-pie"></i>&nbsp; Dashboard / Products</a>
    
    <div class="sidebar-heading">CATALOG</div>
    <a href="add.php" class="nav-link <?= ($current_page == 'add.php') ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i>&nbsp; Add Product</a>
    <a href="categories.php" class="nav-link <?= ($current_page == 'categories.php' || $current_page == 'add_category.php') ? 'active' : '' ?>"><i class="fas fa-tags"></i>&nbsp; Categories</a>
    <a href="companies.php" class="nav-link <?= ($current_page == 'companies.php' || $current_page == 'add_company.php') ? 'active' : '' ?>"><i class="fas fa-building"></i>&nbsp; Brands / Companies</a>
    <a href="reviews.php" class="nav-link <?= ($current_page == 'reviews.php') ? 'active' : '' ?>"><i class="fas fa-star"></i>&nbsp; Product Reviews</a>

    <div class="sidebar-heading">SALES</div>
    <a href="orders.php" class="nav-link <?= ($current_page == 'orders.php') ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i>&nbsp; Orders <span class="badge" style="background: var(--danger); color: white; margin-left: auto; font-size: 0.65rem; padding: 0.15rem 0.4rem;">14 New</span></a>
    <a href="returns.php" class="nav-link <?= ($current_page == 'returns.php') ? 'active' : '' ?>"><i class="fas fa-undo"></i>&nbsp; Returns</a>
    <a href="transactions.php" class="nav-link <?= ($current_page == 'transactions.php') ? 'active' : '' ?>"><i class="fas fa-file-invoice-dollar"></i>&nbsp; Transactions</a>

    <div class="sidebar-heading">CUSTOMERS</div>
    <a href="customers.php" class="nav-link <?= ($current_page == 'customers.php') ? 'active' : '' ?>"><i class="fas fa-users"></i>&nbsp; Customers List</a>

    <div class="sidebar-heading">PROMOTIONS</div>
    <a href="flash_sales.php" class="nav-link <?= ($current_page == 'flash_sales.php') ? 'active' : '' ?>"><i class="fas fa-bullhorn"></i>&nbsp; Flash Sales</a>
    <a href="vouchers.php" class="nav-link <?= ($current_page == 'vouchers.php') ? 'active' : '' ?>"><i class="fas fa-ticket-alt"></i>&nbsp; Vouchers</a>

    <div class="sidebar-heading">SYSTEM</div>
    <a href="settings.php" class="nav-link <?= ($current_page == 'settings.php') ? 'active' : '' ?>"><i class="fas fa-cog"></i>&nbsp; Settings</a>
    <a href="admin_roles.php" class="nav-link <?= ($current_page == 'admin_roles.php') ? 'active' : '' ?>"><i class="fas fa-user-shield"></i>&nbsp; Admin Roles</a>

    <div class="sidebar-heading">ACCOUNT</div>
    <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i>&nbsp; Logout</a>
</div>
