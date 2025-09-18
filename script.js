document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.querySelector('form[action="register.php"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            const name = registerForm.querySelector('#name').value;
            const password = registerForm.querySelector('#password').value;
            const hobbies = registerForm.querySelectorAll('input[name="hobbies[]"]:checked');

            if (name.trim() === '') {
                alert('名前を入力してください。');
                event.preventDefault();
                return;
            }

            if (password.trim() === '') {
                alert('パスワードを入力してください。');
                event.preventDefault();
                return;
            }

            if (hobbies.length === 0) {
                alert('趣味を一つ以上選択してください。');
                event.preventDefault();
                return;
            }
        });
    }

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
