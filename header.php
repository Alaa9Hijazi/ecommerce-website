<?php
session_start();
include_once 'config.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>HIJAZI STOR</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="nav">
        <div class="logo">
            <a href="index.php"><strong>HIJAZI STOR</strong></a>
        </div>

        <div class="nav-link">
            <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : ''; ?>">
                Home
            </a>

            <a href="products.php"
            class="<?= ($current_page == 'products.php' || $current_page == 'product_detail.php') ? 'active' : ''; ?>">
                Products
            </a>

            <a href="cart.php" class="<?= ($current_page == 'cart.php') ? 'active' : ''; ?>">
                Cart
            </a>

            <a href="contact.php" class="<?= ($current_page == 'contact.php') ? 'active' : ''; ?>">
                Contact Us
            </a>

            <?php if(isset($_SESSION['user_id'])): ?>

                <a href="profile.php" class="<?= ($current_page == 'profile.php') ? 'active' : ''; ?>">
                    My Profile
                </a>

                <?php if($_SESSION['role'] == 'admin'): ?>
                    <a href="admin_dashboard.php"
                    class="admin-link <?= ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>">
                        Admin Dashboard
                    </a>
                <?php endif; ?>

                <a href="logout.php">Logout</a>

            <?php else: ?>

                <a href="login.php" class="<?= ($current_page == 'login.php') ? 'active' : ''; ?>">
                    Login
                </a>

            <?php endif; ?>
        </div>
    </div>