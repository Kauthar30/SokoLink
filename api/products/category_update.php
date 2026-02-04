<?php
// api/products/category_update.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

// Check if admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->category_id) || !isset($data->name) || empty(trim($data->name))) {
    echo json_encode(["success" => false, "message" => "Category ID and name are required."]);
    exit;
}

$category_id = $data->category_id;
$name = trim($data->name);

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if another category has the same name
    $check_query = "SELECT category_id FROM categories WHERE name = :name AND category_id != :id LIMIT 1";
    $stmt = $db->prepare($check_query);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":id", $category_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "Category name already exists."]);
        exit;
    }

    $query = "UPDATE categories SET name = :name WHERE category_id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":id", $category_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Category updated successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update category."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>