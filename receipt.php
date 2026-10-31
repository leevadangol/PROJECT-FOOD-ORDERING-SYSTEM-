<?php 
session_start();
include 'header.php';
include 'db.php';

if(!isset($_SESSION['customer_id'])){
    die("You must be logged in to view receipts.");
}

$customer_id = $_SESSION['customer_id'];

// Fetch all orders for this customer
$orders_sql = "
SELECT o.*, s.c_username
FROM orders o
JOIN signup_page s ON o.c_id = s.c_id
WHERE o.c_id = $customer_id
ORDER BY o.o_datetime ASC
";

$orders_result = mysqli_query($conn, $orders_sql);

if(mysqli_num_rows($orders_result) == 0){
    die("<p style='text-align:center; margin-top:50px;'>No orders found.</p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Orders Receipt</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #fff;
    margin:0;
    padding:0;
}

.receipt-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

h2 {
    text-align: center;
    color: #f25d07;
    margin-bottom: 30px;
}

.receipt-box {
    background:#fff;
    padding:20px;
    border-radius:8px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.orders-table {
    width:100%;
    border-collapse: collapse;
    border-radius:8px;
    overflow:hidden;
    margin-top:10px;
}

.orders-table th, .orders-table td {
    padding:12px 15px;
    text-align:center;
    border-bottom:1px solid #e0e0e0;
}

.orders-table th {
    background-color:#dbdbdb86;
    color:#333;
    font-weight:bold;
    border-bottom:2px solid #f25d07;
}

.orders-table tr:last-child td {
    border-bottom:none;
}

.status-completed { color:#14b65d; font-weight:bold; }
.status-cancelled { color:#f44336; font-weight:bold; }
.status-accepted { color:#2196f3; font-weight:bold; }
.status-ready { color:#f0ad4e; font-weight:bold; }

.print-btn {
    display:inline-block;
    background:#f25d07;
    color:white;
    padding:10px 20px;
    text-decoration:none;
    border-radius:5px;
    font-weight:bold;
    margin-top:10px;
}
.print-btn:hover { background:#d35400; }

.whole_receipt_box{
  background: #f4f4f4;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
}

@media (max-width:768px){
    .orders-table, .orders-table th, .orders-table td { font-size:14px; padding:8px; }
}
</style>
</head>
<body>

<section class="receipt-container">
<h2>My Orders Receipt</h2>

<div class="whole_receipt_box">

<?php
$grand_total_all_orders = 0;

while($order = mysqli_fetch_assoc($orders_result)):
    $o_id = $order['o_id'];

    // Fetch items for this order
    $items_sql = "
    SELECT oi.*, f.f_name, f.price
    FROM order_items oi
    JOIN foods f ON oi.f_id = f.f_id
    WHERE oi.o_id = $o_id
    ";
    $items_result = mysqli_query($conn, $items_sql);

    $grand_total_order = 0;
?>
<div class="receipt-box">
    <h3>Order #<?= $o_id; ?> (<?= date("Y-m-d H:i:s", strtotime($order['o_datetime'])); ?>)</h3>
    <p><strong>Table:</strong> <?= htmlspecialchars($order['table_no']); ?> | 
       <strong>Status:</strong> <?= htmlspecialchars($order['status']); ?></p>

    <table class="orders-table">
    <tr>
        <th>Food</th>
        <th>Price (Rs.)</th>
        <th>Quantity</th>
        <th>Subtotal (Rs.)</th>
    </tr>

    <?php while($item = mysqli_fetch_assoc($items_result)):
        $subtotal = $item['price'] * $item['quantity'];
        $grand_total_order += $subtotal;
    ?>
    <tr>
        <td><?= htmlspecialchars($item['f_name']); ?></td>
        <td><?= number_format($item['price'],2); ?></td>
        <td><?= $item['quantity']; ?></td>
        <td><?= number_format($subtotal,2); ?></td>
    </tr>
    <?php endwhile; ?>

    <tr>
        <td colspan="3" style="text-align:right;font-weight:bold;">Order Total:</td>
        <td><?= number_format($grand_total_order,2); ?></td>
    </tr>
    </table>
</div>

<?php
    $grand_total_all_orders += $grand_total_order;
endwhile;
?>

<h3 style="text-align:right; margin-right:20px;">Grand Total of All Orders: Rs. <?= number_format($grand_total_all_orders,2); ?></h3>

<a href="#" onclick="window.print()" class="print-btn">Print All Orders</a>

</div>
</section>
</body>
</html>