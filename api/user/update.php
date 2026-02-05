<?php
// api/user/update.php - Update user profile
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Not logged in."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));
$user_id = $_SESSION['user_id'];

try {
    $database = new Database();
    $db = $database->getConnection();

    // Build update query dynamically
    $updates = [];
    $params = [':user_id' => $user_id];

    if (isset($data->full_name) && !empty($data->full_name)) {
        $updates[] = "full_name = :full_name";
        $params[':full_name'] = $data->full_name;
        $_SESSION['full_name'] = $data->full_name; // Update session
    }

    if (isset($data->phone)) {
        $updates[] = "phone = :phone";
        $params[':phone'] = $data->phone;
    }

    if (isset($data->address)) {
        $updates[] = "address = :address";
        $params[':address'] = $data->address;
    }

    // Handle password change
    if (isset($data->new_password) && !empty($data->new_password)) {
        if (!isset($data->current_password) || empty($data->current_password)) {
            echo json_encode(["success" => false, "message" => "Current password required."]);
            exit;
        }

        // Verify current password
        $check = $db->prepare("SELECT password_hash FROM users WHERE user_id = :user_id");
        $check->execute([':user_id' => $user_id]);
        $user = $check->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($data->current_password, $user['password_hash'])) {
            echo json_encode(["success" => false, "message" => "Current password is incorrect."]);
            exit;
        }

        if (strlen($data->new_password) < 6) {
            echo json_encode(["success" => false, "message" => "New password must be at least 6 characters."]);
            exit;
        }

        $updates[] = "password_hash = :password_hash";
        $params[':password_hash'] = password_hash($data->new_password, PASSWORD_BCRYPT);
    }

    if (empty($updates)) {
        echo json_encode(["success" => false, "message" => "No changes to save."]);
        exit;
    }

    $query = "UPDATE users SET " . implode(", ", $updates) . " WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->execute($params);

    echo json_encode(["success" => true, "message" => "Profile updated successfully."]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>