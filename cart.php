<?php
include 'header.php';

// التحقق من وجود جلسة نشطة للمستخدم لضمان الخصوصية ومنع الوصول غير المصرح به للسلة
if (!isset($_SESSION['user_id'])) {
    // توجيه المستخدم لصفحة تسجيل الدخول في حال لم يكن مسجلاً
    header("Location: login.php");
    exit();
}

// تخزين معرف المستخدم الحالي المستخرج من الجلسة لاستخدامه في جلب بياناته الخاصة
$user_id = $_SESSION['user_id'];

//جلب عناصر السلة مع تفاصيل المنتجات من جدولين مختلفين لضمان دقة البيانات
$sql = "SELECT c.cart_id, p.name, p.price, p.image_url, c.quantity
        FROM cart_items c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ?";

// استخدام الاستعلامات
$stmt = $conn->prepare($sql);
// ربط المتغيرات بالاستعلام وتحديد نوع البيانات
$stmt->bind_param("i", $user_id);
// تنفيذ الاستعلام البرمجي داخل قاعدة البيانات
$stmt->execute();
// الحصول على مجموعة النتائج المسترجعة لمعالجتها برمجياً وعرضها للمستخدم
$result = $stmt->get_result();

// تعريف متغير لتجميع إجمالي مبالغ المنتجات المختارة في السلة
$total_all = 0;
?>
<link rel="stylesheet" href="css/cart_style.css">

<div class="container cart-container">

    <h2 class="section-title">Shopping Cart 🛒</h2>

    <table class="cart-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

        <?php // التحقق برمجياً مما إذا كانت السلة تحتوي على منتجات أم أنها فارغة حالياً  ?>
        <?php if ($result->num_rows > 0): ?>

            <?php // حلقة تكرار للمرور على كافة السجلات المسترجعة من قاعدة البيانات وعرضها في الجدول  ?>
            <?php while($row = $result->fetch_assoc()):
                // حساب الإجمالي الفرعي لكل منتج بناءً على السعر والكمية المحددة
                $subtotal = $row['price'] * $row['quantity'];
                // إضافة الإجمالي الفرعي إلى المجموع الكلي النهائي للسلة
                $total_all += $subtotal;
            ?>
            <tr>
                <td>
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Product" class="cart-img">
                </td>
                <td>
                    <?php echo htmlspecialchars($row['name']); ?>
                </td>
                <td>
                    $<?php echo number_format($row['price'], 2); ?>
                </td>
                <td>
                    <!-- نموذج تحديث الكمية الذي يرسل البيانات عبر وسيلة آمنة للخادم  -->
                    <form action="update_cart.php" method="POST" class="update-cart-form">

                        <input
                            type="hidden"
                            name="cart_id"
                            value="<?php echo $row['cart_id']; ?>"
                        >
                        <input
                            type="number"
                            name="quantity"
                            value="<?php echo $row['quantity']; ?>"
                            min="1"
                            class="qty-input"
                        >
                        <button type="submit" class="btn-update-small">
                            Update
                        </button>
                    </form>
                </td>
                <td>
                    $<?php echo number_format($subtotal, 2); ?>
                </td>
                <td>
                    <!-- رابط حذف المنتج من السلة الذي يمرر المعرف عبر الرابط لمعالجته برمجياً  -->
                    <a href="remove_from_cart.php?id=<?php echo $row['cart_id']; ?>" class="btn-remove">
                        Delete
                    </a>
                </td>
            </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>
                <!-- رسالة تظهر للمستخدم في حال كانت سلة مشترياته لا تحتوي على أي عناصر -->
                <td colspan="6" class="empty-cart">
                    Your cart is empty.
                    <a href="products.php">Start shopping!</a>
                </td>
            </tr>

        <?php endif; ?>

        </tbody>
    </table>

    <div class="checkout-box">

        <h3>
            Total Amount:
            <span class="total-amount-text">
                <?php // عرض المبلغ الإجمالي الكلي النهائي لكافة محتويات السلة  ?>
                $<?php echo number_format($total_all, 2); ?>
            </span>
        </h3>

        <!-- رابط الانتقال لعملية الدفع وإتمام الشراء النهائية  -->
        <a href="checkout.php" class="btn-checkout-link">
            Checkout
        </a>

    </div>

</div>

<?php include 'footer.php';
?>