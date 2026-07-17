<?php
/*
    UPDATE ORDER DETAILS (update_order_details.php)
    Saves customer name, phone, address for all Pending orders,
    then routes to eSewa OR confirms as Cash on Delivery.
*/
session_start();
require_once "db.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $full_name      = trim($_POST['full_name']      ?? '');
    $phone          = trim($_POST['phone']          ?? '');
    $street_no      = trim($_POST['street_no']      ?? '');
    $landmark       = trim($_POST['landmark']       ?? '');
    $house_no       = trim($_POST['house_no']       ?? '');
    $datetime       = !empty($_POST['datetime'])    ? $_POST['datetime'] : date("Y-m-d H:i:s");
    $payment_method = trim($_POST['payment_method'] ?? 'esewa');

    // Validation
    if (empty($full_name) || empty($phone) || empty($street_no) || empty($house_no)) {
        header("Location: myorder.php?error=" . urlencode("Please fill in all required fields."));
        exit();
    }

    $delivery_address = "Street: $street_no | Landmark: $landmark | House/Apt: $house_no";

    // Save address for future use if checkbox ticked
    if (isset($_POST['save_address']) && $_POST['save_address'] === '1') {
        $check_stmt = mysqli_prepare($conn,
            "SELECT address_id FROM saved_addresses WHERE c_id=? AND street_no=? AND house_no=?");
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

    // Update all Pending orders with name, phone, address, datetime
    $stmt = mysqli_prepare($conn,
        "UPDATE orders
         SET c_name = ?, contact = ?, delivery_address = ?, o_datetime = ?
         WHERE c_id = ? AND status = 'Pending'");
    mysqli_stmt_bind_param($stmt, "ssssi",
        $full_name, $phone, $delivery_address, $datetime, $customer_id);
    mysqli_stmt_execute($stmt);

    // Route based on payment method
    if ($payment_method === 'cod') {
        /*
            CASH ON DELIVERY:
            Confirm the orders directly without going to eSewa.
            Record a COD payment row so the admin can track it.
        */
        // Get total of pending orders
        $total_res = mysqli_query($conn,
            "SELECT COALESCE(SUM(total_price),0) AS t FROM orders
             WHERE c_id='$customer_id' AND status='Pending'");
        $cod_total = mysqli_fetch_assoc($total_res)['t'];

        // Insert a payment record for COD
        $uuid = date("Ymd-His") . "-" . $customer_id . "-COD";
        $pay_stmt = mysqli_prepare($conn,
            "INSERT INTO payments (c_id, transaction_id, payment_status, payment_method, amount, payment_date)
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

        header("Location: myorder.php?success=" .
               urlencode("Order confirmed! Pay Rs. " . number_format($cod_total,2) . " in cash on delivery."));
        exit();

    } else {
        // eSewa - proceed to payment
        header("Location: initiate_payment.php");
        exit();
    }
}

header("Location: myorder.php");
exit();
