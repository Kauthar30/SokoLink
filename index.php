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
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <div class="hero-tagline text-fade" id="hero-tagline">
                Innovation at your fingertips
            </div>
            <h1 id="hero-title" class="text-fade"><span>The Future of</span> <span>Electronics.</span></h1>
            <p id="hero-desc" class="text-fade">Discover a curated collection of high-performance tech designed to
                elevate your lifestyle. From smartphones to solar solutions, we bring the best to you.</p>
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
        <div class="categories-grid" id="categories-grid">
            <!-- Dynamically Loaded -->
            <p style="grid-column: 1/-1; text-align: center;">Loading categories...</p>
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

    <!-- Stats Counter Section -->
    <section class="stats-section">
        <div class="stat-item">
            <span class="stat-number">10K+</span>
            <span class="stat-label">Happy Customers</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">500+</span>
            <span class="stat-label">Products Available</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">24/7</span>
            <span class="stat-label">Customer Support</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">15+</span>
            <span class="stat-label">Years Experience</span>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="section why-choose" id="about">
        <div class="why-choose-content">
            <div class="why-text">
                <span class="section-badge">Why SokoLink</span>
                <h2>East Africa's Most Trusted Electronics Partner</h2>
                <p>We believe in providing more than just products. Our commitment to quality, authenticity, and
                    customer satisfaction sets us apart from the rest.</p>

                <div class="why-features">
                    <div class="why-feature-item">
                        <i class="ph-fill ph-seal-check"></i>
                        <div>
                            <h4>100% Authentic Products</h4>
                            <p>Every item is sourced directly from authorized distributors</p>
                        </div>
                    </div>
                    <div class="why-feature-item">
                        <i class="ph-fill ph-lightning"></i>
                        <div>
                            <h4>Fast Nationwide Delivery</h4>
                            <p>Get your orders delivered across Tanzania within 48 hours</p>
                        </div>
                    </div>
                    <div class="why-feature-item">
                        <i class="ph-fill ph-handshake"></i>
                        <div>
                            <h4>Warranty & After-Sales</h4>
                            <p>Comprehensive warranty coverage with dedicated support</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="why-image">
                <img src="assets/images/hero_natural.png" alt="SokoLink Store">
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section testimonials-section" style="background: #fff;">
        <div class="section-title">
            <h2>What Our Customers Say</h2>
            <p>Real reviews from real customers across Tanzania</p>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                </div>
                <p>"Best electronics store in Dar! The Samsung phone I bought is 100% original and came with full
                    warranty. Will definitely buy again."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">JM</div>
                    <div>
                        <h5>John Mwakasege</h5>
                        <span>Dar es Salaam</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                </div>
                <p>"Ordered a solar panel system for my shop. Delivery was super fast and installation support was
                    excellent. Highly recommended!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">FA</div>
                    <div>
                        <h5>Fatuma Ali</h5>
                        <span>Arusha</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star"></i>
                    <i class="ph-fill ph-star-half"></i>
                </div>
                <p>"The customer service is amazing! They helped me choose the perfect laptop for my business. Prices
                    are fair and products are genuine."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">BK</div>
                    <div>
                        <h5>Baraka Kimaro</h5>
                        <span>Mwanza</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Brands Section -->
    <section class="brands-section">
        <div class="section-title" style="margin-bottom: 40px;">
            <h2>Trusted Brands We Carry</h2>
        </div>
        <div class="brands-grid">
            <div class="brand-item">Samsung</div>
            <div class="brand-item">Apple</div>
            <div class="brand-item">Sony</div>
            <div class="brand-item">LG</div>
            <div class="brand-item">Hisense</div>
            <div class="brand-item">JBL</div>
        </div>
    </section>

    <!-- Newsletter CTA Section -->
    <section class="newsletter-section">
        <div class="newsletter-content">
            <i class="ph-fill ph-envelope-simple"></i>
            <h2>Get Exclusive Deals & Updates</h2>
            <p>Subscribe to our newsletter and be the first to know about new arrivals, special offers, and tech tips!
            </p>
            <form class="newsletter-form" onsubmit="return false;">
                <input type="email" placeholder="Enter your email address">
                <button type="submit" class="btn-premium">Subscribe <i class="ph-bold ph-arrow-right"></i></button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="assets/js/landing.js"></script>
</body>

</html>