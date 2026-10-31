<?php
/*
    ADMIN DASHBOARD (Admin/a-dashboard.php)
    Shows:
      - 6 summary stat cards
      - Most selling products table + bar chart
      - Least selling products table
      - Monthly revenue bar chart
      - Order status pie chart
*/
include "a-header.php";
include "db.php";

// ---- STAT CARDS ----
$total_users     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM signup_page"))['t'];
$total_orders    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM orders"))['t'];
$total_revenue   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_price),0) AS t FROM orders WHERE status='Completed'"))['t'];
$pending_orders  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM orders WHERE status='Pending'"))['t'];
$completed_orders= mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM orders WHERE status='Completed'"))['t'];
$total_foods     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM foods"))['t'];

// ---- MOST SELLING PRODUCTS (top 8 by total quantity ordered) ----
$most_sql = "SELECT f.f_name, f.image, f.price,
                    SUM(oi.quantity) AS total_qty,
                    SUM(oi.price)    AS total_revenue
             FROM order_items oi
             JOIN foods f ON oi.f_id = f.f_id
             JOIN orders o ON oi.o_id = o.o_id
             WHERE o.status = 'Completed'
             GROUP BY f.f_id
             ORDER BY total_qty DESC
             LIMIT 8";
$most_result = mysqli_query($conn, $most_sql);
$most_foods  = mysqli_fetch_all($most_result, MYSQLI_ASSOC);

// ---- LEAST SELLING PRODUCTS (bottom 5) ----
$least_sql = "SELECT f.f_name, f.price,
                     COALESCE(SUM(oi.quantity),0) AS total_qty
              FROM foods f
              LEFT JOIN order_items oi ON f.f_id = oi.f_id
              LEFT JOIN orders o ON oi.o_id = o.o_id AND o.status='Completed'
              GROUP BY f.f_id
              ORDER BY total_qty ASC
              LIMIT 5";
$least_result = mysqli_query($conn, $least_sql);
$least_foods  = mysqli_fetch_all($least_result, MYSQLI_ASSOC);

// ---- MONTHLY REVENUE (last 6 months) ----
$monthly_sql = "SELECT DATE_FORMAT(o_datetime,'%b %Y') AS month_label,
                       DATE_FORMAT(o_datetime,'%Y-%m')  AS month_key,
                       SUM(total_price) AS revenue
                FROM orders
                WHERE status='Completed'
                  AND o_datetime >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY month_key
                ORDER BY month_key ASC";
$monthly_result = mysqli_query($conn, $monthly_sql);
$monthly_data   = mysqli_fetch_all($monthly_result, MYSQLI_ASSOC);

// ---- ORDER STATUS COUNTS (for pie chart) ----
$status_sql    = "SELECT status, COUNT(*) AS cnt FROM orders GROUP BY status";
$status_result = mysqli_query($conn, $status_sql);
$status_data   = mysqli_fetch_all($status_result, MYSQLI_ASSOC);

// Build arrays for Chart.js (PHP → JSON)
$chart_most_labels   = json_encode(array_column($most_foods,  'f_name'));
$chart_most_values   = json_encode(array_column($most_foods,  'total_qty'));
$chart_month_labels  = json_encode(array_column($monthly_data,'month_label'));
$chart_month_values  = json_encode(array_column($monthly_data,'revenue'));
$chart_status_labels = json_encode(array_column($status_data, 'status'));
$chart_status_values = json_encode(array_column($status_data, 'cnt'));
?>

<div class="admin-page">

<!-- ===== STAT CARDS ===== -->
<div style="display:flex;flex-wrap:wrap;gap:15px;margin-bottom:25px;">
    <?php
    $cards = [
        ['&#128101;','Total Users',     $total_users,                         '#2196f3'],
        ['&#128203;','Total Orders',    $total_orders,                        '#f25d07'],
        ['&#128176;','Total Revenue',   'Rs. '.number_format($total_revenue,2),'#4caf50'],
        ['&#9203;',  'Pending Orders',  $pending_orders,                      '#ffc107'],
        ['&#10004;', 'Completed',       $completed_orders,                    '#009688'],
        ['&#127829;','Food Items',      $total_foods,                         '#f44336'],
    ];
    foreach ($cards as $c): ?>
    <div style="background:white;border-radius:10px;padding:20px 18px;flex:1;min-width:140px;
                text-align:center;box-shadow:0 2px 10px rgba(0,0,0,0.08);
                border-top:4px solid <?php echo $c[3]; ?>;">
        <div style="font-size:30px;margin-bottom:8px;"><?php echo $c[0]; ?></div>
        <div style="font-size:20px;font-weight:bold;color:#333;"><?php echo $c[2]; ?></div>
        <div style="font-size:13px;color:#888;margin-top:4px;"><?php echo $c[1]; ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ===== CHARTS ROW ===== -->
<div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:25px;">

    <!-- Bar chart: Most Selling Products -->
    <div class="admin-card" style="flex:2;min-width:300px;">
        <h2>&#128200; Most Selling Products</h2>
        <canvas id="mostSellingChart" height="120"></canvas>
    </div>

    <!-- Pie chart: Order Status -->
    <div class="admin-card" style="flex:1;min-width:260px;">
        <h2>&#129529; Order Status</h2>
        <canvas id="statusChart" height="200"></canvas>
    </div>

</div>

<!-- Bar chart: Monthly Revenue -->
<div class="admin-card" style="margin-bottom:25px;">
    <h2>&#128181; Monthly Revenue (Last 6 Months)</h2>
    <canvas id="revenueChart" height="80"></canvas>
</div>

<!-- ===== MOST SELLING TABLE ===== -->
<div class="admin-card" style="margin-bottom:25px;">
    <h2>&#128293; Most Selling Products</h2>
    <?php if (!empty($most_foods)): ?>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Image</th>
                    <th>Food Name</th>
                    <th>Price (Rs.)</th>
                    <th>Total Qty Sold</th>
                    <th>Total Revenue (Rs.)</th>
                    <th>Popularity</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $max_qty = $most_foods[0]['total_qty'] ?? 1;
            foreach ($most_foods as $i => $food):
                $bar_pct = round(($food['total_qty'] / $max_qty) * 100);
            ?>
                <tr>
                    <td><strong>#<?php echo $i+1; ?></strong></td>
                    <td>
                        <img src="../<?php echo htmlspecialchars($food['image']); ?>"
                             style="width:45px;height:45px;border-radius:50%;object-fit:cover;"
                             alt="<?php echo htmlspecialchars($food['f_name']); ?>">
                    </td>
                    <td><?php echo htmlspecialchars($food['f_name']); ?></td>
                    <td><?php echo number_format($food['price'],2); ?></td>
                    <td><strong><?php echo $food['total_qty']; ?></strong></td>
                    <td><?php echo number_format($food['total_revenue'],2); ?></td>
                    <td>
                        <!-- Visual progress bar -->
                        <div style="background:#f0f0f0;border-radius:10px;height:14px;width:120px;">
                            <div style="background:#f25d07;height:14px;border-radius:10px;
                                        width:<?php echo $bar_pct; ?>%;"></div>
                        </div>
                        <small style="color:#888;"><?php echo $bar_pct; ?>%</small>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p style="color:#888;text-align:center;padding:20px;">No completed orders yet.</p>
    <?php endif; ?>
</div>

<!-- ===== LEAST SELLING TABLE ===== -->
<div class="admin-card">
    <h2>&#128308; Least Selling Products</h2>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Food Name</th>
                    <th>Price (Rs.)</th>
                    <th>Total Qty Sold</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($least_foods as $food): ?>
                <tr>
                    <td><?php echo htmlspecialchars($food['f_name']); ?></td>
                    <td><?php echo number_format($food['price'],2); ?></td>
                    <td><?php echo $food['total_qty']; ?></td>
                    <td>
                        <?php if ($food['total_qty'] == 0): ?>
                            <span class="badge badge-cancelled">No Orders</span>
                        <?php elseif ($food['total_qty'] < 5): ?>
                            <span class="badge badge-pending">Low Sales</span>
                        <?php else: ?>
                            <span class="badge badge-confirmed">Moderate</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</div><!-- end admin-page -->

<!-- Chart.js from CDN (no installation needed) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ---- Bar Chart: Most Selling Products ----
new Chart(document.getElementById('mostSellingChart'), {
    type: 'bar',
    data: {
        labels: <?php echo $chart_most_labels; ?>,
        datasets: [{
            label: 'Qty Sold',
            data:  <?php echo $chart_most_values; ?>,
            backgroundColor: 'rgba(242,93,7,0.75)',
            borderColor:     '#f25d07',
            borderWidth: 1,
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { ticks: { maxRotation: 30 } }
        }
    }
});

// ---- Pie Chart: Order Status ----
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: <?php echo $chart_status_labels; ?>,
        datasets: [{
            data: <?php echo $chart_status_values; ?>,
            backgroundColor: ['#9e9e9e','#2196f3','#ff9800','#f25d07','#4caf50','#f44336'],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { font: { size: 12 } } } }
    }
});

// ---- Bar Chart: Monthly Revenue ----
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: <?php echo $chart_month_labels; ?>,
        datasets: [{
            label: 'Revenue (Rs.)',
            data:  <?php echo $chart_month_values; ?>,
            backgroundColor: 'rgba(76,175,80,0.75)',
            borderColor:     '#4caf50',
            borderWidth: 1,
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

</body>
</html>
