<?php

include 'config.php';
include 'admin_header.php';

// الحصول على ID المنتج وجلب بياناته
if (!isset($_GET['id'])) {
    header("Location: admin_products.php");
    exit();
}

$product_id = intval($_GET['id']);

// معالجة التحديث (UPDATE) عند إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $img = mysqli_real_escape_string($conn, $_POST['image_url']);
    $cat = $_POST['category_id'];

    $sql = "UPDATE products SET name=?, description=?, price=?, stock_quantity=?, image_url=?, category_id=? WHERE product_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdisii", $name, $desc, $price, $stock, $img, $cat, $product_id);

    if ($stmt->execute()) {
        $success = "Product updated successfully!";
    }
}

// جلب البيانات الحالية لملء النموذج
$res = $conn->query("SELECT * FROM products WHERE product_id = $product_id");
$p = $res->fetch_assoc();
$cats = $conn->query("SELECT * FROM categories");
?>

<div class="container admin-edit-page">
    <div class="form-card">

        <h2 class="section-title">
            Edit Product: <?php echo htmlspecialchars($p['name']); ?>
        </h2>

        <?php if (!empty($success)): ?>
            <div class="success-msg">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="styled-form">

            <div class="form-group">
                <label>Product Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($p['name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" rows="4" required><?php echo htmlspecialchars($p['description']); ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Price ($):</label>
                    <input type="number" step="0.01" name="price" value="<?php echo $p['price']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Stock Quantity:</label>
                    <input type="number" name="stock" value="<?php echo $p['stock_quantity']; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Image URL:</label>
                <input type="text" name="image_url" value="<?php echo htmlspecialchars($p['image_url']); ?>" required>
            </div>

            <div class="form-group">
                <label>Category:</label>
                <select name="category_id">
                    <?php while($c = $cats->fetch_assoc()): ?>
                        <option value="<?php echo $c['category_id']; ?>"
                            <?php if($c['category_id'] == $p['category_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" name="update_product" class="btn-save">
                    Update Product
                </button>
                <a href="admin_products.php" class="btn-cancel">Cancel</a>
            </div>

        </form>

    </div>
</div>

<?php include 'footer.php'; ?>