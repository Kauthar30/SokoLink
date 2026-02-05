<?php
// api/wishlist/add.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Please login to add to wishlist."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->product_id)) {
    echo json_encode(["success" => false, "message" => "Product ID required."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($data->product_id);

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if already in wishlist
    $check = $db->prepare("SELECT wishlist_id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id");
    $check->execute([':user_id' => $user_id, ':product_id' => $product_id]);

    if ($check->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "Already in wishlist."]);
        exit;
    }

    $insert = $db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)");
    $insert->execute([':user_id' => $user_id, ':product_id' => $product_id]);

    echo json_encode(["success" => true, "message" => "Added to wishlist!"]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>