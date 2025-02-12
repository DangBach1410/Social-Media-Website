<?php 
session_start();
$title = 'Course Help Hub';
ob_start();
$output = '';
include 'home.php';
$output = ob_get_clean();
include 'templates/layout.html.php';
