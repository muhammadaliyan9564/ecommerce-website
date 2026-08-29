<?php
session_start();
include 'db.php';

// Helper function to get item quantity safely
function getItemQuantity($item) {
    if (isset($item['quantity'])) return intval($item['quantity']);
    if (isset($item['qty'])) return intval($item['qty']);
    return 1;
}

// 1. QUANTITY UPDATE HANDLER
if (isset($_POST['update_qty'])) {
    $item_key = $_POST['item_key'];
    $new_qty = intval($_POST['quantity']);

    if (isset($_SESSION['cart'][$item_key])) {
        if ($new_qty > 0) {
            // Save in both keys for maximum compatibility across project
            $_SESSION['cart'][$item_key]['quantity'] = $new_qty;
            $_SESSION['cart'][$item_key]['qty'] = $new_qty;
        } else {
            unset($_SESSION['cart'][$item_key]);
        }
    }
    header("Location: checkout.php");
    exit();
}

// 2. ITEM REMOVE HANDLER
if (isset($_GET['remove'])) {
    $remove_key = $_GET['remove'];
    if (isset($_SESSION['cart'][$remove_key])) {
        unset($_SESSION['cart'][$remove_key]);
    }
    header("Location: checkout.php");
    exit();
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Calculate Subtotal
$subtotal = 0;
foreach ($cart as $item) { 
    $price = isset($item['price']) ? $item['price'] : 0;
    $qty = getItemQuantity($item);
    $subtotal += $price * $qty; 
}

$coupon_msg = '';

// 3. PROMO / COUPON CODE LOGIC
if (isset($_POST['apply_coupon'])) {
    $code = trim($_POST['coupon_code']);
    if (!empty($code) && isset($conn)) {
        $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            $cp = $res->fetch_assoc();
            $_SESSION['coupon'] = [
                'code' => $cp['code'],
                'percent' => $cp['discount_percent']
            ];
            $coupon_msg = "<div class='alert alert-success py-2 mt-2 mb-0'><i class='fa-solid fa-circle-check me-1'></i> Coupon Applied! " . $cp['discount_percent'] . "% OFF</div>";
        } else {
            $coupon_msg = "<div class='alert alert-danger py-2 mt-2 mb-0'><i class='fa-solid fa-circle-exclamation me-1'></i> Invalid Coupon Code!</div>";
        }
    }
}

// Recalculate Discount based on current Subtotal
$applied_discount = 0;
if (isset($_SESSION['coupon'])) {
    $applied_discount = ($subtotal * $_SESSION['coupon']['percent']) / 100;
}
$grand_total = max(0, $subtotal - $applied_discount);

// 4. ORDER SUBMISSION HANDLER
if (isset($_POST['place_order'])) {
    if (empty($cart)) {
        echo "<script>alert('Your cart is empty!'); window.location.href='index.php';</script>";
        exit();
    }

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $payment = $_POST['payment_method'];

    if (isset($conn)) {
        $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_email, customer_address, total_price, payment_method, discount_amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdsd", $name, $email, $address, $grand_total, $payment, $applied_discount);
        $stmt->execute();
    }

    unset($_SESSION['cart']);
    unset($_SESSION['coupon']);
    header("Location: index.php?msg=order_success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - TechStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f8; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .checkout-card { border-radius: 12px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .qty-input { width: 75px; text-align: center; }
    </style>
</head>
<body>

<div class="container my-5" style="max-width: 850px;">
    <div class="card checkout-card p-4 bg-white">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0"><i class="fa-solid fa-cart-shopping text-success me-2"></i>Order Checkout</h4>
            <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to Store</a>
        </div>
        
        <!-- Cart Items List -->
        <div class="mb-4">
            <h6 class="fw-bold text-uppercase text-secondary border-bottom pb-2">Order Items</h6>
            
            <?php if (!empty($cart)): ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Price / Piece</th>
                                <th style="width: 130px;">Quantity</th>
                                <th class="text-end">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart as $key => $item): 
                                $item_price = isset($item['price']) ? $item['price'] : 0;
                                $item_qty = getItemQuantity($item);
                                $item_total = $item_price * $item_qty;
                            ?>
                            <tr>
                                <td>
                                    <span class="fw-semibold text-dark"><?php echo htmlspecialchars($item['name']); ?></span>
                                </td>
                                <td>
                                    <small class="text-muted">Rs. <?php echo number_format($item_price); ?></small>
                                </td>
                                <td>
                                    <!-- Fixed Form: Hidden input ensures update_qty is always sent -->
                                    <form method="POST" action="checkout.php" class="d-flex align-items-center">
                                        <input type="hidden" name="update_qty" value="1">
                                        <input type="hidden" name="item_key" value="<?php echo $key; ?>">
                                        <input type="number" name="quantity" class="form-control form-control-sm qty-input fw-bold" 
                                               value="<?php echo $item_qty; ?>" min="1" onchange="this.form.submit()">
                                        <button type="submit" class="btn btn-sm btn-light border ms-1" title="Update Quantity">
                                            <i class="fa-solid fa-rotate text-success"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    Rs. <?php echo number_format($item_total); ?>
                                </td>
                                <td class="text-end">
                                    <a href="checkout.php?remove=<?php echo $key; ?>" class="text-danger ms-2" onclick="return confirm('Remove this item?');">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center">
                    Your cart is empty. <a href="index.php" class="fw-bold">Continue Shopping</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Coupon Form -->
        <div class="mb-4">
            <form method="POST" action="checkout.php" class="row g-2 align-items-center">
                <div class="col-md-8 col-8">
                    <input type="text" name="coupon_code" class="form-control" placeholder="Enter Coupon Code (e.g. SAVE10)" 
                           value="<?php echo isset($_SESSION['coupon']['code']) ? htmlspecialchars($_SESSION['coupon']['code']) : ''; ?>">
                </div>
                <div class="col-md-4 col-4">
                    <button class="btn btn-outline-dark w-100 fw-bold" type="submit" name="apply_coupon">Apply Coupon</button>
                </div>
            </form>
            <?php echo $coupon_msg; ?>
        </div>

        <!-- Pricing Summary -->
        <div class="my-3 p-3 bg-light rounded border">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Subtotal:</span>
                <strong>Rs. <?php echo number_format($subtotal); ?></strong>
            </div>
            
            <?php if ($applied_discount > 0): ?>
            <div class="d-flex justify-content-between mb-2 text-danger">
                <span>Discount (<?php echo $_SESSION['coupon']['percent']; ?>%):</span>
                <strong>- Rs. <?php echo number_format($applied_discount); ?></strong>
            </div>
            <?php endif; ?>
            
            <hr class="my-2">
            
            <div class="d-flex justify-content-between fs-5 fw-bold text-success">
                <span>Grand Total:</span>
                <span>Rs. <?php echo number_format($grand_total); ?></span>
            </div>
        </div>

        <!-- Shipping & Payment Form -->
        <form method="POST" action="checkout.php">
            <h5 class="fw-bold mt-4 mb-3"><i class="fa-solid fa-truck-fast me-2 text-primary"></i>Shipping Information</h5>
            
            <div class="mb-2">
                <input type="text" name="name" class="form-control" placeholder="Full Name" required>
            </div>
            <div class="mb-2">
                <input type="email" name="email" class="form-control" placeholder="Email Address" required>
            </div>
            <div class="mb-3">
                <textarea name="address" class="form-control" rows="2" placeholder="Full Shipping Address" required></textarea>
            </div>

            <!-- Payment Gateways -->
            <h5 class="fw-bold mb-3"><i class="fa-solid fa-wallet me-2 text-primary"></i>Payment Method</h5>
            
            <div class="card p-3 mb-3 bg-light border-0">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="Cash on Delivery" checked>
                    <label class="form-check-label fw-semibold" for="cod">
                        Cash on Delivery (COD)
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="payment_method" id="jazzcash" value="JazzCash">
                    <label class="form-check-label fw-semibold text-danger" for="jazzcash">
                        JazzCash Mobile Wallet
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="easypaisa" value="EasyPaisa">
                    <label class="form-check-label fw-semibold text-success" for="easypaisa">
                        EasyPaisa Account
                    </label>
                </div>
            </div>

            <button type="submit" name="place_order" class="btn btn-success w-100 btn-lg fw-bold shadow-sm" <?php if(empty($cart)) echo 'disabled'; ?>>
                <i class="fa-solid fa-lock me-2"></i>Confirm & Place Order (Rs. <?php echo number_format($grand_total); ?>)
            </button>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>