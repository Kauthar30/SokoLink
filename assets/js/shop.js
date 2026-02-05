// assets/js/shop.js
document.addEventListener('DOMContentLoaded', () => {
    // Get category from URL
    const urlParams = new URLSearchParams(window.location.search);
    const categoryId = urlParams.get('category_id');

    // Load Sidebar & Products
    loadCategories(categoryId);
    loadProducts(categoryId);
});

async function loadCategories(activeId) {
    const list = document.getElementById('category-list');
    try {
        const response = await fetch('api/products/categories.php');
        const result = await response.json();

        if (result.success) {
            let html = `<li><a href="shop.php" class="category-link ${!activeId ? 'active' : ''}">All Products</a></li>`;

            result.data.forEach(cat => {
                const isActive = activeId == cat.category_id ? 'active' : '';
                html += `<li><a href="shop.php?category_id=${cat.category_id}" class="category-link ${isActive}">${cat.name}</a></li>`;

                // Update page title if this is the active category
                if (isActive) {
                    document.getElementById('page-title').innerText = cat.name;
                    document.querySelector('.breadcrumb').innerText = `Home / Shop / ${cat.name}`;
                }
            });
            list.innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        list.innerHTML = '<li>Error loading categories</li>';
    }
}

async function loadProducts(categoryId) {
    const grid = document.getElementById('products-grid');
    grid.innerHTML = '<p>Loading products...</p>';

    let url = 'api/products/read.php';
    if (categoryId) {
        url += `?category_id=${categoryId}`;
    }

    try {
        const response = await fetch(url);
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            let html = '';
            result.data.forEach(product => {
                const priceFormatted = parseInt(product.price).toLocaleString();
                const imagePath = product.image_url ? `assets/uploads/${product.image_url}` : 'assets/uploads/placeholder.png';

                html += `
                    <div class="product-card">
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
            grid.innerHTML = html;
        } else {
            grid.innerHTML = '<p>No products found in this category.</p>';
        }
    } catch (error) {
        console.error('Error loading products:', error);
        grid.innerHTML = '<p>Error loading products.</p>';
    }
}

// addToCart is now provided by cart.js
