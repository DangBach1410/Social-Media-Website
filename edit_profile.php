<?php
session_start();

$username = $_SESSION['username'];

if (isset($_POST['name']) && isset($_POST['dob']) && $_POST['dob']) {
    try {
        include 'includes/DatabaseConnection.php';
        $name = $_POST['name'];
        $dob = $_POST['dob'];
        $email = $_POST['email'];

        // Update the user's profile information in the database
        $sql_update = "UPDATE user SET 
        name = :name, 
        dob = :dob,
        email = :email
        WHERE username = :username";
        $stmt = $pdo->prepare($sql_update);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':dob', $dob);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        // Redirect to the profile page after updating
        header('Location: profile.php?username=' . $username);
    } catch (PDOException $e) {
        $title = 'An error has occurred';
        $output = 'Database error: ' . $e->getMessage();
    }
} else {
    include 'includes/DatabaseConnection.php';
    include 'includes/DatabaseFunctions.php';
    ob_start();
    $title = 'Edit profile';
    $user_data = getUserByUserName($pdo, $username);
    include 'templates/edit_profile.html.php';
    $output = ob_get_clean();
}
include 'templates/layout.html.php';

