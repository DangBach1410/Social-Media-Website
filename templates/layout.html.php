<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="grid-container">
        <div class="grid-item">
            <header style="float:left; color:black;"><a style="float:left; color:white;" href="index.php">Course Help Hub</a></header>
        </div>
        <div class="grid-item">
            <div class="search-container">
                <form id="searchForm" action="search.php" method="GET">
                    <input type="search" id="searchQuery" name="query" placeholder="Search..." value="<?php if (isset($_GET['query'])){echo htmlspecialchars($_GET['query']);} ?>" required>
                    <input type="hidden" name="category" value="questions">
                    <button type="submit">Search</button>
                </form>
            </div>
        </div>
        <div class="grid-item">
            <ul id="tab-line" style="float:right">
                <div id="menu-icon"><i class="fa fa-bars"></i></div>        
                <li class="tab"><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
                <li class="tab"><a href="question.php"><i class="fa fa-question-circle"></i> Question</a></li>
                <li class="tab"><a href="contact.php"><i class="fa fa-envelope"></i> Contact</a></li>
                <?php if (isset($_SESSION['username'])): 
                    try {                
                        $username = $_SESSION['username'];    
                        include 'includes/DatabaseConnection.php';
                        require_once 'includes/DatabaseFunctions.php';
                        $user_data = getUserByUserName($pdo, $username);  
                        if ($_SESSION['username'] == 'admin'): ?>
                            <li class="tab"><a href="manage_modules.php"><i class="fa fa-cog"></i> Manage Modules</a></li>
                        <?php endif;
                    } catch(PDOException $e) {
                        echo 'Database error' . $e->getMessage();
                    } 
                ?>
                <li class="user-icon" id="userIcon">
                    <img src="<?php echo $user_data['profile_picture']; ?>" onerror="this.onerror=null; this.src='uploads/default.png';" alt="Profile Picture" class="user-picture">
                    <div class="user-options" id="userOptions">
                        <ul>
                            <li class="username"><i class="fa fa-user"></i><span><?php echo htmlspecialchars($user_data['name']); ?></span></li>
                            <li class="profile"><a href="profile.php"><i class="fa fa-user-edit"></i><span>Profile</span></a></li>
                            <li class="logout"><a href="logout.php"><i class="fa fa-sign-out-alt"></i><span>Logout</span></a></li>
                        </ul>
                    </div>
                </li>
                <?php else: ?>
                    <li class="login-tab"><a href="login.php"><i class="fa fa-sign-in-alt"></i> Login</a></li>
                <?php endif; ?>
            </ul>
            <ul id="dropdown-menu">
                <li class="dropdown-tab"><a href="index.php"><i class="fa fa-home"></i> Home</a></li>
                <li class="dropdown-tab"><a href="question.php"><i class="fa fa-question-circle"></i> Question</a></li>
                <li class="dropdown-tab"><a href="contact.php"><i class="fa fa-envelope"></i> Contact</a></li>
                <?php if (isset($_SESSION['username']) && $_SESSION['username'] == 'trandangbach'): ?>
                    <li class="dropdown-tab"><a href="manage_modules.php"><i class="fa fa-cog"></i> Manage Modules</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>     
    <main>
        <?php echo $output ?>
    </main>
    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            var userIcon = document.getElementById('userIcon');
            var menuIcon = document.getElementById('menu-icon');
            var dropdownMenu = document.getElementById('dropdown-menu');
            var userOptions = document.getElementById('userOptions');

            if (userIcon) {
                userIcon.addEventListener('click', function(event) {
                    event.stopPropagation();
                    userOptions.classList.toggle('show');
                });
            }

            if (menuIcon) {
                menuIcon.addEventListener('click', function(event) {
                    event.stopPropagation();
                    dropdownMenu.classList.toggle('show');
                });

                document.addEventListener('click', function(event) {
                    if (!menuIcon.contains(event.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }

            document.addEventListener('click', function(event) {
                if (userOptions && !userIcon.contains(event.target)) {
                    userOptions.classList.remove('show');
                }
            });
        });
    </script>
    <script>
        function submitFormAndSaveSearch() {
            var query = document.getElementById('query').value;
            localStorage.setItem('savedQuery', query); 
            document.getElementById('searchForm').submit(); 
        }
        window.onload = function() {
            var savedQuery = localStorage.getItem('savedQuery');
            if (savedQuery) {
                document.getElementById('query').value = savedQuery;
            }
        };
    </script>
</body>
</html>