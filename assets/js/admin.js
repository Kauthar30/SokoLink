// Check Session on Load
document.addEventListener('DOMContentLoaded', async () => {
    const loadingScreen = document.getElementById('auth-loading');
    const adminContainer = document.getElementById('admin-container');

    try {
        const response = await fetch('../api/auth/check_session.php');
        const session = await response.json();

        if (!session.logged_in || session.role !== 'admin') {
            window.location.href = '../pages/login.html';
            return; // Stop execution
        }

        // Set User Name
        document.getElementById('admin-name').innerText = session.full_name;

        // Hide loading, show dashboard
        if (loadingScreen) loadingScreen.style.display = 'none';
        if (adminContainer) adminContainer.style.display = 'flex';

        // Initialize Admin Logic
        initAdminDashboard();

    } catch (error) {
        console.error('Session check failed:', error);
        // If we can't verify session, redirect to login for security/stability
        window.location.href = '../pages/login.html';
    }
});

function initAdminDashboard() {
    // Mobile Sidebar Toggle
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    if (mobileMenuToggle && sidebar) {
        mobileMenuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
        });
    }

    const sidebarCloseBtn = document.getElementById('sidebar-close-btn');
    if (sidebarCloseBtn) {
        sidebarCloseBtn.addEventListener('click', () => {
            sidebar.classList.remove('active');
            if (sidebarOverlay) sidebarOverlay.classList.remove('active');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
    }

    // Navigation
    const navLinks = document.querySelectorAll('.sidebar nav ul li a');
    const sections = document.querySelectorAll('main section');

    navLinks.forEach(link => {
        if (link.id === 'logout-btn') return; // Skip logout in this loop

        link.addEventListener('click', (e) => {
            e.preventDefault();
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');

            const targetId = link.id.replace('nav-', '') + '-view';
            if (targetId === 'products-view') {
                fetchProducts();
            } else if (targetId === 'users-view') {
                fetchUsers();
            } else if (targetId === 'orders-view') {
                fetchOrders();
            }

            sections.forEach(section => {
                section.classList.add('hidden');
                if (section.id === targetId) {
                    section.classList.remove('hidden');
                }
            });

            // Close sidebar on mobile after navigation
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                if (sidebarOverlay) sidebarOverlay.classList.remove('active');
            }

            // Remember current page
            localStorage.setItem('sokolink_active_page', link.id);
        });
    });

    // Restore last active page
    const lastPage = localStorage.getItem('sokolink_active_page');
    if (lastPage) {
        const activeLink = document.getElementById(lastPage);
        if (activeLink) {
            activeLink.click();
        }
    }

    // Explicit Logout Listener
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('Logout clicked');
            handleLogout();
        });
    }

    // Product Modal Logic
    const productModal = document.getElementById('product-modal');
    const addProductBtn = document.getElementById('add-product-btn');
    const closeModal = document.querySelector('.close-modal');
    const productForm = document.getElementById('product-form');
    const productModalTitle = document.getElementById('product-modal-title');
    const productSubmitBtn = document.getElementById('product-submit-btn');

    if (addProductBtn) {
        addProductBtn.addEventListener('click', () => {
            productModalTitle.innerText = 'Add New Product';
            productSubmitBtn.innerText = 'Save Product';
            productForm.reset();
            document.getElementById('product-id').value = '';
            document.getElementById('current-image-info').innerText = '';
            productModal.classList.remove('hidden');
        });
    }

    if (productForm) {
        productForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(productForm);
            const productId = document.getElementById('product-id').value;
            const url = productId ? '../api/products/update.php' : '../api/products/create.php';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    alert(productId ? 'Product updated successfully!' : 'Product added successfully!');
                    productModal.classList.add('hidden');
                    productForm.reset();
                    fetchProducts();
                    fetchDashboardStats();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error saving product:', error);
                alert('An error occurred.');
            }
        });
    }

    if (closeModal) {
        closeModal.addEventListener('click', () => {
            productModal.classList.add('hidden');
        });
    }

    // Category Management Logic
    const manageCategoriesBtn = document.getElementById('manage-categories-btn');
    const backToProductsBtn = document.getElementById('back-to-products-btn');
    const productsView = document.getElementById('products-view');
    const categoriesView = document.getElementById('categories-view');
    const addCategoryBtn = document.getElementById('add-category-btn');
    const categoryModal = document.getElementById('category-modal');
    const closeCategoryModal = document.querySelector('.close-modal-category');
    const categoryForm = document.getElementById('category-form');
    const categoryModalTitle = document.getElementById('category-modal-title');
    const categorySubmitBtn = document.getElementById('category-submit-btn');

    if (manageCategoriesBtn) {
        manageCategoriesBtn.addEventListener('click', () => {
            productsView.classList.add('hidden');
            categoriesView.classList.remove('hidden');
            fetchCategories();
        });
    }

    if (backToProductsBtn) {
        backToProductsBtn.addEventListener('click', () => {
            categoriesView.classList.add('hidden');
            productsView.classList.remove('hidden');
            fetchProducts();
        });
    }

    if (addCategoryBtn) {
        addCategoryBtn.addEventListener('click', () => {
            categoryModalTitle.innerText = 'Add New Category';
            categorySubmitBtn.innerText = 'Save Category';
            categoryForm.reset();
            document.getElementById('category-id').value = '';
            categoryModal.classList.remove('hidden');
        });
    }

    if (closeCategoryModal) {
        closeCategoryModal.addEventListener('click', () => {
            categoryModal.classList.add('hidden');
        });
    }

    if (categoryForm) {
        categoryForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const categoryId = document.getElementById('category-id').value;
            const name = document.getElementById('category-name').value;
            const url = categoryId ? '../api/products/category_update.php' : '../api/products/category_create.php';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ category_id: categoryId, name })
                });
                const result = await response.json();

                if (result.success) {
                    alert(categoryId ? 'Category updated successfully!' : 'Category added successfully!');
                    categoryModal.classList.add('hidden');
                    categoryForm.reset();
                    fetchCategories();
                    loadCategories(); // Refresh product dropdowns
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error saving category:', error);
            }
        });
    }

    window.onclick = (event) => {
        if (event.target == productModal) {
            productModal.classList.add('hidden');
        }
        if (event.target == categoryModal) {
            categoryModal.classList.add('hidden');
        }
    };

    // Initial Data Fetch
    fetchDashboardStats();
    loadCategories();

    // User Modal Logic
    const userModal = document.getElementById('create-user-modal');
    const createUserBtn = document.getElementById('create-user-btn');
    const closeUserModal = document.querySelector('.close-modal-user');
    const createUserForm = document.getElementById('create-user-form');

    if (createUserBtn) {
        createUserBtn.addEventListener('click', () => {
            userModal.classList.remove('hidden');
        });
    }

    if (closeUserModal) {
        closeUserModal.addEventListener('click', () => {
            userModal.classList.add('hidden');
        });
    }

    // Modal Background Click for User Modal
    window.addEventListener('click', (event) => {
        if (event.target == userModal) {
            userModal.classList.add('hidden');
        }
    });

    if (createUserForm) {
        createUserForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(createUserForm);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('../api/users/create.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await response.json();

                if (result.success) {
                    alert('User created successfully!');
                    userModal.classList.add('hidden');
                    createUserForm.reset();
                    fetchUsers();
                    fetchDashboardStats();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error creating user:', error);
            }
        });
    }
}

async function fetchDashboardStats() {
    try {
        const response = await fetch('../api/admin/stats.php');
        const result = await response.json();

        if (result.success) {
            document.getElementById('total-orders').innerText = result.data.orders;
            document.getElementById('total-products').innerText = result.data.products;
            document.getElementById('total-users').innerText = result.data.users;

            // Revenue (if available, otherwise mock)
            const revenue = result.data.revenue || 0;
            document.getElementById('total-revenue').innerText = 'TSH ' + parseFloat(revenue).toLocaleString();
        }
    } catch (error) {
        console.error('Error fetching stats:', error);
    }

    // Load dashboard widgets
    initSalesChart();
    loadRecentOrders();
    loadTopProducts();
    loadRecentActivity();
}

/**
 * Load Recent Activity Feed
 */
async function loadRecentActivity() {
    const container = document.getElementById('activity-feed');
    if (!container) return;

    try {
        const response = await fetch('../api/admin/activity.php');
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            let html = '';

            result.data.forEach(item => {
                let iconClass = 'ph-info';
                let iconColor = '#1976d2';
                let iconBg = '#e3f2fd';

                // Determine icon based on type
                if (item.type === 'user') {
                    iconClass = 'ph-user';
                    iconColor = '#1976d2'; // Blue
                    iconBg = '#e3f2fd';
                } else if (item.type === 'order') {
                    iconClass = 'ph-shopping-bag';
                    iconColor = '#7b1fa2'; // Purple
                    iconBg = '#f3e5f5';
                } else if (item.type === 'product') {
                    iconClass = 'ph-package';
                    iconColor = '#f57c00'; // Orange
                    iconBg = '#fff3e0';
                }

                const timeAgo = getTimeAgo(new Date(item.created_at));

                html += `
                    <div class="activity-item">
                        <div class="activity-icon" style="background: ${iconBg};">
                            <i class="ph ${iconClass}" style="color: ${iconColor};"></i>
                        </div>
                        <div class="activity-content">
                            <p>${item.message}</p>
                            <span class="activity-time">${timeAgo}</span>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        } else {
            container.innerHTML = '<p class="text-muted" style="padding:15px; text-align:center;">No recent activity</p>';
        }
    } catch (error) {
        console.error('Error loading activity:', error);
        container.innerHTML = '<p class="text-danger" style="padding:15px; text-align:center;">Failed to load activity</p>';
    }
}

function getTimeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    let interval = Math.floor(seconds / 31536000);

    if (interval > 1) return interval + " years ago";
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) return interval + " months ago";
    interval = Math.floor(seconds / 86400);
    if (interval > 1) return interval + " days ago";
    interval = Math.floor(seconds / 3600);
    if (interval > 1) return interval + " hours ago";
    interval = Math.floor(seconds / 60);
    if (interval > 1) return interval + " mins ago";
    return Math.floor(seconds) + " seconds ago";
}

// Sales Chart
function initSalesChart() {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Sales',
                data: [1200, 1900, 1500, 2100, 1800, 2400, 2000],
                borderColor: '#00509E',
                backgroundColor: 'rgba(0, 80, 158, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#00509E',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f0f0f0' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

// Recent Orders for Dashboard
async function loadRecentOrders() {
    const container = document.getElementById('recent-orders-list');
    if (!container) return;

    try {
        const response = await fetch('../api/orders/read.php');
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            const orders = result.data.slice(0, 5); // Get first 5
            let html = `<table class="mini-table">
                <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Total</th></tr></thead>
                <tbody>`;

            orders.forEach(order => {
                html += `<tr>
                    <td>#${order.order_id}</td>
                    <td>${order.full_name}</td>
                    <td><span class="status-badge ${order.status}">${order.status}</span></td>
                    <td>TSH ${order.total_amount}</td>
                </tr>`;
            });

            html += '</tbody></table>';
            container.innerHTML = html;
        } else {
            container.innerHTML = '<p class="loading-text">No recent orders</p>';
        }
    } catch (error) {
        container.innerHTML = '<p class="loading-text">Could not load orders</p>';
    }
}

// Top Products for Dashboard
async function loadTopProducts() {
    const container = document.getElementById('top-products-list');
    if (!container) return;

    try {
        const response = await fetch('../api/products/read.php');
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            const products = result.data.slice(0, 5); // Get first 5
            let html = '';

            products.forEach(product => {
                const imagePath = product.image_url ? `../assets/uploads/${product.image_url}` : '../assets/uploads/placeholder.png';
                html += `<div class="product-item">
                    <img src="${imagePath}" alt="${product.name}">
                    <div class="product-item-info">
                        <h4>${product.name}</h4>
                        <span>${product.category_name || 'Uncategorized'}</span>
                    </div>
                    <span class="product-item-price">TSH ${product.price}</span>
                </div>`;
            });

            container.innerHTML = html;
        } else {
            container.innerHTML = '<p class="loading-text">No products yet</p>';
        }
    } catch (error) {
        container.innerHTML = '<p class="loading-text">Could not load products</p>';
    }
}

async function fetchProducts() {
    const listContainer = document.getElementById('product-list');
    listContainer.innerHTML = '<p>Loading products...</p>';

    try {
        const response = await fetch('../api/products/read.php');
        const result = await response.json();

        if (result.success) {
            if (result.count === 0) {
                listContainer.innerHTML = '<p>No products found.</p>';
                return;
            }

            let html = `
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            result.data.forEach(product => {
                html += `
                    <tr>
                        <td>${product.product_id}</td>
                        <td>
                            <div class="product-item">
                                <img src="../assets/uploads/${product.image_url || 'placeholder.png'}" alt="${product.name}" onerror="this.src='../assets/uploads/placeholder.png'">
                                <div class="product-item-info">
                                    <h4>${product.name}</h4>
                                    <p>${product.category_name}</p>
                                </div>
                            </div>
                        </td>
                        <td>${product.stock} Units</td>
                        <td>TSH ${parseInt(product.price).toLocaleString()}</td>
                        <td>
                            <button class="btn-primary btn-sm" onclick="editProduct(${product.product_id})"><i class="ph ph-pencil"></i></button>
                            <button class="btn-danger btn-sm" onclick="deleteProduct(${product.product_id})"><i class="ph ph-trash"></i></button>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table>';
            listContainer.innerHTML = html;
        } else {
            listContainer.innerHTML = '<p>Error loading products.</p>';
        }
    } catch (error) {
        console.error('Error fetching products:', error);
        listContainer.innerHTML = '<p>Error loading products.</p>';
    }
}

async function loadCategories() {
    try {
        const response = await fetch('../api/products/categories.php');
        const result = await response.json();
        const select = document.getElementById('product-category');

        if (result.success && select) {
            select.innerHTML = '<option value="">Select Category</option>';
            result.data.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.category_id;
                option.textContent = cat.name;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

async function fetchCategories() {
    const listContainer = document.getElementById('category-list');
    listContainer.innerHTML = '<p>Loading categories...</p>';

    try {
        const response = await fetch('../api/products/categories.php');
        const result = await response.json();

        if (result.success) {
            if (result.data.length === 0) {
                listContainer.innerHTML = '<p>No categories found.</p>';
                return;
            }

            let html = `
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            result.data.forEach(cat => {
                html += `
                    <tr>
                        <td>${cat.category_id}</td>
                        <td>${cat.name}</td>
                        <td>${new Date(cat.created_at).toLocaleDateString()}</td>
                        <td>
                            <button class="btn-primary btn-sm" onclick="editCategory(${cat.category_id})"><i class="ph ph-pencil"></i></button>
                            <button class="btn-danger btn-sm" onclick="deleteCategory(${cat.category_id})"><i class="ph ph-trash"></i></button>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table>';
            listContainer.innerHTML = html;
        } else {
            listContainer.innerHTML = '<p>Error loading categories.</p>';
        }
    } catch (error) {
        console.error('Error fetching categories:', error);
        listContainer.innerHTML = '<p>Error loading categories.</p>';
    }
}

window.deleteCategory = async function (id) {
    if (!confirm('Are you sure? Deleting a category will also delete all products associated with it!')) return;

    try {
        const response = await fetch(`../api/products/category_delete.php?id=${id}`, { method: 'POST' });
        const result = await response.json();
        if (result.success) {
            alert('Category deleted!');
            fetchCategories();
            loadCategories(); // Update product dropdown
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error(error);
        alert('Delete failed');
    }
};

async function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product?')) return;

    try {
        const response = await fetch(`../api/products/delete.php?id=${id}`, {
            method: 'GET'
        });
        const result = await response.json();

        if (result.success) {
            alert('Product deleted successfully!');
            fetchProducts();
            fetchDashboardStats();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error deleting product:', error);
    }
}

window.editProduct = async function (id) {
    try {
        const response = await fetch(`../api/products/read_single.php?id=${id}`);
        const result = await response.json();

        if (result.success) {
            const product = result.data;
            document.getElementById('product-id').value = product.product_id;
            document.getElementById('product-name').value = product.name;
            document.getElementById('product-category').value = product.category_id;
            document.getElementById('product-price').value = product.price;
            document.getElementById('product-stock').value = product.stock;
            document.getElementById('product-description').value = product.description;

            document.getElementById('product-modal-title').innerText = 'Edit Product';
            document.getElementById('product-submit-btn').innerText = 'Update Product';
            document.getElementById('current-image-info').innerText = product.image_url ? `Current: ${product.image_url}` : 'No image set';

            document.getElementById('product-modal').classList.remove('hidden');
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error(error);
    }
};

window.editCategory = async function (id) {
    try {
        const response = await fetch(`../api/products/category_read_single.php?id=${id}`);
        const result = await response.json();

        if (result.success) {
            const category = result.data;
            document.getElementById('category-id').value = category.category_id;
            document.getElementById('category-name').value = category.name;

            document.getElementById('category-modal-title').innerText = 'Edit Category';
            document.getElementById('category-submit-btn').innerText = 'Update Category';

            document.getElementById('category-modal').classList.remove('hidden');
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error(error);
    }
};

window.deleteProduct = deleteProduct;

async function handleLogout() {
    if (confirm('Are you sure you want to logout?')) {
        try {
            await fetch('../api/auth/logout.php');
            localStorage.removeItem('sokolink_active_page'); // Clear last page
            window.location.href = '../pages/login.html';
        } catch (error) {
            console.error('Logout failed:', error);
        }
    }
}


// --- User Management ---
window.deleteUser = async function (id) {
    if (!confirm('Are you sure you want to delete this user?')) return;

    try {
        const response = await fetch(`../api/users/delete.php?id=${id}`, { method: 'POST' });
        const result = await response.json();
        if (result.success) {
            alert('User deleted!');
            fetchUsers();
            fetchDashboardStats();
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error(error);
        alert('Delete failed');
    }
}

async function fetchUsers() {
    const listContainer = document.getElementById('user-list');
    listContainer.innerHTML = '<p>Loading users...</p>';

    try {
        const response = await fetch('../api/users/read.php');
        const result = await response.json();

        if (result.success) {
            let html = `
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            result.data.forEach(user => {
                html += `
                    <tr>
                        <td>${user.full_name}</td>
                        <td>${user.email}</td>
                        <td>${user.role}</td>
                        <td>${new Date(user.created_at).toLocaleDateString()}</td>
                        <td>
                            <button class="btn-danger btn-sm" onclick="deleteUser(${user.user_id})">Delete</button>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table>';
            listContainer.innerHTML = html;
        } else {
            listContainer.innerHTML = '<p>Error loading users.</p>';
        }
    } catch (error) {
        console.error('Error fetching users:', error);
        listContainer.innerHTML = '<p>Error loading users.</p>';
    }
}

// --- Order Management ---
async function fetchOrders() {
    const listContainer = document.getElementById('orders-view');
    // Note: We are injecting directly into the section for simplicity as there was no #order-list container in HTML, 
    // but better practice is to have a container. I will overwrite the content after the header.

    // Check if header exists, if not recreate it
    if (!listContainer.querySelector('h2')) {
        listContainer.innerHTML = '<h2>Orders</h2><div id="order-list"></div>';
    }

    const tableContainer = listContainer.querySelector('#order-list') || listContainer;
    tableContainer.innerHTML = '<p>Loading orders...</p>';

    try {
        const response = await fetch('../api/orders/read.php');
        const result = await response.json();

        if (result.success) {
            if (result.count === 0) {
                tableContainer.innerHTML = '<p>No orders found.</p>';
                return;
            }

            let html = `
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            result.data.forEach(order => {
                html += `
                    <tr>
                        <td>#${order.order_id}</td>
                        <td>${order.full_name}</td>
                        <td>TSH ${order.total_amount}</td>
                        <td>
                            <span class="status-badge ${order.status}">${order.status}</span>
                        </td>
                        <td>${new Date(order.created_at).toLocaleDateString()}</td>
                        <td>
                            <select onchange="updateOrderStatus(${order.order_id}, this.value)">
                                <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="completed" ${order.status === 'completed' ? 'selected' : ''}>Completed</option>
                                <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                            </select>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table>';
            tableContainer.innerHTML = html;
        } else {
            tableContainer.innerHTML = '<p>Error loading orders.</p>';
        }
    } catch (error) {
        console.error('Error fetching orders:', error);
        tableContainer.innerHTML = '<p>Error loading orders.</p>';
    }
}

window.updateOrderStatus = async function (id, status) {
    try {
        const response = await fetch('../api/orders/update_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: id, status: status })
        });
        const result = await response.json();

        if (result.success) {
            // Optional: alert('Status updated');
            fetchDashboardStats();
        } else {
            alert('Failed to update status');
        }
    } catch (error) {
        console.error(error);
        alert('Error updating status');
    }
}
