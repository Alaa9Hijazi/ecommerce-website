<?php
include 'header.php';

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // استلام البيانات وتنظيفها
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $password  = $_POST['password'];
    $confirm_password = $_POST['confirm_password']; // تأكيد كلمة المرور
    $phone     = mysqli_real_escape_string($conn, $_POST['phone']);
    $address   = mysqli_real_escape_string($conn, $_POST['address']);

    // التحقق من تطابق كلمتي المرور
    if ($password !== $confirm_password) {

        $error_msg = "Passwords do not match!";

    } else {

        // التحقق من وجود البريد الإلكتروني مسبقاً
        $check_email = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $result = $check_email->get_result();

        if ($result->num_rows > 0) {

            $error_msg = "Sorry, this email is already registered!";

        } else {

            // تشفير كلمة المرور
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // إدراج المستخدم الجديد
            $stmt = $conn->prepare("
                INSERT INTO users (full_name, email, password, phone, address, role)
                VALUES (?, ?, ?, ?, ?, 'customer')
            ");

            $stmt->bind_param(
                "sssss",
                $full_name,
                $email,
                $hashed_password,
                $phone,
                $address
            );

            if ($stmt->execute()) {

                // التحويل مع باراميتر النجاح
                header("Location: register.php?register=success");
                exit();

            } else {

                $error_msg = "Registration error: " . $conn->error;

            }
        }
    }
}
?>

<link rel="stylesheet" href="css/register_style.css">

<div class="container register-wrapper">

    <div class="register-card">

        <h2>Create New Account 📝</h2>

        <?php if($error_msg): ?>
            <div class="error-message">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="register-form">

            <!-- الاسم الكامل -->
            <label>Full Name:</label>
            <input type="text" name="full_name" required>

            <!-- البريد الإلكتروني -->
            <label>Email Address:</label>
            <input type="email" name="email" required>

            <!-- كلمة المرور -->
            <label>Password:</label>
            <input type="password" name="password" required>

            <!-- تأكيد كلمة المرور -->
            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required>

            <!-- رقم الهاتف -->
            <label>Phone Number:</label>
            <input type="text" name="phone">

            <!-- العنوان -->
            <label>Full Address:</label>
            <textarea name="address" rows="3"></textarea>

            <!-- زر التسجيل -->
            <button type="submit" class="btn-register">
                Create Account
            </button>

        </form>

        <div class="login-link">
            Already have an account?
            <a href="login.php">Login here</a>
        </div>

    </div>

</div>

<?php include 'footer.php'; ?>