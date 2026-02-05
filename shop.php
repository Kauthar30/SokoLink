<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop | SokoLink Electronics</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/landing.css">
    <style>
        /* Shop Specific Styles */
        .shop-container {
            display: flex;
            padding: 120px 8% 60px;
            gap: 40px;
            min-height: 80vh;
        }

        .shop-sidebar {
            width: 250px;
            flex-shrink: 0;
        }

        .shop-sidebar h3 {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: var(--primary-dark);
        }

        .category-list {
            list-style: none;
        }

        .category-list li {
            margin-bottom: 12px;
        }

        .category-link {
            text-decoration: none;
            color: var(--text-muted);
            font-size: 1rem;
            transition: color 0.3s;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .category-link:hover,
        .category-link.active {
            color: var(--primary-main);
            font-weight: 600;
        }

        .shop-content {
            flex: 1;
        }

        .shop-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .shop-title h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .breadcrumb {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .shop-container {
                flex-direction: column;
                padding-top: 100px;
            }

            .shop-sidebar {
                width: 100%;
                margin-bottom: 30px;
            }
        }
    </style>
</head>

<body>

    <?php
    $is_shop = true;
    include 'includes/header.php';
    ?>

    <div class="shop-container">
        <!-- Sidebar -->
        <aside class="shop-sidebar">
            <h3>Categories</h3>
            <ul class="category-list" id="category-list">
                <!-- Loaded Dynamically -->
                <li>Loading categories...</li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="shop-content">
            <div class="shop-header">
                <div class="shop-title">
                    <h1 id="page-title">All Products</h1>
                    <div class="breadcrumb">Home / Shop</div>
                </div>
            </div>

            <div class="products-grid" id="products-grid">
                <!-- Products Loaded Dynamically -->
                <p>Loading products...</p>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/shop.js"></script>
</body>

</html>