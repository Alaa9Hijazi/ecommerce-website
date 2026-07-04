<?php
include 'admin_header.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    if ($stmt->execute()) {
        header("Location: admin_orders.php?updated=1");
        exit();
    }
    else {
        echo "<div class='alert alert-danger'>Update error: " . htmlspecialchars($conn->error) . "</div>";
    }
    $stmt->close();
}

//  جلب كافة الطلبات مع أسماء الزبائن
$sql = "SELECT orders.*, users.full_name
        FROM orders 
        JOIN users ON orders.user_id = users.user_id
        ORDER BY order_date DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container admin-orders-page">

    <div class="btn-back">
        <a href="admin_dashboard.php">← Back to Dashboard</a>
    </div>

    <h1 class="section-title">Customer Orders Management 📋</h1>
    <!-- جدول الطلبات  -->
    <div class="table-responsive">
        <table class="cart-table admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Current Status</th>
                    <th>Update Status</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
            
                <?php $i = 1; ?>

                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>
                        #<?php echo $i; $i++; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><?php echo date('Y-m-d H:i', strtotime($row['order_date'])); ?></td>
                    <td class="price-cell">$<?php echo number_format($row['total_amount'], 2); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $row['status']; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td>
                        <form method="POST" class="status-update-form">
                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                            <select name="status" class="status-select">
                                <option value="pending" <?php if($row['status']=='pending') echo 'selected'; ?>>Pending</option>
                                <option value="Paid" <?php if($row['status']=='Paid') echo 'selected'; ?>>Paid</option>
                                <option value="shipped" <?php if($row['status']=='shipped') echo 'selected'; ?>>Shipped</option>
                                <option value="delivered" <?php if($row['status']=='delivered') echo 'selected'; ?>>Delivered</option>
                                <option value="cancelled" <?php if($row['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="btn-update-small">Update</button>
                        </form>
                    </td>
                    <td>
                        <a href="order_details.php?id=<?php echo $row['order_id']; ?>&from=admin_orders" class="view-details-link">
                        Details
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include 'footer.php';
?>
