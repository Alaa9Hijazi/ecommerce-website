<?php
session_start();
include_once 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Hijazi Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin_header_style.css">
</head>

<body class="admin-page">
<header class="admin-main-header">
    <nav class="admin-navbar">
        <div class="nav-container">

            <div class="admin-logo">
                <a href="admin_dashboard.php">Admin Panel 🛠️</a>
            </div>

            <ul class="admin-nav-links">
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="admin_products.php">Products</a></li>
                <li><a href="admin_orders.php">Orders</a></li>
                <li><a href="admin_users.php">Users</a></li>
                <li><a href="admin_messages.php">Messages</a></li>
                <li class="site-preview"><a href="index.php">View Store</a></li>
                <li class="logout-item"><a href="logout.php">Logout</a></li>
            </ul>

        </div>
    </nav>
</header>