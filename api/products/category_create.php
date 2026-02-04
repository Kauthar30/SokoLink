<?php
// api/products/category_create.php
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

if (!isset($data->name) || empty(trim($data->name))) {
    echo json_encode(["success" => false, "message" => "Category name is required."]);
    exit;
}

$name = trim($data->name);

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if category already exists
    $check_query = "SELECT category_id FROM categories WHERE name = :name LIMIT 1";
    $stmt = $db->prepare($check_query);
    $stmt->bindParam(":name", $name);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "Category already exists."]);
        exit;
    }

    $query = "INSERT INTO categories (name) VALUES (:name)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":name", $name);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Category created successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to create category."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>