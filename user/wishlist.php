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
    <title>My Wishlist | SokoLink Electronics</title>
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

        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .wishlist-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
            position: relative;
        }

        .wishlist-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 15px;
            background: #f8fafc;
        }

        .wishlist-card h4 {
            font-size: 1rem;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .wishlist-card .category {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 10px;
        }

        .wishlist-card .price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-main);
            margin-bottom: 15px;
        }

        .wishlist-actions {
            display: flex;
            gap: 10px;
        }

        .btn-cart {
            flex: 1;
            padding: 12px;
            background: var(--primary-main);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-cart:hover {
            background: var(--primary-dark);
        }

        .btn-remove {
            padding: 12px 15px;
            background: #fee2e2;
            color: #dc2626;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-remove:hover {
            background: #fecaca;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
            grid-column: 1 / -1;
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

        .empty-state a {
            display: inline-block;
            padding: 12px 25px;
            background: var(--primary-main);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .page-container {
                flex-direction: column;
                padding: 20px 5%;
            }

            .sidebar {
                width: 100%;
            }

            .wishlist-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
        }

        @media (max-width: 500px) {
            .wishlist-grid {
                grid-template-columns: 1fr;
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
                <li><a href="orders.php"><i class="ph ph-shopping-bag"></i> My Orders</a></li>
                <li><a href="wishlist.php" class="active"><i class="ph ph-heart"></i> Wishlist</a></li>
                <li><a href="settings.php"><i class="ph ph-gear"></i> Settings</a></li>
                <li><a href="#" onclick="logout()"><i class="ph ph-sign-out"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h1>My Wishlist</h1>
                <p>Your saved favorite products</p>
            </div>

            <div class="wishlist-grid" id="wishlist-grid">
                <div style="text-align: center; padding: 40px; grid-column: 1/-1;">Loading wishlist...</div>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', loadWishlist);

        async function loadWishlist() {
            const container = document.getElementById('wishlist-grid');

            try {
                const response = await fetch('../api/wishlist/read.php');
                const result = await response.json();

                if (!result.success) {
                    container.innerHTML = '<div class="empty-state"><i class="ph ph-warning"></i><h3>Error</h3><p>' + result.message + '</p></div>';
                    return;
                }

                if (result.items.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="ph ph-heart"></i>
                            <h3>Your wishlist is empty</h3>
                            <p>Save products you love to your wishlist.</p>
                            <a href="../shop.php">Browse Products</a>
                        </div>
                    `;
                    return;
                }

                let html = '';
                result.items.forEach(item => {
                    html += `
                        <div class="wishlist-card" id="wishlist-${item.wishlist_id}">
                            <img src="../${item.image_url ? 'assets/uploads/' + item.image_url : 'assets/uploads/placeholder.png'}" alt="${item.name}" onerror="this.src='../assets/uploads/placeholder.png'">
                            <h4>${item.name}</h4>
                            <div class="category">${item.category_name || 'Electronics'}</div>
                            <div class="price">TSH ${Number(item.price).toLocaleString()}</div>
                            <div class="wishlist-actions">
                                <button class="btn-cart" onclick="moveToCart(${item.product_id}, ${item.wishlist_id})">
                                    <i class="ph ph-shopping-cart"></i> Add to Cart
                                </button>
                                <button class="btn-remove" onclick="removeFromWishlist(${item.wishlist_id})">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = html;

            } catch (error) {
                console.error('Error:', error);
                container.innerHTML = '<div class="empty-state"><i class="ph ph-warning"></i><h3>Error</h3><p>Failed to load wishlist.</p></div>';
            }
        }

        async function removeFromWishlist(wishlistId) {
            if (!confirm('Remove this item from wishlist?')) return;

            try {
                const response = await fetch('../api/wishlist/remove.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ wishlist_id: wishlistId })
                });

                const result = await response.json();
                if (result.success) {
                    document.getElementById('wishlist-' + wishlistId).remove();

                    // Check if wishlist is now empty
                    const container = document.getElementById('wishlist-grid');
                    if (container.children.length === 0) {
                        loadWishlist();
                    }
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to remove item.');
            }
        }

        async function moveToCart(productId, wishlistId) {
            try {
                // Add to cart
                const cartResponse = await fetch('../api/cart/add.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId })
                });

                const cartResult = await cartResponse.json();
                if (cartResult.success) {
                    // Remove from wishlist
                    await fetch('../api/wishlist/remove.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ wishlist_id: wishlistId })
                    });

                    document.getElementById('wishlist-' + wishlistId).remove();

                    // Update cart badge
                    if (typeof updateCartBadge === 'function') {
                        updateCartBadge();
                    }

                    alert('Added to cart!');

                    // Check if wishlist is now empty
                    const container = document.getElementById('wishlist-grid');
                    if (container.children.length === 0) {
                        loadWishlist();
                    }
                } else {
                    alert(cartResult.message || 'Failed to add to cart.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to move item to cart.');
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