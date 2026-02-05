<?php
// api/orders/create.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));
$user_id = $_SESSION['user_id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    // 1. Get cart items
    $cart_query = $db->prepare("SELECT c.cart_id, c.product_id, c.quantity, p.price 
                                 FROM cart c 
                                 JOIN products p ON c.product_id = p.product_id 
                                 WHERE c.user_id = :user_id");
    $cart_query->execute([':user_id' => $user_id]);
    $cart_items = $cart_query->fetchAll(PDO::FETCH_ASSOC);

    if (count($cart_items) === 0) {
        echo json_encode(["success" => false, "message" => "Cart is empty."]);
        exit;
    }

    // 2. Calculate total
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    // Add shipping cost
    $shipping = 5000;
    $total_amount += $shipping;

    // 3. Create order
    $db->beginTransaction();

    $order_insert = $db->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (:user_id, :total, 'pending')");
    $order_insert->execute([':user_id' => $user_id, ':total' => $total_amount]);
    $order_id = $db->lastInsertId();

    // 4. Insert order items
    $item_insert = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_each) VALUES (:order_id, :product_id, :quantity, :price)");

    foreach ($cart_items as $item) {
        $item_insert->execute([
            ':order_id' => $order_id,
            ':product_id' => $item['product_id'],
            ':quantity' => $item['quantity'],
            ':price' => $item['price']
        ]);
    }

    // 5. Clear cart
    $clear_cart = $db->prepare("DELETE FROM cart WHERE user_id = :user_id");
    $clear_cart->execute([':user_id' => $user_id]);

    // 6. Update user address if provided
    if (isset($data->shipping)) {
        $shipping_data = $data->shipping;
        if (isset($shipping_data->phone) || isset($shipping_data->address)) {
            $update_user = $db->prepare("UPDATE users SET phone = :phone, address = :address WHERE user_id = :user_id");
            $update_user->execute([
                ':phone' => $shipping_data->phone ?? null,
                ':address' => ($shipping_data->address ?? '') . ', ' . ($shipping_data->city ?? '') . ', ' . ($shipping_data->region ?? ''),
                ':user_id' => $user_id
            ]);
        }
    }

    $db->commit();

    echo json_encode([
        "success" => true,
        "message" => "Order placed successfully",
        "order_id" => $order_id
    ]);

} catch (PDOException $e) {
    if (isset($db))
        $db->rollBack();
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>