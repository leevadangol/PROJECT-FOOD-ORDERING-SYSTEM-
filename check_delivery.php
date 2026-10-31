<?php
/*
    CHECK DELIVERY (check_delivery.php)  -- NEW FILE
    Small JSON endpoint called by the map on myorder.php every time the
    customer picks / moves / confirms a point. The distance is calculated
    here in PHP (Haversine), so the browser never decides by itself.

    This is only for showing the customer instant feedback. The real,
    final check is repeated in update_order_details.php when the order
    is submitted.
*/
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "db.php";
require_once "location_helper.php";

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Please log in again.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Invalid request.']);
    exit();
}

$check = check_delivery_location($conn, $_POST['lat'] ?? '', $_POST['lng'] ?? '');

echo json_encode($check);
