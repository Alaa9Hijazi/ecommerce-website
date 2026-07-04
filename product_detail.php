<?php
include 'header.php';

// التحقق من وجود المعرف في الرابط
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // تحويل المعرف لرقم صحيح لزيادة الأمان ]

    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // إذا لم يوجد المنتج، العودة للرئيسية
    if (!$product) {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<div class="container">
    <div class="btn-back">
        <a href="products.php" class="back-link">← Back to Products</a> 
    </div>

    <div class="product-detail-wrapper">
        <div class="product-image">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <!-- معلومات المنتج -->
        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
            <h2 class="price">Price: $<?php echo number_format($product['price'], 2); ?></h2>

            <p>Available Stock: 
                <span class="<?php echo ($product['stock_quantity'] > 0) ? 'in-stock' : 'out-of-stock'; ?>">
                    <strong><?php echo $product['stock_quantity']; ?></strong>
                </span>
            </p>

            <!--نموذج إضافة للسلة يرسل البيانات-->
            <form action="add_to_cart.php" method="POST" class="cart-form">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">

                <div class="quantity-box">
                    <label>Quantity:</label>
                    <input type="number" name="quantity" value="1" min="1"
                        max="<?php echo $product['stock_quantity']; ?>" required>
                </div>

                <button type="submit" class="btn-shop full-width" <?php echo ($product['stock_quantity'] <= 0) ? 'disabled' : ''; ?>>
                    <?php echo ($product['stock_quantity'] > 0) ? 'Add to Cart 🛒' : 'Out of Stock'; ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>