<?php
include "a-header.php";
require_once "../db.php";

$sql = "SELECT 
            o.o_id,
            o.c_id,
            o.c_name,
            o.contact,
            o.table_no,
            o.status,
            GROUP_CONCAT(f.f_name SEPARATOR ', ') AS food_names,
            GROUP_CONCAT(oi.quantity SEPARATOR ', ') AS quantities
        FROM orders o
        LEFT JOIN order_items oi ON o.o_id = oi.o_id
        LEFT JOIN foods f ON oi.f_id = f.f_id
        WHERE o.status != 'Pending'       
        GROUP BY o.o_id
        ORDER BY o.o_datetime DESC";

$result = mysqli_query($conn, $sql);

// status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];

    // Allowed statuses
    $allowed_status = ['Accepted','Ready','Completed','Cancelled'];
    if (in_array($new_status, $allowed_status)) {
        $update_sql = "UPDATE orders SET status = '$new_status' WHERE o_id = $order_id";
        mysqli_query($conn, $update_sql);

        header("Location: a-orderpage.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Admin Page</title>
<link rel="stylesheet" href="/PROJECT (FOOD ORDERING SYSTEM)/CSS/a-orderpage_style.css" />
</head>
<body>

<section class="admin-container">
  <h2>Order Page</h2>

  <div class="orders-box">
    <h3>Orders :</h3>

   

    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>S.N.</th>
            <th>Name</th>
            <th>Food</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Contact</th>
            <th>Table No.</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
          <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php $sn = 1; ?>
            <?php while ($order = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= $sn++; ?></td>
                <td><?= htmlspecialchars($order['c_name']); ?></td>
                <td><?= htmlspecialchars($order['food_names']); ?></td>
                <td><?= htmlspecialchars($order['quantities']); ?></td>

                <?php
                $status_class = '';
                switch ($order['status']) {
                    case 'Cancelled': $status_class = 'status-cancelled'; break;
                    case 'Accepted': $status_class = 'status-accepted'; break;
                    case 'Ready': $status_class = 'status-ready'; break;
                    case 'Completed': $status_class = 'status-completed'; break; 
                }
                ?>
                <td class="status <?= $status_class; ?>">
                    <?= htmlspecialchars($order['status']); ?>
                </td>

                <td><?= htmlspecialchars($order['contact']); ?></td>
                <td><?= htmlspecialchars($order['table_no']); ?></td>
                <td>
                  <form method="POST" style="display:flex; gap:5px;">
                    <input type="hidden" name="order_id" value="<?= $order['o_id']; ?>" />
                    <select name="status" required>
                      <?php
                        $statuses = ['Accepted','Ready','Completed','Cancelled'];
                        foreach ($statuses as $status) {
                            $selected = ($status === $order['status']) ? 'selected' : '';
                            echo "<option value='$status' $selected>$status</option>";
                        }
                      ?>
                    </select>
                    <button type="submit" name="update_status" class="btn-update">Update</button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8" style="text-align:center;">No orders found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
     <a href="admin_invoice.php" target="_blank" 
   class="btn-view" 
   style="background:#f25d07; color:white; padding:8px 15px; border-radius:5px; text-decoration:none; display:inline-block; margin-bottom:10px;">
   View Completed Orders Invoice
</a>

   
  </div>
 
</section>

</body>
</html>