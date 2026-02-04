<?php
// api/orders/read.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT o.order_id, o.total_amount, o.status, o.created_at, u.full_name 
              FROM orders o
              JOIN users u ON o.user_id = u.user_id
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
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>