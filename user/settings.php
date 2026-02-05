<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../pages/login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings | SokoLink Electronics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="../assets/css/landing.css">
    <style>
        nav {
            position: relative !important;
            background: #fff !important;
            border-bottom: 1px solid #e2e8f0;
        }

        nav .logo {
            color: var(--primary-main) !important;
        }

        nav .nav-links a {
            color: var(--text-main) !important;
        }

        nav .nav-actions {
            color: var(--text-main) !important;
        }

        .page-container {
            display: flex;
            padding: 40px 8%;
            gap: 40px;
            min-height: 70vh;
            background: #f8fafc;
        }

        .sidebar {
            width: 280px;
            flex-shrink: 0;
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            height: fit-content;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .avatar {
            width: 50px;
            height: 50px;
            background: var(--primary-main);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 10px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            text-decoration: none;
            color: var(--text-muted);
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #f0f9ff;
            color: var(--primary-main);
        }

        .main-content {
            flex: 1;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .page-header p {
            color: var(--text-muted);
        }

        .settings-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            margin-bottom: 25px;
        }

        .settings-card h3 {
            font-size: 1.2rem;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .settings-card h3 i {
            color: var(--primary-main);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-main);
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-main);
            box-shadow: 0 0 0 3px rgba(0, 80, 158, 0.1);
        }

        .form-group input:disabled {
            background: #f8fafc;
            color: var(--text-muted);
        }

        .btn-save {
            padding: 14px 30px;
            background: var(--primary-main);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-save:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-save:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            transform: none;
        }

        .message {
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .message.success {
            background: #d1fae5;
            color: #047857;
        }

        .message.error {
            background: #fee2e2;
            color: #dc2626;
        }

        .password-note {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 5px;
        }

        @media (max-width: 900px) {
            .page-container {
                flex-direction: column;
                padding: 20px 5%;
            }

            .sidebar {
                width: 100%;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="page-container">
        <aside class="sidebar">
            <div class="user-profile">
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                </div>
                <div>
                    <h4 style="margin-bottom: 2px;">
                        <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                    </h4>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Customer</span>
                </div>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="ph ph-squares-four"></i> Dashboard</a></li>
                <li><a href="orders.php"><i class="ph ph-shopping-bag"></i> My Orders</a></li>
                <li><a href="wishlist.php"><i class="ph ph-heart"></i> Wishlist</a></li>
                <li><a href="settings.php" class="active"><i class="ph ph-gear"></i> Settings</a></li>
                <li><a href="#" onclick="logout()"><i class="ph ph-sign-out"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="page-header">
                <h1>Account Settings</h1>
                <p>Manage your profile and preferences</p>
            </div>

            <div id="message-container"></div>

            <!-- Profile Settings -->
            <div class="settings-card">
                <h3><i class="ph ph-user"></i> Profile Information</h3>
                <form id="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" disabled>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" placeholder="+255 7XX XXX XXX">
                        </div>
                        <div class="form-group">
                            <label for="address">Delivery Address</label>
                            <input type="text" id="address" name="address" placeholder="Your delivery address">
                        </div>
                    </div>
                    <button type="submit" class="btn-save" id="save-profile">
                        <i class="ph ph-floppy-disk"></i> Save Changes
                    </button>
                </form>
            </div>

            <!-- Password Settings -->
            <div class="settings-card">
                <h3><i class="ph ph-lock"></i> Change Password</h3>
                <form id="password-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password">
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password">
                            <p class="password-note">Minimum 6 characters</p>
                        </div>
                    </div>
                    <button type="submit" class="btn-save" id="save-password">
                        <i class="ph ph-key"></i> Update Password
                    </button>
                </form>
            </div>
        </main>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', loadProfile);

        async function loadProfile() {
            try {
                const response = await fetch('../api/user/profile.php');
                const result = await response.json();

                if (result.success) {
                    document.getElementById('full_name').value = result.user.full_name || '';
                    document.getElementById('email').value = result.user.email || '';
                    document.getElementById('phone').value = result.user.phone || '';
                    document.getElementById('address').value = result.user.address || '';
                }
            } catch (error) {
                console.error('Error loading profile:', error);
            }
        }

        function showMessage(message, type) {
            const container = document.getElementById('message-container');
            container.innerHTML = `<div class="message ${type}">${message}</div>`;
            setTimeout(() => {
                container.innerHTML = '';
            }, 5000);
        }

        // Profile Form
        document.getElementById('profile-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = document.getElementById('save-profile');
            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-spinner"></i> Saving...';

            try {
                const response = await fetch('../api/user/update.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        full_name: document.getElementById('full_name').value,
                        phone: document.getElementById('phone').value,
                        address: document.getElementById('address').value
                    })
                });

                const result = await response.json();
                showMessage(result.message, result.success ? 'success' : 'error');

                if (result.success) {
                    // Update sidebar name
                    document.querySelector('.user-profile h4').textContent = document.getElementById('full_name').value;
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="ph ph-floppy-disk"></i> Save Changes';
        });

        // Password Form
        document.getElementById('password-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;

            if (!currentPassword || !newPassword) {
                showMessage('Please enter both current and new password.', 'error');
                return;
            }

            if (newPassword.length < 6) {
                showMessage('New password must be at least 6 characters.', 'error');
                return;
            }

            const btn = document.getElementById('save-password');
            btn.disabled = true;
            btn.innerHTML = '<i class="ph ph-spinner"></i> Updating...';

            try {
                const response = await fetch('../api/user/update.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        current_password: currentPassword,
                        new_password: newPassword
                    })
                });

                const result = await response.json();
                showMessage(result.message, result.success ? 'success' : 'error');

                if (result.success) {
                    document.getElementById('current_password').value = '';
                    document.getElementById('new_password').value = '';
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="ph ph-key"></i> Update Password';
        });

        async function logout() {
            if (confirm('Are you sure you want to logout?')) {
                await fetch('../api/auth/logout.php');
                window.location.href = '../index.php';
            }
        }
    </script>
</body>

</html>