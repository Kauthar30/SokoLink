<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SokoLink | Premium Electronics Store</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/landing.css">
</head>

<body>

    <!-- Navigation -->
    <nav>
        <a href="index.php" class="logo">
            <i class="ph-fill ph-circuitry"></i> SokoLink
        </a>
        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#categories">Categories</a></li>
            <li><a href="#arrivals">New Arrivals</a></li>
            <li><a href="#about">About Us</a></li>
        </ul>
        <div class="nav-actions">
            <i class="ph ph-magnifying-glass"></i>
            <i class="ph ph-shopping-cart"></i>
            <a href="pages/login.html" style="color: inherit; text-decoration: none;"><i class="ph ph-user"></i></a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-tagline text-fade" id="hero-tagline">
                Innovation at your fingertips
            </div>
            <h1 id="hero-title" class="text-fade"><span>The Future of</span> <span>Electronics.</span></h1>
            <p id="hero-desc" class="text-fade">Discover a curated collection of high-performance tech designed to elevate your lifestyle. From smartphones to solar solutions, we bring the best to you.</p>
            <div class="hero-actions">
                <a href="#arrivals" class="btn-premium">Explore Shop <i class="ph-bold ph-arrow-right"></i></a>
                <a href="#about" class="btn-secondary">Learn More</a>
            </div>
        </div>
        <div class="hero-media">
            <img src="assets/images/hero_natural.png" alt="SokoLink Product Showcase">
        </div>
    </section>

    <!-- Features -->
    <div class="features">
        <div class="feature-item">
            <i class="ph ph-truck"></i>
            <h4>Free Shipping</h4>
            <p>On all orders over TSH 500k</p>
        </div>
        <div class="feature-item">
            <i class="ph ph-shield-check"></i>
            <h4>Genuine Products</h4>
            <p>100% Guaranteed Quality</p>
        </div>
        <div class="feature-item">
            <i class="ph ph-headset"></i>
            <h4>Expert Support</h4>
            <p>Available 24/7 for you</p>
        </div>
        <div class="feature-item">
            <i class="ph ph-arrows-clockwise"></i>
            <h4>Easy Returns</h4>
            <p>7 Days no-hassle return</p>
        </div>
    </div>

    <!-- Categories Section -->
    <section class="section" id="categories">
        <div class="section-title">
            <h2>Shop by Category</h2>
            <p>Browse our extensive collection of tech products</p>
        </div>
        <div class="categories-grid">
            <div class="category-card">
                <img src="assets/uploads/placeholder.png" alt="Phones">
                <div class="category-info">
                    <h3>Smartphones</h3>
                </div>
            </div>
            <div class="category-card">
                <img src="assets/uploads/placeholder.png" alt="Laptops">
                <div class="category-info">
                    <h3>Computing</h3>
                </div>
            </div>
            <div class="category-card">
                <img src="assets/uploads/placeholder.png" alt="Power">
                <div class="category-info">
                    <h3>Solar & Power</h3>
                </div>
            </div>
            <div class="category-card">
                <img src="assets/uploads/placeholder.png" alt="Audio">
                <div class="category-info">
                    <h3>Audio & Video</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- New Arrivals Section -->
    <section class="section" id="arrivals" style="background: #fff;">
        <div class="section-title">
            <h2>New Arrivals</h2>
            <p>Latest high-end gadgets just for you</p>
        </div>
        <div class="products-grid" id="arrivals-grid">
            <!-- Dynamically loaded -->
            <p style="text-align: center; grid-column: 1/-1;">Loading awesome tech...</p>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background: #fff; padding: 80px 8% 40px; border-top: 1px solid #eee;">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 60px; margin-bottom: 60px;">
            <div>
                <a href="#" class="logo" style="margin-bottom: 25px;">
                    <i class="ph-fill ph-circuitry"></i> SokoLink
                </a>
                <p style="color: var(--text-muted); line-height: 1.6; max-width: 300px;">
                    Experience the best in electronics with SokoLink. We provide authentic, high-quality technology
                    solutions across East Africa.
                </p>
            </div>
            <div>
                <h4 style="margin-bottom: 25px;">Quick Links</h4>
                <ul style="list-style: none; color: var(--text-muted); line-height: 2;">
                    <li>Home</li>
                    <li>Shop</li>
                    <li>Promotions</li>
                    <li>Contact</li>
                </ul>
            </div>
            <div>
                <h4 style="margin-bottom: 25px;">Support</h4>
                <ul style="list-style: none; color: var(--text-muted); line-height: 2;">
                    <li>Privacy Policy</li>
                    <li>Terms of Service</li>
                    <li>Shipping Info</li>
                    <li>FAQ</li>
                </ul>
            </div>
            <div>
                <h4 style="margin-bottom: 25px;">Stay Connected</h4>
                <p style="color: var(--text-muted); margin-bottom: 20px;">Subscribe to receive updates and exclusive
                    offers.</p>
                <div style="display: flex; gap: 10px;">
                    <input type="email" placeholder="Email Address"
                        style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; flex: 1;">
                    <button class="btn-premium" style="padding: 12px 20px;">Join</button>
                </div>
            </div>
        </div>
        <div
            style="border-top: 1px solid #eee; padding-top: 30px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
            &copy; 2026 SokoLink Electronics. All rights reserved.
        </div>
    </footer>

    <!-- Scripts -->
    <script src="assets/js/landing.js"></script>
</body>

</html>