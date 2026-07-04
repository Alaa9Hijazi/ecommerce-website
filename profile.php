<?php
include 'header.php';

//  التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

//  معالجة طلب التحديث
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $update_stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE user_id = ?");
    $update_stmt->bind_param("sssi", $full_name, $phone, $address, $user_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['full_name'] = $full_name;
        $message = "<div class='alert alert-success'>Profile updated successfully! ✅</div>";
    } else {
        $message = "<div class='alert alert-danger'>An error occurred while updating.</div>";
    }
}

//  جلب بيانات المستخدم الحالية
$query = $conn->prepare("SELECT full_name, email, phone, address FROM users WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$user = $query->get_result()->fetch_assoc();
?>

<div class="container">
    <div class="profile-card">
        <h2>Edit My Profile 👤</h2>
        <?php echo $message; ?>
        
        <form method="POST" action="profile.php" class="auth-form">
            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            
            <label>Email (cannot be changed):</label>
            <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled class="input-disabled">
            
            <label>Phone Number:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
            
            <label>Shipping Address:</label>
            <textarea name="address" class="profile-textarea"><?php echo htmlspecialchars($user['address']); ?></textarea>
            
            <button type="submit" class="btn-update-profile">Save Changes</button>
        </form>

        <div class="links-box">
            <a href="order_history.php"> View My Orders</a>
            <span class="divider">|</span>
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>