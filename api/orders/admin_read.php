<?php
// api/orders/admin_read.php - Get all orders for admin panel
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized."]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT o.order_id, o.total_amount, o.status, o.created_at,
              u.full_name, u.email, u.phone, u.address,
              COUNT(oi.item_id) as item_count
              FROM orders o
              JOIN users u ON o.user_id = u.user_id
              LEFT JOIN order_items oi ON o.order_id = oi.order_id
              GROUP BY o.order_id
              ORDER BY o.created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "count" => count($orders),
        "data" => $orders
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>