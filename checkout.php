<?php
include 'header.php';
?>

<link rel="stylesheet" href="css/checkout.css">

<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// جلب عنوان المستخدم
$user_stmt = $conn->prepare("
    SELECT address
    FROM users
    WHERE user_id = ?
");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();

$user_data = $user_stmt->get_result()->fetch_assoc();
$shipping_address = $user_data['address'];

// جلب عناصر السلة
$cart_stmt = $conn->prepare("
    SELECT
        ci.product_id,
        ci.quantity,
        p.price,
        p.stock_quantity
    FROM cart_items ci
    JOIN products p
        ON ci.product_id = p.product_id
    WHERE ci.user_id = ?
");

$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();

$cart_result = $cart_stmt->get_result();

$total_amount = 0;
$items = [];

while ($row = $cart_result->fetch_assoc()) {

    // التحقق من توفر الكمية
    if ($row['quantity'] > $row['stock_quantity']) {

        echo '
        <div class="container checkout-container">
            <div class="processing-card">
                <h2>Not Enough Stock Available ❌</h2>
                <p>
                    One or more products in your cart exceed the available stock.
                </p>
                <a href="cart.php" class="btn-shop">
                    Back to Cart
                </a>
            </div>
        </div>';

        include 'footer.php';
        exit();
    }

    $total_amount += $row['price'] * $row['quantity'];
    $items[] = $row;
}

?>

<div class="container checkout-container">

<?php

if ($total_amount > 0) {

    // إنشاء الطلب
    $order_stmt = $conn->prepare("
        INSERT INTO orders
        (user_id, total_amount, shipping_address, status)
        VALUES (?, ?, ?, 'pending')
    ");

    $order_stmt->bind_param(
        "ids",
        $user_id,
        $total_amount,
        $shipping_address
    );

    if ($order_stmt->execute()) {

        $order_id = $conn->insert_id;

        // إضافة المنتجات للطلب
        $item_stmt = $conn->prepare("
            INSERT INTO order_items
            (order_id, product_id, quantity, unit_price)
            VALUES (?, ?, ?, ?)
        ");

        // تحديث المخزون
        $stock_stmt = $conn->prepare("
            UPDATE products
            SET stock_quantity = stock_quantity - ?
            WHERE product_id = ?
        ");

        foreach ($items as $item) {

            // إضافة المنتج للطلب
            $item_stmt->bind_param(
                "iiid",
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            );

            $item_stmt->execute();

            // خصم الكمية من المخزون
            $stock_stmt->bind_param(
                "ii",
                $item['quantity'],
                $item['product_id']
            );

            $stock_stmt->execute();
        }

        // حذف السلة
        $clear_stmt = $conn->prepare("
            DELETE FROM cart_items
            WHERE user_id = ?
        ");

        $clear_stmt->bind_param("i", $user_id);
        $clear_stmt->execute();

?>

        <div class="processing-card">

            <h2>Order Placed Successfully! 🎉</h2>

            <p>
                Thank you for shopping with us.<br>
                Your Order Number:
                <strong>#<?php echo $order_id; ?></strong>
            </p>

            <a href="index.php" class="btn-shop">
                Back to Home
            </a>

        </div>

<?php

    }

} else {

    header("Location: products.php?empty=1");
    exit();
}

?>

</div>

<?php include 'footer.php'; ?>