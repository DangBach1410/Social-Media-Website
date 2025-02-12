<?php
session_start();
if (isset($_SESSION['username']) && isset($_GET['id'])){
    try {
        include 'includes/DatabaseConnection.php';
        include 'includes/DatabaseFunctions.php';
        $username = $_SESSION['username'];
        $question_id = $_GET['id'];
        $post = getPostByPostId($pdo, $question_id);    
        $user_data = getUserByUserId($pdo, $post['user_id']);
        if ($_SESSION['username'] ==  $user_data['username']) {
            if (isset($_POST['module']) && isset($_POST['content']) && isset($_FILES['image'])){
                $module_id = $_POST['module'];
                $content = $_POST['content'];
                $images = $_FILES['image']; 
                updatePost($pdo, $module_id, $content, $question_id);

                try {
                    $deleted_images = getImagesOfPost($pdo, $question_id);
                    foreach ($deleted_images as $image) {
                        $image_path = $image['image_path'];
                        if (file_exists($image_path)) {
                            unlink($image_path);
                        }
                    }
                    $stmt_delete_images = $pdo->prepare("DELETE FROM question_image WHERE question_id = :question_id");
                    $stmt_delete_images->bindValue(':question_id', $question_id);
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
                                    $stmt = $pdo->prepare('INSERT INTO question_image SET 
                                    question_id = :question_id,
                                    image_path = :image_path');
                                    $stmt->bindValue(':question_id', $question_id);
                                    $stmt->bindValue(':image_path', $new_file_name);
                                    $stmt->execute();
                                } else {
                                    ob_start();
                                    $title = 'Edit post';
                                    echo "Something went wrong";
                                    $output = ob_get_clean();
                                }
                            } else {
                                ob_start();
                                $title = 'Edit post';
                                echo "Files are not images";
                                $output = ob_get_clean();
                            }
                        } 
                    }
                    $title = 'Edit post';
                    echo '<p style="padding-left:10px;">Edit post successful</p>';
                    $output = ob_get_clean();
                } catch (PDOException $e) {
                    ob_start();
                    $title = 'Edit post';
                    echo 'Error deleting old images: ' . $e->getMessage();
                    $output = ob_get_clean();
                }
            } else {
                ob_start();
                $title = 'Edit post';
                $user = getUserByUserName($pdo, $username);
                $post = getPostByPostId($pdo, $question_id);
                $post_images = getImagesOfPost($pdo, $question_id);
                $images_json = json_encode($post_images);
                $userimage = $user['profile_picture'];
                $name = $user['name'];
                include 'templates/edit_post.html.php';
                $output = ob_get_clean();
            }
        }else{
            ob_start();
            $title = 'Edit post';
            echo '<p style="padding-left:10px;">You do not have permission to edit this post</p>';
            $output = ob_get_clean();
        }
    } catch (PDOException $e) {
        $title = 'An error has occurred';
        $output = 'Database error: ' . $e->getMessage();
    }
}else{
    ob_start();
    $title = 'Edit post';
    echo 'Something went wrong';
    $output = ob_get_clean();
}
include 'templates/layout.html.php';


