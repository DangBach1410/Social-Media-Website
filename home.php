<?php if (isset($_SESSION['username'])){?>
    <h1 style='padding-left:20px;'>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
<?php }?>
<?php
try {
    include 'includes/DatabaseConnection.php';
    include 'includes/DatabaseFunctions.php';
    $stmt = allPosts($pdo);
    while ($row = $stmt->fetch()) {
        $user = getUserByUserId($pdo, $row['user_id']);
        $module = getModuleByModuleId($pdo, $row['module_id']);

        echo '<div class="postForm">';
            echo '<div class="post-header">';
                echo '<div class="left-side">';
                    echo '<a href="profile.php?username='. $user['username']. '"><img src="' . $user['profile_picture'] . '" onerror="this.onerror=null; this.src=\'uploads/default.png\';" alt="Profile Picture" class="user-picture"></a>';
                    echo '<div style="padding-left:10px">';
                        echo '<a href="profile.php?username='. $user['username']. '"><span class="username">'. htmlspecialchars($user['name']). '</span></a><br>';
                        $postTime = strtotime($row['time']); // Chuyển đổi sang định dạng Unix timestamp
                        $formattedTime = date('d/m/Y H:i', $postTime); // Định dạng ngày tháng giờ
                        echo '<span class="post-time">'. htmlspecialchars($formattedTime) .'</span>';
                    echo '</div>';
                echo '</div>';
                echo '<div class="right-side">';
                    echo '<span class="module">'. htmlspecialchars($module['module_name']). '</span>';
                    if (isset($_SESSION['username'])){
                        // Nếu bài post do user đăng, thêm dấu ba chấm
                        if ($_SESSION['username'] == $user['username']) {
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
                echo '</div>';
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
} catch (PDOException $e) {
    $title = 'An error has occurred';
    $output = 'Database error' . $e->getMessage();
}
?>  

<div id="imageModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage" alt="Large Image">
    <a id="prevButton" class="prev">&#10094;</a>
    <a id="nextButton" class="next">&#10095;</a>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
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
