<?php
// api/products/delete.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

// Check if admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "Product ID required"]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Optional: Delete image file before deleting record
    // $stmt = $db->prepare("SELECT image_url FROM products WHERE product_id = ?");
    // ... unlink file ...

    $query = "DELETE FROM products WHERE product_id = ?";
    $stmt = $db->prepare($query);

    if ($stmt->execute([$id])) {
        echo json_encode(["success" => true, "message" => "Product deleted"]);
    } else {
        echo json_encode(["success" => false, "message" => "Delete failed"]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>