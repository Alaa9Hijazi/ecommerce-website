<?php
include 'admin_header.php';

// ============================================================
// صفحة عرض طلبات مستخدم محدد (للأدمن فقط)
// تأخذ user_id من الـ URL وتعرض جميع طلبات هذا المستخدم
// زر "Back" في صفحة التفاصيل سيرجع لهذه الصفحة لنفس المستخدم
// ============================================================

if (!isset($_GET['user_id']) || intval($_GET['user_id']) <= 0) {
    // لو ما في user_id صحيح، نرجّع للأدمن أوردرك
    header("Location: admin_orders.php");
    exit();
}

$viewed_user_id = intval($_GET['user_id']);

// جلب بيانات المستخدم (الاسم) لعرضه في العنوان
$user_stmt = $conn->prepare("SELECT full_name, email FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $viewed_user_id);
$user_stmt->execute();
$user_info = $user_stmt->get_result()->fetch_assoc();
$user_stmt->close();

if (!$user_info) {
    echo "<div class='container'><p class='alert alert-danger'>User not found.</p></div>";
    include 'footer.php';
    exit();
}

// جلب جميع طلبات هذا المستخدم
$orders_stmt = $conn->prepare(
    "SELECT order_id, order_date, total_amount, status
        FROM orders
        WHERE user_id = ?
        ORDER BY order_date DESC"
);
$orders_stmt->bind_param("i", $viewed_user_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
?>

<div class="container admin-orders-page">
    <div class="btn-back">
        <a href="admin_users.php">← Back to Users</a>
    </div>

    <h1 class="section-title">
        Orders of: <?php echo htmlspecialchars($user_info['full_name']); ?> 📦
    </h1>
    <p class="section-subtitle">
        Email: <?php echo htmlspecialchars($user_info['email']); ?>
    </p>

    <div class="table-responsive">
        <table class="cart-table admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Current Status</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders_result->num_rows > 0): ?>
                    <?php while($row = $orders_result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['order_id']; ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($row['order_date'])); ?></td>
                        <td class="price-cell">$<?php echo number_format($row['total_amount'], 2); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $row['status']; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td>
                            <!--
                                نمرر from=user_orders و user_id=$viewed_user_id
                                عشان زر Back في صفحة التفاصيل يرجّع لهذه الصفحة لنفس المستخدم
                            -->
                            <a href="order_details.php?id=<?php echo $row['order_id']; ?>&from=user_orders&user_id=<?php echo $viewed_user_id; ?>"
                               class="view-details-link">
                                Details
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">
                            This user has no orders yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$orders_stmt->close();
include 'footer.php';
?>
