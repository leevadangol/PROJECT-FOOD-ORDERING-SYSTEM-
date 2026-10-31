<?php
include "header.php";
require_once "check_login.php";
require_once "db.php";
require_once "location_helper.php";   // restaurant position + radius for the map

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

// // Load saved addresses for this customer
// $addr_result     = mysqli_query($conn,
//     "SELECT * FROM saved_addresses WHERE c_id = '$customer_id' ORDER BY created_at DESC");
// $saved_addresses = mysqli_fetch_all($addr_result, MYSQLI_ASSOC);

// Restaurant location + delivery radius (null if location_migration.sql not run yet)
$restaurant = get_restaurant_location($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders</title>
  <!-- Leaflet (bundled locally in /leaflet) - map library for OpenStreetMap -->
  <link rel="stylesheet" href="leaflet/leaflet.css">
  <style>
    /* ---- Delivery map ---- */
    #deliveryMap { height: 340px; width: 100%; border-radius: 8px; border: 1px solid #ccc; z-index: 0; }
    .map-btn { padding: 9px 16px; border: none; border-radius: 5px; font-size: 14px;
               font-weight: bold; cursor: pointer; color: #fff; }
    .map-btn:disabled { opacity: .5; cursor: not-allowed; }
    #btnUseGps { background: #2c7be5; }
    #btnConfirmLoc { background: #14b65d; }
    .map-info { background: #fff; border: 1px solid #f7c9aa; border-radius: 8px;
                padding: 10px 14px; font-size: 14px; margin-top: 10px; }
    .map-info div { display: flex; justify-content: space-between; padding: 3px 0; gap: 10px; }
    .map-msg { margin-top: 10px; padding: 10px 14px; border-radius: 6px; font-size: 14px; display: none; }
    .map-msg.ok   { background: #d4edda; color: #155724; display: block; }
    .map-msg.bad  { background: #f8d7da; color: #721c24; display: block; }
    .map-msg.info { background: #e7f1ff; color: #084298; display: block; }
    @media (max-width: 768px) { #deliveryMap { height: 280px; } }
  </style>
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
                  data-house="<?php echo htmlspecialchars($addr['house_no']); ?>"
                  >
            <?php echo htmlspecialchars($addr['full_name']); ?> —
            <?php echo htmlspecialchars($addr['street_no']); ?>,
            <!-- <?php echo htmlspecialchars($addr['house_no']); ?> -->
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
            Street No. 
          </label>
          <input type="text" name="street_no" id="inp_street"
                 placeholder="e.g. Street 12" 
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
            House / Apartment No. 
          </label>
          <input type="text" name="house_no" id="inp_house"
                 placeholder="e.g. House 7B / Apt 304" 
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

      <!-- ===== MAP: PICK EXACT DELIVERY POINT (OpenStreetMap + Leaflet) ===== -->
      <p style="font-weight:bold; color:#555; font-size:14px; margin:0 0 8px 0;">
        &#128506; Pin Your Delivery Location on the Map *
      </p>

      <?php if ($restaurant === null): ?>
        <div class="map-msg bad" style="display:block;">
          Delivery map is not set up yet (run <code>location_migration.sql</code>). Please contact the restaurant.
        </div>
      <?php else: ?>
        <p style="font-size:13px; color:#666; margin:0 0 8px 0;">
          Click on the map to place the marker, or drag the marker to adjust it.
          We deliver within <strong><?= number_format($restaurant['radius_km'], 1); ?> km</strong>
          of the restaurant.
        </p>

        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
          <button type="button" class="map-btn" id="btnUseGps">&#128205; Use My Current Location</button>
          <button type="button" class="map-btn" id="btnConfirmLoc" disabled>&#10004; Confirm This Location</button>
        </div>

        <div id="deliveryMap"></div>

        <div class="map-info">
          <div><span style="color:#555;">Latitude</span>  <strong id="latText">—</strong></div>
          <div><span style="color:#555;">Longitude</span> <strong id="lngText">—</strong></div>
          <div><span style="color:#555;">Distance from restaurant</span> <strong id="distText">—</strong></div>
        </div>
        <p style="font-size:12px; color:#888; margin:6px 0 0 0;">
          &#9432; Distance is calculated with the Haversine formula. It is the
          <strong>straight-line</strong> distance, not the actual road distance, so the real
          route may be longer.
        </p>

        <div class="map-msg" id="mapMsg"></div>
      <?php endif; ?>

      <!-- Filled by JavaScript ONLY after the customer clicks "Confirm This Location".
           The server re-validates and re-calculates these on submit. -->
      <input type="hidden" name="delivery_lat" id="deliveryLat" value="">
      <input type="hidden" name="delivery_lng" id="deliveryLng" value="">

      <div style="height:18px;"></div>

      <!-- Order total breakdown -->
      <?php
        // Calculate food subtotal (sum of all pending orders)
        $sub_res   = mysqli_query($conn,
            "SELECT COALESCE(SUM(total_price),0) AS sub FROM orders
             WHERE c_id='$customer_id' AND status='Pending'");
        $food_subtotal = mysqli_fetch_assoc($sub_res)['sub'];
      ?>
      <div style="background:#fff; border:1px solid #f7c9aa; border-radius:8px;
                  padding:14px 18px; margin-bottom:15px; font-size:14px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <span style="color:#555;">Food Subtotal</span>
          <span>Rs. <?= number_format($food_subtotal, 2); ?></span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
          <span style="color:#555;">Delivery Fee</span>
          <span id="deliveryFeeDisplay">Rs. 100.00</span>
        </div>
        <hr style="border:none; border-top:1px solid #eee; margin:8px 0;">
        <div style="display:flex; justify-content:space-between; font-weight:bold; font-size:16px;">
          <span style="color:#f25d07;">Grand Total</span>
          <span id="grandTotalDisplay" style="color:#f25d07;">
            Rs. <?= number_format($food_subtotal + 100, 2); ?>
          </span>
        </div>
      </div>
      <input type="hidden" name="delivery_fee" id="deliveryFeeInput" value="100">

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

  // ---- Delivery fee live update ----
  var foodSubtotal = <?php echo $food_subtotal ?? 0; ?>;

  function updateDeliveryFee() {
    var isOutside   = document.getElementById('loc_outside').checked;
    var fee         = isOutside ? 150 : 100;
    var grandTotal  = foodSubtotal + fee;

    document.getElementById('deliveryFeeDisplay').textContent = 'Rs. ' + fee.toFixed(2);
    document.getElementById('grandTotalDisplay').textContent  = 'Rs. ' + grandTotal.toFixed(2);
    document.getElementById('deliveryFeeInput').value = fee;

    // Highlight selected card
    document.getElementById('card_inside').style.borderColor  = isOutside ? '#ccc' : '#f25d07';
    document.getElementById('card_outside').style.borderColor = isOutside ? '#f25d07' : '#ccc';
  }

  // Run once on page load to set correct highlight
  updateDeliveryFee();

  // ---- Payment method button label change ----
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

  // Update delivery fee and grand total when location changes
  var foodSubtotal = <?= $food_subtotal ?? 0; ?>;

  function updateDeliveryFee() {
    var isInside   = document.getElementById('loc_inside').checked;
    var fee        = isInside ? 100 : 150;
    var grandTotal = foodSubtotal + fee;

    document.getElementById('deliveryFeeDisplay').textContent = 'Rs. ' + fee.toFixed(2);
    document.getElementById('grandTotalDisplay').textContent  = 'Rs. ' + grandTotal.toFixed(2);
    document.getElementById('deliveryFeeInput').value         = fee;

    // Highlight the selected card
    document.getElementById('card_inside').style.borderColor  = isInside ? '#f25d07' : '#ccc';
    document.getElementById('card_outside').style.borderColor = isInside ? '#ccc' : '#f25d07';
  }

  // Set initial highlight on page load
  updateDeliveryFee();
</script>
<!-- Leaflet library (bundled locally, no CDN / API key needed) -->
<script src="leaflet/leaflet.js"></script>
<script>
(function () {
  var RESTAURANT = <?= $restaurant === null ? 'null' : json_encode($restaurant); ?>;
  var form       = document.getElementById('detailsForm');
  var msgBox     = document.getElementById('mapMsg');

  function showMsg(type, text) {
    if (!msgBox) return;
    msgBox.className = 'map-msg ' + type;
    msgBox.textContent = text;
  }

  // Block submit unless a location has been confirmed (server re-checks anyway)
  form.addEventListener('submit', function (e) {
    var lat = document.getElementById('deliveryLat').value;
    var lng = document.getElementById('deliveryLng').value;
    if (!lat || !lng) {
      e.preventDefault();
      showMsg('bad', 'Please pick your delivery location on the map and click "Confirm This Location" before placing the order.');
      var m = document.getElementById('deliveryMap');
      if (m) m.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  });

  if (!RESTAURANT) return;                       // map not configured
  if (typeof L === 'undefined') {                // Leaflet failed to load
    showMsg('bad', 'The map could not be loaded. Please check that the "leaflet" folder is on the server, then refresh the page.');
    return;
  }

  var latText = document.getElementById('latText');
  var lngText = document.getElementById('lngText');
  var distText = document.getElementById('distText');
  var btnGps = document.getElementById('btnUseGps');
  var btnConfirm = document.getElementById('btnConfirmLoc');
  var hidLat = document.getElementById('deliveryLat');
  var hidLng = document.getElementById('deliveryLng');

  L.Icon.Default.imagePath = 'leaflet/images/';

  var map = L.map('deliveryMap').setView([RESTAURANT.lat, RESTAURANT.lng], 13);

  var tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  var tileErrorShown = false;
  tiles.on('tileerror', function () {
    if (!tileErrorShown) {
      tileErrorShown = true;
      showMsg('bad', 'Map tiles could not be loaded (check your internet connection). You can still use "Use My Current Location".');
    }
  });

  // Restaurant marker + delivery-radius circle (separate from customer point)
  L.circle([RESTAURANT.lat, RESTAURANT.lng], {
    radius: RESTAURANT.radius_km * 1000, color: '#f25d07', weight: 2, fillOpacity: 0.06
  }).addTo(map);
  L.circleMarker([RESTAURANT.lat, RESTAURANT.lng], {
    radius: 9, color: '#fff', weight: 2, fillColor: '#f25d07', fillOpacity: 1
  }).addTo(map).bindTooltip('Restaurant', { permanent: false });
  map.fitBounds(L.latLng(RESTAURANT.lat, RESTAURANT.lng).toBounds(RESTAURANT.radius_km * 2000 * 1.15));

  var marker = null;
  var currentPoint = null;     // {lat, lng, within, distance}
  var reqId = 0;               // ignore out-of-date server answers

  function resetConfirmation() {
    hidLat.value = '';
    hidLng.value = '';
    btnConfirm.disabled = true;
  }

  function setPoint(lat, lng, panTo) {
    lat = Math.round(lat * 1e7) / 1e7;
    lng = Math.round(lng * 1e7) / 1e7;

    if (!marker) {
      marker = L.marker([lat, lng], { draggable: true }).addTo(map);
      marker.on('dragend', function () {
        var p = marker.getLatLng();
        setPoint(p.lat, p.lng, false);
      });
    } else {
      marker.setLatLng([lat, lng]);
    }
    if (panTo) map.setView([lat, lng], Math.max(map.getZoom(), 15));

    latText.textContent = lat.toFixed(6);
    lngText.textContent = lng.toFixed(6);
    distText.textContent = 'checking…';
    resetConfirmation();                         // any change cancels old confirmation
    currentPoint = null;
    showMsg('info', 'Checking delivery availability…');

    var myReq = ++reqId;
    var body = new URLSearchParams({ lat: lat, lng: lng });

    fetch('check_delivery.php', { method: 'POST', body: body, credentials: 'same-origin' })
      .then(function (r) {
        if (r.status === 401) throw new Error('Your session expired. Please log in again.');
        return r.json();
      })
      .then(function (d) {
        if (myReq !== reqId) return;             // a newer click happened meanwhile
        if (typeof d.distance_km === 'number') {
          distText.textContent = d.distance_km.toFixed(2) + ' km (straight line)';
        } else {
          distText.textContent = '—';
        }
        if (d.ok) {
          currentPoint = { lat: lat, lng: lng };
          btnConfirm.disabled = false;
          showMsg('ok', 'Delivery is available at this location (' + d.distance_km.toFixed(2) +
                        ' km from the restaurant). Click "Confirm This Location" to continue.');
        } else {
          showMsg('bad', d.error || 'Delivery is not available at your selected location.');
        }
      })
      .catch(function (err) {
        if (myReq !== reqId) return;
        distText.textContent = '—';
        showMsg('bad', err.message || 'Could not check the location. Please try again.');
      });
  }

  map.on('click', function (e) { setPoint(e.latlng.lat, e.latlng.lng, false); });

  btnConfirm.addEventListener('click', function () {
    if (!currentPoint) return;
    hidLat.value = currentPoint.lat;
    hidLng.value = currentPoint.lng;
    btnConfirm.disabled = true;
    showMsg('ok', '✔ Delivery location confirmed (' + distText.textContent +
                  '). You can now place your order.');
  });

  // ---- "Use My Current Location" (browser GPS) ----
  btnGps.addEventListener('click', function () {
    if (!navigator.geolocation) {
      showMsg('bad', 'Your browser does not support location detection. Please click on the map instead.');
      return;
    }
    btnGps.disabled = true;
    showMsg('info', 'Detecting your location… please allow location access if your browser asks.');

    navigator.geolocation.getCurrentPosition(function (pos) {
      btnGps.disabled = false;
      setPoint(pos.coords.latitude, pos.coords.longitude, true);
    }, function (err) {
      btnGps.disabled = false;
      var text = 'Could not get your location. Please click on the map instead.';
      if (err.code === 1) text = 'Location permission was denied. Please allow it in your browser, or click on the map to choose your location.';
      if (err.code === 2) text = 'Your position is unavailable right now. Please click on the map to choose your location.';
      if (err.code === 3) text = 'Detecting your location took too long. Please try again or click on the map.';
      showMsg('bad', text);
    }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
  });
})();
</script>
</body>
</html>
