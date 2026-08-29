<?php
session_start();
include 'db.php';

// 1. Single Clean Session Security Check
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// 2. Update Order Status
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    $conn->query("UPDATE orders SET status='$new_status' WHERE id=$order_id");
}

// 3. Edit Customer Order Details
if (isset($_POST['edit_order'])) {
    $order_id = intval($_POST['order_id']);
    $name = trim($_POST['customer_name']);
    $email = trim($_POST['customer_email']);
    $address = trim($_POST['customer_address']);
    $total = floatval($_POST['total_price']);

    $stmt = $conn->prepare("UPDATE orders SET customer_name=?, customer_email=?, customer_address=?, total_price=? WHERE id=?");
    $stmt->bind_param("sssdi", $name, $email, $address, $total, $order_id);
    $stmt->execute();
    header("Location: admin.php");
    exit();
}

// 4. Delete Customer Order
if (isset($_GET['delete_order'])) {
    $order_id = intval($_GET['delete_order']);
    $conn->query("DELETE FROM orders WHERE id=$order_id");
    header("Location: admin.php");
    exit();
}

// 5. Add New Product (Gallery File Upload + URL Fallback)
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $desc = $_POST['description'];
    
    $image_path = $_POST['image']; // Default URL if provided

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = time() . '_' . basename($_FILES['image_file']['name']);
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_path)) {
            $image_path = $target_path;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (name, price, category, stock_quantity, image, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsiss", $name, $price, $category, $stock, $image_path, $desc);
    $stmt->execute();
    header("Location: admin.php");
    exit();
}

// 6. Update/Edit Existing Product Card
if (isset($_POST['update_product'])) {
    $id = intval($_POST['product_id']);
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];
    $desc = $_POST['description'];

    // Update standard product fields
    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, category=?, stock_quantity=?, description=? WHERE id=?");
    $stmt->bind_param("sdsisi", $name, $price, $category, $stock, $desc, $id);
    $stmt->execute();

    // Update image if new file uploaded or new URL provided
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = time() . '_' . basename($_FILES['image_file']['name']);
        $target_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_path)) {
            $conn->query("UPDATE products SET image='$target_path' WHERE id=$id");
        }
    } elseif (!empty($_POST['image_url'])) {
        $image_url = mysqli_real_escape_string($conn, $_POST['image_url']);
        $conn->query("UPDATE products SET image='$image_url' WHERE id=$id");
    }

    header("Location: admin.php");
    exit();
}

// 7. Delete Product Card from index.php & Database
if (isset($_GET['delete_product'])) {
    $id = intval($_GET['delete_product']);
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TechStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark py-3">
    <div class="container">
        <span class="navbar-brand fw-bold"><i class="fa-solid fa-user-shield me-2 text-warning"></i>Admin Control Center</span>
        <div>
            <a href="index.php" class="btn btn-outline-light btn-sm me-2">Store View</a>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container my-4">
    
    <!-- SECTION 1: Add New Product Form -->
    <div class="card p-4 shadow-sm mb-4">
        <h5 class="fw-bold mb-3"><i class="fa-solid fa-plus text-success me-2"></i>Add New Product Stock</h5>
        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Product Name</label>
                <input type="text" name="name" class="form-control" placeholder="Product Name" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Price (PKR)</label>
                <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Category</label>
                <select name="category" class="form-select">
                    <option value="Audio">Audio</option>
                    <option value="Wearables">Wearables</option>
                    <option value="Gaming">Gaming</option>
                    <option value="Accessories">Accessories</option>
                    <option value="Cameras">Cameras</option>
                    <option value="Mobiles">Mobiles</option>
                    <option value="Laptops">Laptops</option>
                    <option value="HomeTech">Home Tech</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Stock Qty</label>
                <input type="number" name="stock" class="form-control" placeholder="Qty" required>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Upload Image from Gallery</label>
                <input type="file" name="image_file" class="form-control" accept="image/*">
            </div>
            <div class="col-md-12">
                <input type="url" name="image" class="form-control form-control-sm" placeholder="OR Paste Image URL (Optional)">
            </div>
            <div class="col-12">
                <textarea name="description" class="form-control" placeholder="Short description..." rows="2"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" name="add_product" class="btn btn-success fw-semibold px-4">Publish Product</button>
            </div>
        </form>
    </div>

    <!-- SECTION 2: Manage Store Products (Edit & Delete Cards on index.php) -->
    <div class="card p-4 shadow-sm mb-4">
        <h5 class="fw-bold mb-3"><i class="fa-solid fa-list-check text-info me-2"></i>Manage Store Products (Cards)</h5>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $products = $conn->query("SELECT * FROM products ORDER BY id DESC");
                    if ($products && $products->num_rows > 0):
                        while ($p = $products->fetch_assoc()):
                    ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($p['image']); ?>" style="width: 45px; height: 45px; object-fit: cover;" class="rounded border">
                        </td>
                        <td class="fw-bold"><?php echo htmlspecialchars($p['name']); ?></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($p['category']); ?></span></td>
                        <td class="text-success fw-bold">Rs. <?php echo number_format($p['price']); ?></td>
                        <td><?php echo $p['stock_quantity']; ?> Pcs</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#editProdModal<?php echo $p['id']; ?>">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </button>
                                <a href="admin.php?delete_product=<?php echo $p['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product card from store?');">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Product Modal -->
                    <div class="modal fade" id="editProdModal<?php echo $p['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Product #<?php echo $p['id']; ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                        
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Product Name</label>
                                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($p['name']); ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Price (PKR)</label>
                                            <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $p['price']; ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Category</label>
                                            <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($p['category']); ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Stock Quantity</label>
                                            <input type="number" name="stock" class="form-control" value="<?php echo $p['stock_quantity']; ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Upload New Image (Optional)</label>
                                            <input type="file" name="image_file" class="form-control" accept="image/*">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">OR New Image URL</label>
                                            <input type="url" name="image_url" class="form-control" placeholder="Leave empty to keep current image">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Description</label>
                                            <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($p['description']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="update_product" class="btn btn-success btn-sm fw-bold">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <tr><td colspan="6" class="text-center text-muted py-3">No products found in store.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECTION 3: Customer Orders & Status Control -->
    <div class="card p-4 shadow-sm">
        <h5 class="fw-bold mb-3"><i class="fa-solid fa-boxes-packing text-primary me-2"></i>Customer Orders & Status Control</h5>
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Customer Details</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Invoice</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $orders = $conn->query("SELECT * FROM orders ORDER BY id DESC");
                if ($orders && $orders->num_rows > 0):
                    while ($row = $orders->fetch_assoc()):
                ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td>
                            <strong class="text-dark"><?php echo htmlspecialchars($row['customer_name']); ?></strong><br>
                            <small class="text-muted"><i class="fa-regular fa-envelope me-1"></i><?php echo htmlspecialchars($row['customer_email']); ?></small>
                            <?php if (!empty($row['customer_address'])): ?>
                                <br><small class="text-secondary"><i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($row['customer_address']); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['payment_method']); ?></span></td>
                        <td class="fw-bold text-success">Rs. <?php echo number_format($row['total_price']); ?></td>
                        <td>
                            <form method="POST" class="d-flex gap-1">
                                <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                <select name="status" class="form-select form-select-sm">
                                    <?php 
                                    $statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
                                    foreach($statuses as $st) {
                                        $sel = (isset($row['status']) && $row['status'] == $st) ? 'selected' : '';
                                        echo "<option value='$st' $sel>$st</option>";
                                    }
                                    ?>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-dark">Save</button>
                            </form>
                        </td>
                        <td>
                            <a href="invoice.php?order_id=<?php echo $row['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-print"></i> Receipt</a>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#editOrderModal<?php echo $row['id']; ?>" title="Edit Customer">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <a href="admin.php?delete_order=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this order?');" title="Delete Order">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Customer Order Modal -->
                    <div class="modal fade" id="editOrderModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Order #<?php echo $row['id']; ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Customer Name</label>
                                            <input type="text" name="customer_name" class="form-control" value="<?php echo htmlspecialchars($row['customer_name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Customer Email</label>
                                            <input type="email" name="customer_email" class="form-control" value="<?php echo htmlspecialchars($row['customer_email']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Shipping Address</label>
                                            <textarea name="customer_address" class="form-control" rows="2" required><?php echo htmlspecialchars(isset($row['customer_address']) ? $row['customer_address'] : ''); ?></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Total Price (PKR)</label>
                                            <input type="number" step="0.01" name="total_price" class="form-control" value="<?php echo $row['total_price']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="edit_order" class="btn btn-success btn-sm fw-bold">Update Details</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                <?php 
                    endwhile; 
                else: 
                ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">No orders found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>