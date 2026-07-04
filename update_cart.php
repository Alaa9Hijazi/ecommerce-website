<?php
include 'config.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $cart_id = $_POST['cart_id'];
    $new_qty = intval($_POST['quantity']);
        // فحص صلاحية الكمية الجديدة للتأكد من أنها تزيد عن الصفر لتفادي الأخطاء المنطقية
    if ($new_qty > 0) {
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ?");
        $stmt->bind_param("ii", $new_qty, $cart_id);
        $stmt->execute();
    }
    header("Location: cart.php");
    exit();
}
?>