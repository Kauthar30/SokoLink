<?php
// api/products/category_delete.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

// Check if admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

if (!isset($_GET['id'])) {
    echo json_encode(["success" => false, "message" => "Category ID is required."]);
    exit;
}

$category_id = $_GET['id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    // Note: Due to ON DELETE CASCADE on products table, deleting a category will delete all associated products.
    // If the user wants to prevent this, they should move products first. 
    // For now, we follow the DB schema.

    $query = "DELETE FROM categories WHERE category_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $category_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Category deleted successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to delete category."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>