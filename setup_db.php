<?php
// setup_db.php
// This script initializes the database tables and creates the default admin user.

$host = "localhost";
$db_name = "sokolink_db";
$username = "root";
$password = "";

try {
    // 1. Connect to MySQL Server (DB might not exist yet)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Create Database
    echo "Creating database if not exists...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $db_name");
    $pdo->exec("USE $db_name");

    // 3. Create Tables

    // Users Table
    echo "Creating users table...\n";
    $sql_users = "CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('customer', 'admin') DEFAULT 'customer',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_users);

    // Categories Table
    echo "Creating categories table...\n";
    $sql_categories = "CREATE TABLE IF NOT EXISTS categories (
        category_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql_categories);

    // Products Table
    echo "Creating products table...\n";
    $sql_products = "CREATE TABLE IF NOT EXISTS products (
        product_id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        name VARCHAR(150) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        stock INT DEFAULT 0,
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
    )";
    $pdo->exec($sql_products);

    // Orders Table
    echo "Creating orders table...\n";
    $sql_orders = "CREATE TABLE IF NOT EXISTS orders (
        order_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )";
    $pdo->exec($sql_orders);

    // Order Items Table
    echo "Creating order_items table...\n";
    $sql_order_items = "CREATE TABLE IF NOT EXISTS order_items (
        item_id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price_each DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(product_id)
    )";
    $pdo->exec($sql_order_items);

    // Admin Logs Table
    echo "Creating admin_logs table...\n";
    $sql_admin_logs = "CREATE TABLE IF NOT EXISTS admin_logs (
        log_id INT AUTO_INCREMENT PRIMARY KEY,
        admin_id INT NOT NULL,
        action VARCHAR(255) NOT NULL,
        log_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (admin_id) REFERENCES users(user_id)
    )";
    $pdo->exec($sql_admin_logs);

    // 4. Create Default Admin User
    echo "Checking for admin user...\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute(['admin@sokolink.com']);

    if ($stmt->fetchColumn() == 0) {
        echo "Creating default admin user...\n";
        $password = 'admin123';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $insert = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $insert->execute(['System Admin', 'admin@sokolink.com', $hashed_password, 'admin']);
        echo "Default admin user created. Email: admin@sokolink.com, Password: admin123\n";
    } else {
        echo "Admin user already exists.\n";
    }

    echo "Database setup completed successfully!\n";

} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
?>