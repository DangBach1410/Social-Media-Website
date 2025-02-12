<?php
session_start();
if (isset($_POST['password'])) {
    try {
        $username = $_SESSION['username'];

        // Perform deletion logic here
        include 'includes/DatabaseConnection.php';
        include 'includes/DatabaseFunctions.php';
        
        $user_data = getUserByUserName($pdo, $username);

        if ($user_data['password'] == $_POST['password']) {
            if (file_exists($user_data['profile_picture'])) {
                unlink($user_data['profile_picture']);
            }

            $posts = getPostsOfUser($pdo, $user_data['id']);
            while ($post = $posts->fetch()) {
                $images = getImagesOfPost($pdo, $post['id']);
                foreach ($images as $image) {
                    $image_path = $image['image_path'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            }
            // Get all comments of user to delete
            $stmt = $pdo->prepare('SELECT * FROM comment WHERE user_id = :user_id');
            $stmt->bindValue(':user_id', $user_data['id']);
            $stmt->execute();
            $comments = $stmt->fetchAll();
            foreach ($comments as $comment) {
                $images = getImagesOfComment($pdo, $comment['id']);
                foreach ($images as $image){
                    $image_path = $image['image_path'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            }

            $stmt = $pdo->prepare("DELETE FROM user WHERE username = :username");
            $stmt->bindValue(":username", $username);
            $stmt->execute();

            session_destroy(); // Destroy session after deletion
            header("Location: index.php");
        } else {
            ob_start();
            $title = 'Delete account';
            echo '<b><div style="color:red; text-align:center;">Incorrect password!!!</div></b>';
            include 'templates/delete_account.html.php';
            $output = ob_get_clean();
        }
    } catch(PDOException $e) {
        $title = 'An error has occurred';
        $output = 'Database error' . $e->getMessage();
    }
} else {
    ob_start();
    $title = 'Delete account';
    include 'templates/delete_account.html.php';
    $output = ob_get_clean();
}
include 'templates/layout.html.php';