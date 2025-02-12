<form id="postForm" action="question.php" method="post" enctype="multipart/form-data">
    <div class="post-header">
        <div style="display:flex;">
            <img src="<?php echo $userimage; ?>" onerror="this.onerror=null; this.src='uploads/default.png';" alt="Profile Picture" class="user-picture">
            <span class="username" style="padding-left:10px; display:flex; align-items: center;""><?php echo $name; ?></span>
        </div>
        <?php
        try {
            include 'includes/DatabaseConnection.php';
            $stmt = $pdo->query("SELECT id, module_name FROM module");

            // create dropdown list
            echo '<select class="module" name="module" required>';
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
    </div>
    <div id="content-contain">
        <textarea id="content" name="content" placeholder="Question..." required></textarea><br>
        <button type="button" id="removeAllButton" class="remove-button" onclick="removeAllImages()">&times</button>
        <div id="imagePreview" class="image-preview"></div>
    </div>
    <label for="image" class="file-label">
        <i class="fa fa-camera"></i> Add image
        <input type="file" id="image" name="image[]" class="file-input" accept="image/*" multiple onchange="previewImages(event)">
    </label>
    <button type="submit" class="post-button">Post</button>
</form>

<div id="imageModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="modalImage">
</div>

<script>
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
