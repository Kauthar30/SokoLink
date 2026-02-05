document.addEventListener('DOMContentLoaded', () => {
    // Navbar Scroll Effect
    const nav = document.querySelector('nav');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    });

    // Fetch Products & Categories
    fetchNewArrivals();
    fetchCategories();

    // Scroll Reveal Animation (Simulated)
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('reveal');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.section').forEach(section => {
        section.classList.add('reveal-init');
        observer.observe(section);
    });
});

async function fetchCategories() {
    const list = document.getElementById('categories-grid');
    if (!list) return;

    try {
        const response = await fetch('api/products/categories.php');
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            let html = '';
            // Display first 4 categories for homepage
            const categories = result.data.slice(0, 4);

            categories.forEach(cat => {
                html += `
                    <div class="category-card" onclick="window.location.href='shop.php?category_id=${cat.category_id}'">
                        <img src="assets/uploads/placeholder.png" alt="${cat.name}">
                        <div class="category-info">
                            <h3>${cat.name}</h3>
                        </div>
                    </div>
                `;
            });
            list.innerHTML = html;
        } else {
            list.innerHTML = '<p style="grid-column: 1/-1; text-align: center;">No categories found.</p>';
        }
    } catch (error) {
        console.error('Error fetching categories:', error);
        list.innerHTML = '<p style="grid-column: 1/-1; text-align: center;">Could not load categories</p>';
    }
}

async function fetchNewArrivals() {
    const container = document.getElementById('arrivals-grid');
    if (!container) return;

    try {
        const response = await fetch('api/products/read.php');
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            // Display first 8 products
            const products = result.data.slice(0, 8);
            let html = '';

            products.forEach(product => {
                const priceFormatted = parseInt(product.price).toLocaleString();
                const imagePath = product.image_url ? `assets/uploads/${product.image_url}` : 'assets/uploads/placeholder.png';

                html += `
                    <div class="product-card">
                        <span class="product-badge">New</span>
                        <div class="product-img-wrapper">
                            <img src="${imagePath}" alt="${product.name}" onerror="this.src='assets/uploads/placeholder.png'">
                        </div>
                        <span class="product-cat">${product.category_name || 'Electronics'}</span>
                        <h3>${product.name}</h3>
                        <div class="product-footer">
                            <span class="price">TSH ${priceFormatted}</span>
                            <div class="add-cart" onclick="addToCart(${product.product_id})">
                                <i class="ph-bold ph-shopping-cart-simple"></i>
                            </div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        } else {
            container.innerHTML = '<p style="text-align:center; grid-column: 1/-1;">No products found</p>';
        }
    } catch (error) {
        console.error('Error fetching arrivals:', error);
        container.innerHTML = '<p style="text-align:center; grid-column: 1/-1;">Could not load products</p>';
    }
}

// addToCart is now provided by cart.js

// Language Switcher Data
const translations = [
    {
        tagline: "Innovation at your fingertips",
        title: "<span>The Future of</span> <span>Electronics.</span>",
        desc: "Discover a curated collection of high-performance tech designed to elevate your lifestyle. From smartphones to solar solutions, we bring the best to you."
    },
    {
        tagline: "Ubunifu mkononi mwako",
        title: "<span>Teknolojia Inayoleta</span> <span>Maisha Bora</span>",
        desc: "Gundua mkusanyiko wa vifaa bora vya kielektroniki vilivyochaguliwa kwa makini ili kuboresha maisha yako. Kuanzia simu za mkononi hadi mifumo ya sola, tunakuletea teknolojia bora kwa mahitaji yako ya kila siku."
    }
];

let currentLangIndex = 0;

function switchLanguage() {
    const taglineEl = document.getElementById('hero-tagline');
    const titleEl = document.getElementById('hero-title');
    const descEl = document.getElementById('hero-desc');

    if (!taglineEl || !titleEl || !descEl) return;

    // Add hidden class to fade out
    taglineEl.classList.add('text-hidden');
    titleEl.classList.add('text-hidden');
    descEl.classList.add('text-hidden');

    setTimeout(() => {
        currentLangIndex = (currentLangIndex + 1) % translations.length;
        const data = translations[currentLangIndex];

        taglineEl.innerHTML = data.tagline;
        titleEl.innerHTML = data.title;
        descEl.innerHTML = data.desc;

        // Remove hidden class to fade in
        taglineEl.classList.remove('text-hidden');
        titleEl.classList.remove('text-hidden');
        descEl.classList.remove('text-hidden');
    }, 600); // Match CSS transition time
}

// Start Cycle
setInterval(switchLanguage, 5000);
