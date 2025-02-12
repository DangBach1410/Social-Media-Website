<form id="loginForm" action="login.php" method="POST">
    <h2>Login</h2>
    <div class="input-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="input-group">
        <label for="password">Password</label>
        <div style="position: relative;">
            <input type="password" id="password" name="password" required>
            <span class="password-toggle">
                <i class="fas fa-eye-slash" id="showPassword"></i>
                <i class="fas fa-eye" id="hidePassword" style="display: none;"></i>
            </span>
        </div>
    </div>
    <p>Not registered yet? <a href="signup.php">Create new account</a></p>
    <?php
    if(isset($message)){
        echo '<p style="color: red;">'. $message. '</p>';
    }
    ?>
    <button type="submit">Login</button>
</form>

<script>
    const passwordInput = document.getElementById('password');
    const showPasswordIcon = document.getElementById('showPassword');
    const hidePasswordIcon = document.getElementById('hidePassword');

    showPasswordIcon.addEventListener('click', function() {
        passwordInput.type = 'text';
        showPasswordIcon.style.display = 'none';
        hidePasswordIcon.style.display = 'inline-block';
    });

    hidePasswordIcon.addEventListener('click', function() {
        passwordInput.type = 'password';
        showPasswordIcon.style.display = 'inline-block';
        hidePasswordIcon.style.display = 'none';
    });
</script>