<?php
// api/users/update_password.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit;
}

// Get data from request
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id']) || !isset($data['new_password'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit;
}

$user_id = intval($data['user_id']);
$new_password = $data['new_password'];

// Validate password length
if (strlen($new_password) < 6) {
    echo json_encode(["success" => false, "message" => "Password must be at least 6 characters long."]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password
    $query = "UPDATE users SET password = :password WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Password updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update password."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>