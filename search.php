<?php
session_start();
if (isset($_GET['query'])) {
    try {
        ob_start();
        $title = 'Search';
        include 'includes/DatabaseConnection.php';
        include 'includes/DatabaseFunctions.php';
        $query = $_GET['query'];

        echo "<div class='container'>";
        echo "<div class='filter'>";
        echo "<form id='filterForm' action='search.php' method='GET'>";
        echo "<h2>Filter</h2>";
        echo "<input type='hidden' name='query' value='{$query}'>";
        echo "<label><input type='radio' name='category' value='questions' onchange='submitForm()'> Questions</label>";   
        echo "<label><input type='radio' name='category' value='users' onchange='submitForm()'> Users</label>";
        echo "<label><input type='radio' name='category' value='modules' onchange='submitForm()'> Modules</label>";
        echo "</form>";
        echo "</div>";

        echo "<div class='results'>";
        // Check if category is set (i.e., form is submitted) and display results
        if (isset($_GET['category'])) {
            $category = $_GET['category'];

            // Filter search results by category
            if ($category == 'questions') {
                $stmt = $pdo->prepare("SELECT * FROM question WHERE content LIKE :query ORDER BY time DESC");
                $stmt->bindValue(':query', "%{$query}%");
            } elseif ($category == 'users') {
                $stmt = $pdo->prepare("SELECT username, name, profile_picture FROM user WHERE name LIKE :query");
                $stmt->bindValue(':query', "%{$query}%");
            } elseif ($category == 'modules') {
                $stmt = $pdo->prepare("SELECT * FROM module WHERE module_name LIKE :query");
                $stmt->bindValue(':query', "%{$query}%");
            }
            $stmt->execute();

            // Display filtered search results
            echo "<h2>Search Results for {$category}:</h2>";
            if ($category == 'questions') {
                while ($row = $stmt->fetch()) {
                    $user = getUserByUserId($pdo, $row['user_id']);
                    $module = getModuleByModuleId($pdo, $row['module_id']);

                    echo '<div class="postForm">';
                    echo '<div class="post-header">';
                        echo '<div class="left-side">';
                            echo '<a href="profile.php?username='. $user['username']. '"><img src="' . $user['profile_picture'] . '" alt="Profile Picture" class="user-picture" onerror="this.onerror=null; this.src=\'uploads/default.png\';"></a>';
                            echo '<div style="padding-left:10px">';
                                echo '<a href="profile.php?username='. $user['username']. '"><span class="username">'. htmlspecialchars($user['name']). '</span></a><br>';
                                $postTime = strtotime($row['time']); 
                                $formattedTime = date('d/m/Y H:i', $postTime); 
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
            } elseif ($category == 'users') {
                while ($row = $stmt->fetch()) {
                    echo "<div class='found-name'>";
                        echo "<a href='profile.php?username={$row['username']}'>";
                            echo "<img src='" . htmlspecialchars($row['profile_picture']) . "' onerror='this.onerror=null; this.src=\"uploads/default.png\";' alt='Profile Picture' class='user-picture'>";
                        echo "</a>";
                        echo '<div style="padding-left:10px; display:flex; align-items: center;">';
                            echo "<a style='font-weight: bold;' href='profile.php?username={$row['username']}'>{$row['name']}</a>";
                        echo "</div>";
                    echo "</div>";
                }
            } elseif ($category == 'modules') {
                while ($row = $stmt->fetch()) {
                    $module_id = $row['id'];
                    $posts = $pdo->prepare("SELECT * FROM question WHERE module_id = :module_id ORDER BY time DESC");
                    $posts->bindValue(':module_id', $module_id);
                    $posts->execute();
                    while ($post = $posts->fetch()) {
                        $user = getUserByUserId($pdo, $post['user_id']);
                        $module = getModuleByModuleId($pdo, $post['module_id']);
    
                        echo '<div class="postForm">';
                            echo '<div class="post-header">';
                                echo '<div class="left-side">';
                                    echo '<a href="profile.php?username='. $user['username']. '"><img src="' . $user['profile_picture'] . '" alt="Profile Picture" class="user-picture" onerror="this.onerror=null; this.src=\'uploads/default.png\';"></a>';
                                    echo '<div style="padding-left:10px">';
                                        echo '<a href="profile.php?username='. $user['username']. '"><span class="username">'. htmlspecialchars($user['name']). '</span></a><br>';
                                        $postTime = strtotime($post['time']); 
                                        $formattedTime = date('d/m/Y H:i', $postTime); 
                                        echo '<span class="post-time">'. htmlspecialchars($formattedTime) .'</span>';
                                    echo '</div>';
                                echo '</div>';
                                echo '<div class="right-side">';
                                    echo '<span class="module">'. htmlspecialchars($module['module_name']). '</span>';
                                    if (isset($_SESSION['username'])){
                                        // Nếu bài post do user đăng, thêm dấu ba chấm
                                        if ($_SESSION['username'] == $user['username']) {
                                            echo '<span class="options">';
                                                echo '<i class="fas fa-ellipsis-h" onclick="toggleMenu(' . $post['id'] . ')"></i>';
                                                echo '<div class="dropdown-menu" id="dropdown-' . $post['id'] . '">';
                                                    echo '<a href="edit_post.php?id=' . $post['id'] . '">Edit</a>';
                                                    echo '<a href="delete_post.php?id=' . $post['id'] . '" onclick="return confirm(\'Are you sure you want to delete this post?\')">Delete</a>';
                                                echo '</div>';
                                            echo '</span>';
                                        }
                                    }
                                echo '</div>';
                            echo '</div>';
                            echo '<div class="post-content">';
                                echo '<div class="content-contain">';
                                    // Xử lý nội dung dài
                                    $content = htmlspecialchars($post['content']);
                                    $lines = explode("\n", $content);
                                    $max_display_lines = 5; // adjust this value to your liking
                                    $max_display_chars = 200; // adjust this value to your liking
                
                                    if (strlen($content) > $max_display_chars) {
                                        $short_content = substr($content, 0, $max_display_chars) . '...';
                                        $full_content = $content;
                                        echo '<div id="postContent-' . $post['id'] . '">' . $short_content . '<span id="readMore-' . $post['id'] . '" style="cursor: pointer; color:#777;">Read more</span></div>';
                                        echo '<div id="fullContent-' . $post['id'] . '" style="display: none;">' . $full_content . '</div>';
                                    }
                                    elseif (count($lines) > $max_display_lines) {
                                        $short_content = implode("\n", array_slice($lines, 0, $max_display_lines)) . '...';
                                        $full_content = $content;
                                        echo '<div id="postContent-' . $post['id'] . '">' . $short_content . '<span id="readMore-' . $post['id'] . '" style="cursor: pointer; color:#777;">Read more</span></div>';
                                        echo '<div id="fullContent-' . $post['id'] . '" style="display: none;">' . $full_content . '</div>';
                                    }
                                    else {
                                        echo '<span class="content">' . $content . '</span>';
                                    }
                                echo '</div>'; 
                                echo '<div class="post-images">';
        
                                    $images = getImagesOfPost($pdo, $post['id']);
                                    
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
                                $comment_numbers = CountCommentsByPostId($pdo, $post['id']);
                                if ($comment_numbers == 1){
                                    echo '<p> ' . htmlspecialchars($comment_numbers) . ' comment</p>';  
                                } else{
                                    echo '<p> '. htmlspecialchars($comment_numbers) .' comments</p>';
                                }
                                echo '<a href="post_detail.php?id=' . htmlspecialchars($post['id']) . '">View Comments</a>';
                            echo '</div>';     
                        echo '</div>';
                    }
                }
            }
        }
        echo "</div>";

        $output = ob_get_clean();
    } catch (PDOException $e) {
        $title = 'An error has occurred';
        $output = 'Database error: ' . $e->getMessage();
    }
    include 'templates/layout.html.php';
} else {
    header('Location: index.php');
}
?>
<div id="imageModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage" alt="Large Image">
    <a id="prevButton" class="prev" onclick="changeImage(-1)">&#10094;</a>
    <a id="nextButton" class="next" onclick="changeImage(1)">&#10095;</a>
</div>
<script>
    function submitForm() {
        document.getElementById("filterForm").submit();
    }
</script>
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