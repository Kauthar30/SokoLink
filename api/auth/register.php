<?php
// api/auth/register.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->full_name) || !isset($data->email) || !isset($data->password)) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

$full_name = filter_var($data->full_name, FILTER_SANITIZE_STRING);
$email = filter_var($data->email, FILTER_SANITIZE_EMAIL);
$password = $data->password;

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email format."]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(["success" => false, "message" => "Password must be at least 6 characters."]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if email exists
    $check_query = "SELECT user_id FROM users WHERE email = :email LIMIT 1";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(":email", $email);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "Email already registered."]);
        exit;
    }

    // Insert new user
    $query = "INSERT INTO users (full_name, email, password_hash, role) VALUES (:full_name, :email, :password_hash, 'customer')";
    $stmt = $db->prepare($query);

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt->bindParam(":full_name", $full_name);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password_hash", $password_hash);

    if ($stmt->execute()) {
        // Auto-login logic
        $user_id = $db->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['role'] = 'customer';

        echo json_encode([
            "success" => true,
            "message" => "Registration successful",
            "redirect" => "../user/dashboard.php"
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Registration failed. Please try again."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>