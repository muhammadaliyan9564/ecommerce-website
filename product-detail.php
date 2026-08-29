<?php
session_start();
include 'db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name']); ?> - Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container my-5">
    <a href="index.php" class="btn btn-outline-dark mb-4"><i class="fa-solid fa-arrow-left me-1"></i> Back to Store</a>
    <div class="card border-0 shadow-lg p-4">
        <div class="row g-4 align-items-center">
            <div class="col-md-6">
                <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid rounded-4 w-100" style="max-height: 400px; object-fit: cover;">
            </div>
            <div class="col-md-6">
                <span class="badge bg-secondary mb-2"><?php echo $product['category']; ?></span>
                <h2 class="fw-bold"><?php echo htmlspecialchars($product['name']); ?></h2>
                <p class="text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
                
                <h3 class="text-success fw-bold my-3">Rs. <?php echo number_format($product['price']); ?></h3>

                <!-- Stock Urgency Alert -->
                <?php if ($product['stock_quantity'] <= 5 && $product['stock_quantity'] > 0): ?>
                    <div class="alert alert-danger py-2 mb-3 fw-bold">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Hurry! Only <?php echo $product['stock_quantity']; ?> left in stock!
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                    <input type="hidden" name="product_price" value="<?php echo $product['price']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product['image']); ?>">
                    <button type="submit" name="add_to_cart" class="btn btn-success btn-lg w-100 mb-2">
                        <i class="fa-solid fa-cart-plus me-2"></i> Add to Cart
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>