<form id="editProfileForm" action="edit_profile.php" method="POST">
    <h2>Edit Profile</h2>
    <div class="input-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" readonly>
    </div>
    <div class="input-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
    </div>
    <div class="input-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
    </div>
    <div class="input-group">
        <label for="dob">Date of Birth</label>
        <input type="text" id="dob" name="dob" value="<?php echo date('d/m/Y', strtotime($user_data['dob'])); ?>" placeholder="dd/mm/yyyy" pattern="\d{2}/\d{2}/\d{4}" required>
        <p id="dobMessage" style="color: red;"></p>
    </div>
    <button type="submit">Update Profile</button>
</form>
<script>
    // Hàm kiểm tra ngày tháng hợp lệ
    function isValidDate(day, month, year) {
        const date = new Date(year, month - 1, day);
        return date.getFullYear() === year && date.getMonth() === (month - 1) && date.getDate() === day;
    }
    // Kiểm tra ngày sinh trước khi gửi biểu mẫu
    const dobInput = document.getElementById('dob');
    const dobMessage = document.getElementById('dobMessage');
    dobInput.addEventListener('input', function(e) {
        const dobValue = dobInput.value;
        const parts = dobValue.split('/');
        if (parts.length === 3) {
            const day = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10);
            const year = parseInt(parts[2], 10);

            if (isValidDate(day, month, year)) {
                dobMessage.textContent = '';
                submitBtn.disabled = false;
            } else {
                dobMessage.textContent = '*Invalid date';
                submitBtn.disabled = true;
            }
        } else {
            dobMessage.textContent = '*Invalid date format';
            submitBtn.disabled = true;
        }
    });
    // JavaScript to convert date format before submitting the form
    document.getElementById('editProfileForm').addEventListener('submit', function(e) {
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