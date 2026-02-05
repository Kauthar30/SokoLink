<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../pages/login.html?redirect=checkout");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | SokoLink Electronics</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="../assets/css/landing.css">
    <style>
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

        nav .nav-actions {
            color: var(--text-main) !important;
        }

        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
            padding: 40px 8%;
            min-height: 70vh;
            background: #f8fafc;
        }

        .checkout-form,
        .order-summary {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .checkout-form h2,
        .order-summary h2 {
            font-size: 1.3rem;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-main);
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-main);
        }

        .order-items {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .order-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .order-item-info {
            flex: 1;
        }

        .order-item-info h4 {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .order-totals {
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }

        .order-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            color: var(--text-muted);
        }

        .order-row.total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-main);
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid var(--primary-main);
        }

        .btn-place-order {
            width: 100%;
            padding: 15px;
            background: var(--primary-main);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s;
        }

        .btn-place-order:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        @media (max-width: 900px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .checkout-container {
                padding: 20px 5%;
            }
        }
    </style>
</head>

<body>

    <?php include '../includes/header.php'; ?>

    <div class="checkout-container">
        <!-- Checkout Form -->
        <div class="checkout-form">
            <h2><i class="ph ph-map-pin"></i> Shipping Information</h2>

            <div class="form-row">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" value="<?php echo htmlspecialchars($_SESSION['full_name']); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" placeholder="+255 7XX XXX XXX" required>
                </div>
            </div>

            <div class="form-group">
                <label for="address">Delivery Address</label>
                <textarea id="address" rows="3" placeholder="Enter your full delivery address..." required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" placeholder="e.g. Dar es Salaam" required>
                </div>
                <div class="form-group">
                    <label for="region">Region</label>
                    <input type="text" id="region" placeholder="e.g. Ilala" required>
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Order Notes (Optional)</label>
                <textarea id="notes" rows="2" placeholder="Any special instructions for delivery..."></textarea>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <h2><i class="ph ph-receipt"></i> Order Summary</h2>

            <div class="order-items" id="checkout-items">
                <!-- Loaded dynamically -->
                <p style="text-align: center; color: var(--text-muted);">Loading cart...</p>
            </div>

            <div class="order-totals">
                <div class="order-row">
                    <span>Subtotal</span>
                    <span id="checkout-subtotal">TSH 0</span>
                </div>
                <div class="order-row">
                    <span>Shipping</span>
                    <span id="checkout-shipping">TSH 5,000</span>
                </div>
                <div class="order-row total">
                    <span>Total</span>
                    <span id="checkout-total">TSH 0</span>
                </div>
            </div>

            <button class="btn-place-order" onclick="placeOrder()">
                <i class="ph ph-check-circle"></i> Place Order
            </button>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        const SHIPPING_COST = 5000;

        document.addEventListener('DOMContentLoaded', loadCheckoutItems);

        async function loadCheckoutItems() {
            const container = document.getElementById('checkout-items');

            try {
                const response = await fetch('../api/cart/read.php');
                const result = await response.json();

                if (!result.success || result.items.length === 0) {
                    container.innerHTML = '<p style="text-align: center;">Your cart is empty. <a href="../shop.php">Continue Shopping</a></p>';
                    return;
                }

                let html = '';
                result.items.forEach(item => {
                    html += `
                        <div class="order-item">
                            <img src="../${item.image_url || 'assets/images/placeholder.png'}" alt="${item.name}">
                            <div class="order-item-info">
                                <h4>${item.name}</h4>
                                <p style="color: var(--text-muted); font-size: 0.85rem;">Qty: ${item.quantity}</p>
                            </div>
                            <span style="font-weight: 600;">TSH ${Number(item.line_total).toLocaleString()}</span>
                        </div>
                    `;
                });

                container.innerHTML = html;

                // Update totals
                const subtotal = result.subtotal;
                const total = subtotal + SHIPPING_COST;

                document.getElementById('checkout-subtotal').textContent = `TSH ${Number(subtotal).toLocaleString()}`;
                document.getElementById('checkout-total').textContent = `TSH ${Number(total).toLocaleString()}`;

            } catch (error) {
                console.error('Error loading checkout items:', error);
                container.innerHTML = '<p style="color: red;">Error loading cart.</p>';
            }
        }

        async function placeOrder() {
            const full_name = document.getElementById('full_name').value;
            const phone = document.getElementById('phone').value;
            const address = document.getElementById('address').value;
            const city = document.getElementById('city').value;
            const region = document.getElementById('region').value;
            const notes = document.getElementById('notes').value;

            if (!full_name || !phone || !address || !city || !region) {
                alert('Please fill all required fields.');
                return;
            }

            try {
                const response = await fetch('../api/orders/create.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        shipping: { full_name, phone, address, city, region, notes }
                    })
                });

                const result = await response.json();

                if (result.success) {
                    alert('Order placed successfully! Order ID: ' + result.order_id);
                    window.location.href = 'dashboard.php';
                } else {
                    alert(result.message || 'Failed to place order.');
                }
            } catch (error) {
                console.error('Order error:', error);
                alert('An error occurred. Please try again.');
            }
        }
    </script>
</body>

</html>