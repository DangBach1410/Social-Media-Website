<?php
session_start();
if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message'])){
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Basic email headers
    $to = "trandangbach2004@gmail.com"; 
    $subject = "New message from contact form";

    // Compose the email content
    $email_content = "You have received a new message from your website contact form.\n\n";
    $email_content .= "Name: $name\n";
    $email_content .= "Email: $email\n";
    $email_content .= "Message:\n$message\n";

    // Send email
    if (mail($to, $subject, $email_content)) {
        ob_start();
        $title = 'Contact';
        echo "<p style='padding-left:10px;'>Thank you, your message has been sent.</p>";
        $output = ob_get_clean();
    } else {
        ob_start();
        $title = 'Contact';
        echo "<p style='padding-left:10px;>Sorry, there was an error sending your message. Please try again later.</p>";
        $output = ob_get_clean();
    }
} else {
    ob_start();
    $title = 'Contact';
    include 'templates/contact.html.php';
    $output = ob_get_clean();
}
include 'templates/layout.html.php';