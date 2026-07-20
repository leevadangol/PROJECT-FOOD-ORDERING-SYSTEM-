<?php
/*
    UPDATE ORDER DETAILS (update_order_details.php)
    Saves customer name, phone, address, delivery fee for all
    Pending orders, then routes to eSewa or Cash on Delivery.
*/
if (session_status() === PHP_SESSION_NONE) session_start();
require_once "db.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name         = trim($_POST['full_name']         ?? '');
    $phone             = trim($_POST['phone']             ?? '');
    $street_no         = trim($_POST['street_no']         ?? '');
    $landmark          = trim($_POST['landmark']          ?? '');
    $house_no          = trim($_POST['house_no']          ?? '');
    $datetime          = !empty($_POST['datetime'])       ? $_POST['datetime'] : date("Y-m-d H:i:s");
    $payment_method    = trim($_POST['payment_method']    ?? 'esewa');
    $delivery_location = trim($_POST['delivery_location'] ?? 'inside');

    // Delivery fee based on location
    $delivery_fee = ($delivery_location === 'outside') ? 150.00 : 100.00;

    // Validation
    if (empty($full_name) || empty($phone) || empty($street_no) || empty($house_no)) {
        header("Location: myorder.php?error=" . urlencode("Please fill in all required fields."));
        exit();
    }

    $delivery_address = "Street: $street_no | Landmark: $landmark | House/Apt: $house_no";

    // Save address for future use if checkbox ticked
    if (isset($_POST['save_address']) && $_POST['save_address'] === '1') {
        $check_stmt = mysqli_prepare($conn,
            "SELECT address_id FROM saved_addresses
             WHERE c_id=? AND street_no=? AND house_no=?");
        mysqli_stmt_bind_param($check_stmt, "iss", $customer_id, $street_no, $house_no);
        mysqli_stmt_execute($check_stmt);
        if (mysqli_num_rows(mysqli_stmt_get_result($check_stmt)) === 0) {
            $save_stmt = mysqli_prepare($conn,
                "INSERT INTO saved_addresses (c_id, full_name, phone, street_no, landmark, house_no)
                 VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($save_stmt, "isssss",
                $customer_id, $full_name, $phone, $street_no, $landmark, $house_no);
            mysqli_stmt_execute($save_stmt);
        }
    }

    // Update all Pending orders with name, phone, address, datetime, delivery fee
    $stmt = mysqli_prepare($conn,
        "UPDATE orders
         SET c_name = ?, contact = ?, delivery_address = ?,
             o_datetime = ?, delivery_fee = ?
         WHERE c_id = ? AND status = 'Pending'");
    mysqli_stmt_bind_param(
    $stmt,
    "ssssdi",
    $full_name,
    $phone,
    $delivery_address,
    $datetime,
    $delivery_fee,
    $customer_id
);
    mysqli_stmt_execute($stmt);

    // Save delivery fee in session for initiate_payment.php to use
    $_SESSION['delivery_fee']      = $delivery_fee;
    $_SESSION['delivery_location'] = $delivery_location;

    // Route based on payment method
    if ($payment_method === 'cod') {
        // Get food subtotal of pending orders
        $total_res = mysqli_query($conn,
            "SELECT COALESCE(SUM(total_price),0) AS t FROM orders
             WHERE c_id='$customer_id' AND status='Pending'");
        $food_total = mysqli_fetch_assoc($total_res)['t'];
        $cod_total  = $food_total + $delivery_fee;

        // Record COD payment
        $uuid = date("Ymd-His") . "-" . $customer_id . "-COD";
        $pay_stmt = mysqli_prepare($conn,
            "INSERT INTO payments
                 (c_id, transaction_id, payment_status, payment_method, amount, payment_date)
             VALUES (?, ?, 'Pending', 'Cash on Delivery', ?, NOW())");
        mysqli_stmt_bind_param($pay_stmt, "isd", $customer_id, $uuid, $cod_total);
        mysqli_stmt_execute($pay_stmt);
        $payment_id = mysqli_insert_id($conn);

        // Confirm all pending orders
        $confirm_stmt = mysqli_prepare($conn,
            "UPDATE orders SET status='Confirmed', payment_id=?
             WHERE c_id=? AND status='Pending'");
        mysqli_stmt_bind_param($confirm_stmt, "ii", $payment_id, $customer_id);
        mysqli_stmt_execute($confirm_stmt);

        $loc_label = $delivery_location === 'outside' ? 'Outside Ringroad' : 'Inside Ringroad';
        header("Location: myorder.php?success=" . urlencode(
            "Order confirmed! " .
            "Food: Rs. " . number_format($food_total, 2) .
            " + Delivery (" . $loc_label . "): Rs. " . number_format($delivery_fee, 2) .
            " = Total: Rs. " . number_format($cod_total, 2) . " (Pay on delivery)"
        ));
        exit();

    } else {
        // eSewa — proceed to payment (delivery fee added there)
        header("Location: initiate_payment.php");
        exit();
    }
}

header("Location: myorder.php");
exit();
