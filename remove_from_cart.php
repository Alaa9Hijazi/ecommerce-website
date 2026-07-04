<?php
//  تشغيل الاتصال وبدء الجلسة
include 'config.php';
session_start();

// التأكد من تسجيل الدخول قبل الحذف
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

//  التأكد من استقبال المعرف ومعالجته برمجياً
if (isset($_GET['id'])) {
    $cart_id = intval($_GET['id']); // تحويل القيمة لرقم صحيح لزيادة الأمان
    $user_id = $_SESSION['user_id'];

    // (Prepared Statements)
    $stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);

    if ($stmt->execute()) {
        // العودة للسلة بعد الحذف بنجاح لرؤية المجموع الجديد
        header("Location: cart.php?msg=removed");
        exit();
    } else {
        // إظهار صفحة خطأ منسقة في حال الفشل
        include 'header.php';
        echo "<div class='container error-box'>";
        echo "<h2>Deletion Error:</h2>";
        echo "<p>Could not remove item. Please try again.</p>";
        echo "<a href='cart.php' class='btn-shop'>Back to Cart</a>";
        echo "</div>";
        include 'footer.php';
    }
    $stmt->close();
} else {
    header("Location: cart.php");
    exit();
}
?>