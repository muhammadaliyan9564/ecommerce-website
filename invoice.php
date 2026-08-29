<?php
session_start();
include 'db.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$order = $conn->query("SELECT * FROM orders WHERE id=$order_id")->fetch_assoc();

if (!$order) { die("Invoice Not Found!"); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $order['id']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="bg-white p-5">

<div class="container border p-4 rounded shadow-sm" style="max-width: 750px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-success mb-0">TechStore.pk</h2>
            <small class="text-muted">Official Order Receipt</small>
        </div>
        <div class="text-end">
            <h5>INVOICE #<?php echo $order['id']; ?></h5>
            <small class="text-muted">Date: <?php echo date('d M Y', strtotime($order['order_date'])); ?></small>
        </div>
    </div>
    
    <hr>
    
    <div class="row mb-4">
        <div class="col-6">
            <h6>Billed To:</h6>
            <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
            <?php echo htmlspecialchars($order['customer_email']); ?><br>
            <span class="small"><?php echo htmlspecialchars($order['customer_address']); ?></span>
        </div>
        <div class="col-6 text-end">
            <h6>Payment Details:</h6>
            Method: <strong><?php echo $order['payment_method']; ?></strong><br>
            Status: <span class="badge bg-success"><?php echo $order['status']; ?></span>
        </div>
    </div>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Description</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Order Total Items Purchase</td>
                <td class="text-end">Rs. <?php echo number_format($order['total_price'] + $order['discount_amount']); ?></td>
            </tr>
            <?php if ($order['discount_amount'] > 0): ?>
            <tr>
                <td class="text-danger">Applied Coupon Discount</td>
                <td class="text-end text-danger">- Rs. <?php echo number_format($order['discount_amount']); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="fw-bold">Total Payable</td>
                <td class="text-end fw-bold text-success">Rs. <?php echo number_format($order['total_price']); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-dark px-4"><i class="fa-solid fa-print"></i> Print Invoice / Save PDF</button>
    </div>
</div>

</body>
</html>