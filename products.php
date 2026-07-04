<?php
include 'header.php';

//  جلب التصنيفات لقائمة الفلترة
$cat_result = mysqli_query($conn, "SELECT * FROM categories");

//  استقبال بارامترات البحث والفلترة والفرز
$search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%";
$category = isset($_GET['category']) && $_GET['category'] != '' ? $_GET['category'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';

$query = "SELECT * FROM products WHERE name LIKE ?";
if ($category) {
    $query .= " AND category_id = ?";
}

// إضافة ترتيب السعر
if ($sort == 'price_asc') {
    $query .= " ORDER BY price ASC";
} elseif ($sort == 'price_desc') {
    $query .= " ORDER BY price DESC";
}

$stmt = $conn->prepare($query);
if ($category) {
    $stmt->bind_param("si", $search, $category);
} else {
    $stmt->bind_param("s", $search);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h1 class="section-title">Browse Our Products</h1>

    <!-- قسم الفلترة --->
    <div class="filter-section">
        <form action="products.php" method="GET" class="filter-form">
            <input type="text" name="search" placeholder="Search product name..." 
                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            
            <select name="category">
                <option value="">All Categories</option>
                <?php while($cat = mysqli_fetch_assoc($cat_result)): ?>
                    <option value="<?php echo $cat['category_id']; ?>" 
                        <?php if(isset($_GET['category']) && $_GET['category'] == $cat['category_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!--  قائمة الفرز -->
            <select name="sort">
                <option value="default">Default Sorting</option>
                <option value="price_asc" <?php if($sort == 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
                <option value="price_desc" <?php if($sort == 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
            </select>

            <button type="submit" class="btn-filter">Filter Results</button>
            <a href="products.php" class="btn-reset">Reset</a>
        </form>
    </div>

    <div class="product-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Product Image">
                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                <p class="price">$<?php echo number_format($row['price'], 2); ?></p>
                
                <div class="card-actions">
                    <a href="product_detail.php?id=<?php echo $row['product_id']; ?>" class="btn-details">Details</a>
                    <form action="add_to_cart.php" method="POST" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn-add">Add to Cart</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-results">No products found matches your search.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>