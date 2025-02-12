<?php
session_start();
try {
    include 'includes/DatabaseConnection.php';
    include 'includes/DatabaseFunctions.php';
    if (isset($_FILES['profile_picture'])) {
 
        $target_dir = 'uploads/';
        $target_file = $target_dir . basename($_FILES['profile_picture']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $new_file_name = $target_dir . uniqid() . '.' . $imageFileType;
    
        $check = getimagesize($_FILES['profile_picture']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $new_file_name)) {
                $user_data = getUserByUserName($pdo, $_SESSION['username']);
                if (file_exists($user_data['profile_picture'])) {
                    unlink($user_data['profile_picture']);
                }
                $stmt = $pdo->prepare('UPDATE user SET profile_picture = :profile_picture WHERE username = :username');
                $stmt->bindValue(':profile_picture', $new_file_name);
                $stmt->bindValue(':username', $_SESSION['username']);
                $stmt->execute();
                header('Location: profile.php?username='. $_SESSION['username']); 
            }
            ob_start();
            $title = 'An error has occurred';
            echo 'Error moving uploaded file';
            $output = ob_get_clean();
        }
        ob_start();
        $title = 'An error has occurred';
        echo 'Uploaded file is not an image';
        $output = ob_get_clean();
    }
    ob_start();
    $title = 'An error has occurred';
    echo 'No file or username provided';
    $output = ob_get_clean();
}catch (PDOException $e) {
    ob_start();
    $title = 'An error has occurred';
    echo 'Database error: ' . $e->getMessage();
    $output = ob_get_clean();
}
include 'templates/layout.html.php';


