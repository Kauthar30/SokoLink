<?php
// api/auth/login.php
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->email) || !isset($data->password)) {
    echo json_encode(["success" => false, "message" => "Email and Password are required."]);
    exit;
}

$email = filter_var($data->email, FILTER_SANITIZE_EMAIL);
$password = $data->password;

try {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT user_id, full_name, password_hash, role FROM users WHERE email = :email LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['password_hash'])) {
            // Password correct, start session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Handle Remember Me
            if (isset($data->remember) && $data->remember === true) {
                // Set session cookie to last for 30 days
                $params = session_get_cookie_params();
                setcookie(session_name(), session_id(), time() + (86400 * 30), $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }

            echo json_encode([
                "success" => true,
                "message" => "Login successful",
                "role" => $user['role'],
                "redirect" => ($user['role'] === 'admin') ? 'admin.html' : '../user/dashboard.php'
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid email or password."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>