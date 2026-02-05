<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    // Redirect admins to admin panel, guests to login
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: ../pages/admin.html");
    } else {
        header("Location: ../pages/login.html");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | SokoLink Electronics</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="../assets/css/landing.css">
    <style>
        .dashboard-container {
            display: flex;
            padding: 120px 8% 60px;
            gap: 40px;
            min-height: 80vh;
            background: #f8fafc;
        }

        .dashboard-sidebar {
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

        .dashboard-content {
            flex: 1;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #00509E 0%, #003366 100%);
            color: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            border: 1px solid #e2e8f0;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 10px 0 5px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .orders-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            border-collapse: collapse;
        }

        .orders-table th,
        .orders-table td {
            text-align: left;
            padding: 15px 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .orders-table th {
            background: #f8fafc;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-pending {
            background: #fff7ed;
            color: #c2410c;
        }

        .status-completed {
            background: #f0fdf4;
            color: #15803d;
        }

        /* Dashboard Header Override */
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

        nav .nav-links a:hover {
            color: var(--primary-main) !important;
        }

        nav .nav-actions {
            color: var(--text-main) !important;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .dashboard-container {
                flex-direction: column;
                padding: 30px 5%;
            }

            .dashboard-sidebar {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .orders-table {
                display: block;
                overflow-x: auto;
            }

            .welcome-banner h2 {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 480px) {
            .dashboard-container {
                padding: 20px 4%;
            }

            .dashboard-sidebar {
                padding: 20px;
            }

            .stat-card {
                padding: 15px;
            }

            .stat-value {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <!-- Reuse generic header -->
    <?php include '../includes/header.php'; ?>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar">
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
                <li><a href="dashboard.php" class="active"><i class="ph ph-squares-four"></i> Dashboard</a></li>
                <li><a href="orders.php"><i class="ph ph-shopping-bag"></i> My Orders</a></li>
                <li><a href="wishlist.php"><i class="ph ph-heart"></i> Wishlist</a></li>
                <li><a href="settings.php"><i class="ph ph-gear"></i> Settings</a></li>
                <li><a href="#" onclick="logout()"><i class="ph ph-sign-out"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="welcome-banner">
                <h2>Welcome back,
                    <?php echo htmlspecialchars(explode(' ', $_SESSION['full_name'])[0]); ?>!
                </h2>
                <p>Track your orders and manage your account.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <span style="color: var(--text-muted); font-size: 0.9rem;">Total Orders</span>
                    <div class="stat-value" id="stat-orders">-</div>
                </div>
                <div class="stat-card">
                    <span style="color: var(--text-muted); font-size: 0.9rem;">Pending</span>
                    <div class="stat-value" id="stat-pending">-</div>
                </div>
                <div class="stat-card">
                    <span style="color: var(--text-muted); font-size: 0.9rem;">Wishlist</span>
                    <div class="stat-value" id="stat-wishlist">-</div>
                </div>
            </div>

            <div class="section-header">
                <h3>Recent Orders</h3>
                <a href="orders.php" style="color: var(--primary-main); text-decoration: none; font-weight: 500;">View All</a>
            </div>

            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="recent-orders">
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted);">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </main>
    </div>

    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            loadDashboardStats();
            loadRecentOrders();
        });

        async function loadDashboardStats() {
            try {
                // Load orders stats
                const ordersRes = await fetch('../api/orders/read.php');
                const ordersData = await ordersRes.json();
                
                if (ordersData.success) {
                    const orders = ordersData.orders || [];
                    document.getElementById('stat-orders').textContent = orders.length;
                    document.getElementById('stat-pending').textContent = orders.filter(o => o.status === 'pending').length;
                }

                // Load wishlist count
                const wishlistRes = await fetch('../api/wishlist/read.php');
                const wishlistData = await wishlistRes.json();
                
                if (wishlistData.success) {
                    document.getElementById('stat-wishlist').textContent = (wishlistData.items || []).length;
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        async function loadRecentOrders() {
            const tbody = document.getElementById('recent-orders');
            
            try {
                const response = await fetch('../api/orders/read.php');
                const result = await response.json();

                if (!result.success || result.orders.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--text-muted);">No orders found.</td></tr>';
                    return;
                }

                let html = '';
                const recentOrders = result.orders.slice(0, 5); // Show last 5

                recentOrders.forEach(order => {
                    const date = new Date(order.created_at).toLocaleDateString('en-US', {
                        month: 'short', day: 'numeric', year: 'numeric'
                    });

                    const statusClass = `status-${order.status}`;
                    
                    html += `
                        <tr>
                            <td>#${order.order_id}</td>
                            <td>${date}</td>
                            <td>TSH ${Number(order.total_amount).toLocaleString()}</td>
                            <td><span class="status-badge ${statusClass}">${order.status}</span></td>
                            <td><a href="order-details.php?id=${order.order_id}" style="color: var(--primary-main); text-decoration: none;">View</a></td>
                        </tr>
                    `;
                });

                tbody.innerHTML = html;
            } catch (error) {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: var(--text-muted);">Error loading orders.</td></tr>';
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