<?php
session_start();
if (isset($_POST['username']) && isset($_POST['password'])){
    try {
        include 'includes/DatabaseConnection.php';
        $username = $_POST['username'];
        $password = $_POST['password'];
        $name = $_POST['name'];
        $dob = $_POST['dob'];
        $email = $_POST['email'];

        $sql_check = "SELECT * FROM user WHERE username='$username'";
        $result_check = $pdo->query($sql_check);

        if ($result_check->rowCount() > 0) {
            ob_start();
            $title = 'Sign up';
            $message= "Username already exists.";
            include 'templates/signup.html.php';
            $output = ob_get_clean();
        } else {
            $sql_insert = "INSERT INTO user SET
            username=:username,
            password=:password,
            name=:name,
            dob=:dob,
            email=:email";

            $stmt = $pdo->prepare($sql_insert);
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':password', $password);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':dob', $dob);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $_SESSION['username'] = $username;
            header('Location: index.php');
        }    
    } catch(PDOException $e) {
        $title = 'An error has occurred';
        $output = 'Database error' . $e->getMessage();
    }
}else{
    ob_start();
    $title = 'Sign up';
    include 'templates/signup.html.php';
    $output = ob_get_clean();
}
include 'templates/layout.html.php';


