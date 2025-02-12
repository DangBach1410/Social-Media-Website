<?php
session_start();

try {
    include 'includes/DatabaseConnection.php';
    include 'includes/DatabaseFunctions.php';
    if (isset($_SESSION['username']) && isset($_GET['id'])) {
        $postId = $_GET['id'];
        $post = getPostByPostId($pdo, $postId);
        $user = getUserByUserId($pdo, $post['user_id']);

        if ($_SESSION['username'] ==  $user['username']) {
            $images = getImagesOfPost($pdo, $postId);
            foreach ($images as $image) {
                $image_path = $image['image_path'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $comments = getCommentsByPostId($pdo, $postId);
            foreach ($comments as $comment) {
                $images = getImagesOfComment($pdo, $comment['id']);
                foreach ($images as $image){
                    $image_path = $image['image_path'];
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
            }
            deletePost($pdo, $postId);
            ob_start();
            $title = 'Delete post';
            echo '<p style="padding-left:10px">Delete post successful</p>';
            $output = ob_get_clean();
        } else {
            ob_start();
            $title = 'Delete post';
            echo '<p style="padding-left:10px">You do not have permission to delete this post</p>';
            $output = ob_get_clean();
        }
    } else {
        ob_start();
        $title = 'Delete post';
        echo 'Something went wrong';
        $output = ob_get_clean();
    }
} catch (PDOException $e) {
    $title = 'An error has occurred';
    $output = 'Database error ' . $e->getMessage();
}
include 'templates/layout.html.php';
