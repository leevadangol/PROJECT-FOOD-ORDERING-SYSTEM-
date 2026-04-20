<?php 
session_start();
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
    die("No orders found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Orders Receipt</title>
<style>
/* ------------------ General Styles ------------------ */
body { 
    font-family: Arial, sans-serif; 
    background: #f4f4f4; 
}
.receipt-box { 
    max-width: 1000px; 
    margin: 40px auto; 
    background: #fff; 
    padding: 30px; 
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    color: #f25d07;
}
h3 {
    margin-top: 30px;
    color: #f25d07;
}
p {
    font-size: 14px;
}

/* ------------------ Table Styles ------------------ */
table { 
    width: 100%; 
    border-collapse: collapse; 
    margin-top: 10px;
    border-radius: 8px;
    overflow: hidden;
}
table th, table td { 
    border: 1px solid #ddd; 
    padding: 10px; 
    text-align: center;
}
table th { 
    background: #f25d07d5; 
    color: white; 
}
table tr:hover { 
    background: #fef3e6;
}
.total {
    text-align: right;
    font-size: 16px;
    font-weight: bold;
}

/* ------------------ Button Styles ------------------ */
.print-btn { 
    display: inline-block; 
    margin-top: 20px; 
    padding: 10px 20px; 
    background: #f25d07; 
    color: white; 
    text-decoration: none; 
    border-radius: 5px; 
    font-weight: bold;
    transition: background 0.3s;
}
.print-btn:hover { 
    background: #d94b00; 
} 

/* ------------------ Responsive ------------------ */
@media (max-width: 768px) {
    .receipt-box {
        padding: 20px;
        margin: 20px;
    }
    table th, table td {
        padding: 8px;
        font-size: 12px;
    }
}
</style>
</head>
<body>

<div class="receipt-box">
<h2>MY FOOD ORDERS RECEIPT</h2>

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
    <h3>Order #<?= $o_id; ?> (<?= date("Y-m-d H:i:s", strtotime($order['o_datetime'])); ?>)</h3>
    <p><strong>Table:</strong> <?= htmlspecialchars($order['table_no']); ?> | 
       <strong>Status:</strong> <?= htmlspecialchars($order['status']); ?></p>

    <table>
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
        <td colspan="3" class="total">Order Total:</td>
        <td><?= number_format($grand_total_order,2); ?></td>
    </tr>
    </table>

<?php
    $grand_total_all_orders += $grand_total_order;
endwhile;
?>

<h3 style="text-align:right;">Grand Total of All Orders: Rs. <?= number_format($grand_total_all_orders,2); ?></h3>

<a href="#" onclick="window.print()" class="print-btn">Print All Orders</a>
</div>

</body>
</html>