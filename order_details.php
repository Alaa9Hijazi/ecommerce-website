<?php
include 'header.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// التحقق من وجود رقم الطلب
if (!isset($_GET['id'])) {

    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_orders.php");
    } else {
        header("Location: order_history.php");
    }

    exit();
}

$order_id = intval($_GET['id']);


// تحديد مصدر الزيارة لمعرفة وجهة زر "Back"
//   - my_orders     : العميل قادم من order_history.php
//   - admin_orders  : الأدمن قادم من admin_orders.php
//   - user_orders   : الأدمن قادم من admin_user_orders.php (طلبات مستخدم محدد)

$from = isset($_GET['from']) ? $_GET['from'] : '';

// لو الأدمن داخل ع صفحة عميل بدون تحديد from، نعتبره قادم من admin_orders
if ($from === '' && $_SESSION['role'] == 'admin') {
    $from = 'admin_orders';
}
// لو العميل بدون تحديد from، نعتبره قادم من order_history
if ($from === '' && $_SESSION['role'] != 'admin') {
    $from = 'my_orders';
}

// بناء رابط العودة بناءً على المصدر
$user_id_param = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

switch ($from) {
    case 'admin_orders':
        $back_url = 'admin_orders.php';
        $back_label = '← Back to All Orders';
        break;

    case 'user_orders':
        // العودة لصفحة طلبات نفس المستخدم (مع الحفاظ على user_id)
        if ($user_id_param > 0) {
            $back_url = 'admin_user_orders.php?user_id=' . $user_id_param;
        } else {
            // لو ما في user_id، نرجّع للأدمن أوردرك كـ fallback
            $back_url = 'admin_orders.php';
        }
        $back_label = '← Back to This User\'s Orders';
        break;

    case 'my_orders':
    default:
        $back_url = 'order_history.php';
        $back_label = '← Back to My Orders';
        break;
}

// الأدمن يستطيع مشاهدة أي طلب
if ($_SESSION['role'] == 'admin') {

    $order_query = $conn->prepare(
        "SELECT * FROM orders WHERE order_id = ?"
    );
    $order_query->bind_param("i", $order_id);

} else {

    // العميل يستطيع مشاهدة طلباته فقط
    $user_id = $_SESSION['user_id'];

    $order_query = $conn->prepare(
        "SELECT * FROM orders WHERE order_id = ? AND user_id = ?"
    );
    $order_query->bind_param("ii", $order_id, $user_id);
}

$order_query->execute();
$order_data = $order_query->get_result()->fetch_assoc();

if (!$order_data) {
    echo "<div class='container'><p class='alert alert-danger'>Order not found or access denied.</p></div>";
    include 'footer.php';
    exit();
}

// جلب عناصر الطلب
$items_stmt = $conn->prepare("
    SELECT oi.*, p.name, p.image_url
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
");

$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
?>

<div class="container">
    <div class="profile-card">

        <h2 class="section-title">
            Order Details #<?php echo $order_id; ?>
        </h2>

        <div class="order-info-summary">
            <p>
                <strong>Date:</strong>
                <?php echo date('F j, Y', strtotime($order_data['order_date'])); ?>
            </p>

            <p>
                <strong>Status:</strong>
                <span class="status-badge status-<?php echo $order_data['status']; ?>">
                    <?php echo ucfirst($order_data['status']); ?>
                </span>
            </p>

            <p>
                <strong>Shipping Address:</strong>
                <?php echo htmlspecialchars($order_data['shipping_address']); ?>
            </p>
        </div>

        <div class="table-responsive">
            <table class="history-table">

                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>

                <?php while($item = $items_result->fetch_assoc()): ?>

                    <tr>
                        <td>
                            <img
                                src="<?php echo htmlspecialchars($item['image_url']); ?>"
                                alt="Product"
                                class="cart-img"
                            >
                        </td>

                        <td>
                            <?php echo htmlspecialchars($item['name']); ?>
                        </td>

                        <td>
                            $<?php echo number_format($item['unit_price'], 2); ?>
                        </td>

                        <td>
                            <?php echo $item['quantity']; ?>
                        </td>

                        <td style="font-weight:bold;">
                            $
                            <?php
                            echo number_format(
                                $item['unit_price'] * $item['quantity'],
                                2
                            );
                            ?>
                        </td>
                    </tr>

                <?php endwhile; ?>

                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="4">
                            Grand Total:
                        </td>

                        <td>
                            $<?php echo number_format($order_data['total_amount'], 2); ?>
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>

        <div class="links-box">
            <a href="<?php echo $back_url; ?>">
                <?php echo $back_label; ?>
            </a>
        </div>

    </div>
</div>

<?php include 'footer.php'; ?>

