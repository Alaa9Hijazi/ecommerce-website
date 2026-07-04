<?php
include 'header.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        if (password_verify($password, $row['password'])) {

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();

        } else {
            $error = "Incorrect password!";
        }

    } else {
        $error = "This email is not registered!";
    }
}
?>

<div class="container login-wrapper">
    <div class="login-card">

        <h2>Login to Store 🔑</h2>
        <p>Welcome back, please enter your details to continue.</p>

        <?php if (!empty($error)): ?>
            <div class="error-msg">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="auth-form">

            <label>Email Address:</label>
            <input type="email" name="email" placeholder="example@mail.com" required>

            <label>Password:</label>
            <input type="password" name="password" placeholder="********" required>

            <button type="submit" class="btn-shop">Login</button>

        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="register.php">Create a new account here</a></p>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>