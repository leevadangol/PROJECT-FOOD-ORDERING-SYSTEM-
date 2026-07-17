<?php
/*
    ADMIN ORDER PAGE (Admin/a-orderpage.php)
    FIX: All header() redirects must run BEFORE a-header.php
    is included, because a-header.php outputs HTML immediately.

    Correct order:
      1. session_start + DB + handle POST/GET (redirects here)
      2. include "a-header.php"  ← HTML starts here
      3. Show page content
*/

// Step 1: Session + DB BEFORE any HTML
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once "../db.php";

// Handle status update (POST) — redirect BEFORE any HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id   = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    $allowed    = ['Pending','Confirmed','Accepted','Ready','Completed','Cancelled'];

    if (in_array($new_status, $allowed)) {
        $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE o_id = ?");
        mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
        mysqli_stmt_execute($stmt);
    }

    header("Location: a-orderpage.php?tab=" . ($_POST['tab'] ?? 'active'));
    exit();
}

// Handle delete (GET) — redirect BEFORE any HTML
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    $stmt = mysqli_prepare($conn, "DELETE FROM order_items WHERE o_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);

    $stmt = mysqli_prepare($conn, "DELETE FROM orders WHERE o_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);

    header("Location: a-orderpage.php?tab=" . ($_GET['tab'] ?? 'active') . "&msg=deleted");
    exit();
}

// Step 2: NOW safe to include a-header.php (outputs HTML navbar)
include "a-header.php";

// Fetch orders
$tab    = isset($_GET['tab']) && $_GET['tab'] === 'history' ? 'history' : 'active';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$base_select = "SELECT o.o_id, o.c_name, o.contact, o.delivery_address, o.status,
                       o.total_price, o.o_datetime,
                       GROUP_CONCAT(f.f_name ORDER BY f.f_name SEPARATOR ', ') AS food_names,
                       GROUP_CONCAT(oi.quantity ORDER BY f.f_name SEPARATOR ', ') AS quantities
                FROM orders o
                LEFT JOIN order_items oi ON o.o_id = oi.o_id
                LEFT JOIN foods f ON oi.f_id = f.f_id";

$status_filter = ($tab === 'active')
    ? "o.status IN ('Pending','Confirmed','Accepted','Ready')"
    : "o.status IN ('Completed','Cancelled')";

if ($search !== '') {
    $sql  = "$base_select WHERE $status_filter AND o.c_name LIKE ?
             GROUP BY o.o_id ORDER BY o.o_datetime DESC";
    $stmt = mysqli_prepare($conn, $sql);
    $like = "%" . $search . "%";
    mysqli_stmt_bind_param($stmt, "s", $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn,
        "$base_select WHERE $status_filter
         GROUP BY o.o_id ORDER BY o.o_datetime DESC");
}

// Tab badge counts
$active_count  = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS c FROM orders WHERE status IN ('Pending','Confirmed','Accepted','Ready')"))['c'];
$history_count = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS c FROM orders WHERE status IN ('Completed','Cancelled')"))['c'];
?>

<!-- Step 3: Page content (safe to output HTML now) -->
<div class="admin-page">

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <div class="alert-success">&#10004; Order deleted successfully.</div>
<?php endif; ?>

<div class="admin-card">
    <h2>&#128203; Order Management</h2>

    <!-- TABS -->
    <div style="display:flex; gap:0; margin-bottom:20px; border-bottom:2px solid #f25d07;">
        <a href="a-orderpage.php?tab=active"
           style="padding:10px 22px; text-decoration:none; font-weight:bold; font-size:14px;
                  border-radius:6px 6px 0 0;
                  <?php echo $tab==='active'
                      ? 'background:#f25d07; color:white;'
                      : 'background:#f5f5f5; color:#555;'; ?>">
            &#9203; Active Orders
            <span style="border-radius:10px; padding:1px 7px; font-size:12px; margin-left:5px;
                         background:<?php echo $tab==='active'?'white':'#f25d07'; ?>;
                         color:<?php echo $tab==='active'?'#f25d07':'white'; ?>;">
                <?php echo $active_count; ?>
            </span>
        </a>
        <a href="a-orderpage.php?tab=history"
           style="padding:10px 22px; text-decoration:none; font-weight:bold; font-size:14px;
                  border-radius:6px 6px 0 0; margin-left:4px;
                  <?php echo $tab==='history'
                      ? 'background:#f25d07; color:white;'
                      : 'background:#f5f5f5; color:#555;'; ?>">
            &#10003; Completed / Cancelled
            <span style="border-radius:10px; padding:1px 7px; font-size:12px; margin-left:5px;
                         background:<?php echo $tab==='history'?'white':'#f25d07'; ?>;
                         color:<?php echo $tab==='history'?'#f25d07':'white'; ?>;">
                <?php echo $history_count; ?>
            </span>
        </a>
    </div>

    <!-- SEARCH -->
    <form class="search-form" method="GET">
        <input type="hidden" name="tab" value="<?php echo $tab; ?>">
        <input type="text" name="search" placeholder="Search by customer name..."
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
        <?php if ($search): ?>
            <a href="a-orderpage.php?tab=<?php echo $tab; ?>" class="clear-btn">Clear</a>
        <?php endif; ?>
    </form>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Food Items</th>
                    <th>Qty</th>
                    <th>Total (Rs.)</th>
                    <th>Status</th>
                    <th>Contact</th>
                    <th>Delivery Address</th>
                    <th>Date/Time</th>
                    <th>Update Status</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0):
                $sn = 1;
                while ($order = mysqli_fetch_assoc($result)):
                    $badge_map = [
                        'Pending'  =>'badge-pending',   'Confirmed'=>'badge-confirmed',
                        'Accepted' =>'badge-accepted',  'Ready'    =>'badge-ready',
                        'Completed'=>'badge-completed', 'Cancelled'=>'badge-cancelled'
                    ];
                    $badge = $badge_map[$order['status']] ?? 'badge-pending';
            ?>
                <tr>
                    <td><?php echo $sn++; ?></td>
                    <td>#<?php echo $order['o_id']; ?></td>
                    <td><?php echo htmlspecialchars($order['c_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['food_names'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($order['quantities'] ?? '-'); ?></td>
                    <td><?php echo number_format($order['total_price'] ?? 0, 2); ?></td>
                    <td><span class="badge <?php echo $badge; ?>"><?php echo $order['status']; ?></span></td>
                    <td><?php echo htmlspecialchars($order['contact'] ?? '-'); ?></td>
                    <td style="font-size:12px;">
                        <?php echo !empty($order['delivery_address'])
                            ? htmlspecialchars($order['delivery_address'])
                            : '<span style="color:#ccc;">—</span>'; ?>
                    </td>
                    <td style="font-size:13px;">
                        <?php echo !empty($order['o_datetime'])
                            ? date("Y-m-d H:i", strtotime($order['o_datetime']))
                            : '—'; ?>
                    </td>
                    <td>
                        <?php if ($tab === 'active'): ?>
                        <!--
                            No Update button — the dropdown auto-submits
                            using onchange="this.form.submit()" so the
                            admin just picks a status and it saves instantly.
                        -->
                        <form method="POST">
                            <input type="hidden" name="order_id"      value="<?php echo $order['o_id']; ?>">
                            <input type="hidden" name="update_status" value="1">
                            <input type="hidden" name="tab"           value="<?php echo $tab; ?>">
                            <select name="status"
                                    onchange="this.form.submit()"
                                    style="padding:6px 10px; border:1px solid #ccc;
                                           border-radius:5px; font-size:13px; cursor:pointer;">
                                <?php
                                foreach (['Pending','Confirmed','Accepted','Ready','Completed','Cancelled'] as $s) {
                                    $sel = ($s === $order['status']) ? 'selected' : '';
                                    echo "<option value='$s' $sel>$s</option>";
                                }
                                ?>
                            </select>
                        </form>
                        <?php else: ?>
                            <span style="color:#aaa; font-size:12px;">Archived</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="a-orderpage.php?delete_id=<?php echo $order['o_id']; ?>&tab=<?php echo $tab; ?>"
                           class="btn-danger" style="font-size:12px;"
                           onclick="return confirm('Delete order #<?php echo $order['o_id']; ?>?')">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr>
                    <td colspan="12" style="text-align:center; padding:30px; color:#888;">
                        <?php echo $tab === 'active'
                            ? '&#10003; No active orders right now.'
                            : 'No completed or cancelled orders yet.'; ?>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <br>
    <a href="admin_invoice.php" class="btn-primary">&#128424; View Invoice</a>
</div>
</div>
</body>
</html>
