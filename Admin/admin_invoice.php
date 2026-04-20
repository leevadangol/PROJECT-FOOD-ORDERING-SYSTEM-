<?php
include "a-header.php";
require_once "../db.php";

// Fetch all completed orders
$sql = "SELECT o.*, s.c_username 
        FROM orders o
        JOIN signup_page s ON o.c_id = s.c_id
        WHERE o.status='Completed'
        ORDER BY o.o_datetime ASC";

$result = mysqli_query($conn, $sql);

if(!$result || mysqli_num_rows($result) == 0){
    die("<p style='text-align:center; margin-top:50px;'>No completed orders found.</p>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Invoice - Completed Orders</title>
<style>
body {
    font-family: Arial, sans-serif;
    margin:0;
    padding:0;
}

.invoice-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

h2 {
    text-align: center;
    color: #f25d07;
    margin-bottom: 30px;
}

/* Table  */
.orders-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border-radius: 8px;
    overflow: hidden;
}

.orders-table th, .orders-table td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
}

.orders-table th {
    background-color: #dbdbdb86;
    color: #333;
    font-weight: bold;
    border-bottom: 2px solid #f25d07;
}

.orders-table tr:last-child td {
    border-bottom: none;
}

.status-completed {
    color: #14b65d;
    font-weight: bold;
}

.status-cancelled { color:#f44336; font-weight:bold; }
.status-accepted { color:#2196f3; font-weight:bold; }
.status-ready { color:#f0ad4e; font-weight:bold; }

.print-btn {
    display: inline-block;
    background: #f25d07;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    margin-top: 20px;
}

.invoice-box{ 
  background: #fff;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
}

/* .orders-box h3 {
  margin-bottom: 15px;
} */


.print-btn:hover { background: #d35400; }

@media (max-width:768px){
    .orders-table, .orders-table th, .orders-table td { font-size:14px; padding:8px; }
}
</style>
</head>
<body>

<section class="invoice-container">
<h2>Completed Orders Invoice</h2>

<div class="invoice-box">
<table class="orders-table">
    <thead>
        <tr>
            <th>Invoice No</th>
            <th>Customer</th>
            <th>Food Items</th>
            <th>Quantity</th>
            <th>Table No</th>
            <th>Status</th>
            <th>DateTime</th>
        </tr>
    </thead>
    <tbody>
        <?php while($order = mysqli_fetch_assoc($result)): 
            $o_id = $order['o_id'];

            // Fetch items for this order
            $items_sql = "SELECT oi.*, f.f_name, f.price 
                          FROM order_items oi 
                          JOIN foods f ON oi.f_id = f.f_id 
                          WHERE oi.o_id = $o_id";
            $items_result = mysqli_query($conn, $items_sql);
            $food_names = [];
            $quantities = [];
            while($item = mysqli_fetch_assoc($items_result)){
                $food_names[] = $item['f_name'];
                $quantities[] = $item['quantity'];
            }
        ?>
        <tr>
            <td>#<?= $o_id; ?></td>
            <td><?= htmlspecialchars($order['c_name']); ?></td>
            <td><?= htmlspecialchars(implode(', ', $food_names)); ?></td>
            <td><?= htmlspecialchars(implode(', ', $quantities)); ?></td>
            <td><?= htmlspecialchars($order['table_no']); ?></td>
            <td class="status-completed"><?= htmlspecialchars($order['status']); ?></td>
            <td><?= date("Y-m-d H:i:s", strtotime($order['o_datetime'])); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<a href="#" onclick="window.print()" class="print-btn">Print All Completed Orders</a>
</div>
</section>

</body>
</html>