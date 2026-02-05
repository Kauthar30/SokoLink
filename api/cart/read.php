<?php
// api/cart/read.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => true, "guest" => true, "items" => []]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT c.cart_id, c.product_id, c.quantity, p.name, p.price, p.image_url 
              FROM cart c 
              JOIN products p ON c.product_id = p.product_id 
              WHERE c.user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->execute([':user_id' => $user_id]);

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $subtotal = 0;
    foreach ($items as &$item) {
        $item['line_total'] = $item['price'] * $item['quantity'];
        $subtotal += $item['line_total'];
    }

    echo json_encode([
        "success" => true,
        "items" => $items,
        "subtotal" => $subtotal
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>