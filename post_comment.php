<?php
session_start();
if (isset($_POST['content']) && isset($_POST['post_id']) && isset($_FILES['image'])) {
    try {
        include 'includes/DatabaseConnection.php';
        include 'includes/DatabaseFunctions.php';

        $username = $_SESSION['username'];
        $content = $_POST['content'];
        $post_id = $_POST['post_id'];
        $images = $_FILES['image'];

        $user = getUserByUserName($pdo, $username);
        $user_id = $user['id'];

        // Insert the comment into the database
        insertComment($pdo, $post_id, $user_id,  $content);
        $comment_id = $pdo->lastInsertId();
        // Xử lý lưu trữ ảnh
        $target_dir = "uploads/";
        for ($i = 0; $i < count($images['name']); $i++) {
            if ($images['error'][$i] == 0) {
                $target_file = $target_dir . basename($images['name'][$i]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $new_file_name = $target_dir . uniqid() . '.' . $imageFileType;

                // Kiểm tra loại file ảnh
                $check = getimagesize($images['tmp_name'][$i]);
                if ($check !== false) {
                    if (move_uploaded_file($images['tmp_name'][$i], $new_file_name)) {
                        // Lưu đường dẫn ảnh vào cơ sở dữ liệu
                        $stmt = $pdo->prepare('INSERT INTO comment_image SET 
                        comment_id = :comment_id,
                        image_path = :image_path');
                        $stmt->bindValue(':comment_id', $comment_id);
                        $stmt->bindValue(':image_path', $new_file_name);
                        $stmt->execute();
                    } else {
                        ob_start();
                        $title = 'Post comment';
                        echo "Something went wrong";
                        $output = ob_get_clean();
                    }
                } else {
                    ob_start();
                    $title = 'Post comment';
                    echo "Files are not images";
                    $output = ob_get_clean();
                }
            }
        }
        // Redirect to the post detail page
        header('Location: post_detail.php?id=' . htmlspecialchars($post_id));
    } catch (PDOException $e) {
        ob_start();
        $title = 'An error has occurred';
        echo 'Database error: ' . htmlspecialchars($e->getMessage());
        $output = ob_get_clean();
    }
} else {
     ob_start();
    $title = 'Post Comment';
    echo 'Invalid request';
    $output = ob_get_clean();
}
include 'templates/layout.html.php';
