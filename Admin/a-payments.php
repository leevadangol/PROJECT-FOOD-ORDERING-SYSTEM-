<?php
/*
    PAYMENT MANAGEMENT (Admin/a-payments.php)
    Shows all payment records including eSewa transaction IDs
    and payment status. Admin can search and filter.
*/
include "a-header.php";
require_once "../db.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build query with optional search/filter
$where = [];
$params = [];
$types  = '';

if ($search !== '') {
    $where[]  = "(s.c_username LIKE ? OR p.transaction_id LIKE ? OR p.esewa_ref_id LIKE ?)";
    $like     = "%" . $search . "%";
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types   .= 'sss';
}
if ($filter !== '') {
    $where[]  = "p.payment_status = ?";
    $params[] = $filter;
    $types   .= 's';
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : '';

$sql = "SELECT p.*, s.c_username, s.email
        FROM payments p
        LEFT JOIN signup_page s ON p.c_id = s.c_id
        $where_sql
        ORDER BY p.payment_date DESC";

if ($params) {
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $sql);
}

// Summary counts
$total_completed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM payments WHERE payment_status='Completed'"))['t'];
$total_pending   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM payments WHERE payment_status='Pending'"))['t'];
$total_failed    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS t FROM payments WHERE payment_status='Failed'"))['t'];
$total_revenue   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount),0) AS t FROM payments WHERE payment_status='Completed'"))['t'];
?>
<div class="admin-page">

<!-- Summary Cards -->
<div style="display:flex;gap:15px;flex-wrap:wrap;margin-bottom:20px;">
    <div style="background:white;border-radius:8px;padding:18px 20px;flex:1;min-width:130px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-top:4px solid #4caf50;">
        <div style="font-size:24px;font-weight:bold;color:#333;"><?php echo $total_completed; ?></div>
        <div style="font-size:13px;color:#888;">Completed</div>
    </div>
    <div style="background:white;border-radius:8px;padding:18px 20px;flex:1;min-width:130px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-top:4px solid #ffc107;">
        <div style="font-size:24px;font-weight:bold;color:#333;"><?php echo $total_pending; ?></div>
        <div style="font-size:13px;color:#888;">Pending</div>
    </div>
    <div style="background:white;border-radius:8px;padding:18px 20px;flex:1;min-width:130px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-top:4px solid #f44336;">
        <div style="font-size:24px;font-weight:bold;color:#333;"><?php echo $total_failed; ?></div>
        <div style="font-size:13px;color:#888;">Failed</div>
    </div>
    <div style="background:white;border-radius:8px;padding:18px 20px;flex:2;min-width:180px;text-align:center;box-shadow:0 2px 8px rgba(0,0,0,0.08);border-top:4px solid #f25d07;">
        <div style="font-size:24px;font-weight:bold;color:#333;">Rs. <?php echo number_format($total_revenue,2); ?></div>
        <div style="font-size:13px;color:#888;">Total Revenue Collected</div>
    </div>
</div>

<div class="admin-card">
    <h2>&#128176; Payment Records</h2>

    <!-- Search and Filter -->
    <form class="search-form" method="GET" style="margin-bottom:15px;">
        <input type="text" name="search" placeholder="Search username, transaction ID..."
               value="<?php echo htmlspecialchars($search); ?>">
        <select name="status" style="padding:8px 12px;border:1px solid #ccc;border-radius:5px;font-size:14px;">
            <option value="">All Status</option>
            <option value="Completed" <?php if($filter==='Completed') echo 'selected'; ?>>Completed</option>
            <option value="Pending"   <?php if($filter==='Pending')   echo 'selected'; ?>>Pending</option>
            <option value="Failed"    <?php if($filter==='Failed')    echo 'selected'; ?>>Failed</option>
        </select>
        <button type="submit">Filter</button>
        <?php if ($search || $filter): ?>
            <a href="a-payments.php" class="clear-btn">Clear</a>
        <?php endif; ?>
    </form>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th><th>Customer</th><th>Amount (Rs.)</th>
                    <th>Method</th><th>Our Transaction ID</th>
                    <th>eSewa Ref ID</th><th>Status</th><th>Date</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0):
                while ($p = mysqli_fetch_assoc($result)):
                    $badge_class = 'badge-pending';
                    if ($p['payment_status']==='Completed') $badge_class = 'badge-completed';
                    if ($p['payment_status']==='Failed')    $badge_class = 'badge-cancelled';
            ?>
                <tr>
                    <td>#<?php echo $p['payment_id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($p['c_username'] ?? '—'); ?></strong><br>
                        <small style="color:#888;"><?php echo htmlspecialchars($p['email'] ?? ''); ?></small>
                    </td>
                    <td><strong><?php echo number_format($p['amount'],2); ?></strong></td>
                    <td><?php echo htmlspecialchars($p['payment_method']); ?></td>
                    <td style="font-family:monospace;font-size:12px;"><?php echo htmlspecialchars($p['transaction_id']); ?></td>
                    <td style="font-family:monospace;font-size:13px;font-weight:bold;color:#2e7d32;">
                        <?php echo !empty($p['esewa_ref_id']) ? htmlspecialchars($p['esewa_ref_id']) : '<span style="color:#ccc;">—</span>'; ?>
                    </td>
                    <td><span class="badge <?php echo $badge_class; ?>"><?php echo $p['payment_status']; ?></span></td>
                    <td style="font-size:13px;"><?php echo date("Y-m-d H:i", strtotime($p['payment_date'])); ?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="8" style="text-align:center;padding:20px;color:#888;">No payment records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
</body>
</html>
