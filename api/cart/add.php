<?php
// api/cart/add.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->product_id)) {
    echo json_encode(["success" => false, "message" => "Product ID is required."]);
    exit;
}

$product_id = intval($data->product_id);
$quantity = isset($data->quantity) ? intval($data->quantity) : 1;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // For guests, return success and let frontend handle localStorage
    echo json_encode([
        "success" => true,
        "guest" => true,
        "message" => "Add to localStorage"
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if product already in cart
    $check = $db->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = :user_id AND product_id = :product_id");
    $check->execute([':user_id' => $user_id, ':product_id' => $product_id]);

    if ($check->rowCount() > 0) {
        // Update quantity
        $row = $check->fetch(PDO::FETCH_ASSOC);
        $new_qty = $row['quantity'] + $quantity;
        $update = $db->prepare("UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id");
        $update->execute([':quantity' => $new_qty, ':cart_id' => $row['cart_id']]);
    } else {
        // Insert new item
        $insert = $db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)");
        $insert->execute([':user_id' => $user_id, ':product_id' => $product_id, ':quantity' => $quantity]);
    }

    // Get updated cart count
    $count = $db->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = :user_id");
    $count->execute([':user_id' => $user_id]);
    $total = $count->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    echo json_encode([
        "success" => true,
        "message" => "Added to cart",
        "cart_count" => intval($total)
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>