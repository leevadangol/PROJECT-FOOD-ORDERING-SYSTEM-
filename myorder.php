<?php
include "header.php";
require_once "check_login.php";
require_once "db.php";

$customer_id = $_SESSION['customer_id'];
$customer_name = $_SESSION['username'];

// Handle success messages
if (isset($_GET['success'])) {
  $success_message = urldecode($_GET['success']);
}

// Fetch customer's orders from database
$orders_sql = "SELECT 
    o.o_id,
    o.o_datetime,
    o.table_no,
    o.total_price,
    o.status
FROM orders o 
WHERE o.c_id = '$customer_id' 
ORDER BY o.o_datetime DESC";

$orders_result = mysqli_query($conn, $orders_sql);

$cartItems = $_SESSION['cart'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders</title>
  <style>
    /* ---------------------------CSS--------------------------- */
    .order-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }

    .text-center {
      text-align: center;
      margin-bottom: 30px;
      color: #333;
      font-size: 28px;
      font-weight: bold;
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .empty-state p {
      margin-bottom: 15px;
      color: #666;
    }

    .empty-state a {
      color: #2196f3;
      text-decoration: none;
      font-weight: bold;
    }

    .empty-state a:hover {
      text-decoration: underline;
    }

    /* Tables  */

    .orders-table {
      width: 100%;
      background: white;
      border-collapse: collapse;
      margin: 20px 0 30px;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .orders-table th,
    .cart-table th {
      background-color: #dbdbdb86;
      padding: 15px 20px;
      text-align: center;
      font-weight: bold;
      color: #333;
      border-bottom: 2px solid #f25d07;
      font-size: 16px;
    }

    .orders-table {
      background-color: #dbdbdb2c;
      padding: 15px 20px;
      text-align: left;
      font-weight: bold;
      color: #333;
      font-size: 16px;
    }

    .orders-table td,
    .cart-table td {
      text-align: center;
      padding: 15px 20px;
      border-bottom: 1px solid #e0e0e0;
      vertical-align: middle;
    }

    .orders-table tr:last-child td,
    .cart-table tbody tr:last-child td {
      border-bottom: none;
    }

    .orders-table tr:hover,
    .cart-table tbody tr:hover {
      background-color: #f9f9f9;
    }

    /* table footer */
    .t-foot {
      background-color: #dbdbdb86;
      border-top: 1px solid #f25d07;
      border-bottom: 1px solid #f25d07;
    }

    /* Action Buttons  */
    .btn-change,
    .btn-delete,
    .btn-view {
      padding: 6px 15px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      text-decoration: none;
      display: inline-block;
      transition: background-color 0.3s;
      font-weight: bold;
    }

    .btn-change,
    .btn-view {
      background-color: #14b65dff;
      color: white;
      margin-right: 8px;
    }

    .btn-change:hover,
    .btn-view:hover {
      background-color: #0b6634ff;
    }

    .btn-delete {
      background-color: #f44336;
      color: white;
    }

    .btn-delete:hover {
      background-color: #d32f2f;
    }

    /* Quantity  */
    .qty-input {
      width: 60px;
      padding: 8px 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      text-align: center;
      font-size: 14px;
    }

    .qty-input:focus {
      outline: none;
      border-color: #2196f3;
      box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.2);
    }

    /* Cart  */
    .cart-content {
      background: white;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      margin-top: 30px;
    }

    .cart-content h3 {
      margin-bottom: 20px;
      color: #333;
      font-size: 22px;
    }

    /* Confirm order \button and print reciept */
    .btn-primary {
      background-color: #f25d07;
      color: white;
      text-align: center;
      padding: 12px 30px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      font-weight: bold;
      display: block;
      margin: 20px auto 0;
      transition: background-color 0.3s;
      width: 200px;
    }

    /* order is confirm message */
    .success-message {
      background-color: #d4edda;
      color: #155724;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 5px;
      font-weight: bold;
      text-align: center;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .order-container {
        padding: 0 15px;
        margin: 20px auto;
      }

      .orders-table,
      .cart-table {
        display: block;
        overflow-x: auto;
      }

      .orders-table th,
      .orders-table td,
      .cart-table th,
      .cart-table td {
        padding: 10px 15px;
        font-size: 14px;
      }

      .btn-change,
      .btn-delete,
      .btn-view {
        padding: 5px 10px;
        font-size: 13px;
        margin-bottom: 5px;
      }

      .text-center {
        font-size: 24px;
      }
    }
  </style>
</head>

<body>

  <section class="order-container">
    <h2 class="text-center" style="color: #f25d07;">My Orders</h2>

    <?php if (!empty($_SESSION['order_confirmed'])): ?>
      <div class="success-message">
        Your order is confirmed ✅
      </div>
    <?php unset($_SESSION['order_confirmed']); endif; ?>

    <?php if ($orders_result && mysqli_num_rows($orders_result) > 0): ?>
      <table class="orders-table">
        <thead>
          <tr>
            <th>Food</th>
            <th>Quantity</th>
            <th>Table No</th>
            <th>Total</th>
            <th>DateTime</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $ordersTotal = 0;
          while ($order = mysqli_fetch_assoc($orders_result)):
            $ordersTotal += $order['total_price'];

            // Fetch all items for this order
            $items_sql = "SELECT oi.*, f.f_name, f.price 
                          FROM order_items oi 
                          JOIN foods f ON oi.f_id = f.f_id 
                          WHERE oi.o_id = {$order['o_id']}";
            $items_result = mysqli_query($conn, $items_sql);
          ?>
            <tr>
              <td>
                <?php
                while ($item = mysqli_fetch_assoc($items_result)) {
                    echo htmlspecialchars($item['f_name']);
                }
                ?>
              </td>

              <td>
                <?php
                $items_result_qty = mysqli_query($conn, $items_sql);
                while ($item = mysqli_fetch_assoc($items_result_qty)) {
                    echo $item['quantity'] . "<br>";
                }
                ?>
              </td>

              <td><?= htmlspecialchars($order['table_no']); ?></td>
              <td>Rs. <?= number_format($order['total_price'],2); ?></td>
              <td><?= date("Y-m-d H:i:s", strtotime($order['o_datetime'])); ?></td>

              <?php
                $status_class = '';
                switch($order['status']) {
                    case 'Cancelled': $status_class = 'status-cancelled'; break;
                    case 'Accepted': $status_class = 'status-accepted'; break;
                    case 'Ready': $status_class = 'status-ready'; break;
                    case 'Completed': $status_class = 'status-completed'; break;
                    case 'Confirmed': $status_class = 'status-confirmed'; break;
                    default: $status_class = 'status-pending';
                }
              ?>
              <td class="<?= $status_class; ?>"><?= htmlspecialchars($order['status']); ?></td>

              <td>
                <a href="change_item.php?id=<?= $order['o_id'] ?>" class="btn-change">CHANGE</a>
                <a href="delete_item.php?o_id=<?= $order['o_id'] ?>" class="btn-delete">DELETE</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
        <tfoot>
          <tr class="t-foot">
            <td colspan="4" style="text-align: right; font-weight: bold;">TOTAL</td>
            <td style="font-weight: bold;">Rs. <?= number_format($ordersTotal,2); ?></td>
            <td></td>
          </tr>
        </tfoot>
      </table>
      <a href="confirm_order.php" class="btn-primary">Confirm Order</a>
    <?php else: ?>
      <div class="empty-state">
        <p>You haven't placed any orders yet.</p>
        <p><a href="home.php">Browse our menu</a> to place your first order!</p>
      </div>
    <?php endif; ?>
    <?php if ($orders_result && mysqli_num_rows($orders_result) > 0): ?>
  <a href="receipt.php" target="_blank" class="btn-primary" >Print All Orders Receipt</a>
<?php endif; ?>
  </section>
</body>
</html>


<!-- style="background-color:#f25d07; margin-top:10px; display:inline-block;" -->