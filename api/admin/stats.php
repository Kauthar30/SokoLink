<?php
// api/admin/stats.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

// Check if admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Total Orders
    $stmt = $db->query("SELECT COUNT(*) as count FROM orders");
    $orders_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Total Products
    $stmt = $db->query("SELECT COUNT(*) as count FROM products");
    $products_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Total Users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $users_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Total Revenue
    $stmt = $db->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    echo json_encode([
        "success" => true,
        "data" => [
            "orders" => $orders_count,
            "products" => $products_count,
            "users" => $users_count,
            "revenue" => $revenue
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>