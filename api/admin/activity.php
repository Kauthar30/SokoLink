<?php
// api/admin/activity.php
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

    // Union query to get recent activities
    $query = "
        (SELECT 'user' as type, CONCAT('New user registered: ', full_name) as message, created_at FROM users)
        UNION ALL
        (SELECT 'order' as type, CONCAT('New order placed #', order_id) as message, created_at FROM orders)
        UNION ALL
        (SELECT 'product' as type, CONCAT('Product added: ', name) as message, created_at FROM products)
        ORDER BY created_at DESC
        LIMIT 10
    ";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $activities
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>