<?php
include 'admin_header.php';

// حذف منتج
if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin_products.php?deleted=1");
        exit();
    }

    $stmt->close();
}

// جلب المنتجات مع الأقسام
$result = mysqli_query($conn, "
    SELECT products.*, categories.name AS cat_name
    FROM products
    LEFT JOIN categories
    ON products.category_id = categories.category_id
");
?>

<div class="container admin-products-page">

    <div class="btn-back">
        <a href="admin_dashboard.php">← Back to Dashboard</a>
    </div>

    <h1 class="section-title">
        Product Inventory Management 📦
    </h1>

    <div class="btn-add-new">
        <a href="add_product.php">+ Add New Product</a>
    </div>

    <div class="table-responsive">

        <table class="cart-table admin-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php $i = 1; ?>
            
                <?php while ($row = mysqli_fetch_assoc($result)): ?>

                    <tr>

                        <td>
                            <?php echo $i; $i++; ?>
                        </td>

                        <td>
                            <img
                                src="<?php echo htmlspecialchars($row['image_url']); ?>"
                                alt="Product"
                                class="cart-img">
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['name']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row['cat_name'] ?? 'No Category'); ?>
                        </td>

                        <td class="price-cell">
                            $<?php echo number_format($row['price'], 2); ?>
                        </td>

                        <td>
                            <?php echo $row['stock_quantity']; ?> pcs
                        </td>

                        <td class="table-actions">

                            <a
                                href="edit_product.php?id=<?php echo $row['product_id']; ?>"
                                class="action-edit">
                                Edit
                            </a>

                            <span class="divider">|</span>

                            <a
                                href="admin_products.php?delete=<?php echo $row['product_id']; ?>"
                                class="btn-del delete-product">
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

            </tbody>


        </table>

    </div>

</div>

<?php include 'footer.php'; ?>