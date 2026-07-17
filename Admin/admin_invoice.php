<?php
/*
    ADMIN INVOICE (Admin/admin_invoice.php)
    FIXES:
    - Added Total Price column and Grand Total row
    - Added eSewa Transaction ID column
    - Fixed N+1 query (was running SQL inside loop) - now one JOIN query
    - Hardcoded CSS path replaced with relative path
*/
include "a-header.php";
require_once "../db.php";

$sql = "
    SELECT o.o_id, o.c_name, o.table_no, o.contact, o.status, o.o_datetime, o.total_price,
           GROUP_CONCAT(f.f_name ORDER BY f.f_name SEPARATOR ', ')    AS food_names,
           GROUP_CONCAT(oi.quantity ORDER BY f.f_name SEPARATOR ', ') AS quantities,
           p.esewa_ref_id
    FROM orders o
    LEFT JOIN order_items oi ON o.o_id  = oi.o_id
    LEFT JOIN foods f         ON oi.f_id = f.f_id
    LEFT JOIN payments p      ON o.payment_id = p.payment_id
    WHERE o.status = 'Completed'
    GROUP BY o.o_id
    ORDER BY o.o_datetime DESC
";
$result = mysqli_query($conn, $sql);

$grand_res   = mysqli_query($conn, "SELECT COALESCE(SUM(total_price),0) AS grand FROM orders WHERE status='Completed'");
$grand_total = mysqli_fetch_assoc($grand_res)['grand'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Invoice</title>
    <style>
        body { font-family:Arial,sans-serif; margin:0; padding:0; background:#f4f4f4; }
        .invoice-container { max-width:1200px; margin:40px auto; padding:20px; }
        h2 { text-align:center; color:#f25d07; margin-bottom:5px; }
        .subtitle { text-align:center; color:#666; margin-bottom:25px; font-size:14px; }
        .invoice-box { background:#fff; padding:25px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.08); }
        .orders-table { width:100%; border-collapse:collapse; }
        .orders-table th, .orders-table td { padding:11px 13px; text-align:center; border-bottom:1px solid #e0e0e0; font-size:14px; }
        .orders-table th { background:#f7f7f7; font-weight:bold; border-bottom:2px solid #f25d07; }
        .orders-table tr:hover { background:#fffaf7; }
        .status-completed { color:#14b65d; font-weight:bold; }
        .grand-total-row td { font-weight:bold; font-size:15px; background:#fff3ec; border-top:2px solid #f25d07; }
        .empty-msg { text-align:center; padding:30px; color:#666; }
        .btn-row { display:flex; gap:10px; margin-top:20px; }
        .print-btn { display:inline-block; background:#f25d07; color:white; padding:10px 22px; text-decoration:none; border-radius:5px; font-weight:bold; cursor:pointer; border:none; font-size:14px; }
        .back-btn { display:inline-block; background:#607d8b; color:white; padding:10px 22px; text-decoration:none; border-radius:5px; font-size:14px; }
        @media print { .btn-row, .navbar { display:none !important; } body { background:white; } .invoice-container { margin:0; padding:0; } }
    </style>
</head>
<body>
<section class="invoice-container">
    <h2>Completed Orders Invoice</h2>
    <p class="subtitle">Printed on: <?php echo date("Y-m-d H:i:s"); ?></p>

    <div class="invoice-box">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Invoice #</th><th>Customer</th><th>Food Items</th>
                    <th>Qty</th><th>Table No</th><th>Total (Rs.)</th>
                    <th>Status</th><th>eSewa Txn ID</th><th>Date / Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>#<?php echo $order['o_id']; ?></td>
                    <td><?php echo htmlspecialchars($order['c_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['food_names'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($order['quantities'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($order['table_no']); ?></td>
                    <td><?php echo number_format($order['total_price'], 2); ?></td>
                    <td class="status-completed"><?php echo htmlspecialchars($order['status']); ?></td>
                    <td><?php echo !empty($order['esewa_ref_id']) ? htmlspecialchars($order['esewa_ref_id']) : '<span style="color:#999;">N/A</span>'; ?></td>
                    <td><?php echo date("Y-m-d H:i", strtotime($order['o_datetime'])); ?></td>
                </tr>
                <?php endwhile; ?>
                <tr class="grand-total-row">
                    <td colspan="5" style="text-align:right;">Grand Total</td>
                    <td>Rs. <?php echo number_format($grand_total, 2); ?></td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>
        <?php else: ?>
            <p class="empty-msg">No completed orders found yet.</p>
        <?php endif; ?>

        <div class="btn-row">
            <a href="#" onclick="window.print(); return false;" class="print-btn">&#128424; Print Invoice</a>
            <a href="a-orderpage.php" class="back-btn">&#8592; Back to Orders</a>
        </div>
    </div>
</section>
</body>
</html>
