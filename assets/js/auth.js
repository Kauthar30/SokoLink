// assets/js/auth.js

// Handle Login Form
const loginForm = document.getElementById('login-form');
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const remember = document.getElementById('remember').checked;
        const messageDiv = document.getElementById('login-message');

        messageDiv.innerText = 'Logging in...';
        messageDiv.className = 'message';

        try {
            const response = await fetch('../api/auth/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password, remember })
            });

            const result = await response.json();

            if (result.success) {
                messageDiv.innerText = 'Success! Redirecting...';
                messageDiv.className = 'message success';

                // Merge guest cart if exists
                if (typeof mergeGuestCart === 'function') {
                    await mergeGuestCart();
                }

                // Check for redirect param in URL
                const urlParams = new URLSearchParams(window.location.search);
                const redirectTo = urlParams.get('redirect');

                setTimeout(() => {
                    if (redirectTo === 'checkout') {
                        window.location.href = '../user/checkout.php';
                    } else {
                        window.location.href = result.redirect;
                    }
                }, 1000);
            } else {
                messageDiv.innerText = result.message;
                messageDiv.className = 'message error';
            }
        } catch (error) {
            console.error('Error:', error);
            messageDiv.innerText = 'An error occurred. Please try again.';
            messageDiv.className = 'message error';
        }
    });
}

// Handle Register Form
const registerForm = document.getElementById('register-form');
if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const full_name = document.getElementById('full_name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirm_password = document.getElementById('confirm_password').value;
        const messageDiv = document.getElementById('register-message');

        if (password !== confirm_password) {
            messageDiv.innerText = 'Passwords do not match.';
            messageDiv.className = 'message error';
            return;
        }

        messageDiv.innerText = 'Creating account...';
        messageDiv.className = 'message';

        try {
            const response = await fetch('../api/auth/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ full_name, email, password })
            });

            const result = await response.json();

            if (result.success) {
                messageDiv.innerText = 'Success! Redirecting...';
                messageDiv.className = 'message success';
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 1500);
            } else {
                messageDiv.innerText = result.message;
                messageDiv.className = 'message error';
            }
        } catch (error) {
            console.error('Error:', error);
            messageDiv.innerText = 'An error occurred. Please try again.';
            messageDiv.className = 'message error';
        }
    });
}
