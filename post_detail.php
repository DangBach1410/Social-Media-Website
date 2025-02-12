<?php
session_start();
$title = 'Course Help Hub';
ob_start();
$output = '';
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $postId = $_GET['id']; 
    try {
        include 'includes/DatabaseConnection.php';
        include 'includes/DatabaseFunctions.php';

        // Fetch post details
        $post = getPostByPostId($pdo, $postId);
        if ($post) {
            $user = getUserByUserId($pdo, $post['user_id']);
            $module = getModuleByModuleId($pdo, $post['module_id']);

            // Display post details
            echo '<div class="postForm" style="margin-bottom:220px">';
                echo '<div class="post-header">';
                    echo '<div class="left-side">';
                        echo '<a href="profile.php?username='. htmlspecialchars($user['username']) .'"><img src="' . htmlspecialchars($user['profile_picture']) . '" onerror="this.onerror=null; this.src=\'uploads/default.png\';" alt="Profile Picture" class="user-picture"></a>';
                        echo '<div style="padding-left:10px">';
                            echo '<a href="profile.php?username='. htmlspecialchars($user['username']) .'"><span class="username">'. htmlspecialchars($user['name']) .'</span></a><br>';
                            $postTime = strtotime($post['time']);
                            $formattedTime = date('d/m/Y H:i', $postTime);
                            echo '<span class="post-time">'. htmlspecialchars($formattedTime) .'</span>';
                        echo '</div>';
                    echo '</div>';
                    echo '<div class="right-side">';
                        echo '<span class="module">'. htmlspecialchars($module['module_name']) .'</span>';
                        if (isset($_SESSION['username']) && $_SESSION['username'] == $user['username']) {
                            echo '<span class="options">';
                                echo '<i class="fas fa-ellipsis-h" onclick="toggleMenu(' . $post['id'] . ')"></i>';
                                echo '<div class="dropdown-menu" id="dropdown-' . $post['id'] . '">';
                                    echo '<a href="edit_post.php?id=' . $post['id'] . '">Edit</a>';
                                    echo '<a href="delete_post.php?id=' . $post['id'] . '" onclick="return confirm(\'Are you sure you want to delete this post?\')">Delete</a>';
                                echo '</div>';
                            echo '</span>';
                        }
                    echo '</div>';
                echo '</div>';
                echo '<div class="post-content">';
                    echo '<div class="content-contain">';
                        $content = htmlspecialchars($post['content']);
                        $lines = explode("\n", $content);
                        $maxDisplayLines = 5; // Adjust this value to your liking
                        $maxDisplayChars = 200; // Adjust this value to your liking

                        if (count($lines) > $maxDisplayLines) {
                            $shortContent = implode("\n", array_slice($lines, 0, $maxDisplayLines)) . '...';
                            $fullContent = $content;
                            echo '<div id="postContent-' . $post['id'] . '">' . htmlspecialchars($shortContent) . '<span id="readMore-' . $post['id'] . '" style="cursor: pointer; color:#777;">Read more</span></div>';
                            echo '<div id="fullContent-' . $post['id'] . '" style="display: none;">' . htmlspecialchars($fullContent) . '</div>';
                        } elseif (strlen($content) > $maxDisplayChars) {
                            $shortContent = substr($content, 0, $maxDisplayChars) . '...';
                            $fullContent = $content;
                            echo '<div id="postContent-' . $post['id'] . '">' . htmlspecialchars($shortContent) . '<span id="readMore-' . $post['id'] . '" style="cursor: pointer; color:#777;">Read more</span></div>';
                            echo '<div id="fullContent-' . $post['id'] . '" style="display: none;">' . htmlspecialchars($fullContent) . '</div>';
                        } else {
                            echo '<span class="content">' . htmlspecialchars($content) . '</span>';
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
                    echo '</div>';
                echo '</div>';
                echo '<div class="comments-section">';
                    $comments = getCommentsByPostId($pdo, $postId);
                    foreach ($comments as $comment) {
                        $commentUser = getUserByUserId($pdo, $comment['user_id']);
                        echo '<div class="comment">';
                            echo '<a href="profile.php?username='. htmlspecialchars($commentUser['username']) .'"><img src="' . htmlspecialchars($commentUser['profile_picture']) . '" onerror="this.onerror=null; this.src=\'uploads/default.png\';" alt="Profile Picture" class="user-picture"></a>';
                            echo '<div style="padding-left:10px">';
                                echo '<a href="profile.php?username='. htmlspecialchars($commentUser['username']) .'"><span class="username">'. htmlspecialchars($commentUser['name']) .'</span></a>';
                                $commentContent = htmlspecialchars($comment['content']);
                                $commentLines = explode("\n", $commentContent);
                                $maxCommentLines = 3; // Adjust this value to your liking
                                $maxCommentChars = 100; // Adjust this value to your liking

                                if (count($commentLines) > $maxCommentLines) {
                                    $shortComment = implode("\n", array_slice($commentLines, 0, $maxCommentLines)) . '...';
                                    $fullComment = $commentContent;
                                    echo '<div class="content" id="commentContent-' . $comment['id'] . '">' . nl2br(htmlspecialchars($shortComment)) . '<span id="readMoreComment-' . $comment['id'] . '" style="cursor: pointer; color:#777;">Read more</span></div>';
                                    echo '<div class="content" id="fullComment-' . $comment['id'] . '" style="display: none;">' . nl2br(htmlspecialchars($fullComment)) . '</div>';
                                } elseif (strlen($commentContent) > $maxCommentChars) {
                                    $shortComment = substr($commentContent, 0, $maxCommentChars) . '...';
                                    $fullComment = $commentContent;
                                    echo '<div class="content" id="commentContent-' . $comment['id'] . '">' . nl2br(htmlspecialchars($shortComment)) . '<span id="readMoreComment-' . $comment['id'] . '" style="cursor: pointer; color:#777;">Read more</span></div>';
                                    echo '<div class="content" id="fullComment-' . $comment['id'] . '" style="display: none;">' . nl2br(htmlspecialchars($fullComment)) . '</div>';
                                } else {
                                    echo '<span class="content">' . nl2br(htmlspecialchars($commentContent)) . '</span>';
                                }
                                // Display images for the comment
                                echo '<div class="comment-images">';
                                    $commentImages = getImagesOfComment($pdo, $comment['id']);
                                    foreach ($commentImages as $commentImageRow) {
                                        echo '<div class="comment-image">';
                                            echo '<img class="thumbnail" src="' . htmlspecialchars($commentImageRow['image_path']) . '" alt="Comment Image">';
                                        echo '</div>';
                                    }
                                echo '</div>';
                                // Edit and Delete options for comments
                                if (isset($_SESSION['username']) && $_SESSION['username'] == $commentUser['username']) {
                                    echo '<div class="options">';
                                        echo '<i class="fas fa-ellipsis-h" onclick="toggleCommentMenu(' . $comment['id'] . ')"></i>';
                                        echo '<div class="dropdown-menu" id="comment-dropdown-' . $comment['id'] . '">';
                                            echo '<a href="edit_comment.php?id=' . $comment['id'] . '">Edit</a>';
                                            echo '<a href="delete_comment.php?id=' . $comment['id'] . '" onclick="return confirm(\'Are you sure you want to delete this comment?\')">Delete</a>';
                                        echo '</div>';
                                    echo '</div>';
                                }
                            echo '</div>';
                        echo '</div>';
                    }
                echo '</div>';
            echo '</div>';
            if (isset($_SESSION['username'])) {
            echo '<div class="comment-form">';
                echo '<form action="post_comment.php" method="post" enctype="multipart/form-data">';
                    echo '<div id="content-contain">';
                        echo '<textarea id="content" name="content" placeholder="Add a comment..." required></textarea>';
                        echo '<button type="button" id="removeAllButton" class="remove-button" onclick="removeAllImages()">&times</button>';
                        echo '<div id="imagePreview" class="image-preview"></div>';
                        echo '<input type="hidden" name="post_id" value="' . htmlspecialchars($postId) . '">';
                    echo '</div>';
                    echo '<div style="display:flex; justify-content:space-between;">';
                        echo '<label for="image" class="file-label">';
                            echo '<i class="fa fa-camera"></i> Add image';
                            echo '<input type="file" id="image" name="image[]" class="file-input" accept="image/*" onchange="previewImages(event)" multiple>';
                        echo '</label>';
                        echo '<button type="submit">Post Comment</button>';
                    echo '</div>';
                echo '</form>';
            echo '</div>';
            }
        } else {
            echo '<p>Post not found.</p>';
        }
    } catch (PDOException $e) {
        echo 'Database error: ' . htmlspecialchars($e->getMessage());
    }
} else {
    echo '<p>Invalid post ID.</p>';
}
$output = ob_get_clean();
include 'templates/layout.html.php';
?>

<div id="imageModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage" alt="Large Image">
    <a id="prevButton" class="prev" onclick="changeImage(-1)">&#10094;</a>
    <a id="nextButton" class="next" onclick="changeImage(1)">&#10095;</a>
</div>

<script>
    // Lấy tất cả các phần tử .post-images trong các bài post
    const postImages = document.querySelectorAll('.post-images');

    // Lặp qua từng phần tử .post-images để thêm sự kiện cho mỗi bài post
    postImages.forEach(Image => {
        const images = Image.querySelectorAll('.thumbnail');
        const allimages = Image.querySelectorAll('.hidden-image'); 
        const viewmores = Image.querySelectorAll('.view-more');
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
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy tất cả các phần tử .comment-images trong các bình luận
        const commentImagesContainers = document.querySelectorAll('.comment-images');

        commentImagesContainers.forEach(container => {
            const images = container.querySelectorAll('.thumbnail');
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const prevButton = document.getElementById('prevButton');
            const nextButton = document.getElementById('nextButton');
            let currentImageIndex = 0;

            // Kiểm tra nếu trong mỗi bình luận có ít nhất một ảnh
            if (images.length > 0) {
                images.forEach((image) => {
                    image.addEventListener('click', function() {
                        openModal(Array.from(images).indexOf(image));
                    });
                });
            }

            function openModal(startIndex) {
                // Hiển thị modal và thiết lập ảnh bắt đầu từ startIndex
                modal.style.display = 'flex';
                currentImageIndex = startIndex;
                modalImg.src = images[currentImageIndex].src;

                // Xử lý sự kiện nút Prev
                prevButton.onclick = function() {
                    currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
                    modalImg.src = images[currentImageIndex].src;
                };

                // Xử lý sự kiện nút Next
                nextButton.onclick = function() {
                    currentImageIndex = (currentImageIndex + 1) % images.length;
                    modalImg.src = images[currentImageIndex].src;
                };

                // Đóng modal khi bấm vào nút đóng
                const closeButton = document.getElementsByClassName('close')[0];
                closeButton.onclick = function() {
                    modal.style.display = 'none';
                };
            }
        });
    });
    document.addEventListener("DOMContentLoaded", function() {
        var readMoreButtons = document.querySelectorAll("[id^='readMore-'], [id^='readMoreComment-']");
        readMoreButtons.forEach(function(button) {
            button.addEventListener("click", function() {
                var postId = button.id.split("-")[1];
                var postContentDiv = document.getElementById("postContent-" + postId);
                var fullContentDiv = document.getElementById("fullContent-" + postId);
                var commentContentDiv = document.getElementById("commentContent-" + postId);
                var fullCommentDiv = document.getElementById("fullComment-" + postId);

                if (postContentDiv) {
                    postContentDiv.style.display = "none";
                    fullContentDiv.style.display = "block";
                } else if (commentContentDiv) {
                    commentContentDiv.style.display = "none";
                    fullCommentDiv.style.display = "block";
                }
            });
        });
    });
    function toggleCommentMenu(commentId) {
        var dropdown = document.getElementById("comment-dropdown-" + commentId);
        if (dropdown.style.display === "none" || dropdown.style.display === "") {
            dropdown.style.display = "block";
        } else {
            dropdown.style.display = "none";
        }
    }
    function toggleMenu(postId) {
        var dropdown = document.getElementById("dropdown-" + postId);
        if (dropdown.style.display === "none" || dropdown.style.display === "") {
            dropdown.style.display = "block";
        } else {
            dropdown.style.display = "none";
        }
    }

    // Close all dropdowns if click outside
    document.addEventListener('click', function(event) {
        var target = event.target;
        var isOptionsMenu = false;
        while (target !== null && !isOptionsMenu) {
            if (target.classList && target.classList.contains('options')) {
                isOptionsMenu = true;
            }
            target = target.parentNode;
        }
        if (!isOptionsMenu) {
            var allDropdowns = document.querySelectorAll(".dropdown-menu");
            allDropdowns.forEach(function(dropdown) {
                dropdown.style.display = "none";
            });
        }
    });

    function autoResizeTextarea() {
        var textarea = document.getElementById('content');
        textarea.style.height = 'auto'; 
        textarea.style.height = textarea.scrollHeight + 'px'; 
    }

    // Gọi hàm autoResizeTextarea() khi nội dung của textarea thay đổi
    document.getElementById('content').addEventListener('input', autoResizeTextarea);

    let imageFiles = [];
    let uniqueId = 0;  // Chỉ số duy nhất toàn cầu

    function previewImages(event) {
        var files = event.target.files;
        var imagePreview = document.getElementById('imagePreview');

        var dt = new DataTransfer();

        // Thêm các tệp hiện có vào DataTransfer
        for (var i = 0; i < imageFiles.length; i++) {
            dt.items.add(imageFiles[i].file);
        }

        // Thêm các tệp mới vào DataTransfer và imageFiles
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var id = uniqueId++;  // Chỉ số duy nhất
            dt.items.add(file);
            imageFiles.push({ id: id, file: file });

            var reader = new FileReader();
            reader.onload = (function(fileId) {
                return function(e) {
                    var imgSrc = e.target.result;

                    var imageContainer = document.createElement('div');
                    imageContainer.className = 'image-container';
                    imageContainer.dataset.index = fileId; // Store the unique identifier

                    var img = document.createElement('img');
                    img.src = imgSrc;
                    img.alt = 'Preview Image';
                    img.className = 'preview-image';
                    img.onclick = function() {
                        showModal(imgSrc);
                    };

                    var removeButton = document.createElement('button');
                    removeButton.className = 'removebutton';
                    removeButton.innerHTML = '&times;';
                    removeButton.onclick = function() {
                        removeImage(fileId);
                    };

                    imageContainer.appendChild(img);
                    imageContainer.appendChild(removeButton);
                    imagePreview.appendChild(imageContainer);
                    sortImagePreview();
                };
            })(id);

            reader.readAsDataURL(file);
        }

        // Cập nhật các tệp cho input file
        var imageInput = document.getElementById('image');
        imageInput.files = dt.files;
    }

    function sortImagePreview() {
        var imagePreview = document.getElementById('imagePreview');
        var imageContainers = Array.from(imagePreview.children);
        
        imageContainers.sort(function(a, b) {
            return parseInt(a.dataset.index) - parseInt(b.dataset.index);
        });

        imagePreview.innerHTML = '';
        imageContainers.forEach(function(container) {
            imagePreview.appendChild(container);
        });
    }

    function showModal(imgSrc) {
        var modal = document.getElementById('imageModal');
        var modalImage = document.getElementById('modalImage');
        modal.style.display = "flex";
        modalImage.src = imgSrc;
        modalImage.style.maxWidth = "90%";
        modalImage.style.maxHeight = "90%";
    }

    function removeAllImages() {
        imageFiles = []; 
        imagePreview.innerHTML = '';

        var imageInput = document.getElementById('image');
        imageInput.value = null; 
    }

    function removeImage(fileId) {
        imageFiles = imageFiles.filter(function(imageFile) {
            return imageFile.id !== fileId;
        });

        var imagePreview = document.getElementById('imagePreview');
        var imageContainers = Array.from(imagePreview.children);
        imageContainers.forEach(function(container) {
            if (parseInt(container.dataset.index) === fileId) {
                container.remove();
            }
        });

        var dt = new DataTransfer();
        imageFiles.forEach(function(imageFile) {
            dt.items.add(imageFile.file);
        });

        var imageInput = document.getElementById('image');
        imageInput.files = dt.files;
    }

    var modal = document.getElementById('imageModal');
    var closeModal = document.getElementsByClassName('close')[0];
    closeModal.onclick = function() {
        modal.style.display = "none";
    }
</script>
