<?php
session_start();
if (isset($_SESSION['username'])) {
    if (isset($_GET['username'])) {
        $username = $_GET['username'];
    } else{
        $username = $_SESSION['username'];
    }    
    try {
        ob_start();
        $title = $username;
        include 'includes/DatabaseConnection.php';
        include 'includes/DatabaseFunctions.php';
    
        $user_data = getUserByUserName($pdo, $username);
        $stmt = getPostsOfUser($pdo, $user_data['id']);
        
        echo'<div class="profile-container">';
            echo'<div class="profile-info">';
                if (!empty($user_data['profile_picture'])) {
                    if ((isset($_SESSION['username']) && $username == $_SESSION['username']) || !isset($_SESSION['username'])) {
                        $profilePictureClass = 'profile-picture current-user'; 
                    } else {
                        $profilePictureClass = 'profile-picture'; 
                    }
                    echo '<img src="' . htmlspecialchars($user_data['profile_picture']) . '" alt="Profile Picture" class="' . htmlspecialchars($profilePictureClass) . '">';
                } else {
                    if ((isset($_SESSION['username']) && $username == $_SESSION['username']) || !isset($_SESSION['username'])) {
                        $profilePictureClass = 'profile-picture current-user'; 
                    } else {
                        $profilePictureClass = 'profile-picture'; 
                    }
                    echo '<img src="uploads/default.png" alt="Default Profile Picture" class="' . htmlspecialchars($profilePictureClass) . '">';
                }
                echo'<div style="padding-left:10px; white-space:nowrap;">';
                    echo'<h2>'. htmlspecialchars($user_data['name']). '</h2>';
                    echo '<p>Birthday: ' . htmlspecialchars(date('d/m/Y', strtotime($user_data['dob']))) . '</p>';
                    echo'<p>Contact Information:</p>';
                    echo'<ul>';
                        echo'<li>Email: '. htmlspecialchars($user_data['email']). '</li>';
                    echo'</ul>';
                echo'</div>';
            echo'</div>';
            if (isset($_SESSION['username'])){
                if ($username == $_SESSION['username']) {
                    echo'<div class="profile-actions">';
                        echo'<a href="edit_profile.php">Edit Profile</a>';
                        echo'<a href="change_password.php">Change Password</a>';
                        echo'<a href="delete_account.php" onclick="return confirm(\'Are you sure you want to delete your account?\')">Delete Account</a>';
                    echo'</div>';
                }
            }
        echo'</div>';
        echo'<hr>';
        echo'<h3>Posted Questions</h3>';
        while ($row = $stmt->fetch()) {
            $module = getModuleByModuleId($pdo, $row['module_id']);
    
            echo '<div class="postForm">';
                echo '<div class="post-header">';
                    echo '<div class="left-side">';
                        echo '<a href="#"><img src="' . $user_data['profile_picture'] . '" alt="Profile Picture" class="user-picture" onerror="this.onerror=null; this.src=\'uploads/default.png\';"></a>';
                            echo '<div style="padding-left:10px">';
                            echo '<a href="#"><span class="username">'. htmlspecialchars($user_data['name']). '</span></a><br>';
                            $postTime = strtotime($row['time']); // Chuyển đổi sang định dạng Unix timestamp
                            $formattedTime = date('d/m/Y H:i', $postTime); // Định dạng ngày tháng giờ
                            echo '<span class="post-time">'. htmlspecialchars($formattedTime) .'</span>';
                        echo '</div>';
                    echo '</div>';
                    echo '<div class="right-side">';
                        echo '<span class="module">'. htmlspecialchars($module['module_name']). '</span>';
                            if (isset($_SESSION['username'])){
                                // Nếu bài post do user đăng, thêm dấu ba chấm
                                if ($username == $_SESSION['username']) {
                                    echo '<span class="options">';
                                        echo '<i class="fas fa-ellipsis-h" onclick="toggleMenu(' . $row['id'] . ')"></i>';
                                        echo '<div class="dropdown-menu" id="dropdown-' . $row['id'] . '">';
                                            echo '<a href="edit_post.php?id=' . $row['id'] . '">Edit</a>';
                                            echo '<a href="delete_post.php?id=' . $row['id'] . '" onclick="return confirm(\'Are you sure you want to delete this post?\')">Delete</a>';
                                        echo '</div>';
                                    echo '</span>';
                                }
                            }
                    echo '</div>';
                echo '</div>';
                echo '<div class="post-content">';
                    echo '<div class="content-contain">';
                        // Xử lý nội dung dài
                        $content = htmlspecialchars($row['content']);
                        $lines = explode("\n", $content);
                        $max_display_lines = 5; // adjust this value to your liking
                        $max_display_chars = 200; // adjust this value to your liking
     
                        if (strlen($content) > $max_display_chars) {
                            $short_content = substr($content, 0, $max_display_chars) . '...';
                            $full_content = $content;
                            echo '<div id="postContent-' . $row['id'] . '">' . $short_content . '<span id="readMore-' . $row['id'] . '" style="cursor: pointer; color:#777;">Read more</span></div>';
                            echo '<div id="fullContent-' . $row['id'] . '" style="display: none;">' . $full_content . '</div>';
                        }
                        elseif (count($lines) > $max_display_lines) {
                            $short_content = implode("\n", array_slice($lines, 0, $max_display_lines)) . '...';
                            $full_content = $content;
                            echo '<div id="postContent-' . $row['id'] . '">' . $short_content . '<span id="readMore-' . $row['id'] . '" style="cursor: pointer; color:#777;">Read more</span></div>';
                            echo '<div id="fullContent-' . $row['id'] . '" style="display: none;">' . $full_content . '</div>';
                        }   
                        else {
                            echo '<span class="content">' . $content . '</span>';
                        }
                    echo '</div>'; 
                    echo '<div class="post-images">';
                        $images = getImagesOfPost($pdo, $row['id']);
                        
                        $imageLimit = 2; // Số lượng ảnh tối đa hiển thị ban đầu
                        if (count($images) > $imageLimit) {
    
                            // Hiển thị ảnh thứ nhất
                            echo '<div class="post-image">';
                            echo '<img class="thumbnail" src="' . htmlspecialchars($images[0]['image_path']) . '" alt="Post Image">';
                            echo '</div>';
                        
                            // Hiển thị ảnh thứ hai và dấu cộng (+)
                            echo '<div class="post-image">';
                                echo '<img class="thumbnail" src="' . htmlspecialchars($images[1]['image_path']) . '" alt="Post Image">';
                                echo '<div class="view-more">';
                                    echo '<span class="expand-images">+' . (count($images) - $imageLimit) . '</span>';
                                echo '</div>';
                            echo '</div>';
                        } else {
                            // Hiển thị tất cả các ảnh nếu không vượt quá số lượng tối đa
                            foreach ($images as $imageRow) {
                                echo '<div class="post-image">';
                                echo '<img class="thumbnail" src="' . htmlspecialchars($imageRow['image_path']) . '" alt="Post Image">';
                                echo '</div>';
                            }
                        }
                        foreach ($images as $imageRow) {
                            echo '<div class="post-image hidden">';
                            echo '<img class="hidden-image" src="' . htmlspecialchars($imageRow['image_path']) . '" alt="Post Image">';
                            echo '</div>';
                        }
                    echo '</div>'; // Đóng div post-images
                echo '</div>'; 
                echo '<div class="comment-section">';
                    $comment_numbers = CountCommentsByPostId($pdo, $row['id']);
                    if ($comment_numbers == 1){
                        echo '<p> ' . htmlspecialchars($comment_numbers) . ' comment</p>';  
                    } else{
                        echo '<p> '. htmlspecialchars($comment_numbers) .' comments</p>';
                    }
                    echo '<a href="post_detail.php?id=' . htmlspecialchars($row['id']) . '">View Comments</a>';
                echo '</div>';                
            echo '</div>';
        }
        $output = ob_get_clean();
    }catch(PDOException $e) {
        $title = 'An error has occurred';
        $output = 'Database error' . $e->getMessage();
    }
} else{
    $title = 'An error has occurred';
    $output = 'Something went wrong';
}
include 'templates/layout.html.php';
?>
<input type="file" id="fileInput" style="display: none;" accept="image/*" onchange="updateProfilePicture(event)">
<!-- Context Menu -->
<div id="contextMenu" class="context-menu">
    <a onclick="viewProfilePicture()">View Profile Picture</a>
    <a onclick="triggerFileInput()">Change Profile Picture</a>
</div>

<div id="imageModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage" alt="Large Image">
    <a id="prevButton" class="prev" onclick="changeImage(-1)">&#10094;</a>
    <a id="nextButton" class="next" onclick="changeImage(1)">&#10095;</a>
</div>

<form id="profilePictureForm" action="change_profile_picture.php" method="post" enctype="multipart/form-data" style="display: none;">
    <input type="file" name="profile_picture" id="profilePictureInput">
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        function handleProfilePictureClick(event) {
            var profilePicture = event.target;
            var isCurrentUser = profilePicture.classList.contains('current-user');

            if (isCurrentUser) {
                toggleContextMenu(event);
            } else {
                viewProfilePicture();
            }
        }
        // Function to toggle the context menu
        function toggleContextMenu(event) {
            var contextMenu = document.getElementById("contextMenu");
            var display = contextMenu.style.display;

            if (display === "block") {
            contextMenu.style.display = "none";
            } else {
            contextMenu.style.display = "block";
            contextMenu.style.left = event.pageX + "px";
            contextMenu.style.top = event.pageY + "px";
            }
        }

        // Function to view profile picture
        function viewProfilePicture() {
            var modal = document.getElementById('imageModal');
            var modalImg = document.getElementById('modalImage');
            modal.style.display = 'flex';
            modalImg.src = document.querySelector('.profile-picture').src;
            const closeButton = document.getElementsByClassName('close')[0];
            closeButton.onclick = function() {
                modal.style.display = 'none';
            };
        }

        // Close context menu on click outside
        document.addEventListener("click", function(event) {
            var contextMenu = document.getElementById("contextMenu");
            if (event.target.closest(".profile-picture") === null) {
                contextMenu.style.display = "none";
            }
        });

        function triggerFileInput() {
            document.getElementById('fileInput').click();
        }

        function updateProfilePicture(event) {
            var file = event.target.files[0];
            if (file) {
                var form = document.getElementById('profilePictureForm');
                var fileInput = document.getElementById('profilePictureInput');
                
                var formData = new FormData();
                formData.append('profile_picture', file);

                // Update file input with new file
                fileInput.files = event.target.files;

                // Submit form
                form.submit();
            }
        }
        // Add event listeners
        document.querySelector(".profile-picture").addEventListener("click", handleProfilePictureClick);
        window.viewProfilePicture = viewProfilePicture;
        window.triggerFileInput = triggerFileInput;
        window.updateProfilePicture = updateProfilePicture;

        var readMoreButtons = document.querySelectorAll("[id^='readMore-']");
        readMoreButtons.forEach(function(button) {
            button.addEventListener("click", function() {
                var postId = button.id.split("-")[1];
                var postContentDiv = document.getElementById("postContent-" + postId);
                var fullContentDiv = document.getElementById("fullContent-" + postId);
                postContentDiv.style.display = "none";
                fullContentDiv.style.display = "block";
            });
        });
    });

    // Lấy tất cả các phần tử .post-images trong các bài post
    const postImages = document.querySelectorAll('.post-images');

    // Lặp qua từng phần tử .post-images để thêm sự kiện cho mỗi bài post
    postImages.forEach(post => {
        const images = post.querySelectorAll('.thumbnail');
        const allimages = post.querySelectorAll('.hidden-image'); 
        const viewmores = post.querySelectorAll('.view-more');
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        const prevButton = document.getElementById('prevButton');
        const nextButton = document.getElementById('nextButton');
        let currentImageIndex = 0;

        // Kiểm tra nếu trong mỗi bài post có ít nhất một ảnh
        if (images.length > 0) {
            images.forEach((image) => {
                image.addEventListener('click', function() {
                    openModal(Array.from(images).indexOf(image));
                });
            });
            viewmores.forEach((viewmore) => {
                viewmore.addEventListener('click', function() {
                    openModal(1);
                });
            });
        }

        function openModal(startIndex) {
            // Hiển thị modal và thiết lập ảnh bắt đầu từ startIndex
            modal.style.display = 'flex';
            currentImageIndex = startIndex;
            modalImg.src = allimages[currentImageIndex].src;

            // Xử lý sự kiện nút Prev
            prevButton.onclick = function() {
                currentImageIndex = (currentImageIndex - 1 + allimages.length) % allimages.length;
                modalImg.src = allimages[currentImageIndex].src;
            };

            // Xử lý sự kiện nút Next
            nextButton.onclick = function() {
                currentImageIndex = (currentImageIndex + 1) % allimages.length;
                modalImg.src = allimages[currentImageIndex].src;
            };

            // Đóng modal khi bấm vào nút đóng
            const closeButton = document.getElementsByClassName('close')[0];
            closeButton.onclick = function() {
                modal.style.display = 'none';
            };
        }
    });
    function toggleMenu(questionId) {
        var dropdown = document.getElementById("dropdown-" + questionId);
        if (dropdown.style.display === "none" || dropdown.style.display === "") {
            dropdown.style.display = "block";
        } else {
            dropdown.style.display = "none";
        }
    } 
    // Xử lý sự kiện click trên toàn bộ tài liệu
    document.addEventListener('click', function(event) {
        var target = event.target;

        // Kiểm tra xem có phải là một phần tử .options không
        var isOptionsMenu = false;
        while (target !== null && !isOptionsMenu) {
            if (target.classList && target.classList.contains('options')) {
                isOptionsMenu = true;
            }
            target = target.parentNode;
        }

        // Nếu không phải là phần tử .options thì đóng tất cả dropdown-menu
        if (!isOptionsMenu) {
            var allDropdowns = document.querySelectorAll(".dropdown-menu");
            allDropdowns.forEach(function(dropdown) {
                dropdown.style.display = "none";
            });
        }
    });
</script>