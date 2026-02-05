<?php
// api/orders/details.php - Get order details with items
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit;
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

if (!$order_id) {
    echo json_encode(["success" => false, "message" => "Order ID required."]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get order details
    $order_query = $db->prepare("SELECT * FROM orders WHERE order_id = :order_id AND user_id = :user_id");
    $order_query->execute([':order_id' => $order_id, ':user_id' => $user_id]);
    $order = $order_query->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(["success" => false, "message" => "Order not found."]);
        exit;
    }

    // Get order items
    $items_query = $db->prepare("SELECT oi.*, p.name, p.image_url 
                                  FROM order_items oi 
                                  JOIN products p ON oi.product_id = p.product_id 
                                  WHERE oi.order_id = :order_id");
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