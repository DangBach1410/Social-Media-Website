<?php
session_start();
if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_new_password'])){
    try {
        include 'includes/DatabaseConnection.php';
        include 'includes/DatabaseFunctions.php';
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];

        $username = $_SESSION['username'];

        $user_data = getUserByUserName($pdo, $username);
        if ($currentPassword === $user_data['password']) {
            // Cập nhật mật khẩu mới vào database
            $updateStmt = $pdo->prepare("UPDATE user SET password = :newPassword WHERE username = :username");
            $updateStmt->bindValue(':newPassword', $newPassword);
            $updateStmt->bindValue(':username', $username);
            $updateStmt->execute();

            ob_start();
            $title = 'Change Password';
            echo "Password changed successfully.";
            $output = ob_get_clean();
        } else {
            ob_start(); 
            $title = 'Change Password';
            echo '<b><div style="color:red; text-align:center;">Current password is incorrect.</div></b>';
            include 'templates/change_password.html.php';
            $output = ob_get_clean();
        }
    } catch (PDOException $e) {
        $title = 'An error has occurred';
        $output = 'Database error' . $e->getMessage();
    }
}else{
    ob_start();
    $title = 'Change Password';
    include 'templates/change_password.html.php';
    $output = ob_get_clean();
}
include 'templates/layout.html.php';
