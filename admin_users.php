<?php
include 'admin_header.php';

//تحديثات الرتبة سواء ادمن او عميل
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_role'])) {
    $u_id = intval($_POST['user_id']);
    $new_role = $_POST['role'];
    
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_role, $u_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('User role updated successfully! '); window.location.href='admin_users.php';</script>";
    }
    $stmt->close();
}

//  معالجة حذف المستخدم مع التحقق من القيود
if (isset($_GET['delete'])) {
    $u_id = intval($_GET['delete']);
    
    // التحقق من وجود طلبات سابقة
    $check_stmt = $conn->prepare("SELECT order_id FROM orders WHERE user_id = ? LIMIT 1");
    $check_stmt->bind_param("i", $u_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // إظهار تحذير في حال وجود طلبات مرتبطة بالمستخدم
        echo "<script>alert('Warning: Cannot delete this user because they have existing orders in the system! ⚠️');
                window.location.href='admin_users.php';</script>";
    } else {
        //  تنفيذ الحذف في حال عدم وجود طلبات
        $del_stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $del_stmt->bind_param("i", $u_id);
        if ($del_stmt->execute()) {
            echo "<script>alert('User account deleted successfully.');
            window.location.href='admin_users.php';</script>";
        }
        $del_stmt->close();
    }
    $check_stmt->close();
}

// جلب كافة المستخدمين لعرضهم في الجدول
$result = $conn->query("SELECT user_id, full_name, email, role FROM users ORDER BY created_at DESC");
?>

<div class="container admin-users-page">
    <div class="btn-back">
        <a href="admin_dashboard.php" >← Back to Dashboard</a>
    </div>
    
    <h1 class="section-title">Store Users Management 👥</h1>

    <div class="admin-table-card">
        <div class="table-responsive">
            <table class="cart-table admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Current Role</th>
                        <th>Change Role</th>
                        <th>Orders</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $i = 1; ?>
                        
                        <?php while($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php echo $i; $i++; ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="role-badge <?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" class="role-update-form">
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                    <select name="role" class="role-select">
                                        <option value="customer" <?php if($user['role']=='customer') echo 'selected'; ?>>Customer</option>
                                        <option value="admin" <?php if($user['role']=='admin') echo 'selected'; ?>>Admin</option>
                                    </select>
                                    <button type="submit" name="update_role" class="btn-update-small">Update</button>
                                </form>
                            </td>
                            <td>
                                <a href="admin_user_orders.php?user_id=<?php echo $user['user_id']; ?>"
                                class="view-details-link">
                                    View Orders
                                </a>
                            </td>
                            <td>
                                <a href="admin_users.php?delete=<?php echo $user['user_id']; ?>" class="btn-del ">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;">No users found in the system.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>
