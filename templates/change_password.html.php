<div class="change-password-form">
    <h2>Change Password</h2>
    <form action="change_password.php" method="POST">
        <div class="input-group">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" required>
            <span class="password-toggle">
                <i class="fas fa-eye-slash" data-id="password0"></i>
                <i class="fas fa-eye" data-id="password0" style="display: none;"></i>
            </span>
        </div>
        <div class="input-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" required>
            <span class="password-toggle">
                <i class="fas fa-eye-slash" data-id="password1"></i>
                <i class="fas fa-eye" data-id="password1" style="display: none;"></i>
            </span>
        </div>
        <div class="input-group">
            <label for="confirm_new_password">Confirm New Password</label>
            <input type="password" id="confirm_new_password" name="confirm_new_password" required>
            <p id="message"></p>
            <span class="password-toggle">
                <i class="fas fa-eye-slash" data-id="password2"></i>
                <i class="fas fa-eye" data-id="password2" style="display: none;"></i>
            </span> 
        </div>
        <button type="submit" id="submitBtn" disabled>Change Password</button>
    </form>
</div>

    <!-- JavaScript for password toggle -->
<script>
    const passwordInput = document.getElementById('new_password');
    const confirmPassInput = document.getElementById('confirm_new_password');
    const passwordMatchMessage = document.getElementById('message');
    const submitBtn = document.getElementById('submitBtn');

    // Kiểm tra mật khẩu khi người dùng nhập vào
    passwordInput.addEventListener('input', validatePasswordMatch);
    confirmPassInput.addEventListener('input', validatePasswordMatch);

    // Hàm kiểm tra sự khớp của password và confirm password
    function validatePasswordMatch() {
        const password = passwordInput.value;
        const confirm_password = confirmPassInput.value;

        if (password.length === 0 && confirm_password.length === 0) {
            passwordMatchMessage.textContent = '';
            submitBtn.disabled = true;
        }else if (password === confirm_password && password.length > 0 && confirm_password.length > 0) {
            passwordMatchMessage.textContent = '';
            submitBtn.disabled = false;
        } else {
            passwordMatchMessage.textContent = '*Passwords do not match';
            submitBtn.disabled = true;
        }
    }
    const passwordInput0 = document.getElementById('current_password');
    const showPasswordIcon0 = document.querySelector('[data-id="password0"]');
    const hidePasswordIcon0 = document.querySelector('[data-id="password0"][style="display: none;"]');

    showPasswordIcon0.addEventListener('click', function() {
        passwordInput0.type = 'text';
        showPasswordIcon0.style.display = 'none';
        hidePasswordIcon0.style.display = 'inline-block';
    });

    hidePasswordIcon0.addEventListener('click', function() {
        passwordInput0.type = 'password';
        showPasswordIcon0.style.display = 'inline-block';
        hidePasswordIcon0.style.display = 'none';
    });
    // Lấy element password 1
    const passwordInput1 = document.getElementById('new_password');
    const showPasswordIcon1 = document.querySelector('[data-id="password1"]');
    const hidePasswordIcon1 = document.querySelector('[data-id="password1"][style="display: none;"]');

    showPasswordIcon1.addEventListener('click', function() {
        passwordInput1.type = 'text';
        showPasswordIcon1.style.display = 'none';
        hidePasswordIcon1.style.display = 'inline-block';
    });

    hidePasswordIcon1.addEventListener('click', function() {
        passwordInput1.type = 'password';
        showPasswordIcon1.style.display = 'inline-block';
        hidePasswordIcon1.style.display = 'none';
    });

    // Lấy element password 2
    const passwordInput2 = document.getElementById('confirm_new_password');
    const showPasswordIcon2 = document.querySelector('[data-id="password2"]');
    const hidePasswordIcon2 = document.querySelector('[data-id="password2"][style="display: none;"]');

    showPasswordIcon2.addEventListener('click', function() {
        passwordInput2.type = 'text';
        showPasswordIcon2.style.display = 'none';
        hidePasswordIcon2.style.display = 'inline-block';
    });

    hidePasswordIcon2.addEventListener('click', function() {
        passwordInput2.type = 'password';
        showPasswordIcon2.style.display = 'inline-block';
        hidePasswordIcon2.style.display = 'none';
    });
</script>