<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../pages/login.html");
    exit;
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$order_id) {
    header("Location: orders.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #
        <?php echo $order_id; ?> | SokoLink Electronics
    </title>
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
            padding: 40px 8%;
            min-height: 70vh;
            background: #f8fafc;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .back-link:hover {
            color: var(--primary-main);
        }

        .order-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }

        .order-items-card,
        .order-summary-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .card-header h2 {
            font-size: 1.3rem;
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

        .order-item {
            display: flex;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            background: #f8fafc;
        }

        .item-info {
            flex: 1;
        }

        .item-info h4 {
            margin-bottom: 5px;
        }

        .item-info p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .item-price {
            text-align: right;
        }

        .item-price .unit {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .item-price .total {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: var(--text-muted);
        }

        .summary-row.total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-main);
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid var(--primary-main);
        }

        .order-meta {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .meta-item {
            margin-bottom: 15px;
        }

        .meta-item label {
            display: block;
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .meta-item span {
            font-weight: 500;
        }

        @media (max-width: 900px) {
            .order-container {
                grid-template-columns: 1fr;
            }

            .page-container {
                padding: 20px 5%;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="page-container">
        <a href="orders.php" class="back-link"><i class="ph ph-arrow-left"></i> Back to Orders</a>

        <div class="order-container">
            <div class="order-items-card">
                <div class="card-header">
                    <h2>Order #
                        <?php echo $order_id; ?>
                    </h2>
                    <span class="status-badge" id="order-status">Loading...</span>
                </div>
                <div id="order-items">
                    <p style="text-align: center; color: var(--text-muted);">Loading items...</p>
                </div>
            </div>

            <div class="order-summary-card">
                <h3 style="margin-bottom: 20px;">Order Summary</h3>
                <div id="order-summary">
                    <p>Loading...</p>
                </div>

                <div class="order-meta">
                    <div class="meta-item">
                        <label>Order Date</label>
                        <span id="order-date">-</span>
                    </div>
                    <div class="meta-item">
                        <label>Order Status</label>
                        <span id="order-status-text">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        const orderId = <?php echo $order_id; ?>;

        document.addEventListener('DOMContentLoaded', loadOrderDetails);

        async function loadOrderDetails() {
            try {
                const response = await fetch(`../api/orders/details.php?id=${orderId}`);
                const result = await response.json();

                if (!result.success) {
                    alert(result.message);
                    window.location.href = 'orders.php';
                    return;
                }

                const order = result.order;
                const items = result.items;

                // Update status
                document.getElementById('order-status').textContent = order.status;
                document.getElementById('order-status').className = `status-badge status-${order.status}`;
                document.getElementById('order-status-text').textContent = order.status.charAt(0).toUpperCase() + order.status.slice(1);

                // Update date
                const date = new Date(order.created_at).toLocaleDateString('en-US', {
                    year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
                });
                document.getElementById('order-date').textContent = date;

                // Render items
                let itemsHtml = '';
                let subtotal = 0;
                items.forEach(item => {
                    const lineTotal = item.price_each * item.quantity;
                    subtotal += lineTotal;
                    const imagePath = item.image_url ? `../assets/uploads/${item.image_url}` : '../assets/uploads/placeholder.png';
                    itemsHtml += `
                        <div class="order-item">
                            <img src="${imagePath}" alt="${item.name}" onerror="this.src='../assets/uploads/placeholder.png'">
                            <div class="item-info">
                                <h4>${item.name}</h4>
                                <p>Qty: ${item.quantity}</p>
                            </div>
                            <div class="item-price">
                                <div class="unit">TSH ${Number(item.price_each).toLocaleString()} each</div>
                                <div class="total">TSH ${Number(lineTotal).toLocaleString()}</div>
                            </div>
                        </div>
                    `;
                });
                document.getElementById('order-items').innerHTML = itemsHtml;

                // Render summary
                const shipping = 5000;
                const total = Number(order.total_amount);
                document.getElementById('order-summary').innerHTML = `
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>TSH ${Number(subtotal).toLocaleString()}</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>TSH ${Number(shipping).toLocaleString()}</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>TSH ${Number(total).toLocaleString()}</span>
                    </div>
                `;

            } catch (error) {
                console.error('Error:', error);
                alert('Failed to load order details.');
            }
        }
    </script>
</body>

</html>