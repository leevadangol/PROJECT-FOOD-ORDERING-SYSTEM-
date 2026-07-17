<?php
/*
    REPORTS (Admin/a-reports.php)
    - Daily Sales (last 7 days)
    - Monthly Sales (last 12 months)
    - Most Ordered Foods (all time)
    - Order Status Summary
*/
include "a-header.php";
require_once "../db.php";

// ---- DAILY SALES (last 7 days) ----
$daily_result = mysqli_query($conn,
    "SELECT DATE(o_datetime) AS day,
            COUNT(*)         AS total_orders,
            SUM(total_price) AS revenue
     FROM orders
     WHERE status='Completed'
       AND o_datetime >= DATE_SUB(NOW(), INTERVAL 7 DAY)
     GROUP BY day
     ORDER BY day DESC");
$daily_data = mysqli_fetch_all($daily_result, MYSQLI_ASSOC);

// ---- MONTHLY SALES (last 12 months) ----
$monthly_result = mysqli_query($conn,
    "SELECT DATE_FORMAT(o_datetime,'%b %Y') AS month,
            DATE_FORMAT(o_datetime,'%Y-%m')  AS sort_key,
            COUNT(*)                         AS total_orders,
            SUM(total_price)                 AS revenue
     FROM orders
     WHERE status='Completed'
       AND o_datetime >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
     GROUP BY sort_key
     ORDER BY sort_key DESC");
$monthly_data = mysqli_fetch_all($monthly_result, MYSQLI_ASSOC);

// ---- MOST ORDERED FOODS (all time) ----
$most_result = mysqli_query($conn,
    "SELECT f.f_name, f.price,
            SUM(oi.quantity)  AS total_qty,
            SUM(oi.price)     AS total_revenue,
            COUNT(DISTINCT oi.o_id) AS order_count
     FROM order_items oi
     JOIN foods f  ON oi.f_id  = f.f_id
     JOIN orders o ON oi.o_id  = o.o_id
     WHERE o.status = 'Completed'
     GROUP BY f.f_id
     ORDER BY total_qty DESC");
$most_data = mysqli_fetch_all($most_result, MYSQLI_ASSOC);

// ---- ORDER STATUS SUMMARY ----
$status_result = mysqli_query($conn,
    "SELECT status, COUNT(*) AS cnt, COALESCE(SUM(total_price),0) AS revenue
     FROM orders
     GROUP BY status");
$status_data = mysqli_fetch_all($status_result, MYSQLI_ASSOC);
?>
<div class="admin-page">

<!-- ===== DAILY SALES ===== -->
<div class="admin-card">
    <h2>&#128197; Daily Sales Report (Last 7 Days)</h2>
    <?php if (!empty($daily_data)): ?>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr><th>Date</th><th>Total Orders</th><th>Revenue (Rs.)</th><th>Avg. Order (Rs.)</th></tr>
            </thead>
            <tbody>
            <?php foreach ($daily_data as $d): ?>
                <tr>
                    <td><?php echo date("D, d M Y", strtotime($d['day'])); ?></td>
                    <td><?php echo $d['total_orders']; ?></td>
                    <td><strong><?php echo number_format($d['revenue'],2); ?></strong></td>
                    <td><?php echo number_format($d['revenue'] / max(1,$d['total_orders']),2); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:#fff3ec;font-weight:bold;">
                    <td>Total</td>
                    <td><?php echo array_sum(array_column($daily_data,'total_orders')); ?></td>
                    <td>Rs. <?php echo number_format(array_sum(array_column($daily_data,'revenue')),2); ?></td>
                    <td>—</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?><p style="color:#888;text-align:center;padding:15px;">No completed orders in the last 7 days.</p><?php endif; ?>
</div>

<!-- ===== MONTHLY SALES ===== -->
<div class="admin-card">
    <h2>&#128200; Monthly Sales Report (Last 12 Months)</h2>
    <?php if (!empty($monthly_data)): ?>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr><th>Month</th><th>Total Orders</th><th>Revenue (Rs.)</th><th>Avg. Order (Rs.)</th></tr>
            </thead>
            <tbody>
            <?php foreach ($monthly_data as $m): ?>
                <tr>
                    <td><strong><?php echo $m['month']; ?></strong></td>
                    <td><?php echo $m['total_orders']; ?></td>
                    <td><strong><?php echo number_format($m['revenue'],2); ?></strong></td>
                    <td><?php echo number_format($m['revenue'] / max(1,$m['total_orders']),2); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:#fff3ec;font-weight:bold;">
                    <td>Total</td>
                    <td><?php echo array_sum(array_column($monthly_data,'total_orders')); ?></td>
                    <td>Rs. <?php echo number_format(array_sum(array_column($monthly_data,'revenue')),2); ?></td>
                    <td>—</td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?><p style="color:#888;text-align:center;padding:15px;">No completed orders yet.</p><?php endif; ?>
</div>

<!-- ===== MOST ORDERED FOODS ===== -->
<div class="admin-card">
    <h2>&#127942; Most Ordered Foods (All Time)</h2>
    <?php if (!empty($most_data)): ?>
    <?php $max = $most_data[0]['total_qty'] ?? 1; ?>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Rank</th><th>Food Name</th><th>Unit Price (Rs.)</th>
                    <th>Qty Sold</th><th>Order Count</th>
                    <th>Total Revenue (Rs.)</th><th>Popularity</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($most_data as $i => $f):
                $pct = round(($f['total_qty'] / $max) * 100);
            ?>
                <tr>
                    <td>
                        <?php if ($i===0): ?>&#129351;
                        <?php elseif($i===1): ?>&#129352;
                        <?php elseif($i===2): ?>&#129353;
                        <?php else: echo '#'.($i+1); endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($f['f_name']); ?></strong></td>
                    <td><?php echo number_format($f['price'],2); ?></td>
                    <td><?php echo $f['total_qty']; ?></td>
                    <td><?php echo $f['order_count']; ?></td>
                    <td><?php echo number_format($f['total_revenue'],2); ?></td>
                    <td>
                        <div style="background:#f0f0f0;border-radius:10px;height:14px;width:120px;display:inline-block;">
                            <div style="background:#f25d07;height:14px;border-radius:10px;width:<?php echo $pct; ?>%;"></div>
                        </div>
                        <small style="color:#888;"> <?php echo $pct; ?>%</small>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?><p style="color:#888;text-align:center;padding:15px;">No order data yet.</p><?php endif; ?>
</div>

<!-- ===== ORDER STATUS SUMMARY ===== -->
<div class="admin-card">
    <h2>&#128203; Order Status Summary</h2>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr><th>Status</th><th>Number of Orders</th><th>Revenue (Rs.)</th></tr>
            </thead>
            <tbody>
            <?php foreach ($status_data as $s):
                $badge_map = [
                    'Pending'  =>'badge-pending', 'Confirmed'=>'badge-confirmed',
                    'Accepted' =>'badge-accepted', 'Ready'   =>'badge-ready',
                    'Completed'=>'badge-completed','Cancelled'=>'badge-cancelled'
                ];
                $badge = $badge_map[$s['status']] ?? 'badge-pending';
            ?>
                <tr>
                    <td><span class="badge <?php echo $badge; ?>"><?php echo $s['status']; ?></span></td>
                    <td><?php echo $s['cnt']; ?></td>
                    <td><?php echo number_format($s['revenue'],2); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="margin-top:15px;">
        <button onclick="window.print()" class="btn-primary">&#128424; Print Report</button>
    </div>
</div>

</div>
</body>
</html>
