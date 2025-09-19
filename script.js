document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('form[action="login.php"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            const publicId = loginForm.querySelector('#public_id').value;
            const password = loginForm.querySelector('#password').value;

            if (publicId.trim() === '') {
                alert('IDを入力してください。');
                event.preventDefault();
                return;
            }

            if (password.trim() === '') {
                alert('パスワードを入力してください。');
                event.preventDefault();
                return;
            }
        });
    }
});
