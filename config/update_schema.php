<?php
// config/update_schema.php
require_once 'db.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    echo "Updating schema...\n";

    // 1. Add phone and address to users if not exists
    // (A bit hacky in pure SQL without stored procs to check existence, 
    // but we can catch duplicate column errors or just run pure ALTER and ignore errors)
    try {
        $db->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL");
        echo "Added phone column.\n";
    } catch (PDOException $e) { /* Ignore if exists */
    }

    try {
        $db->exec("ALTER TABLE users ADD COLUMN address TEXT DEFAULT NULL");
        echo "Added address column.\n";
    } catch (PDOException $e) { /* Ignore if exists */
    }

    // 2. Create Wishlist Table
    $sql_wishlist = "CREATE TABLE IF NOT EXISTS wishlist (
        wishlist_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
        UNIQUE(user_id, product_id)
    )";
    $db->exec($sql_wishlist);
    echo "Created wishlist table.\n";

    echo "Schema updated successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>