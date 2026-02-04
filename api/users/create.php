<?php
// api/users/create.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit;
}

// Get JSON input
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->full_name) || !isset($data->email) || !isset($data->password)) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if email exists
    $stmt = $db->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$data->email]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "Email already exists."]);
        exit;
    }

    // Insert
    $role = isset($data->role) ? $data->role : 'customer'; // Default to customer if not specified
    $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);

    if ($stmt->execute([$data->full_name, $data->email, $hashed_password, $role])) {
        echo json_encode(["success" => true, "message" => "User created successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to create user."]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>