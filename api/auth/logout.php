<?php
// api/auth/logout.php
session_start();

// Destroy the session
session_unset();
session_destroy();

header("Content-Type: application/json");
echo json_encode([
    "success" => true,
    "message" => "Logged out successfully."
]);
?>