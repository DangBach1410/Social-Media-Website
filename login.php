<?php
session_start();
if (isset($_POST['username']) && isset($_POST['password'])){
    try {
        include 'includes/DatabaseConnection.php';
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT id FROM user WHERE username = :username AND password = :password");
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password', $password);   
        $stmt->execute();

        // Kiểm tra xem có kết quả nào không
        if ($stmt->rowCount() == 1) {
            // Đăng nhập thành công
            $_SESSION['username'] = $username;
            header('Location: index.php');
        } else {
            // Đăng nhập thất bại
            ob_start();
            $title = 'Login';
            $message = "Invalid username or password";
            include 'templates/login.html.php';
            $output = ob_get_clean();
        }
    } catch(PDOException $e) {
        $title = 'An error has occurred';
        $output = 'Database error' . $e->getMessage();
    }
}else{
    $title = 'Login';
    ob_start();
    include 'templates/login.html.php';
    $output = ob_get_clean();
}
include 'templates/layout.html.php';
