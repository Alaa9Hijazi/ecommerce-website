<?php
include 'config.php';
include 'header.php';
?>
<div class="hero-new">
    <div class="hero-content">
        <span class="hero-subtitle">Latest Tech. Best Experience.</span>

        <h1>
            Smart Gadgets <br>
            <span>For Every Lifestyle</span>
        </h1>
        <p>
            Discover top-quality electronics
            that power your world.
        </p>
        <!--  زر وشريط البحث  -->
        <form action="products.php" method="GET" class="hero-search-form">
            <input type="text" name="search" placeholder="Search for gadgets..." required>
            <button type="submit" class="btn-search">Search</button>
        </form>

        <a href="products.php" class="btn-shop">
            Shop Now
        </a>
    </div>
</div>

<div class="container">
    <h2 class="section-title">Featured Products✨</h2>

    <div class="product-grid">
        <?php
        // جلب 4 منتجات عشوائية
        $query = "SELECT * FROM products ORDER BY RAND() LIMIT 4";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            die("Database Error: " . mysqli_error($conn));
        }

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
        ?>
                <div class="product-card">
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Product">
                    <div class="card-info">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="price">$<?php echo htmlspecialchars($row['price']); ?></p>
                        <div class="card-actions">
                            <a href="product_detail.php?id=<?php echo $row['product_id']; ?>" class="btn-details">
                                Details
                            </a>
                            <form action="add_to_cart.php" method="POST" style="display:inline;">
                                <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-add">
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<p>No products available at the moment.</p>";
        }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>