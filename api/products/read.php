<?php
// api/products/read.php
header("Content-Type: application/json");
require_once '../../config/db.php';

try {
    $database = new Database();
    $db = $database->getConnection();


    // Get optional category filter
    $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;

    // Base query
    $query = "SELECT p.*, c.name as category_name 
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.category_id";

    // Add filter if present
    if ($category_id) {
        $query .= " WHERE p.category_id = :category_id";
    }

    $query .= " ORDER BY p.created_at DESC";

    $stmt = $db->prepare($query);

    if ($category_id) {
        $stmt->bindParam(':category_id', $category_id);
    }
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "count" => count($products),
        "data" => $products
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>