<?php
session_start();
if (isset($_POST['module_name'])) {
    $module_name = $_POST['module_name'];
    if ($_SESSION['username'] == 'admin') {
        try {
            include 'includes/DatabaseConnection.php';
            include 'includes/DatabaseFunctions.php';

            $sql = 'SELECT * FROM module WHERE module_name = :module_name';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':module_name', $module_name);
            $stmt->execute();
            $count = $stmt->rowCount();

            if ($count == 0) {
            $sql = 'INSERT INTO module 
            SET module_name = :module_name';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':module_name', $module_name);
            $stmt->execute();
            ob_start();
            $title = 'Manage Modules';
            echo 'Add module sucessfully';
            $output = ob_get_clean();
            }else{
                ob_start();
                $title = 'An error has occurred';
                echo 'This module already exists';
                $output = ob_get_clean();
            }
        }catch (PDOException $e) {
            ob_start();
            $title = 'An error has occurred';
            echo 'Database error: ' . $e->getMessage();
            $output = ob_get_clean();
        }
    } else {
        header("Location: index.php");
    }
}else{
    ob_start();
    $title = 'An error has occurred';
    echo 'Something went wrong';
    $output = ob_get_clean();
}
include 'templates/layout.html.php'; 
