<?php
include "header.php";
require_once "check_login.php";
require_once "db.php";
 
$customer_id   = $_SESSION['customer_id'];
$customer_name = $_SESSION['username'];
 
$success_message = isset($_GET['success']) ? urldecode($_GET['success']) : '';
$error_message   = isset($_GET['error'])   ? urldecode($_GET['error'])   : '';
 
// Only show ACTIVE orders - hide Completed and Cancelled
$orders_sql = "SELECT
    o.o_id,
    o.o_datetime,
    o.total_price,
    o.status
FROM orders o
WHERE o.c_id = '$customer_id'
  AND o.status NOT IN ('Completed', 'Cancelled')
ORDER BY o.o_datetime DESC";
 
$orders_result = mysqli_query($conn, $orders_sql);
 
// Load saved addresses for this customer
$addr_result     = mysqli_query($conn,
    "SELECT * FROM saved_addresses WHERE c_id = '$customer_id' ORDER BY created_at DESC");
$saved_addresses = mysqli_fetch_all($addr_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders</title>
  <style>
    .order-container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }
    .text-center {
      text-align: center;
      margin-bottom: 30px;
      color: #f25d07;
      font-size: 28px;
      font-weight: bold;
    }
    .orders-table {
      width: 100%;
      background: white;
      border-collapse: collapse;
      margin: 20px 0 30px;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .orders-table th {
      background-color: #dbdbdb86;
      padding: 15px 20px;
      text-align: center;
      font-weight: bold;
      color: #333;
      border-bottom: 2px solid #f25d07;
      font-size: 16px;
    }
    .orders-table td {
      text-align: center;
      padding: 15px 20px;
      border-bottom: 1px solid #e0e0e0;
      vertical-align: middle;
    }
    .orders-table tr:last-child td { border-bottom: none; }
    .orders-table tr:hover { background-color: #f9f9f9; }
    .t-foot {
      background-color: #dbdbdb86;
      border-top: 1px solid #f25d07;
      border-bottom: 1px solid #f25d07;
    }
    .btn-change, .btn-delete {
      padding: 6px 15px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      text-decoration: none;
      display: inline-block;
      font-weight: bold;
    }
    .btn-change { background-color: #14b65d; color: white; margin-right: 8px; }
    .btn-change:hover { background-color: #0b6634; }
    .btn-delete { background-color: #f44336; color: white; }
    .btn-delete:hover { background-color: #d32f2f; }
    .success-message {
      background-color: #d4edda;
      color: #155724;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 5px;
      font-weight: bold;
      text-align: center;
    }
    .payment-option {
      display: flex;
      gap: 15px;
      margin-bottom: 16px;
      flex-wrap: wrap;
    }
    .payment-card {
      flex: 1;
      min-width: 180px;
      border: 2px solid #ccc;
      border-radius: 8px;
      padding: 14px 16px;
      cursor: pointer;
      transition: border-color 0.2s, background 0.2s;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      font-weight: bold;
      color: #444;
      background: white;
    }
    .payment-card input[type="radio"] { width: 16px; height: 16px; cursor: pointer; }
    .payment-card:has(input:checked) {
      border-color: #f25d07;
      background: #fff8f3;
      color: #f25d07;
    }
    @media (max-width: 768px) {
      .order-container { padding: 0 15px; margin: 20px auto; }
      .orders-table { display: block; overflow-x: auto; }
      .orders-table th, .orders-table td { padding: 10px 15px; font-size: 14px; }
    }
  </style>
</head>
<body>
<section class="order-container">
  <h2 class="text-center">My Orders</h2>
 
  <?php if (!empty($success_message)): ?>
    <div class="success-message"><?= htmlspecialchars($success_message); ?></div>
  <?php endif; ?>
  <?php if (!empty($error_message)): ?>
    <div class="success-message" style="background:#f8d7da; color:#721c24;">
      <?= htmlspecialchars($error_message); ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($_SESSION['order_confirmed'])): ?>
    <div class="success-message">Your order is confirmed ✅</div>
    <?php unset($_SESSION['order_confirmed']); ?>
  <?php endif; ?>
 
  <!-- ===== ORDERS TABLE (only when there are active orders) ===== -->
  <?php if ($orders_result && mysqli_num_rows($orders_result) > 0): ?>
    <table class="orders-table">
      <thead>
        <tr>
          <th>Food</th>
          <th>Quantity</th>
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
 
          $items_sql    = "SELECT oi.*, f.f_name, f.price
                           FROM order_items oi
                           JOIN foods f ON oi.f_id = f.f_id
                           WHERE oi.o_id = {$order['o_id']}";
          $items_result = mysqli_query($conn, $items_sql);
 
          // Get quantity for +/- controls
          $qty_row  = mysqli_fetch_assoc(mysqli_query($conn,
              "SELECT quantity FROM order_items WHERE o_id = {$order['o_id']} LIMIT 1"));
          $item_qty = $qty_row['quantity'] ?? 1;
        ?>
          <tr>
            <td>
              <?php while ($item = mysqli_fetch_assoc($items_result)):
                echo htmlspecialchars($item['f_name']);
              endwhile; ?>
            </td>
            <!-- Quantity column with inline - / + controls -->
            <td>
              <?php if ($order['status'] === 'Pending'): ?>
                <div style="display:inline-flex; align-items:center; gap:6px;">
                  <a href="update_qty.php?o_id=<?= $order['o_id'] ?>&action=decrease"
                     style="display:inline-flex; align-items:center; justify-content:center;
                            width:28px; height:28px; background:#f25d07; color:white;
                            border-radius:50%; text-decoration:none; font-size:18px;
                            font-weight:bold; line-height:1;">&#8722;</a>
                  <span style="font-size:16px; font-weight:bold; min-width:22px; text-align:center;">
                    <?= $item_qty ?>
                  </span>
                  <a href="update_qty.php?o_id=<?= $order['o_id'] ?>&action=increase"
                     style="display:inline-flex; align-items:center; justify-content:center;
                            width:28px; height:28px; background:#f25d07; color:white;
                            border-radius:50%; text-decoration:none; font-size:18px;
                            font-weight:bold; line-height:1;">&#43;</a>
                </div>
              <?php else: ?>
                <?= $item_qty ?>
              <?php endif; ?>
            </td>
            <td>Rs. <?= number_format($order['total_price'],2); ?></td>
            <td><?= date("Y-m-d H:i:s", strtotime($order['o_datetime'])); ?></td>
            <td><?= htmlspecialchars($order['status']); ?></td>
            <!-- DELETE button stays exactly as original -->
            <td>
              <?php if ($order['status'] === 'Pending'): ?>
                <a href="delete_item.php?o_id=<?= $order['o_id'] ?>" class="btn-delete">DELETE</a>
              <?php else: ?>
                <span style="font-size:12px; color:#888;">Processing...</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
      <tfoot>
        <tr class="t-foot">
          <td colspan="2" style="text-align:right; font-weight:bold;">TOTAL</td>
          <td style="font-weight:bold;">Rs. <?= number_format($ordersTotal,2); ?></td>
          <td colspan="3"></td>
        </tr>
      </tfoot>
    </table>
  <?php endif; ?>
 
  <!-- =====================================================
       CUSTOMER DETAILS + ADDRESS FORM
       Always shown so customers can pre-fill details.
       Payment method: eSewa OR Cash on Delivery.
  ====================================================== -->
  <div style="background:#fff3ec; border:1px solid #f25d07; border-radius:8px;
              padding:22px; margin-top:10px;">
 
    <h3 style="color:#f25d07; margin:0 0 18px 0; font-size:17px;">
      &#128203; Fill Your Details to Confirm Order
    </h3>
 
    <!-- SAVED ADDRESS SELECTOR -->
    <?php if (!empty($saved_addresses)): ?>
    <div style="background:#fffaf7; border:1px solid #f7c9aa; border-radius:6px;
                padding:12px; margin-bottom:18px;">
      <label style="font-weight:bold; font-size:14px; color:#555; display:block; margin-bottom:8px;">
        &#128205; Use a saved address:
      </label>
      <select id="savedAddressSelect"
              style="width:100%; padding:9px 12px; border:1px solid #ccc;
                     border-radius:5px; font-size:14px; margin-bottom:8px;">
        <option value="">-- Select a saved address --</option>
        <?php foreach ($saved_addresses as $i => $addr): ?>
          <option value="<?php echo $i; ?>"
                  data-name="<?php echo htmlspecialchars($addr['full_name']); ?>"
                  data-phone="<?php echo htmlspecialchars($addr['phone']); ?>"
                  data-street="<?php echo htmlspecialchars($addr['street_no']); ?>"
                  data-landmark="<?php echo htmlspecialchars($addr['landmark']); ?>"
                  data-house="<?php echo htmlspecialchars($addr['house_no']); ?>">
            <?php echo htmlspecialchars($addr['full_name']); ?> —
            <?php echo htmlspecialchars($addr['street_no']); ?>,
            <?php echo htmlspecialchars($addr['house_no']); ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="button" onclick="fillSavedAddress()"
              style="padding:7px 16px; background:#f25d07; color:white; border:none;
                     border-radius:5px; cursor:pointer; font-size:13px;">
        &#10004; Use This Address
      </button>
    </div>
    <?php endif; ?>
 
    <!-- DETAILS FORM -->
    <form action="update_order_details.php" method="POST" id="detailsForm">
 
      <!-- Row 1: Full Name + Phone -->
      <div style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:12px;">
        <div style="flex:1; min-width:180px;">
          <label style="font-weight:bold; font-size:14px; display:block; margin-bottom:5px;">
            Full Name *
          </label>
          <input type="text" name="full_name" id="inp_name"
                 placeholder="Your full name" required
                 style="width:100%; padding:9px 12px; border:1px solid #ccc;
                        border-radius:5px; font-size:14px; box-sizing:border-box;">
        </div>
        <div style="flex:1; min-width:160px;">
          <label style="font-weight:bold; font-size:14px; display:block; margin-bottom:5px;">
            Phone Number *
          </label>
          <input type="tel" name="phone" id="inp_phone"
                 placeholder="97XXXXXXXX" required
                 style="width:100%; padding:9px 12px; border:1px solid #ccc;
                        border-radius:5px; font-size:14px; box-sizing:border-box;">
        </div>
      </div>
 
      <!-- Row 2: Address -->
      <p style="font-weight:bold; color:#555; font-size:14px; margin:0 0 8px 0;">
        &#127968; Delivery Address
      </p>
      <div style="display:flex; gap:15px; flex-wrap:wrap; margin-bottom:12px;">
        <div style="flex:1; min-width:150px;">
          <label style="font-size:13px; color:#666; display:block; margin-bottom:4px;">
            Street No. *
          </label>
          <input type="text" name="street_no" id="inp_street"
                 placeholder="e.g. Street 12" required
                 style="width:100%; padding:9px 12px; border:1px solid #ccc;
                        border-radius:5px; font-size:14px; box-sizing:border-box;">
        </div>
        <div style="flex:1; min-width:150px;">
          <label style="font-size:13px; color:#666; display:block; margin-bottom:4px;">
            Landmark
          </label>
          <input type="text" name="landmark" id="inp_landmark"
                 placeholder="e.g. Near City Mall"
                 style="width:100%; padding:9px 12px; border:1px solid #ccc;
                        border-radius:5px; font-size:14px; box-sizing:border-box;">
        </div>
        <div style="flex:1; min-width:150px;">
          <label style="font-size:13px; color:#666; display:block; margin-bottom:4px;">
            House / Apartment No. *
          </label>
          <input type="text" name="house_no" id="inp_house"
                 placeholder="e.g. House 7B / Apt 304" required
                 style="width:100%; padding:9px 12px; border:1px solid #ccc;
                        border-radius:5px; font-size:14px; box-sizing:border-box;">
        </div>
      </div>
 
      <!-- Row 3: Date & Time -->
      <div style="margin-bottom:15px;">
        <?php $now = date("Y-m-d\TH:i"); ?>
        <label style="font-weight:bold; font-size:14px; display:block; margin-bottom:5px;">
          Date &amp; Time *
        </label>
        <input type="datetime-local" name="datetime"
               min="<?php echo $now; ?>" value="<?php echo $now; ?>" required
               style="width:100%; padding:9px 12px; border:1px solid #ccc;
                      border-radius:5px; font-size:14px; box-sizing:border-box;">
      </div>
 
      <!-- Save address checkbox -->
      <label style="font-size:13px; color:#555; display:flex; align-items:center; gap:8px; margin-bottom:18px;">
        <input type="checkbox" name="save_address" value="1" checked
               style="width:16px; height:16px; cursor:pointer;">
        Save this address for future orders
      </label>
 
      <!-- ===== PAYMENT METHOD ===== -->
      <p style="font-weight:bold; color:#555; font-size:14px; margin:0 0 10px 0;">
        &#128179; Payment Method *
      </p>
      <div class="payment-option">
        <label class="payment-card">
          <input type="radio" name="payment_method" value="esewa" checked>
          &#128274; Pay with eSewa
        </label>
        <label class="payment-card">
          <input type="radio" name="payment_method" value="cod">
          &#128181; Cash on Delivery
        </label>
      </div>
 
      <!-- Submit button (label changes based on selection) -->
      <button type="submit" id="submitBtn"
              style="width:100%; background:#f25d07; color:white; padding:13px;
                     border:none; border-radius:6px; font-size:15px; font-weight:bold;
                     cursor:pointer; transition:background 0.2s;">
        &#128274; Pay with eSewa &amp; Confirm Order
      </button>
    </form>
  </div>
 
</section>
 
<script>
  // Auto-fill form when saved address is selected
  function fillSavedAddress() {
    var sel = document.getElementById('savedAddressSelect');
    var opt = sel.options[sel.selectedIndex];
    if (!opt.value) return;
    document.getElementById('inp_name').value     = opt.getAttribute('data-name');
    document.getElementById('inp_phone').value    = opt.getAttribute('data-phone');
    document.getElementById('inp_street').value   = opt.getAttribute('data-street');
    document.getElementById('inp_landmark').value = opt.getAttribute('data-landmark');
    document.getElementById('inp_house').value    = opt.getAttribute('data-house');
  }
 
  // Update button label when payment method changes
  document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
      var btn = document.getElementById('submitBtn');
      if (this.value === 'cod') {
        btn.innerHTML = '&#128181; Confirm Order (Cash on Delivery)';
        btn.style.background = '#4caf50';
      } else {
        btn.innerHTML = '&#128274; Pay with eSewa &amp; Confirm Order';
        btn.style.background = '#f25d07';
      }
    });
  });
</script>
</body>
</html>