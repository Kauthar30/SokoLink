<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine base path based on current directory
$base_path = '';
if (strpos($_SERVER['PHP_SELF'], '/user/') !== false) {
    $base_path = '../';
}

// Determine user link based on login status
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        $user_link = $base_path . 'pages/admin.html';
    } else {
        $user_link = $base_path . 'user/dashboard.php';
    }
    $user_name = htmlspecialchars($_SESSION['full_name']);
} else {
    $user_link = $base_path . 'pages/login.html';
    $user_name = '';
}
?>
<!-- Navigation -->
<nav class="<?php echo isset($is_shop) && $is_shop ? 'shop-nav' : ''; ?>">
    <a href="<?php echo $base_path; ?>index.php" class="logo">
        <i class="ph-fill ph-circuitry"></i> SokoLink
    </a>
    <ul class="nav-links">
        <li><a href="<?php echo $base_path; ?>index.php">Home</a></li>
        <li><a href="<?php echo $base_path; ?>shop.php">Shop</a></li>
        <li><a href="<?php echo $base_path; ?>index.php#arrivals">New Arrivals</a></li>
        <li><a href="<?php echo $base_path; ?>index.php#about">About Us</a></li>
    </ul>
    <div class="nav-actions">
        <i class="ph ph-magnifying-glass"></i>
        <div class="cart-icon-wrapper" onclick="openCart()" style="position: relative; cursor: pointer;">
            <i class="ph ph-shopping-cart"></i>
            <span id="cart-badge" class="cart-badge" style="display: none;">0</span>
        </div>
        <a href="<?php echo $user_link; ?>" style="color: inherit; text-decoration: none;"
            title="<?php echo $user_name ? $user_name : 'Login'; ?>">
            <i class="ph ph-user<?php echo $user_name ? '-circle' : ''; ?>"></i>
        </a>
    </div>
</nav>

<!-- Include Cart Modal -->
<?php include $base_path . 'includes/cart-modal.php'; ?>

<!-- Cart Script -->
<script src="<?php echo $base_path; ?>assets/js/cart.js"></script>

<style>
    /* Cart Badge */
    .cart-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ef4444;
        color: white;
        font-size: 0.7rem;
        font-weight: 600;
        min-width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Shop specific nav style override */
    .shop-nav {
        background: #fff !important;
        border-bottom: 1px solid #e2e8f0;
        position: relative !important;
        padding: 20px 8% !important;
    }

    .shop-nav .logo {
        color: var(--primary-main) !important;
    }

    .shop-nav .nav-links a {
        color: var(--text-main) !important;
    }

    .shop-nav .nav-links a:hover {
        color: var(--primary-main) !important;
    }

    .shop-nav .nav-actions {
        color: var(--text-main) !important;
    }
</style>