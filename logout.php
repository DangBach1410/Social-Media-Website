<?php
session_start();
session_destroy();

// Chuyển hướng về trang chủ sau khi logout
header("Location: index.php");
