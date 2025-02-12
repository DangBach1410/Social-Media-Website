<form id="contactForm" action="contact.php" method="POST">
    <h2>Contact Us</h2>
    <label for="name">Your Name</label><br>
    <input type="text" id="name" name="name" required><br><br>

    <label for="email">Your Email</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="message">Message</label><br>
    <textarea id="content" name="message" placeholder="What can we help you?" required></textarea><br><br>

    <button type="submit">Send</button>
</form>