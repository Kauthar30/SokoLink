<?php
// api/cart/update.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->cart_id) || !isset($data->quantity)) {
    echo json_encode(["success" => false, "message" => "Cart ID and quantity are required."]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit;
}

$cart_id = intval($data->cart_id);
$quantity = intval($data->quantity);
$user_id = $_SESSION['user_id'];

if ($quantity < 1) {
    echo json_encode(["success" => false, "message" => "Quantity must be at least 1."]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $update = $db->prepare("UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id AND user_id = :user_id");
    $update->execute([':quantity' => $quantity, ':cart_id' => $cart_id, ':user_id' => $user_id]);

    if ($update->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Quantity updated"]);
    } else {
        echo json_encode(["success" => false, "message" => "Item not found"]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>