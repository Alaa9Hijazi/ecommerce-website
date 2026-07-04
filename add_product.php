<?php
include 'admin_header.php'; 

// احضار كافة التصنيفات المتوفرة من قاعدة البيانات لعرضها لاحقاً في القائمة المنسدلة لسهولة اختيار قسم المنتج
$categories_res = mysqli_query($conn, "SELECT * FROM categories");

// التحقق من أن إرسال البيانات تم عبر النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // استلام البيانات المدخلة من المدير وتخزينها في متغيرات تمهيداً لمعالجتها وحفظها في الجداول
    $name        = $_POST['name'];
    $description = $_POST['description'];
    $price       = $_POST['price'];
    $stock       = $_POST['stock'];
    $image_url   = $_POST['image_url'];
    $category_id = $_POST['category_id'];

    // استخدام الاستعلامات
    $stmt = $conn->prepare(
        "INSERT INTO products (name, description, price, stock_quantity, image_url, category_id)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param("ssdisi", $name, $description, $price, $stock, $image_url, $category_id);

    // تنفيذ عملية الإضافة وفي حال النجاح يتم تحويل المستخدم لصفحة المنتجات مع تمرير باراميتر للتنبيه عبر JavaScript
    if ($stmt->execute()) {

        echo "<script>
            window.location.href = 'admin_products.php?added=1';
        </script>";

        exit();

    } else {
        // عرض رسالة خطأ في حال فشل العملية لضمان قدرة المدير على تتبع المشاكل التقنية
        echo "<div class='error'>Insert error: " . $conn->error . "</div>";
    }

    // إغلاق الاستعلام بعد الانتهاء من استخدامه للحفاظ على موارد الخادم وتحسين أداء الموقع
    $stmt->close();
}
?>

<link rel="stylesheet" href="css/admin_style.css">

<!-- زر الرجوع -->
<div class="btn-back">
    <a href="admin_products.php">← Back to Products List</a>
</div>

<div class="container">
    <div class="form-wrapper">

        <h2>Add New Product</h2>

        <!-- نموذج الإضافة الذي يرسل البيانات لنفس الصفحة لمعالجتها برمجياً وحفظها في قاعدة البيانات -->
        <form action="add_product.php" method="POST" class="admin-form">

            <label>Product Name:</label>
            <input type="text" name="name" required placeholder="Example: UltraBook Laptop">

            <label>Product Description:</label>
            <textarea name="description" rows="4" placeholder="Write product details and features here..."></textarea>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Price ($):</label>
                    <input type="number" step="0.01" name="price" required>
                </div>

                <div style="flex: 1;">
                    <label>Stock Quantity:</label>
                    <input type="number" name="stock" required>
                </div>
            </div>

            <label>Product Image URL:</label>
            <input type="text" name="image_url" placeholder="Example: images/product1.jpg">

            <label>Product Category:</label>
            <select name="category_id" required>
                <option value="">-- Select Category --</option>

                <?php while($cat = mysqli_fetch_assoc($categories_res)): ?>
                    <option value="<?php echo $cat['category_id']; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endwhile; ?>

            </select>

            <div>
                <button type="submit" class="btn-update">
                    Save & Add Product
                </button>
            </div>

        </form>
    </div>
</div>

<?php
include 'footer.php';
?>