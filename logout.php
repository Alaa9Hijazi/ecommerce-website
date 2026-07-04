<?php
header("refresh:5;url=index.php");

include 'header.php';

$_SESSION = array();
session_destroy();
?>

<div class="container logout-wrapper">
    <div class="logout-card">
        <h2>Logged out successfully 👋</h2>
        <p>Thank you for your visit
            <br>We look forward to seeing you again.</p>
        <a href="index.php" class="btn-shop">
            Back to Home
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>