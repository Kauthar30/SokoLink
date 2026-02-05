<?php
// config/update_schema_wishlist.php - Add wishlist table
require_once 'db.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Create wishlist table
    $sql = "CREATE TABLE IF NOT EXISTS wishlist (
        wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
        UNIQUE KEY unique_wishlist (user_id, product_id)
    )";

    $db->exec($sql);
    echo "Wishlist table created successfully or already exists.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>