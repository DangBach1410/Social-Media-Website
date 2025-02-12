<?php
session_start();
if (isset($_SESSION['username']) && isset($_GET['id'])){
    try {
        include 'includes/DatabaseConnection.php';
        include 'includes/DatabaseFunctions.php';
        $username = $_SESSION['username'];
        $comment_id = $_GET['id'];
        if (isset($_POST['content']) && isset($_FILES['image'])){
            $content = $_POST['content'];
            $images = $_FILES['image'];

            $comment = getCommentById($pdo, $comment_id);     
            $user_data = getUserByUserId($pdo, $comment['user_id']);

            if ($_SESSION['username'] ==  $user_data['username']) {
                updateComment($pdo, $content, $comment_id);

                try {
                    $deleted_images = getImagesOfComment($pdo, $comment['id']);
                    foreach ($deleted_images as $image){
                        $image_path = $image['image_path'];
                        if (file_exists($image_path)) {
                            unlink($image_path);
                        }
                    }
                    $stmt_delete_images = $pdo->prepare("DELETE FROM comment_image WHERE comment_id = :comment_id");
                    $stmt_delete_images->bindValue(':comment_id', $comment_id);
                    $stmt_delete_images->execute();

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
                                    $title = 'Edit comment';
                                    echo "Something went wrong";
                                    $output = ob_get_clean();
                                }
                            } else {
                                ob_start();
                                $title = 'Edit comment';
                                echo "Files are not images";
                                $output = ob_get_clean();
                            }
                        } 
                    }
                    $title = 'Edit comment';
                    echo '<p style="padding-left:10px;">Edit comment successful</p>';
                    $output = ob_get_clean();
                } catch (PDOException $e) {
                    ob_start();
                    $title = 'Edit comment';
                    echo 'Error deleting old images: ' . $e->getMessage();
                    $output = ob_get_clean();
                }
            } else {
                ob_start();
                $title = 'Edit comment';
                echo '<p style="padding-left:10px;">You do not have permission to edit this comment</p>';
                $output = ob_get_clean();
            }
        }else{
            ob_start();
            $title = 'Edit comment';
            $user = getUserByUserName($pdo, $username);
            $comment = getCommentById($pdo, $comment_id);
            $comment_images = getImagesOfComment($pdo, $comment_id);
            $images_json = json_encode($comment_images);
            $userimage = $user['profile_picture'];
            $name = $user['name'];
            include 'templates/edit_comment.html.php';
            $output = ob_get_clean();
        }
    } catch (PDOException $e) {
        $title = 'An error has occurred';
        $output = 'Database error: ' . $e->getMessage();
    }
}else{
    ob_start();
    $title = 'Edit comment';
    echo 'Something went wrong';
    $output = ob_get_clean();
}
include 'templates/layout.html.php';
