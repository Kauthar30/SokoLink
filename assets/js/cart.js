// assets/js/cart.js

const CART_KEY = 'sokolink_guest_cart';

// Get base path for API calls
function getBasePath() {
    const path = window.location.pathname;
    if (path.includes('/user/')) {
        return '../';
    }
    return '';
}

// Get guest cart from localStorage
function getGuestCart() {
    const cart = localStorage.getItem(CART_KEY);
    return cart ? JSON.parse(cart) : [];
}

// Save guest cart to localStorage
function saveGuestCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
}

// Clear guest cart
function clearGuestCart() {
    localStorage.removeItem(CART_KEY);
}

// Add to cart
async function addToCart(productId, quantity = 1, productData = null) {
    const basePath = getBasePath();

    try {
        const response = await fetch(`${basePath}api/cart/add.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: quantity })
        });

        const result = await response.json();

        if (result.guest) {
            // Store in localStorage for guests
            let cart = getGuestCart();
            const existingIndex = cart.findIndex(item => item.product_id === productId);

            if (existingIndex > -1) {
                cart[existingIndex].quantity += quantity;
            } else {
                cart.push({
                    product_id: productId,
                    quantity: quantity,
                    ...productData
                });
            }

            saveGuestCart(cart);
            updateCartBadge();
            showToast('Added to cart!');
        } else if (result.success) {
            updateCartBadge(result.cart_count);
            showToast('Added to cart!');
        } else {
            showToast(result.message || 'Failed to add to cart', 'error');
        }
    } catch (error) {
        console.error('Add to cart error:', error);
        showToast('Error adding to cart', 'error');
    }
}

// Remove from cart
async function removeFromCart(cartId, isGuest = false, productId = null) {
    const basePath = getBasePath();

    if (isGuest) {
        let cart = getGuestCart();
        cart = cart.filter(item => item.product_id !== productId);
        saveGuestCart(cart);
        renderCartModal();
        updateCartBadge();
        return;
    }

    try {
        const response = await fetch(`${basePath}api/cart/remove.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart_id: cartId })
        });

        const result = await response.json();
        if (result.success) {
            renderCartModal();
            updateCartBadge();
        }
    } catch (error) {
        console.error('Remove from cart error:', error);
    }
}

// Update quantity
async function updateCartQuantity(cartId, quantity, isGuest = false, productId = null) {
    const basePath = getBasePath();

    if (isGuest) {
        let cart = getGuestCart();
        const index = cart.findIndex(item => item.product_id === productId);
        if (index > -1) {
            cart[index].quantity = quantity;
            saveGuestCart(cart);
            renderCartModal();
            updateCartBadge();
        }
        return;
    }

    try {
        const response = await fetch(`${basePath}api/cart/update.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart_id: cartId, quantity: quantity })
        });

        const result = await response.json();
        if (result.success) {
            renderCartModal();
        }
    } catch (error) {
        console.error('Update cart error:', error);
    }
}

// Fetch cart and render modal
async function renderCartModal() {
    const basePath = getBasePath();
    const cartItems = document.getElementById('cart-items');
    const cartSubtotal = document.getElementById('cart-subtotal');
    const cartEmpty = document.getElementById('cart-empty');
    const cartContent = document.getElementById('cart-content');

    if (!cartItems) return;

    try {
        const response = await fetch(`${basePath}api/cart/read.php`);
        const result = await response.json();

        let items = [];
        let subtotal = 0;
        let isGuest = false;

        if (result.guest) {
            isGuest = true;
            const guestCart = getGuestCart();
            items = guestCart;
            subtotal = guestCart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        } else {
            items = result.items || [];
            subtotal = result.subtotal || 0;
        }

        if (items.length === 0) {
            cartEmpty.style.display = 'block';
            cartContent.style.display = 'none';
            return;
        }

        cartEmpty.style.display = 'none';
        cartContent.style.display = 'block';

        cartItems.innerHTML = items.map(item => `
            <div class="cart-item" data-id="${isGuest ? item.product_id : item.cart_id}">
                <img src="${basePath}${item.image_url || 'assets/images/placeholder.png'}" alt="${item.name}">
                <div class="cart-item-info">
                    <h4>${item.name}</h4>
                    <p class="cart-item-price">TSH ${Number(item.price).toLocaleString()}</p>
                    <div class="cart-item-qty">
                        <button onclick="updateCartQuantity(${isGuest ? 'null' : item.cart_id}, ${item.quantity - 1}, ${isGuest}, ${isGuest ? item.product_id : 'null'})" ${item.quantity <= 1 ? 'disabled' : ''}>-</button>
                        <span>${item.quantity}</span>
                        <button onclick="updateCartQuantity(${isGuest ? 'null' : item.cart_id}, ${item.quantity + 1}, ${isGuest}, ${isGuest ? item.product_id : 'null'})">+</button>
                    </div>
                </div>
                <button class="cart-item-remove" onclick="removeFromCart(${isGuest ? 'null' : item.cart_id}, ${isGuest}, ${isGuest ? item.product_id : 'null'})">
                    <i class="ph ph-trash"></i>
                </button>
            </div>
        `).join('');

        cartSubtotal.textContent = `TSH ${Number(subtotal).toLocaleString()}`;

    } catch (error) {
        console.error('Fetch cart error:', error);
    }
}

// Update cart badge count
async function updateCartBadge(count = null) {
    const basePath = getBasePath();
    const badge = document.getElementById('cart-badge');
    if (!badge) return;

    if (count !== null) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'flex' : 'none';
        return;
    }

    try {
        const response = await fetch(`${basePath}api/cart/count.php`);
        const result = await response.json();

        let total = 0;
        if (result.guest) {
            const guestCart = getGuestCart();
            total = guestCart.reduce((sum, item) => sum + item.quantity, 0);
        } else {
            total = result.count || 0;
        }

        badge.textContent = total;
        badge.style.display = total > 0 ? 'flex' : 'none';
    } catch (error) {
        console.error('Cart count error:', error);
    }
}

// Open cart modal
function openCart() {
    const modal = document.getElementById('cart-modal');
    if (modal) {
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
        renderCartModal();
    }
}

// Close cart modal
function closeCart() {
    const modal = document.getElementById('cart-modal');
    if (modal) {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }
}

// Show toast notification
function showToast(message, type = 'success') {
    let toast = document.getElementById('cart-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'cart-toast';
        document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.className = `cart-toast ${type} show`;

    setTimeout(() => {
        toast.classList.remove('show');
    }, 2500);
}

// Proceed to checkout
function proceedToCheckout() {
    const basePath = getBasePath();
    const guestCart = getGuestCart();

    if (guestCart.length > 0) {
        // Guest with items - redirect to login with return URL
        window.location.href = `${basePath}pages/login.html?redirect=checkout`;
    } else {
        window.location.href = `${basePath}user/checkout.php`;
    }
}

// Merge guest cart on login (call this after successful login)
async function mergeGuestCart() {
    const guestCart = getGuestCart();
    if (guestCart.length === 0) return;

    const basePath = getBasePath();

    try {
        await fetch(`${basePath}api/cart/merge.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ items: guestCart })
        });

        clearGuestCart();
    } catch (error) {
        console.error('Merge cart error:', error);
    }
}

// Initialize cart on page load
document.addEventListener('DOMContentLoaded', () => {
    updateCartBadge();

    // Close modal on overlay click
    const modal = document.getElementById('cart-modal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeCart();
            }
        });
    }
});
