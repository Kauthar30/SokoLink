<?php
// api/cart/merge.php - Merge guest cart (localStorage) into user cart after login
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit;
}

if (!isset($data->items) || !is_array($data->items)) {
    echo json_encode(["success" => false, "message" => "No items to merge."]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    foreach ($data->items as $item) {
        $product_id = intval($item->product_id);
        $quantity = intval($item->quantity);

        // Check if product already in cart
        $check = $db->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id");
        $check->execute([':user_id' => $user_id, ':product_id' => $product_id]);

        if ($check->rowCount() > 0) {
            $row = $check->fetch(PDO::FETCH_ASSOC);
            $new_qty = $row['quantity'] + $quantity;
            $update = $db->prepare("UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id");
            $update->execute([':quantity' => $new_qty, ':cart_id' => $row['cart_id']]);
        } else {
            $insert = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
            $insert->execute([':user_id' => $user_id, ':product_id' => $product_id, ':quantity' => $quantity]);
        }
    }

    echo json_encode(["success" => true, "message" => "Cart merged successfully"]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>