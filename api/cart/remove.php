<?php
// api/cart/remove.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->cart_id)) {
    echo json_encode(["success" => false, "message" => "Cart ID is required."]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit;
}

$cart_id = intval($data->cart_id);
$user_id = $_SESSION['user_id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    // Make sure user owns this cart item
    $delete = $db->prepare("DELETE FROM cart WHERE cart_id = :cart_id AND user_id = :user_id");
    $delete->execute([':cart_id' => $cart_id, ':user_id' => $user_id]);

    if ($delete->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Item removed"]);
    } else {
        echo json_encode(["success" => false, "message" => "Item not found"]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>