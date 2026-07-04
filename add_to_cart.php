<?php
session_start();
include 'config.php';

// التحقق من أن الزبون قد قام بتسجيل الدخول فعلياً حيث تمنع سياسة المتجر إضافة الأصناف للسلة دون وجود هوية معروفة لضمان الخصوصية
if (!isset($_SESSION['user_id'])) {

    // إرسال المستخدم لصفحة تسجيل الدخول مع تنبيه بضرورة تسجيل الدخول أولاً
    echo "<script>
        window.location.href = 'login.php?login_required=1';
    </script>";
    exit();
}

// استقبال البيانات المرسلة من نموذج إضافة المنتج إلى السلة والتأكد من أنها صحيحة
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // تخزين بيانات المستخدم والمنتج والكمية القادمة من الفورم
    $user_id    = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $quantity   = intval($_POST['quantity']);

    // فحص صلاحية الكمية المختارة والتأكد من أنها أكبر من صفر
    if ($quantity <= 0) {

        // إرجاع المستخدم للصفحة السابقة إذا كانت الكمية غير صحيحة
        echo "<script>
            window.history.back();
        </script>";
        exit();
    }

    // التحقق من وجود المنتج مسبقاً في سلة المستخدم لمنع التكرار
    $check_stmt = $conn->prepare("
        SELECT * FROM cart_items
        WHERE user_id = ? AND product_id = ?
    ");
    $check_stmt->bind_param("ii", $user_id, $product_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    // إذا كان المنتج موجوداً مسبقاً يتم تحديث الكمية بدل إنشاء سجل جديد
    if ($result->num_rows > 0) {

        // تحديث الكمية بإضافة الكمية الجديدة إلى القديمة
        $update_stmt = $conn->prepare("
            UPDATE cart_items
            SET quantity = quantity + ?
            WHERE user_id = ? AND product_id = ?
        ");
        $update_stmt->bind_param("iii", $quantity, $user_id, $product_id);
        $update_stmt->execute();

    } else {

        // إذا لم يكن المنتج موجوداً يتم إضافته لأول مرة إلى السلة
        $insert_stmt = $conn->prepare("
            INSERT INTO cart_items (user_id, product_id, quantity)
            VALUES (?, ?, ?)
        ");
        $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insert_stmt->execute();
    }

    // تحويل المستخدم إلى صفحة السلة بعد إضافة المنتج مع تمرير باراميتر للتنبيه عبر JavaScript
    echo "<script>
        window.location.href = 'cart.php?added=1';
    </script>";

    // إغلاق الاستعلامات المحضرة بعد الانتهاء لتحسين الأداء
    $check_stmt->close();
    if(isset($update_stmt)) $update_stmt->close();
    if(isset($insert_stmt)) $insert_stmt->close();
}
?>