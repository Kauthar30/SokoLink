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

    // Password Modal Logic
    const passwordModal = document.getElementById('password-modal');
    const closePasswordModal = document.querySelector('.close-modal-password');
    const passwordForm = document.getElementById('password-form');

    if (closePasswordModal) {
        closePasswordModal.addEventListener('click', () => {
            passwordModal.classList.add('hidden');
        });
    }

    // Modal Background Click for Password Modal
    window.addEventListener('click', (event) => {
        if (event.target == passwordModal) {
            passwordModal.classList.add('hidden');
        }
    });

    if (passwordForm) {
        passwordForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const userId = document.getElementById('password-user-id').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            if (newPassword !== confirmPassword) {
                alert("Passwords do not match!");
                return;
            }

            if (newPassword.length < 6) {
                alert("Password must be at least 6 characters!");
                return;
            }

            try {
                const response = await fetch('../api/users/update_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ user_id: userId, new_password: newPassword })
                });
                const result = await response.json();

                if (result.success) {
                    alert('Password updated successfully!');
                    passwordModal.classList.add('hidden');
                    passwordForm.reset();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error(error);
                alert('Failed to update password');
            }
        });
    }

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

// Sales Chart - Using Real Data
async function initSalesChart() {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    try {
        const response = await fetch('../api/admin/sales_data.php');
        const result = await response.json();

        let labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        let data = [0, 0, 0, 0, 0, 0, 0];

        if (result.success) {
            labels = result.labels;
            data = result.data;
        }

        // Destroy existing chart if it exists
        if (window.salesChartInstance) {
            window.salesChartInstance.destroy();
        }

        window.salesChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sales (TSH)',
                    data: data,
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
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'TSH ' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' },
                        ticks: {
                            callback: function (value) {
                                return 'TSH ' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error loading sales chart:', error);
    }
}

// Recent Orders for Dashboard - Using Admin API
async function loadRecentOrders() {
    const container = document.getElementById('recent-orders-list');
    if (!container) return;

    try {
        const response = await fetch('../api/orders/admin_read.php');
        const result = await response.json();

        if (result.success && result.data && result.data.length > 0) {
            const orders = result.data.slice(0, 5); // Get first 5
            let html = `<table class="mini-table">
                <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Total</th></tr></thead>
                <tbody>`;

            orders.forEach(order => {
                const statusClass = order.status === 'completed' ? 'status-completed' :
                    order.status === 'cancelled' ? 'status-cancelled' : 'status-pending';
                html += `<tr>
                    <td>#${order.order_id}</td>
                    <td>${order.full_name}</td>
                    <td><span class="status-badge ${statusClass}">${order.status}</span></td>
                    <td>TSH ${Number(order.total_amount).toLocaleString()}</td>
                </tr>`;
            });

            html += '</tbody></table>';
            container.innerHTML = html;
        } else {
            container.innerHTML = '<p class="loading-text">No orders yet</p>';
        }
    } catch (error) {
        console.error('Error loading recent orders:', error);
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
                            <button class="btn-primary btn-sm" onclick="changePassword(${user.user_id})" style="margin-right: 5px;">
                                <i class="ph ph-key"></i> Key
                            </button>
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

window.changePassword = function (id) {
    const passwordModal = document.getElementById('password-modal');
    document.getElementById('password-user-id').value = id;
    document.getElementById('password-form').reset();
    passwordModal.classList.remove('hidden');
}

// --- Order Management ---
async function fetchOrders() {
    const listContainer = document.getElementById('orders-view');

    // Check if header exists, if not recreate it
    if (!listContainer.querySelector('h2')) {
        listContainer.innerHTML = '<h2>Orders</h2><div id="order-list"></div>';
    }

    const tableContainer = listContainer.querySelector('#order-list') || listContainer;
    tableContainer.innerHTML = '<p>Loading orders...</p>';

    try {
        const response = await fetch('../api/orders/admin_read.php');
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
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            result.data.forEach(order => {
                const statusClass = order.status === 'completed' ? 'status-completed' :
                    order.status === 'cancelled' ? 'status-cancelled' : 'status-pending';
                html += `
                    <tr>
                        <td>#${order.order_id}</td>
                        <td>
                            <strong>${order.full_name}</strong><br>
                            <small style="color: #666;">${order.email}</small>
                        </td>
                        <td>${order.item_count} item(s)</td>
                        <td>TSH ${Number(order.total_amount).toLocaleString()}</td>
                        <td>
                            <span class="status-badge ${statusClass}">${order.status}</span>
                        </td>
                        <td>${new Date(order.created_at).toLocaleDateString()}</td>
                        <td>
                            <button class="btn-view" onclick="viewOrderDetails(${order.order_id})" style="margin-right: 5px;">
                                <i class="ph ph-eye"></i> View
                            </button>
                            <select onchange="updateOrderStatus(${order.order_id}, this.value)" style="padding: 5px; border-radius: 5px; border: 1px solid #ddd;">
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

window.viewOrderDetails = async function (orderId) {
    try {
        const response = await fetch(`../api/orders/admin_details.php?id=${orderId}`);
        const result = await response.json();

        if (!result.success) {
            alert(result.message);
            return;
        }

        const order = result.order;
        const items = result.items;

        // Calculate subtotal
        let subtotal = 0;
        items.forEach(item => {
            subtotal += item.price_each * item.quantity;
        });

        // Build items HTML
        let itemsHtml = '';
        items.forEach(item => {
            const lineTotal = item.price_each * item.quantity;
            const imagePath = item.image_url ? `../assets/uploads/${item.image_url}` : '../assets/uploads/placeholder.png';
            itemsHtml += `
                <div style="display: flex; gap: 15px; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                    <img src="${imagePath}" alt="${item.name}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                    <div style="flex: 1;">
                        <strong>${item.name}</strong>
                        <div style="color: #666; font-size: 0.85rem;">Qty: ${item.quantity} × TSH ${Number(item.price_each).toLocaleString()}</div>
                    </div>
                    <div style="font-weight: 600;">TSH ${Number(lineTotal).toLocaleString()}</div>
                </div>
            `;
        });

        const statusClass = order.status === 'completed' ? 'status-completed' :
            order.status === 'cancelled' ? 'status-cancelled' : 'status-pending';

        const modalHtml = `
            <div id="order-modal-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                <div style="background: white; border-radius: 16px; max-width: 700px; width: 90%; max-height: 90vh; overflow-y: auto; padding: 30px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                        <h2 style="margin: 0;">Order #${order.order_id}</h2>
                        <button onclick="closeOrderModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                        <div style="background: #f8fafc; padding: 20px; border-radius: 12px;">
                            <h4 style="margin: 0 0 15px 0; color: #00509E;"><i class="ph ph-user"></i> Customer Details</h4>
                            <p style="margin: 5px 0;"><strong>Name:</strong> ${order.full_name}</p>
                            <p style="margin: 5px 0;"><strong>Email:</strong> ${order.email}</p>
                            <p style="margin: 5px 0;"><strong>Phone:</strong> ${order.phone || 'Not provided'}</p>
                        </div>
                        <div style="background: #f8fafc; padding: 20px; border-radius: 12px;">
                            <h4 style="margin: 0 0 15px 0; color: #00509E;"><i class="ph ph-map-pin"></i> Delivery Address</h4>
                            <p style="margin: 0;">${order.address || 'No address provided'}</p>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px;">
                        <h4 style="margin: 0 0 15px 0;"><i class="ph ph-package"></i> Order Items</h4>
                        ${itemsHtml}
                    </div>

                    <div style="background: #f0f9ff; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span>Subtotal:</span>
                            <span>TSH ${Number(subtotal).toLocaleString()}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span>Shipping:</span>
                            <span>TSH 5,000</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.2rem; padding-top: 10px; border-top: 2px solid #00509E;">
                            <span>Total:</span>
                            <span>TSH ${Number(order.total_amount).toLocaleString()}</span>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="color: #666;">Status:</span>
                            <span class="status-badge ${statusClass}" style="margin-left: 10px;">${order.status}</span>
                        </div>
                        <div>
                            <span style="color: #666;">Order Date:</span>
                            <strong style="margin-left: 10px;">${new Date(order.created_at).toLocaleString()}</strong>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if any
        const existingModal = document.getElementById('order-modal-overlay');
        if (existingModal) existingModal.remove();

        document.body.insertAdjacentHTML('beforeend', modalHtml);

    } catch (error) {
        console.error('Error:', error);
        alert('Failed to load order details.');
    }
}

window.closeOrderModal = function () {
    const modal = document.getElementById('order-modal-overlay');
    if (modal) modal.remove();
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
            fetchOrders(); // Refresh the list
            fetchDashboardStats();
        } else {
            alert('Failed to update status');
        }
    } catch (error) {
        console.error(error);
        alert('Error updating status');
    }
}
