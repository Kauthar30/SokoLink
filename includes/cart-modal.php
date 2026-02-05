<!-- Cart Modal -->
<div id="cart-modal" class="cart-modal">
    <div class="cart-modal-content">
        <div class="cart-header">
            <h3><i class="ph ph-shopping-cart"></i> Your Cart</h3>
            <button class="cart-close" onclick="closeCart()"><i class="ph ph-x"></i></button>
        </div>

        <div id="cart-empty" class="cart-empty">
            <i class="ph ph-basket" style="font-size: 4rem; color: #ccc;"></i>
            <p>Your cart is empty</p>
            <a href="<?php echo $base_path; ?>shop.php" class="btn-premium" style="margin-top: 20px;">Start Shopping</a>
        </div>

        <div id="cart-content" class="cart-content" style="display: none;">
            <div id="cart-items" class="cart-items">
                <!-- Items loaded dynamically -->
            </div>

            <div class="cart-footer">
                <div class="cart-subtotal">
                    <span>Subtotal:</span>
                    <span id="cart-subtotal">TSH 0</span>
                </div>
                <button class="btn-premium" onclick="proceedToCheckout()" style="width: 100%;">
                    Proceed to Checkout <i class="ph ph-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Cart Modal Styles */
    .cart-modal {
        position: fixed;
        top: 0;
        right: -100%;
        width: 100%;
        height: 100%;
        z-index: 99999;
        transition: right 0.3s ease;
    }

    .cart-modal.open {
        right: 0;
        background: rgba(0, 0, 0, 0.5);
    }

    .cart-modal-content {
        position: absolute;
        right: 0;
        top: 0;
        width: 100%;
        max-width: 420px;
        height: 100%;
        background: white;
        display: flex;
        flex-direction: column;
        box-shadow: -5px 0 30px rgba(0, 0, 0, 0.1);
    }

    .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .cart-header h3 {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.2rem;
        color: var(--text-main);
    }

    .cart-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-muted);
        transition: color 0.3s;
    }

    .cart-close:hover {
        color: var(--primary-main);
    }

    .cart-empty {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px;
        text-align: center;
        color: var(--text-muted);
    }

    .cart-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }

    .cart-item {
        display: flex;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .cart-item img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        background: #f8fafc;
    }

    .cart-item-info {
        flex: 1;
    }

    .cart-item-info h4 {
        font-size: 0.95rem;
        margin-bottom: 5px;
        color: var(--text-main);
    }

    .cart-item-price {
        font-weight: 600;
        color: var(--primary-main);
        margin-bottom: 10px;
    }

    .cart-item-qty {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cart-item-qty button {
        width: 28px;
        height: 28px;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .cart-item-qty button:hover:not(:disabled) {
        background: var(--primary-main);
        color: white;
        border-color: var(--primary-main);
    }

    .cart-item-qty button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .cart-item-remove {
        background: none;
        border: none;
        color: #ef4444;
        cursor: pointer;
        font-size: 1.2rem;
        padding: 5px;
        transition: transform 0.2s;
    }

    .cart-item-remove:hover {
        transform: scale(1.1);
    }

    .cart-footer {
        padding: 20px;
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .cart-subtotal {
        display: flex;
        justify-content: space-between;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    /* Toast Notification */
    .cart-toast {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        background: #10b981;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        z-index: 999999;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .cart-toast.error {
        background: #ef4444;
    }

    .cart-toast.show {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    /* Mobile Responsive */
    @media (max-width: 480px) {
        .cart-modal-content {
            max-width: 100%;
        }
    }
</style>