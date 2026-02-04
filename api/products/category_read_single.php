<?php
// api/products/category_read_single.php
header("Content-Type: application/json");
require_once '../../config/db.php';

if (!isset($_GET['id'])) {
    echo json_encode(["success" => false, "message" => "Category ID is required."]);
    exit;
}

$id = $_GET['id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT * FROM categories WHERE category_id = :id LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $category]);
    } else {
        echo json_encode(["success" => false, "message" => "Category not found."]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>