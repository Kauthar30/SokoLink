<?php
// api/orders/admin_details.php - Get order details for admin (includes customer info)
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized."]);
    exit;
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    echo json_encode(["success" => false, "message" => "Order ID required."]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get order with customer details
    $order_query = $db->prepare("
        SELECT o.*, u.full_name, u.email, u.phone, u.address
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        WHERE o.order_id = :order_id
    ");
    $order_query->execute([':order_id' => $order_id]);
    $order = $order_query->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(["success" => false, "message" => "Order not found."]);
        exit;
    }

    // Get order items
    $items_query = $db->prepare("
        SELECT oi.*, p.name, p.image_url 
        FROM order_items oi 
        JOIN products p ON oi.product_id = p.product_id 
        WHERE oi.order_id = :order_id
    ");
    $items_query->execute([':order_id' => $order_id]);
    $items = $items_query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "order" => $order,
        "items" => $items
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>