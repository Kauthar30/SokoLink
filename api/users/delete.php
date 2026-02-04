<?php
// api/users/delete.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "User ID required"]);
    exit;
}

// Prevent deleting self
if ($id == $_SESSION['user_id']) {
    echo json_encode(["success" => false, "message" => "You cannot delete your own account."]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $db->prepare($query);

    if ($stmt->execute([$id])) {
        echo json_encode(["success" => true, "message" => "User deleted"]);
    } else {
        echo json_encode(["success" => false, "message" => "Delete failed"]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>