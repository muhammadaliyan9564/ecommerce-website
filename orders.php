<?php
session_start();
include 'db.php';

// 1. DELETE ORDER HANDLER
if (isset($_POST['delete_order'])) {
    $order_id = intval($_POST['order_id']);
    
    if (isset($conn)) {
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
    }
    header("Location: orders.php?msg=deleted");
    exit();
}

// 2. EDIT / UPDATE ORDER HANDLER
if (isset($_POST['update_order'])) {
    $order_id = intval($_POST['order_id']);
    $customer_name = trim($_POST['customer_name']);
    $customer_phone = trim($_POST['customer_phone']);
    $customer_address = trim($_POST['customer_address']);
    $status = $_POST['status'];

    if (isset($conn)) {
        $stmt = $conn->prepare("UPDATE orders SET customer_name = ?, customer_phone = ?, customer_address = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $customer_name, $customer_phone, $customer_address, $status, $order_id);
        $stmt->execute();
    }
    header("Location: orders.php?msg=updated");
    exit();
}

// Fetch Orders from DB
$orders = array();
if (isset($conn)) {
    $result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
}

// Demo Data (Database empty hone ki surat mein)
if (empty($orders)) {
    $orders = array(
        array(
            'id' => 1001,
            'customer_name' => 'Ali Raza',
            'customer_phone' => '03001234567',
            'customer_address' => 'House #12, Block 4, Gulshan-e-Iqbal, Karachi',
            'total_amount' => 4499,
            'payment_method' => 'Cash on Delivery',
            'status' => 'Pending',
            'created_at' => '2026-08-25 14:30:00'
        ),
        array(
            'id' => 1002,
            'customer_name' => 'Usman Khan',
            'customer_phone' => '03219876543',
            'customer_address' => 'Street 5, F-8/2, Islamabad',
            'total_amount' => 5299,
            'payment_method' => 'EasyPaisa',
            'status' => 'Shipped',
            'created_at' => '2026-08-24 11:15:00'
        )
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders & Status Control - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --pk-green: #118b50; --pk-dark: #092e20; }
        body { background-color: #f4f6f8; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-header { background-color: var(--pk-dark); color: #fff; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-custom th { background-color: #f8fafc; color: #475569; font-size: 0.85rem; text-transform: uppercase; }
        .btn-action { padding: 5px 10px; font-size: 0.8rem; border-radius: 6px; }
    </style>
</head>
<body>

<!-- Admin Navbar Header -->
<div class="admin-header py-3 mb-4 shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <h5 class="m-0 fw-bold"><i class="fa-solid fa-user-shield me-2 text-success"></i>TechStore Admin Control</h5>
        <div>
            <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3 me-2"><i class="fa-solid fa-store me-1"></i> View Store</a>
            <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</a>
        </div>
    </div>
</div>

<div class="container pb-5">

    <!-- Alert Messages -->
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> Customer order details updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-trash-can me-2"></i> Order record deleted successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Main Card -->
    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="fw-bold m-0 text-dark">Customer Orders & Status Control</h4>
                <small class="text-muted">Manage, Edit Customer Info, Update Status or Delete Orders</small>
            </div>
            <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">Total Orders: <?php echo count($orders); ?></span>
        </div>

        <!-- Orders Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle table-custom mb-0">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Details</th>
                        <th>Delivery Address</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $ord): 
                        // Status Badge Color Logic
                        $badge_bg = 'bg-warning text-dark';
                        if ($ord['status'] == 'Shipped') $badge_bg = 'bg-info text-white';
                        if ($ord['status'] == 'Delivered') $badge_bg = 'bg-success';
                        if ($ord['status'] == 'Cancelled') $badge_bg = 'bg-danger';
                    ?>
                    <tr>
                        <td class="fw-bold">#<?php echo $ord['id']; ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($ord['customer_name']); ?></div>
                            <small class="text-muted"><i class="fa-solid fa-phone me-1"></i><?php echo htmlspecialchars($ord['customer_phone']); ?></small>
                        </td>
                        <td style="max-width: 220px;">
                            <small class="text-secondary d-block text-truncate" title="<?php echo htmlspecialchars($ord['customer_address']); ?>">
                                <?php echo htmlspecialchars($ord['customer_address']); ?>
                            </small>
                        </td>
                        <td class="fw-bold text-success">Rs. <?php echo number_format($ord['total_amount']); ?></td>
                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($ord['payment_method']); ?></span></td>
                        <td><span class="badge <?php echo $badge_bg; ?>"><?php echo htmlspecialchars($ord['status']); ?></span></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                
                                <!-- Edit Button (Triggers Edit Modal) -->
                                <button type="button" class="btn btn-warning text-dark btn-action fw-semibold" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $ord['id']; ?>">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </button>

                                <!-- Delete Form -->
                                <form method="POST" action="orders.php" onsubmit="return confirm('Are you sure you want to delete order #<?php echo $ord['id']; ?>?');">
                                    <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                                    <button type="submit" name="delete_order" class="btn btn-danger btn-action">
                                        <i class="fa-solid fa-trash-can"></i> Delete
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>

                    <!-- EDIT CUSTOMER & ORDER MODAL -->
                    <div class="modal fade" id="editModal<?php echo $ord['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <form method="POST" action="orders.php">
                                    <div class="modal-header bg-dark text-white">
                                        <h6 class="modal-title fw-bold"><i class="fa-solid fa-user-pen me-2 text-success"></i>Edit Order #<?php echo $ord['id']; ?></h6>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Customer Name</label>
                                            <input type="text" name="customer_name" class="form-control" value="<?php echo htmlspecialchars($ord['customer_name']); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Customer Phone</label>
                                            <input type="text" name="customer_phone" class="form-control" value="<?php echo htmlspecialchars($ord['customer_phone']); ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Delivery Address</label>
                                            <textarea name="customer_address" class="form-control" rows="2" required><?php echo htmlspecialchars($ord['customer_address']); ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold small">Order Status</label>
                                            <select name="status" class="form-select">
                                                <option value="Pending" <?php if($ord['status']=='Pending') echo 'selected'; ?>>Pending</option>
                                                <option value="Processing" <?php if($ord['status']=='Processing') echo 'selected'; ?>>Processing</option>
                                                <option value="Shipped" <?php if($ord['status']=='Shipped') echo 'selected'; ?>>Shipped</option>
                                                <option value="Delivered" <?php if($ord['status']=='Delivered') echo 'selected'; ?>>Delivered</option>
                                                <option value="Cancelled" <?php if($ord['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="update_order" class="btn btn-success btn-sm px-3 fw-bold"><i class="fa-solid fa-floppy-disk me-1"></i> Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- END MODAL -->

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>