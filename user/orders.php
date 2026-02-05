<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../pages/login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | SokoLink Electronics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="../assets/css/landing.css">
    <style>
        nav {
            position: relative !important;
            background: #fff !important;
            border-bottom: 1px solid #e2e8f0;
        }

        nav .logo {
            color: var(--primary-main) !important;
        }

        nav .nav-links a {
            color: var(--text-main) !important;
        }

        nav .nav-actions {
            color: var(--text-main) !important;
        }

        .page-container {
            display: flex;
            padding: 40px 8%;
            gap: 40px;
            min-height: 70vh;
            background: #f8fafc;
        }

        .sidebar {
            width: 280px;
            flex-shrink: 0;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            height: fit-content;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .avatar {
            width: 50px;
            height: 50px;
            background: var(--primary-main);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 10px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            text-decoration: none;
            color: var(--text-muted);
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #f0f9ff;
            color: var(--primary-main);
        }

        .main-content {
            flex: 1;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .page-header p {
            color: var(--text-muted);
        }

        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .order-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .order-id {
            font-weight: 700;
            color: var(--primary-main);
        }

        .order-date {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background: #fef3c7;
            color: #b45309;
        }

        .status-completed {
            background: #d1fae5;
            color: #047857;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }

        .order-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-info span {
            display: block;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .order-info strong {
            font-size: 1.1rem;
        }

        .btn-view {
            padding: 10px 20px;
            background: var(--primary-main);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .btn-view:hover {
            background: var(--primary-dark);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
        }

        .empty-state i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            margin-bottom: 10px;
            color: var(--text-main);
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        @media (max-width: 900px) {
            .page-container {
                flex-direction: column;
                padding: 20px 5%;
            }

            .sidebar {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="page-container">
        <aside class="sidebar">
            <div class="user-profile">
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                </div>
                <div>
                    <h4 style="margin-bottom: 2px;">
                        <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                    </h4>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Customer</span>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="ph ph-squares-four"></i> Dashboard</a></li>
                <li><a href="orders.php" class="active"><i class="ph ph-shopping-bag"></i> My Orders</a></li>
                <li><a href="wishlist.php"><i class="ph ph-heart"></i> Wishlist</a></li>
                <li><a href="settings.php"><i class="ph ph-gear"></i> Settings</a></li>
                <li><a href="#" onclick="logout()"><i class="ph ph-sign-out"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h1>My Orders</h1>
                <p>View and track all your orders</p>
            </div>

            <div class="orders-list" id="orders-list">
                <div style="text-align: center; padding: 40px;">Loading orders...</div>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', loadOrders);

        async function loadOrders() {
            const container = document.getElementById('orders-list');

            try {
                const response = await fetch('../api/orders/read.php');
                const result = await response.json();

                if (!result.success) {
                    container.innerHTML = '<div class="empty-state"><i class="ph ph-warning"></i><h3>Error</h3><p>' + result.message + '</p></div>';
                    return;
                }

                if (result.orders.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="ph ph-shopping-bag"></i>
                            <h3>No orders yet</h3>
                            <p>When you place orders, they will appear here.</p>
                            <a href="../shop.php" class="btn-view">Start Shopping</a>
                        </div>
                    `;
                    return;
                }

                let html = '';
                result.orders.forEach(order => {
                    const date = new Date(order.created_at).toLocaleDateString('en-US', {
                        year: 'numeric', month: 'short', day: 'numeric'
                    });

                    html += `
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <span class="order-id">Order #${order.order_id}</span>
                                    <span class="order-date">${date}</span>
                                </div>
                                <span class="status-badge status-${order.status}">${order.status}</span>
                            </div>
                            <div class="order-details">
                                <div class="order-info">
                                    <span>${order.item_count} item(s)</span>
                                    <strong>TSH ${Number(order.total_amount).toLocaleString()}</strong>
                                </div>
                                <a href="order-details.php?id=${order.order_id}" class="btn-view">
                                    View Details <i class="ph ph-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;

            } catch (error) {
                console.error('Error:', error);
                container.innerHTML = '<div class="empty-state"><i class="ph ph-warning"></i><h3>Error</h3><p>Failed to load orders.</p></div>';
            }
        }

        async function logout() {
            if (confirm('Are you sure you want to logout?')) {
                await fetch('../api/auth/logout.php');
                window.location.href = '../index.php';
            }
        }
    </script>
</body>

</html>