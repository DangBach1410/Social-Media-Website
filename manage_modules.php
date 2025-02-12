<?php
session_start();
if ($_SESSION['username'] != 'admin') {
    header("Location: index.php");
    exit();
} else{
    ob_start();?>
    <h1 style="margin-left:1.5em;">Manage Modules</h1>
    
    <div>
        <form id=addmoduleForm action="add_module.php" method="post">
            <h2>Add Module</h2>
            <label for="module_name">Module Name:</label>
            <input style="margin-bottom:10px;" type="text" id="module_name" name="module_name" required>
            <button type="submit">Add Module</button>
        </form>
    </div>
    
    <div>
        <form id=editmoduleForm action="edit_module.php" method="post">
            <h2>Edit Module</h2>
            <?php
            try {
                include 'includes/DatabaseConnection.php';
                $stmt = $pdo->query("SELECT id, module_name FROM module");
                // create dropdown list
                echo '<select style="margin-bottom:10px;float:left;" class="module" name="module" required>';
                echo '<option value="">Select a module</option>';
                while ($row = $stmt->fetch()) {
                    echo '<option value="'.$row['id'].'">'.$row['module_name'].'</option>';
                }
                echo '</select>';
            } catch(PDOException $e) {
                $title = 'An error has occurred';
                $output = 'Database error' . $e->getMessage();
            }
            ?>
            <label for="new_module_name">New Module Name:</label>
            <input style="margin-bottom:10px;" type="text" id="new_module_name" name="new_module_name" required>
            <button type="submit">Edit Module</button>
        </form>
    </div>
    
    <div>
        <form id=deletemoduleForm action="delete_module.php" method="post">
            <h2>Delete Module</h2>
            <?php
            try {
                include 'includes/DatabaseConnection.php';
                $stmt = $pdo->query("SELECT id, module_name FROM module");
                // create dropdown list
                echo '<select style="margin-bottom:10px; float:left;" class="module" name="module" required>';
                echo '<option value="">Select a module</option>';
                while ($row = $stmt->fetch()) {
                    echo '<option value="'.$row['id'].'">'.$row['module_name'].'</option>';
                }
                echo '</select>';
            } catch(PDOException $e) {
                $title = 'An error has occurred';
                $output = 'Database error' . $e->getMessage();
            }
            ?>
            <button type="submit">Delete Module</button>
        </form>
    </div>
<?php
    $title = 'Manage Modules';
    $output = ob_get_clean();
}
include 'templates/layout.html.php';

