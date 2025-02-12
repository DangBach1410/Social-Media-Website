<?php
session_start();
$output = '';
if (!isset($_SESSION['username'])) {
    ob_start();
    $title = 'Post question';
    echo 'You need to login before you post a question';
    $output = ob_get_clean();
}else{
    if (isset($_POST['module']) && isset($_POST['content']) && isset($_FILES['image'])) {
        try {
            include 'includes/DatabaseConnection.php';
            include 'includes/DatabaseFunctions.php';
            $username = $_SESSION['username'];
            $module_id = $_POST['module'];
            $content = $_POST['content'];
            $images = $_FILES['image'];

            $user = getUserByUserName($pdo, $username);
            $user_id = $user['id'];

            insertPost($pdo, $user_id, $module_id, $content);
            $question_id = $pdo->lastInsertId();
    
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
                            $stmt = $pdo->prepare('INSERT INTO question_image SET 
                            question_id = :question_id,
                            image_path = :image_path');
                            $stmt->bindValue(':question_id', $question_id);
                            $stmt->bindValue(':image_path', $new_file_name);
                            $stmt->execute();
                        } else {
                            ob_start();
                            $title = 'Post question';
                            echo "Something went wrong";
                            $output = ob_get_clean();
                        }
                    } else {
                        ob_start();
                        $title = 'Post question';
                        echo "Files are not images";
                        $output = ob_get_clean();
                    }
                }
            }
            header ('Location: index.php');
        }catch (PDOException $e) {
            ob_start();
            $title = 'An error has occurred';
            echo 'Database error: ' . $e->getMessage();
            $output = ob_get_clean();
        }  
    }else{
        try {
            include 'includes/DatabaseConnection.php';
            include 'includes/DatabaseFunctions.php';
            $username = $_SESSION['username'];
            $user = getUserByUserName($pdo, $username);
            $title = 'Post question';
            ob_start();
            $userimage = $user['profile_picture'];
            $name = $user['name'];
            include 'templates/question.html.php';
            $output = ob_get_clean();
        }catch (PDOException $e) {
            ob_start();
            $title = 'An error has occurred';
            echo 'Database error: ' . $e->getMessage();
            $output = ob_get_clean();
        }  
    }   
}
include 'templates/layout.html.php';

