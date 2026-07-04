<?php
include 'admin_header.php'; 

$total_products = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM products"));
$total_orders   = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM orders"));
$total_users    = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users"));

$unread_msgs    = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM contacts WHERE is_read = false"));
?>

<link rel="stylesheet" href="css/admin_style.css">

<div class="admin-main-wrapper">

    <div class="admin-content">
        <h1>Welcome to the Admin Dashboard 👋</h1>
        <p>Overview of current store performance:</p>

        <div class="stats-container">
            <div class="stat-box blue">
                <h3>Products</h3>
                <p class="number"><?php echo $total_products; ?></p>
                <a href="admin_products.php">Details </a>
            </div>

            <div class="stat-box green">
                <h3>Orders</h3>
                <p class="number"><?php echo $total_orders; ?></p>
                <a href="admin_orders.php">Details </a>
            </div>

            <div class="stat-box purple">
                <h3>Users</h3>
                <p class="number"><?php echo $total_users; ?></p>
                <a href="admin_users.php">Details </a>
            </div>

            <div class="stat-box orange">
                <h3>New Messages</h3>
                <p class="number"><?php echo $unread_msgs; ?></p>
                <a href="admin_messages.php">Read Messages </a>
            </div>
        </div>
    </div>
</div>

<?php 

include 'footer.php'; 
?>