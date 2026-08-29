<?php
session_start();
include 'db.php';

// Remove Single Item
if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $id = $_GET['id'];
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $id) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    header('Location: cart.php');
    exit();
}

// Clear Entire Cart
if (isset($_GET['action']) && $_GET['action'] == 'clear') {
    unset($_SESSION['cart']);
    header('Location: cart.php');
    exit();
}

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore - Shopping Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .cart-card { border: none; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .item-img { width: 60px; height: 60px; object-fit: cover; border-radius: 10px; }
        .btn-checkout {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            border: none;
        }
        .btn-checkout:hover { color: #fff; opacity: 0.95; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary fs-4" href="index.php"><i class="fa-solid fa-bag-shopping me-2"></i>TechStore</a>
        <a href="index.php" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fa-solid fa-arrow-left me-1"></i> Continue Shopping
        </a>
    </div>
</nav>

<div class="container my-5">
    <h4 class="fw-bold mb-4"><i class="fa-solid fa-cart-shopping text-primary me-2"></i>Your Shopping Cart</h4>

    <?php if (!empty($_SESSION['cart'])): ?>
        <div class="row g-4">
            <!-- Cart Items List -->
            <div class="col-lg-8">
                <div class="card cart-card p-4">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="text-muted small">
                                <tr>
                                    <th>PRODUCT</th>
                                    <th>PRICE</th>
                                    <th>QTY</th>
                                    <th>SUBTOTAL</th>
                                    <th>ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['cart'] as $item): 
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $total_price += $subtotal;
                                ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?php echo htmlspecialchars($item['image']); ?>" class="item-img" alt="Product">
                                                <span class="fw-semibold text-dark"><?php echo htmlspecialchars($item['name']); ?></span>
                                            </div>
                                        </td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td><span class="badge bg-secondary rounded-pill px-3 py-2"><?php echo $item['quantity']; ?></span></td>
                                        <td class="fw-bold text-primary">$<?php echo number_format($subtotal, 2); ?></td>
                                        <td>
                                            <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger border-0">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-end">
                        <a href="cart.php?action=clear" class="btn btn-sm btn-outline-secondary rounded-3">
                            <i class="fa-solid fa-xmark me-1"></i> Clear Cart
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total Price Summary -->
            <div class="col-lg-4">
                <div class="card cart-card p-4 bg-white">
                    <h5 class="fw-bold mb-3">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-2 text-secondary">
                        <span>Items Total</span>
                        <span class="fw-semibold text-dark">$<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-secondary">
                        <span>Shipping Fee</span>
                        <span class="text-success fw-semibold">FREE</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold text-dark">Grand Total</span>
                        <span class="fs-5 fw-bold text-primary">$<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <a href="checkout.php" class="btn btn-checkout w-100 py-3 text-center">
                        Proceed to Checkout <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Empty Cart State -->
        <div class="card cart-card text-center p-5">
            <i class="fa-solid fa-cart-flatbed fa-4x text-muted mb-3"></i>
            <h5 class="fw-bold text-secondary">Your shopping cart is empty</h5>
            <p class="text-muted small">You haven't added any products to your cart yet.</p>
            <div>
                <a href="index.php" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold mt-2">
                    <i class="fa-solid fa-bag-shopping me-2"></i>Explore Products
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>