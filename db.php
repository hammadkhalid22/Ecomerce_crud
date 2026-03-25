<?php
// Start the session globally to manage authentication across pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = '127.0.0.1';
$dbname = 'admin_panel_db';
$user = 'root'; // Adjust to match your standard MySQL root user if needed
$pass = '';     // Adjust to match your standard MySQL root password if needed

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ensure database exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo->exec("USE `$dbname`");
    
    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create the categories table
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE
    )");

    // Create the companies table
    $pdo->exec("CREATE TABLE IF NOT EXISTS companies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE
    )");

    // Create the products table if it doesn't already exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Safely add new columns to products (user_id, image, category_id, company_id) if they don't exist yet
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN user_id INT AFTER id");
        $pdo->exec("ALTER TABLE products ADD CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL");
    } catch(PDOException $e) { /* Column or constraint might already exist */ }
    
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN category_id INT AFTER user_id");
        $pdo->exec("ALTER TABLE products ADD CONSTRAINT fk_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL");
    } catch(PDOException $e) { /* Column or constraint might already exist */ }

    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN company_id INT AFTER category_id");
        $pdo->exec("ALTER TABLE products ADD CONSTRAINT fk_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL");
    } catch(PDOException $e) { /* Column or constraint might already exist */ }

    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN image VARCHAR(255) DEFAULT NULL AFTER price");
    } catch(PDOException $e) { /* Column might already exist */ }
    
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div style='color:red; font-weight:bold;'>Database connection failed. Please ensure MySQL is running. Error: " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Helper function to enforce authentication
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}
?>
