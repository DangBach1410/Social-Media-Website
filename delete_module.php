<?php
session_start();
if (isset($_POST['module'])) {
    $module_id = $_POST['module'];
    if ($_SESSION['username'] == 'admin') {
        try {
            include 'includes/DatabaseConnection.php';
            include 'includes/DatabaseFunctions.php';

            // Get all posts related to the module
            $sql = 'SELECT * FROM question WHERE module_id = :module_id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':module_id', $module_id);
            $stmt->execute();
            while ($post = $stmt->fetch()) {
                $images = getImagesOfPost($pdo, $post['id']);
                foreach ($images as $image) {
                    $image_path = $image['image_path'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
                $comments = getCommentsByPostId($pdo, $post['id']);
                foreach ($comments as $comment) {
                    $images = getImagesOfComment($pdo, $comment['id']);
                    foreach ($images as $image){
                        $image_path = $image['image_path'];
                        if (file_exists($image_path)) {
                            unlink($image_path);
                        }
                    }
                }
            }

            // Delete the module
            $sql = 'DELETE FROM module WHERE id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $module_id);
            $stmt->execute();
            
            ob_start();
            $title = 'Manage Modules';
            echo 'Module deleted successfully';
            $output = ob_get_clean();
        } catch (PDOException $e) {
            ob_start();
            $title = 'An error has occurred';
            echo 'Database error: ' . $e->getMessage();
            $output = ob_get_clean();
        }
    } else {
        header("Location: index.php");
    }
} else {
    ob_start();
    $title = 'An error has occurred';
    echo 'Something went wrong';
    $output = ob_get_clean();
}
include 'templates/layout.html.php'; 
?>
