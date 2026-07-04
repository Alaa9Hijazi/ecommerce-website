<?php
include 'header.php'; 

// التحقق من أن المستخدم قد قام بتسجيل الدخول قبل السماح له برؤية هذه الصفحة 
if (!isset($_SESSION['user_id'])) {
    // إعادة التوجيه لصفحة الدخول في حال لم يتم العثور على جلسة نشطة للمستخدم
    header("Location: login.php");
    exit();
}

// تخزين معرف المستخدم من الجلسة لاستخدامه في جلب البيانات الخاصة به فقط
$user_id = $_SESSION['user_id'];

// استخدام الاستعلامات  لحماية الموقع من هجمات حقن قواعد البيانات كما هو مطلوب في معايير الأمان
$stmt = $conn->prepare("SELECT order_id, order_date, total_amount, status FROM orders WHERE user_id = ? ORDER BY order_date DESC");
// ربط معرف المستخدم بالاستعلام لضمان جلب طلباته هو فقط وليس طلبات غيره
$stmt->bind_param("i", $user_id);
// تنفيذ الاستعلام في قاعدة البيانات
$stmt->execute();
// الحصول على مجموعة النتائج النهائية لمعالجتها وعرضها
$result = $stmt->get_result();
?>

<!-- بداية قسم عرض واجهة سجل الطلبات بتنسيق متجاوب -->
<div class="container order-history-page">
    <div class="btn-back">
        <!-- رابط للعودة لصفحة الملف الشخصي لتسهيل التنقل للمستخدم -->
        <a href="profile.php">← Back to My Profile</a>
    </div>
    
    <h2 class="section-title">Order History 📋</h2>
    <p class="section-subtitle">View and track all your previous purchases.</p>

    <div class="table-responsive">
        <table class="history-table">
            <thead>
                <tr>
                    <th>#</th> <!-- تم تغيير اسم العمود إلى # ليتناسب مع الترقيم التلقائي -->
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php // فحص ما إذا كان لدى المستخدم طلبات سابقة في قاعدة البيانات أم أن السجل فارغ ?>
                <?php if ($result->num_rows > 0): ?>
                    
                    <!-- 1. تعريف العداد بالرقم 1 قبل بدء حلقة التكرار -->
                    <?php $i = 1; ?>

                    <?php // حلقة تكرار للمرور على كافة الصفوف المسترجعة وعرض بيانات كل طلب بشكل منفصل ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <!-- 2. طباعة رقم السطر الحالي ثم زيادة العداد بمقدار 1 -->
                                <strong>#<?php echo $i; $i++; ?></strong>
                            </td>
                            <!-- عرض تاريخ الطلب بتنسيق سهل القراءة للمستخدم -->
                            <td><?php echo date('Y-m-d H:i', strtotime($row['order_date'])); ?></td>
                            <td class="total-amount">$<?php echo number_format($row['total_amount'], 2); ?></td>
                            <td>
                                <!-- عرض حالة الطلب داخل شارة ملونة تختلف باختلاف الحالة البرمجية -->
                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <!-- رابط ينقل المستخدم لصفحة التفاصيل لعرض الأصناف المشتراة داخل هذا الطلب يظل محتفظاً بالـ order_id الحقيقي -->
                                <a href="order_details.php?id=<?php echo $row['order_id']; ?>&from=my_orders" class="btn-details">View Details</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <?php // في حال عدم وجود بيانات يظهر هذا السطر لإبلاغ المستخدم أن سجله فارغ حالياً ?>
                    <tr>
                        <td colspan="5" class="empty-history-msg">You haven't placed any orders yet. 🛍️</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
include 'footer.php'; 
?>
