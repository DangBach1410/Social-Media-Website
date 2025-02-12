<form id="registerForm" action="signup.php" method="POST" onsubmit="return validatePassword()">
    <h2>Sign up</h2>
    <div class="input-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <span class="password-toggle">
            <i class="fas fa-eye-slash" data-id="password1"></i>
            <i class="fas fa-eye" data-id="password1" style="display: none;"></i>
        </span>
    </div>
    <div class="input-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <p id="message"></p>
        <span class="password-toggle">
            <i class="fas fa-eye-slash" data-id="password2"></i>
            <i class="fas fa-eye" data-id="password2" style="display: none;"></i>
        </span> 
    </div>
    <div class="input-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required>
    </div>
    <div class="input-group">
        <label for="dob">Date of Birth</label>
        <input type="text" id="dob" name="dob" value="<?php echo date('d/m/Y', strtotime('')); ?>" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" required>
        <p id="dobMessage" style="color: red;"></p>
    </div>
    <div class="input-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
    </div>
    <?php
    if(isset($message)){
        echo '<p style="color: red;">'. $message. '</p>';
    }
    ?>
    <button type="submit" id="submitBtn" disabled>Sign up</button> 
</form>
<script>
    const passwordInput = document.getElementById('password');
    const confirmPassInput = document.getElementById('confirm_password');
    const passwordMatchMessage = document.getElementById('message');
    const submitBtn = document.getElementById('submitBtn');

    // Kiểm tra mật khẩu khi người dùng nhập vào
    passwordInput.addEventListener('input', validateForm);
    confirmPassInput.addEventListener('input', validateForm);

    // Hàm kiểm tra sự khớp của password và confirm password
    function validatePasswordMatch() {
        const password = passwordInput.value;
        const confirm_password = confirmPassInput.value;

        if (password.length === 0 && confirm_password.length === 0) {
            passwordMatchMessage.textContent = '';
            return false;
        } else if (password === confirm_password && password.length > 0 && confirm_password.length > 0) {
            passwordMatchMessage.textContent = '';
            return true;
        } else {
            passwordMatchMessage.textContent = '*Passwords do not match';
            return false;
        }
    }
    function validateDOB() {
        const dobValue = dobInput.value;
        const parts = dobValue.split('/');
        if (parts.length === 3) {
            const day = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10);
            const year = parseInt(parts[2], 10);

            if (isValidDate(day, month, year)) {
                dobMessage.textContent = '';
                return true;
            } else {
                dobMessage.textContent = '*Invalid date';
                return false;
            }
        } else {
            dobMessage.textContent = '*Invalid date format';
            return false;
        }
    }   
    function validateForm() {
        const isPasswordValid = validatePasswordMatch();
        const isDOBValid = validateDOB();
        submitBtn.disabled = !(isPasswordValid && isDOBValid);
    }
    // Lấy element password 1
    const passwordInput1 = document.getElementById('password');
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
    const passwordInput2 = document.getElementById('confirm_password');
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

    // Hàm kiểm tra ngày tháng hợp lệ
    function isValidDate(day, month, year) {
        const date = new Date(year, month - 1, day);
        return date.getFullYear() === year && date.getMonth() === (month - 1) && date.getDate() === day;
    }
    // Kiểm tra ngày sinh trước khi gửi biểu mẫu
    const dobInput = document.getElementById('dob');
    const dobMessage = document.getElementById('dobMessage');
    dobInput.addEventListener('input', validateForm);
    // JavaScript to convert date format before submitting the form
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const dobInput = document.getElementById('dob');
        const dobValue = dobInput.value;

        // Convert dd/mm/yyyy to yyyy-mm-dd
        const parts = dobValue.split('/');
        if (parts.length === 3) {
            const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
            dobInput.value = formattedDate;
        }
    });
</script>