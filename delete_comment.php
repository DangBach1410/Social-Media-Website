<?php
session_start();
// Kiểm tra nếu ID bình luận có trong URL và là số
try {
    include 'includes/DatabaseConnection.php';
    include 'includes/DatabaseFunctions.php';
    if (isset($_SESSION['username']) && isset($_GET['id'])){
        $commentId = $_GET['id'];   
        // Lấy thông tin bình luận từ cơ sở dữ liệu
        $stmt = $pdo->prepare('SELECT user_id FROM comment WHERE id = :comment_id');
        $stmt->bindValue(':comment_id', $commentId);
        $stmt->execute();
        $comment = $stmt->fetch();
        $user = getUserByUserId($pdo, $comment['user_id']);
        if ($_SESSION['username'] ==  $user['username']) {
            $images = getImagesOfComment($pdo, $commentId);
            foreach ($images as $image) {
                $image_path = $image['image_path'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $stmt = $pdo->prepare('DELETE FROM comment WHERE id = :comment_id');
            $stmt->bindValue(':comment_id', $commentId);
            $stmt->execute();
            ob_start();
            $title = 'Delete comment';
            echo '<p style="padding-left:10px">Delete comment successful</p>';
            $output = ob_get_clean();
        } else {
            ob_start();
            $title = 'Delete post';
            echo '<p style="padding-left:10px">You do not have permission to delete this comment</p>';
            $output = ob_get_clean();
        }
    } else {
        ob_start();
        $title = 'Delete comment';
        echo 'Something went wrong';
        $output = ob_get_clean();
    }
} catch (PDOException $e) {
    $title = 'An error has occurred';
    $output = 'Database error ' . $e->getMessage();
}
include 'templates/layout.html.php';