<?php
session_start();
if (isset($_POST['module']) && isset($_POST['new_module_name'])) {
    $module_id = $_POST['module'];
    $new_module_name = $_POST['new_module_name'];
    if ($_SESSION['username'] == 'admin') {
        try {
            include 'includes/DatabaseConnection.php';
            include 'includes/DatabaseFunctions.php';
    
            // Check if the new module name already exists
            $sql = 'SELECT * FROM module WHERE module_name = :module_name AND id != :id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':module_name', $new_module_name);
            $stmt->bindValue(':id', $module_id);
            $stmt->execute();
            $count = $stmt->rowCount();
    
            if ($count == 0) {
                // Update the module
                $sql = 'UPDATE module SET module_name = :module_name WHERE id = :id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':module_name', $new_module_name);
                $stmt->bindValue(':id', $module_id);
                $stmt->execute();
                ob_start();
                $title = 'Manage Modules';
                echo 'Module updated successfully';
                $output = ob_get_clean();
            } else {
                ob_start();
                $title = 'An error has occurred';
                echo 'This module name already exists';
                $output = ob_get_clean();
            }
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
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
