<?php
// api/admin/sales_data.php - Get sales data for chart (last 7 days)
session_start();
header("Content-Type: application/json");
require_once '../../config/db.php';

// Check if admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Unauthorized."]);
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Get sales data for last 7 days (completed orders only)
    $query = "SELECT 
                DATE(created_at) as date,
                DAYNAME(created_at) as day_name,
                SUM(total_amount) as daily_total,
                COUNT(*) as order_count
              FROM orders 
              WHERE status = 'completed' 
                AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
              GROUP BY DATE(created_at)
              ORDER BY date ASC";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create array for last 7 days
    $labels = [];
    $data = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $dayName = date('D', strtotime("-$i days")); // Mon, Tue, etc.
        $labels[] = $dayName;

        // Find if we have data for this date
        $found = false;
        foreach ($sales as $sale) {
            if ($sale['date'] === $date) {
                $data[] = floatval($sale['daily_total']);
                $found = true;
                break;
            }
        }
        if (!$found) {
            $data[] = 0;
        }
    }

    // Also get total completed sales
    $totalQuery = "SELECT COUNT(*) as total_completed, SUM(total_amount) as total_revenue 
                   FROM orders WHERE status = 'completed'";
    $totalStmt = $db->prepare($totalQuery);
    $totalStmt->execute();
    $totals = $totalStmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "labels" => $labels,
        "data" => $data,
        "total_completed" => intval($totals['total_completed']),
        "total_revenue" => floatval($totals['total_revenue'] ?? 0)
    ]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>