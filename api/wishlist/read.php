<?php
// api/wishlist/read.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT w.wishlist_id, w.product_id, w.created_at, p.name, p.price, p.image_url, c.name as category_name
              FROM wishlist w
              JOIN products p ON w.product_id = p.product_id
              LEFT JOIN categories c ON p.category_id = c.category_id
              WHERE w.user_id = :user_id
              ORDER BY w.created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->execute([':user_id' => $user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "items" => $items
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>