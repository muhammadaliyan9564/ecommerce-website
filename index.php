<?php
session_start();
include 'db.php';

// Add to Cart Handler
if (isset($_POST['add_to_cart'])) {
    $item = array(
        'id' => $_POST['product_id'],
        'name' => $_POST['product_name'],
        'price' => $_POST['product_price'],
        'image' => $_POST['product_image'],
        'quantity' => 1
    );

    if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = array(); }
    $found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if ($cart_item['id'] == $_POST['product_id']) {
            $cart_item['quantity'] += 1;
            $found = true; break;
        }
    }
    if (!$found) { array_push($_SESSION['cart'], $item); }
    header('Location: index.php?msg=added');
    exit();
}

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'All';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechStore Pakistan - Premium Electronics Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --pk-green: #118b50; 
            --pk-dark: #092e20; 
            --sale-red: #ff385c;
        }
        body { background-color: #f4f6f8; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

        /* Announcement Bar & Header */
        .top-header { background-color: var(--pk-dark); font-size: 0.8rem; color: #d1d5db; }
        .main-navbar { background-color: #ffffff; position: sticky; top: 0; z-index: 1020; box-shadow: 0 4px 14px rgba(0,0,0,0.05); }

        /* Navigation Controls */
        .btn-category-nav { background-color: var(--pk-green); color: #fff; font-weight: 600; border: none; }
        .btn-category-nav:hover, .btn-category-nav:focus { background-color: #0e7041; color: #fff; }

        /* Product Cards UI */
        .product-card { 
            border: 1px solid rgba(0,0,0,0.04); 
            border-radius: 16px; 
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1); 
            background: #ffffff; 
            overflow: hidden; 
        }
        .product-card:hover { 
            transform: translateY(-6px); 
            box-shadow: 0 14px 28px rgba(0,0,0,0.09) !important; 
        }
        .product-img-wrap { 
            height: 200px; 
            position: relative; 
            background-color: #ffffff; 
            overflow: hidden; 
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
        }
        .product-img { 
            max-height: 100%;
            max-width: 100%;
            object-fit: contain; 
            transition: transform 0.4s ease; 
        }
        .product-card:hover .product-img { transform: scale(1.06); }

        /* SALE Badge Animation */
        @keyframes salePulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 56, 92, 0.6); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 8px rgba(255, 56, 92, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 56, 92, 0); }
        }
        .badge-sale-pop { 
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 5;
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            color: #ffffff;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            animation: salePulse 2s infinite ease-in-out;
            border: 1px solid rgba(255,255,255,0.4);
        }

        .badge-cod { background-color: #0284c7; color: #fff; font-size: 0.68rem; font-weight: 600; border-radius: 6px; padding: 3px 7px; }
        .price-text { color: var(--pk-green); font-weight: 800; font-size: 1.15rem; }
        .original-price { text-decoration: line-through; color: #94a3b8; font-size: 0.82rem; }

        /* Buttons */
        .btn-add-cart { background-color: var(--pk-green); color: #fff; font-weight: 600; border: none; border-radius: 10px; transition: background 0.2s; }
        .btn-add-cart:hover { background-color: #0e7041; color: #fff; }
        .btn-wa { background-color: #25D366; color: #fff; font-weight: 600; border: none; border-radius: 10px; transition: background 0.2s; }
        .btn-wa:hover { background-color: #1eba57; color: #fff; }
        .product-title-link { color: #1e293b; text-decoration: none; font-weight: 700; transition: color 0.2s ease; }
        .product-title-link:hover { color: var(--pk-green); }

        /* Footer Custom CSS */
        .main-footer { background-color: var(--pk-dark); color: #e2e8f0; font-size: 0.9rem; }
        .footer-title { color: #ffffff; font-weight: 700; margin-bottom: 1.2rem; position: relative; }
        .footer-title::after { content: ''; position: absolute; left: 0; bottom: -6px; width: 35px; height: 3px; background-color: var(--pk-green); border-radius: 2px; }
        .footer-links { list-style: none; padding: 0; margin: 0; }
        .footer-links li { margin-bottom: 8px; }
        .footer-links a { color: #94a3b8; text-decoration: none; transition: all 0.2s ease; }
        .footer-links a:hover { color: #ffffff; padding-left: 5px; }
        .payment-badge { background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 8px; padding: 5px 10px; font-size: 0.78rem; font-weight: 600; color: #ffffff; display: inline-flex; align-items: center; gap: 6px; }
        .copyright-bar { background-color: #051d14; border-top: 1px solid rgba(255, 255, 255, 0.08); font-size: 0.82rem; }

        @media (max-width: 576px) {
            .product-img-wrap { height: 150px; padding: 8px; }
            .price-text { font-size: 1rem; }
            .badge-sale-pop { font-size: 0.62rem; padding: 3px 7px; }
        }
    </style>
</head>
<body>

<!-- 1. Top Announcement Bar -->
<div class="top-header py-1 d-none d-sm-block">
    <div class="container d-flex justify-content-between align-items-center">
        <div><i class="fa-solid fa-truck-fast text-warning me-1"></i> Cash on Delivery Available All Over Pakistan | 7 Days Warranty</div>
        <div>
            <a href="login.php" class="text-white text-decoration-none me-3"><i class="fa-solid fa-user-shield me-1"></i> Admin Panel</a>
            <i class="fa-solid fa-phone me-1"></i> Helplines: +92 300 1234567
        </div>
    </div>
</div>

<!-- 2. Sticky Navbar -->
<nav class="main-navbar py-2 py-lg-3">
    <div class="container">
        <div class="row align-items-center g-2">

            <!-- Logo -->
            <div class="col-6 col-lg-3 d-flex align-items-center">
                <a class="navbar-brand fs-3 fw-bold text-dark m-0" href="index.php">
                    <span style="color: var(--pk-green);"><i class="fa-solid fa-bolt me-1"></i>TechStore</span><small class="fs-6 text-muted">.pk</small>
                </a>
            </div>

            <!-- Mobile Cart Link -->
            <div class="col-6 d-lg-none text-end">
                <a href="checkout.php" class="btn btn-outline-dark position-relative rounded-pill px-3">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo $cart_count; ?></span>
                </a>
            </div>

            <!-- Search & Categories Header Dropdown -->
            <div class="col-12 col-lg-7">
                <form action="index.php" method="GET" class="input-group">
                    <button class="btn btn-category-nav dropdown-toggle px-3 d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-list-ul"></i>
                        <span class="d-none d-sm-inline"><?php echo htmlspecialchars($category_filter); ?></span>
                    </button>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item fw-semibold" href="index.php?category=All">All Categories</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="index.php?category=Audio"><i class="fa-solid fa-headphones me-2 text-muted"></i> Audio & Airbuds</a></li>
                        <li><a class="dropdown-item" href="index.php?category=Wearables"><i class="fa-solid fa-clock me-2 text-muted"></i> Smart Watches</a></li>
                        <li><a class="dropdown-item" href="index.php?category=Gaming"><i class="fa-solid fa-gamepad me-2 text-muted"></i> Gaming Gear</a></li>
                        <li><a class="dropdown-item" href="index.php?category=Accessories"><i class="fa-solid fa-plug me-2 text-muted"></i> Accessories & Power</a></li>
                        <li><a class="dropdown-item" href="index.php?category=Mobiles"><i class="fa-solid fa-mobile-screen me-2 text-muted"></i> Mobile Phones</a></li>
                    </ul>

                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search Wireless Charger, Power Bank, Airbuds..." value="<?php echo htmlspecialchars($search_query); ?>">
                    <button class="btn btn-dark px-4" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>

            <!-- Desktop Cart Link -->
            <div class="col-lg-2 d-none d-lg-block text-end">
                <a href="checkout.php" class="btn btn-outline-dark position-relative rounded-pill px-4">
                    <i class="fa-solid fa-cart-shopping me-1"></i> Cart
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo $cart_count; ?></span>
                </a>
            </div>

        </div>
    </div>
</nav>

<!-- 3. Category Filter Navigation Bar -->
<div class="container my-4">
    <div class="bg-white p-3 rounded-4 shadow-sm mb-4">
        <div class="d-flex align-items-center justify-content-start overflow-auto gap-2 pb-1">
            <span class="fw-bold text-dark text-nowrap me-2"><i class="fa-solid fa-filter text-success me-1"></i> Categories:</span>
            <?php 
            $cats = ['All', 'Audio', 'Wearables', 'Gaming', 'Accessories', 'Mobiles', 'Laptops'];
            foreach($cats as $cat): 
                $active_class = ($category_filter == $cat) ? 'bg-success text-white' : 'bg-light text-dark';
            ?>
                <a href="index.php?category=<?php echo $cat; ?>" class="btn btn-sm rounded-pill px-3 text-nowrap <?php echo $active_class; ?>"><?php echo $cat; ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 4. Product Cards Grid -->
    <div class="row g-3 g-md-4 mb-5">
        <?php
        $products_list = array();

        if (isset($conn)) {
            $sql = "SELECT * FROM products WHERE 1=1";
            if ($category_filter != 'All') { $sql .= " AND category='$category_filter'"; }
            if (!empty($search_query)) { $sql .= " AND (name LIKE '%$search_query%' OR description LIKE '%$search_query%')"; }
            $sql .= " ORDER BY id DESC";
            
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($r = $result->fetch_assoc()) {
                    $products_list[] = $r;
                }
            }
        }

        // Demo Items with Image Fallbacks
        if (empty($products_list)) {
            $products_list = array(
                array(
                    'id' => 101,
                    'name' => 'Samsung 15W Fast Wireless Charger Duo',
                    'category' => 'Accessories',
                    'price' => 4499,
                    'description' => 'Original 15W Super Fast Wireless Charging Pad with cooling fan.',
                    'image' => 'https://images.unsplash.com/photo-1622445268465-8428a36a9ae9?w=600&auto=format&fit=crop&q=80',
                    'stock_quantity' => 4
                ),
                array(
                    'id' => 102,
                    'name' => 'Ronin 20000mAh 22.5W Fast Power Bank',
                    'category' => 'Accessories',
                    'price' => 5299,
                    'description' => 'Ronin R-990 22.5W Power Bank with digital LED display screen.',
                    'image' => 'https://images.unsplash.com/photo-1609592424074-b5a88e7a6fa2?w=600&auto=format&fit=crop&q=80',
                    'stock_quantity' => 3
                ),
                array(
                    'id' => 103,
                    'name' => 'Audionic Airbud 550 TWS Wireless Earbuds',
                    'category' => 'Audio',
                    'price' => 3899,
                    'description' => 'Quad Mic ENC, Environmental Noise Cancellation, 40H Playtime.',
                    'image' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=600&auto=format&fit=crop&q=80',
                    'stock_quantity' => 8
                ),
                array(
                    'id' => 104,
                    'name' => 'T900 Ultra Smart Watch 2.09 Display',
                    'category' => 'Wearables',
                    'price' => 2499,
                    'description' => 'Bluetooth Calling, Heart Rate Sensor & Sports Fitness Tracker.',
                    'image' => 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?w=600&auto=format&fit=crop&q=80',
                    'stock_quantity' => 2
                )
            );
        }

        if (!empty($products_list)):
            foreach ($products_list as $row):
                $original_price = $row['price'] * 1.25;
                $wa_msg = urlencode("Salam! I want to order " . $row['name'] . " for Rs. " . number_format($row['price']));
                $prod_img = !empty($row['image']) ? $row['image'] : 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=600&auto=format&fit=crop&q=80';
        ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="card product-card h-100 shadow-sm">
                    
                    <span class="badge-sale-pop"><i class="fa-solid fa-fire me-1"></i> SALE 20% OFF</span>

                    <div class="product-img-wrap">
                        <a href="product-detail.php?id=<?php echo $row['id']; ?>" class="w-100 h-100 d-flex align-items-center justify-content-center">
                            <img src="<?php echo htmlspecialchars($prod_img); ?>" 
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1583394838336-acd977736f90?w=600&auto=format&fit=crop&q=80';" 
                                 class="product-img" 
                                 alt="<?php echo htmlspecialchars($row['name']); ?>" 
                                 loading="lazy">
                        </a>
                        <span class="position-absolute bottom-0 start-0 m-2 badge-cod"><i class="fa-solid fa-truck-fast me-1"></i> COD</span>
                    </div>

                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-uppercase text-muted fw-bold" style="font-size:0.68rem;"><?php echo htmlspecialchars($row['category']); ?></small>
                                <?php if (isset($row['stock_quantity']) && $row['stock_quantity'] <= 5 && $row['stock_quantity'] > 0): ?>
                                    <small class="text-danger fw-bold" style="font-size:0.68rem;"><i class="fa-solid fa-bolt me-1"></i>Low Stock</small>
                                <?php endif; ?>
                            </div>

                            <h6 class="fw-bold my-1 text-truncate" title="<?php echo htmlspecialchars($row['name']); ?>">
                                <a href="product-detail.php?id=<?php echo $row['id']; ?>" class="product-title-link">
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </a>
                            </h6>

                            <p class="text-muted small mb-2 d-none d-sm-block text-truncate" style="font-size: 0.8rem;"><?php echo htmlspecialchars($row['description']); ?></p>

                            <div class="mb-2">
                                <div class="price-text">Rs. <?php echo number_format($row['price']); ?></div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="original-price">Rs. <?php echo number_format($original_price); ?></span>
                                    <span class="badge bg-danger-subtle text-danger font-monospace" style="font-size: 0.7rem;">Save 20%</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2">
                            <form method="POST" action="index.php" class="mb-2">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['name']); ?>">
                                <input type="hidden" name="product_price" value="<?php echo $row['price']; ?>">
                                <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($row['image']); ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-add-cart w-100 py-2 btn-sm">
                                    <i class="fa-solid fa-cart-plus me-1"></i> Add to Cart
                                </button>
                            </form>

                            <a href="https://wa.me/923001234567?text=<?php echo $wa_msg; ?>" target="_blank" class="btn btn-wa w-100 py-1 btn-sm text-decoration-none d-flex align-items-center justify-content-center">
                                <i class="fa-brands fa-whatsapp me-1"></i> Order on WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <div class="col-12"><div class="alert alert-warning text-center py-4">No products found matching your search.</div></div>
        <?php endif; ?>
    </div>
</div>

<!-- 5. Integrated Inter-Connected Footer -->
<footer class="main-footer pt-5">
    <div class="container pb-4">
        <div class="row g-4">
            
            <!-- Column 1: Brand Info -->
            <div class="col-12 col-md-4">
                <a href="index.php" class="fs-3 fw-bold text-white text-decoration-none d-inline-block mb-2">
                    <span style="color: #22c55e;"><i class="fa-solid fa-bolt me-1"></i>TechStore</span>.pk
                </a>
                <p class="text-secondary small mb-3">
                    Pakistan's trusted destination for original gadgets, fast wireless chargers, power banks, and smartwatch accessories with 100% Cash on Delivery across Pakistan.
                </p>
                <div class="d-flex flex-column gap-2 text-secondary small">
                    <div><i class="fa-solid fa-location-dot text-success me-2"></i> Karachi, Sindh, Pakistan</div>
                    <div><i class="fa-solid fa-phone text-success me-2"></i> +92 300 1234567</div>
                    <div><i class="fa-solid fa-envelope text-success me-2"></i> support@techstore.pk</div>
                </div>
            </div>

            <!-- Column 2: Connected Quick Links -->
            <div class="col-6 col-md-2">
                <h6 class="footer-title">Quick Links</h6>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fa-solid fa-angle-right me-1 small"></i> Home Store</a></li>
                    <li><a href="checkout.php"><i class="fa-solid fa-angle-right me-1 small"></i> View Cart & Checkout</a></li>
                    <li><a href="login.php"><i class="fa-solid fa-angle-right me-1 small"></i> Admin Portal</a></li>
                    <li><a href="https://wa.me/923001234567?text=Salam%20TechStore%20Support" target="_blank"><i class="fa-solid fa-angle-right me-1 small"></i> Customer Help</a></li>
                </ul>
            </div>

            <!-- Column 3: Category Links -->
            <div class="col-6 col-md-3">
                <h6 class="footer-title">Top Categories</h6>
                <ul class="footer-links">
                    <li><a href="index.php?category=Audio"><i class="fa-solid fa-angle-right me-1 small"></i> Wireless Airbuds</a></li>
                    <li><a href="index.php?category=Wearables"><i class="fa-solid fa-angle-right me-1 small"></i> Smart Watches</a></li>
                    <li><a href="index.php?category=Accessories"><i class="fa-solid fa-angle-right me-1 small"></i> Fast Power Banks & Chargers</a></li>
                    <li><a href="index.php?category=Gaming"><i class="fa-solid fa-angle-right me-1 small"></i> Gaming Accessories</a></li>
                    <li><a href="index.php?category=Mobiles"><i class="fa-solid fa-angle-right me-1 small"></i> Mobile Phones</a></li>
                </ul>
            </div>

            <!-- Column 4: Payment Badges & WhatsApp Support -->
            <div class="col-12 col-md-3">
                <h6 class="footer-title">Payment Methods</h6>
                <p class="text-secondary small mb-3">Safe & secure checkout with local payment options:</p>
                
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="payment-badge"><i class="fa-solid fa-truck-fast text-warning"></i> Cash on Delivery</span>
                    <span class="payment-badge"><i class="fa-solid fa-mobile-screen-button text-info"></i> JazzCash</span>
                    <span class="payment-badge"><i class="fa-solid fa-wallet text-success"></i> EasyPaisa</span>
                    <span class="payment-badge"><i class="fa-solid fa-building-columns text-light"></i> Bank Transfer</span>
                </div>

                <a href="https://wa.me/923001234567?text=Salam!%20I%20need%20help%20with%20an%20order." target="_blank" class="btn btn-success btn-sm w-100 py-2 rounded-3 text-white fw-semibold d-flex align-items-center justify-content-center gap-2">
                    <i class="fa-brands fa-whatsapp fs-5"></i> Chat on WhatsApp
                </a>
            </div>

        </div>
    </div>

    <!-- Copyright Sub-Bar -->
    <div class="copyright-bar py-3">
        <div class="container d-flex flex-column flex-sm-row justify-content-between align-items-center text-center gap-2">
            <div class="text-secondary">
                © <?php echo date('Y'); ?> <strong>TechStore.pk</strong>. All rights reserved. Powered by <span class="text-white">DigiGrowth</span>.
            </div>
            <div class="d-flex gap-3">
                <a href="index.php" class="text-secondary text-decoration-none small">Privacy Policy</a>
                <span class="text-secondary">•</span>
                <a href="index.php" class="text-secondary text-decoration-none small">Terms of Service</a>
                <span class="text-secondary">•</span>
                <a href="login.php" class="text-secondary text-decoration-none small">Admin Portal</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>