<?php
// api/wishlist/remove.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->wishlist_id)) {
    echo json_encode(["success" => false, "message" => "Wishlist ID required."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$wishlist_id = intval($data->wishlist_id);

try {
    $database = new Database();
    $db = $database->getConnection();

    $delete = $db->prepare("DELETE FROM wishlist WHERE wishlist_id = :wishlist_id AND user_id = :user_id");
    $delete->execute([':wishlist_id' => $wishlist_id, ':user_id' => $user_id]);

    if ($delete->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => "Removed from wishlist."]);
    } else {
        echo json_encode(["success" => false, "message" => "Item not found."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>