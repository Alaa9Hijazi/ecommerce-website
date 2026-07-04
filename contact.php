<?php 
include 'header.php';
?>
<link rel="stylesheet" href="css/contact_style.css">

<div class="contact-hero">
    <img src="images/contactus.jpg" alt="Contact Us">
</div>

<div class="contact-wrapper">

    <!-- نموذج المراسلة - الجزء اليسار  -->
    <div class="contact-form-card">
        <h2>Send Us a Message</h2>

        <form action="contact_process.php" method="POST">
            <label>Full Name:</label>
            <input type="text" name="name" required>

            <label>Email Address:</label>
            <input type="email" name="email" required>

            <label>Subject:</label>
            <select name="subject" required>
                <option value="Inquiry">General Inquiry</option>
                <option value="Complaint">Report a Complaint</option>
                <option value="Suggestion">Give a Suggestion</option>
            </select>

            <label>Message:</label>
            <textarea name="message" rows="5" required></textarea>

            <button type="submit" class="btn-send">Send Now</button>
        </form>
    </div>

    <!-- معلومات التواصل - الجزء اليمين -->
    <div class="contact-right">

        <div class="contact-info-card">
            <h3>Contact Information</h3>
            <div class="info-item">🏢 Khanyounis - Al-Tina Street</div>
            <div class="info-item">📱 +970 5XX XXX XXX</div>
            <div class="info-item">✉️ support@hijazistore.com</div>
            <div class="info-item">🗓️ Sat - Thu: 9 AM - 9 PM</div>
        </div>

        <div class="help-box">
            <h3>💬 We're Here to Help</h3>
            <p>
                Our customer support team is available to assist you with any questions or concerns.
            </p>
        </div>

    </div>
</div>

<div class="features-section">
    <div class="feature-box">🚀 Fast Delivery</div>
    <div class="feature-box">🔐 Secure Payment</div>
    <div class="feature-box">🔄 Easy Returns</div>
    <div class="feature-box">🏆 Best Quality</div>
</div>

<?php include 'footer.php'; ?>

<!-- ربط ملف الجافاسكربت -->
<script src="js/script.js"></script>