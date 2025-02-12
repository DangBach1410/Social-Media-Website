<form id="deleteaccountForm" action="delete_account.php" method="post">
    <h2>Confirm Account Deletion</h2>
    <div class="input-group">
        <label for="password">Please enter your password to confirm deletion:</label>
        <div style="position: relative;">
            <input type="password" id="password" name="password" required>
            <span class="password-toggle">
                <i class="fas fa-eye-slash" id="showPassword"></i>
                <i class="fas fa-eye" id="hidePassword" style="display: none;"></i>
            </span>
        </div>
    </div>
    <button type="submit" value="Delete Account" onclick="return confirm('Are you sure you want to delete your account?')">Delete Account</button>
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