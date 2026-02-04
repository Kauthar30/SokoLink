<?php
// api/products/read_single.php
header("Content-Type: application/json");
require_once '../../config/db.php';

if (!isset($_GET['id'])) {
    echo json_encode(["success" => false, "message" => "Product ID is required."]);
    exit;
}

$id = $_GET['id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT * FROM products WHERE product_id = :id LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $product]);
    } else {
        echo json_encode(["success" => false, "message" => "Product not found."]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>