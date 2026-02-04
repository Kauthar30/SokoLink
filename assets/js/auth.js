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
                setTimeout(() => {
                    window.location.href = result.redirect;
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
