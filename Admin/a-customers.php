<?php
/*
    CUSTOMER MANAGEMENT (Admin/a-customers.php)
    Admin can: View all customers, search by name/email, delete.
*/
include "a-header.php";
require_once "../db.php";

$msg = '';

// ---- DELETE CUSTOMER ----
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);

    // Delete the customer's orders and order_items first
    $order_ids_res = mysqli_query($conn, "SELECT o_id FROM orders WHERE c_id = $del_id");
    while ($row = mysqli_fetch_assoc($order_ids_res)) {
        $oid  = $row['o_id'];
        mysqli_query($conn, "DELETE FROM order_items WHERE o_id = $oid");
    }
    mysqli_query($conn, "DELETE FROM orders WHERE c_id = $del_id");

    $stmt = mysqli_prepare($conn, "DELETE FROM signup_page WHERE c_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $del_id);
    mysqli_stmt_execute($stmt);
    $msg = "Customer deleted successfully.";
}

// ---- SEARCH ----
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
    $sql  = "SELECT s.*,
                    COUNT(o.o_id)            AS total_orders,
                    COALESCE(SUM(o.total_price),0) AS total_spent
             FROM signup_page s
             LEFT JOIN orders o ON s.c_id = o.c_id AND o.status='Completed'
             WHERE s.c_username LIKE ? OR s.email LIKE ?
             GROUP BY s.c_id
             ORDER BY s.c_id DESC";
    $stmt = mysqli_prepare($conn, $sql);
    $like = "%" . $search . "%";
    mysqli_stmt_bind_param($stmt, "ss", $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn,
        "SELECT s.*,
                COUNT(o.o_id)                  AS total_orders,
                COALESCE(SUM(o.total_price),0) AS total_spent
         FROM signup_page s
         LEFT JOIN orders o ON s.c_id = o.c_id AND o.status='Completed'
         GROUP BY s.c_id
         ORDER BY s.c_id DESC");
}
?>
<div class="admin-page">

<?php if ($msg): ?><div class="alert-success">&#10004; <?php echo htmlspecialchars($msg); ?></div><?php endif; ?>

<div class="admin-card">
    <h2>&#128101; Customer Management</h2>

    <form class="search-form" method="GET">
        <input type="text" name="search" placeholder="Search by username or email..."
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
        <?php if ($search): ?><a href="a-customers.php" class="clear-btn">Clear</a><?php endif; ?>
    </form>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th><th>Username</th><th>Email</th>
                    <th>Phone</th><th>Total Orders</th>
                    <th>Total Spent (Rs.)</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($result) > 0):
                while ($c = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $c['c_id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($c['c_username']); ?></strong></td>
                    <td><?php echo htmlspecialchars($c['email']); ?></td>
                    <td><?php echo htmlspecialchars($c['phone'] ?? '—'); ?></td>
                    <td><?php echo $c['total_orders']; ?></td>
                    <td><?php echo number_format($c['total_spent'],2); ?></td>
                    <td>
                        <a href="a-customers.php?delete_id=<?php echo $c['c_id']; ?>"
                           class="btn-danger"
                           onclick="return confirm('Delete customer <?php echo htmlspecialchars($c['c_username']); ?>? This will also delete all their orders.')">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="7" style="text-align:center;padding:20px;color:#888;">
                    <?php echo $search ? 'No customers found.' : 'No customers registered yet.'; ?>
                </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
</body>
</html>
