<?php
include 'admin_header.php'; 

// تحديث حالة الرسالة لتصبح مقروءة"
if (isset($_GET['read'])) {
    $id = intval($_GET['read']);
    $stmt = $conn->prepare("UPDATE contacts SET is_read = true WHERE message_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: admin_messages.php");
        exit();
    }
}

// حذف الرسالة نهائياً
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM contacts WHERE message_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: admin_messages.php");
        exit();
    }
}

//  جلب كافة الرسائل (الأحدث أولاً)
$result = mysqli_query($conn, "SELECT * FROM contacts ORDER BY submitted_at DESC");
?>

<div class="container admin-messages-page">
        <div class="btn-back">
        <a href="admin_dashboard.php">← Back to Dashboard</a>
    </div>
    <h1 class="section-title">Customer Contact Messages ✉️</h1>
    
    <div class="table-responsive">
        <table class="cart-table admin-table">
            <thead>
                <tr>
                    <th>Sender</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($msg = mysqli_fetch_assoc($result)): ?>
                <!-- تمييز الرسائل غير المقروءة بكلاس -->
                <tr class="<?php echo $msg['is_read'] ? '' : 'unread-row'; ?>">
                    <td><?php echo htmlspecialchars($msg['name']); ?></td>
                    <td><?php echo htmlspecialchars($msg['email']); ?></td>
                    <td><strong><?php echo htmlspecialchars($msg['subject']); ?></strong></td>
                    <td class="message-text-cell"><?php echo htmlspecialchars($msg['message']); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($msg['submitted_at'])); ?></td>
                    <td class="table-actions">
                        <?php if(!$msg['is_read']): ?>
                            <a href="admin_messages.php?read=<?php echo $msg['message_id']; ?>" class="action-read">
                                Mark Read
                            </a>
                        <?php endif; ?>
                        
                        <a href="admin_messages.php?delete=<?php echo $msg['message_id']; ?>" 
                                class="btn-del delete-message-link">
                                Delete
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