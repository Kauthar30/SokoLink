<?php
// api/products/update.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

// Check if admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;
$category_id = isset($_POST['category_id']) ? $_POST['category_id'] : null;
$price = isset($_POST['price']) ? $_POST['price'] : null;
$stock = isset($_POST['stock']) ? $_POST['stock'] : null;
$description = isset($_POST['description']) ? $_POST['description'] : null;

if (!$product_id || !$name || !$category_id || !$price) {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if image is uploaded
    $image_query = "";
    $params = [
        ':name' => $name,
        ':cat_id' => $category_id,
        ':price' => $price,
        ':stock' => $stock,
        ':desc' => $description,
        ':id' => $product_id
    ];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../assets/uploads/";
        if (!is_dir($target_dir))
            mkdir($target_dir, 0777, true);

        $file_ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_query = ", image_url = :image";
            $params[':image'] = $new_filename;
        }
    }

    $query = "UPDATE products SET name = :name, category_id = :cat_id, price = :price, stock = :stock, description = :desc $image_query WHERE product_id = :id";
    $stmt = $db->prepare($query);

    if ($stmt->execute($params)) {
        echo json_encode(["success" => true, "message" => "Product updated successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update product"]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>