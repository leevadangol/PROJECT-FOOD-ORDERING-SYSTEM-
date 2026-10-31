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
require_once "../location_helper.php";   // restaurant position for the admin map

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
                       o.delivery_lat, o.delivery_lng, o.delivery_distance_km,
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

// Restaurant position (for the "View Location on Map" popup)
$restaurant = get_restaurant_location($conn);

// Tab badge counts
$active_count  = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS c FROM orders WHERE status IN ('Pending','Confirmed','Accepted','Ready')"))['c'];
$history_count = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS c FROM orders WHERE status IN ('Completed','Cancelled')"))['c'];
?>

<!-- Step 3: Page content (safe to output HTML now) -->
<link rel="stylesheet" href="../leaflet/leaflet.css">
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
                    <th>Distance / Map</th>
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
                    <td style="font-size:12px; white-space:nowrap;">
                        <?php if ($order['delivery_lat'] !== null && $order['delivery_lng'] !== null): ?>
                            <strong><?php echo number_format((float)$order['delivery_distance_km'], 2); ?> km</strong>
                            <div style="color:#888; font-size:11px;">straight line</div>
                            <button type="button" class="view-loc-btn"
                                    data-order="<?php echo (int)$order['o_id']; ?>"
                                    data-lat="<?php echo htmlspecialchars($order['delivery_lat']); ?>"
                                    data-lng="<?php echo htmlspecialchars($order['delivery_lng']); ?>"
                                    data-dist="<?php echo htmlspecialchars($order['delivery_distance_km']); ?>"
                                    style="margin-top:4px; padding:5px 9px; background:#2c7be5; color:#fff;
                                           border:none; border-radius:4px; font-size:12px; cursor:pointer;">
                                &#128205; View Location on Map
                            </button>
                        <?php else: ?>
                            <span style="color:#ccc;">—</span>
                        <?php endif; ?>
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
                    <td colspan="13" style="text-align:center; padding:30px; color:#888;">
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

<!-- ===== MAP POPUP: customer's delivery location ===== -->
<div id="locModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.55);
                          z-index:9999; align-items:center; justify-content:center; padding:15px;">
    <div style="background:#fff; border-radius:10px; width:100%; max-width:640px; padding:16px;
                box-shadow:0 8px 30px rgba(0,0,0,.3);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <h3 id="locTitle" style="margin:0; font-size:17px; color:#f25d07;">Delivery Location</h3>
            <button type="button" id="locClose"
                    style="border:none; background:#eee; border-radius:4px; font-size:18px;
                           width:32px; height:32px; cursor:pointer;">&times;</button>
        </div>
        <div id="locInfo" style="font-size:13px; color:#555; margin-bottom:8px;"></div>
        <div id="locMsg" style="display:none; background:#f8d7da; color:#721c24; padding:8px 12px;
                                border-radius:5px; font-size:13px; margin-bottom:8px;"></div>
        <div id="adminMap" style="height:380px; width:100%; border-radius:8px; border:1px solid #ccc;"></div>
        <p style="font-size:12px; color:#888; margin:8px 0 0;">
            Distance is the Haversine <strong>straight-line</strong> distance, not the road distance.
            Orange dot = restaurant, blue pin = customer.
        </p>
    </div>
</div>

<script src="../leaflet/leaflet.js"></script>
<script>
(function () {
    var RESTAURANT = <?php echo $restaurant === null ? 'null' : json_encode($restaurant); ?>;
    var modal = document.getElementById('locModal');
    var msg   = document.getElementById('locMsg');
    var map = null, layer = null;

    function showErr(t) { msg.textContent = t; msg.style.display = 'block'; }

    function closeModal() { modal.style.display = 'none'; }
    document.getElementById('locClose').addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    document.querySelectorAll('.view-loc-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var lat  = parseFloat(btn.dataset.lat);
            var lng  = parseFloat(btn.dataset.lng);
            var dist = parseFloat(btn.dataset.dist);

            msg.style.display = 'none';
            document.getElementById('locTitle').textContent = 'Order #' + btn.dataset.order + ' - Delivery Location';
            document.getElementById('locInfo').innerHTML =
                'Lat: <strong>' + lat.toFixed(6) + '</strong> &nbsp; Lng: <strong>' + lng.toFixed(6) +
                '</strong> &nbsp; Distance: <strong>' + dist.toFixed(2) + ' km</strong> (straight line)';
            modal.style.display = 'flex';

            if (typeof L === 'undefined') {
                showErr('The map library could not be loaded (is the "leaflet" folder in the project root?).');
                return;
            }
            if (isNaN(lat) || isNaN(lng)) { showErr('This order has an invalid saved location.'); return; }

            L.Icon.Default.imagePath = '../leaflet/images/';
            if (!map) {
                map = L.map('adminMap');
                var tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);
                tiles.on('tileerror', function () {
                    showErr('Map tiles could not be loaded (check the internet connection). Coordinates are shown above.');
                });
                layer = L.layerGroup().addTo(map);
            }
            layer.clearLayers();

            var cust = L.marker([lat, lng]).addTo(layer).bindPopup('Customer delivery point').openPopup();
            var points = [[lat, lng]];

            if (RESTAURANT) {
                L.circleMarker([RESTAURANT.lat, RESTAURANT.lng], {
                    radius: 9, color: '#fff', weight: 2, fillColor: '#f25d07', fillOpacity: 1
                }).addTo(layer).bindTooltip('Restaurant');
                L.circle([RESTAURANT.lat, RESTAURANT.lng], {
                    radius: RESTAURANT.radius_km * 1000, color: '#f25d07', weight: 1, fillOpacity: 0.05
                }).addTo(layer);
                L.polyline([[RESTAURANT.lat, RESTAURANT.lng], [lat, lng]],
                           { color: '#2c7be5', dashArray: '6 6', weight: 2 }).addTo(layer);
                points.push([RESTAURANT.lat, RESTAURANT.lng]);
            }

            // Map is inside a hidden box until now - tell Leaflet its real size
            setTimeout(function () {
                map.invalidateSize();
                if (points.length > 1) map.fitBounds(points, { padding: [40, 40], maxZoom: 16 });
                else map.setView([lat, lng], 15);
            }, 50);
        });
    });
})();
</script>
</body>
</html>
