
<?php
/*
    UPDATE ORDER DETAILS
    File: update_order_details.php

    Uses existing orders table columns:
    - c_name
    - contact
    - o_datetime
    - delivery_lat
    - delivery_lng
    - delivery_distance_km
    - payment_id
    - status

    No delivery_address or delivery_fee column needed.
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php";
require_once "location_helper.php";

// Check customer login
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = (int) $_SESSION['customer_id'];

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: myorder.php");
    exit();
}

// Redirect with an error message
function order_error($message) {
    header(
        "Location: myorder.php?error=" .
        urlencode($message)
    );
    exit();
}

// -----------------------------------------
// STEP 1: GET FORM VALUES
// -----------------------------------------

$full_name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');

$datetime = trim($_POST['datetime'] ?? '');

$payment_method = trim(
    $_POST['payment_method'] ?? 'esewa'
);

$delivery_location = trim(
    $_POST['delivery_location'] ?? 'inside'
);

// -----------------------------------------
// STEP 2: VALIDATE FORM
// -----------------------------------------

if ($full_name === '' || $phone === '') {
    order_error(
        "Please enter your full name and phone number."
    );
}

if (!in_array(
    $payment_method,
    ['esewa', 'cod'],
    true
)) {
    order_error("Invalid payment method.");
}

if (!in_array(
    $delivery_location,
    ['inside', 'outside'],
    true
)) {
    order_error("Invalid delivery location.");
}

// -----------------------------------------
// STEP 3: VALIDATE DATE AND TIME
// -----------------------------------------

if ($datetime === '') {

    $datetime = date("Y-m-d H:i:s");

} else {

    $date_check = DateTime::createFromFormat(
        '!Y-m-d\TH:i',
        $datetime
    );

    if (
        !$date_check ||
        $date_check->format('Y-m-d\TH:i') !== $datetime
    ) {
        order_error(
            "Please select a valid date and time."
        );
    }

    $datetime = $date_check->format(
        'Y-m-d H:i:s'
    );
}

// -----------------------------------------
// STEP 4: CALCULATE DELIVERY FEE
// -----------------------------------------

if ($delivery_location === 'outside') {
    $delivery_fee = 150.00;
} else {
    $delivery_fee = 100.00;
}

// -----------------------------------------
// STEP 5: VALIDATE MAP LOCATION
// -----------------------------------------

$loc = check_delivery_location(
    $conn,
    $_POST['delivery_lat'] ?? '',
    $_POST['delivery_lng'] ?? ''
);

if (!$loc['ok']) {

    $message = $loc['error'];

    if (isset(
        $loc['distance_km'],
        $loc['radius_km']
    )) {

        $message .=
            " Your location is " .
            number_format(
                $loc['distance_km'],
                2
            ) .
            " km away. Maximum delivery distance is " .
            number_format(
                $loc['radius_km'],
                2
            ) .
            " km.";
    }

    order_error($message);
}

// Verified map coordinates
$delivery_lat = (float) $loc['lat'];
$delivery_lng = (float) $loc['lng'];

$delivery_distance = (float) $loc['distance_km'];

// -----------------------------------------
// STEP 6: CHECK PENDING ORDERS
// -----------------------------------------

$total_sql = "
    SELECT
        COUNT(*) AS order_count,
        COALESCE(SUM(total_price), 0) AS food_total
    FROM orders
    WHERE c_id = ?
    AND status = 'Pending'
";

$total_stmt = mysqli_prepare(
    $conn,
    $total_sql
);

if (!$total_stmt) {
    die(
        "SQL Prepare Error: " .
        htmlspecialchars(mysqli_error($conn))
    );
}

mysqli_stmt_bind_param(
    $total_stmt,
    "i",
    $customer_id
);

if (!mysqli_stmt_execute($total_stmt)) {
    die(
        "SQL Execute Error: " .
        htmlspecialchars(mysqli_stmt_error($total_stmt))
    );
}

$total_result = mysqli_stmt_get_result(
    $total_stmt
);

$total_row = mysqli_fetch_assoc(
    $total_result
);

$order_count = (int) $total_row['order_count'];
$food_total = (float) $total_row['food_total'];

mysqli_stmt_close($total_stmt);

if ($order_count === 0 || $food_total <= 0) {
    order_error("No pending orders found.");
}

// -----------------------------------------
// STEP 7: UPDATE ORDER DETAILS
// -----------------------------------------

$sql = "
    UPDATE orders
    SET
        c_name = ?,
        contact = ?,
        o_datetime = ?,
        delivery_lat = ?,
        delivery_lng = ?,
        delivery_distance_km = ?
    WHERE c_id = ?
    AND status = 'Pending'
";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die(
        "SQL Prepare Error: " .
        htmlspecialchars(mysqli_error($conn))
    );
}

mysqli_stmt_bind_param(
    $stmt,
    "sssdddi",
    $full_name,
    $phone,
    $datetime,
    $delivery_lat,
    $delivery_lng,
    $delivery_distance,
    $customer_id
);

if (!mysqli_stmt_execute($stmt)) {
    die(
        "SQL Execute Error: " .
        htmlspecialchars(mysqli_stmt_error($stmt))
    );
}

mysqli_stmt_close($stmt);

// -----------------------------------------
// STEP 8: SAVE DELIVERY DETAILS IN SESSION
// -----------------------------------------

$_SESSION['delivery_fee'] = $delivery_fee;

$_SESSION['delivery_location'] = $delivery_location;

$_SESSION['delivery_lat'] = $delivery_lat;

$_SESSION['delivery_lng'] = $delivery_lng;

$_SESSION['delivery_distance_km'] =
    $delivery_distance;

// -----------------------------------------
// STEP 9: CASH ON DELIVERY
// -----------------------------------------

if ($payment_method === 'cod') {

    $cod_total = $food_total + $delivery_fee;

    $transaction_id =
        date("Ymd-His") .
        "-" .
        $customer_id .
        "-" .
        bin2hex(random_bytes(4)) .
        "-COD";

    mysqli_begin_transaction($conn);

    try {

        // Insert COD payment record
        $payment_sql = "
            INSERT INTO payments
            (
                c_id,
                transaction_id,
                payment_status,
                payment_method,
                amount,
                payment_date
            )
            VALUES
            (
                ?,
                ?,
                'Pending',
                'Cash on Delivery',
                ?,
                NOW()
            )
        ";

        $pay_stmt = mysqli_prepare(
            $conn,
            $payment_sql
        );

        if (!$pay_stmt) {
            throw new Exception(
                mysqli_error($conn)
            );
        }

        mysqli_stmt_bind_param(
            $pay_stmt,
            "isd",
            $customer_id,
            $transaction_id,
            $cod_total
        );

        if (!mysqli_stmt_execute($pay_stmt)) {
            throw new Exception(
                mysqli_stmt_error($pay_stmt)
            );
        }

        $payment_id = mysqli_insert_id($conn);

        mysqli_stmt_close($pay_stmt);

        // Confirm pending orders
        $confirm_sql = "
            UPDATE orders
            SET
                status = 'Confirmed',
                payment_id = ?
            WHERE c_id = ?
            AND status = 'Pending'
        ";

        $confirm_stmt = mysqli_prepare(
            $conn,
            $confirm_sql
        );

        if (!$confirm_stmt) {
            throw new Exception(
                mysqli_error($conn)
            );
        }

        mysqli_stmt_bind_param(
            $confirm_stmt,
            "ii",
            $payment_id,
            $customer_id
        );

        if (!mysqli_stmt_execute($confirm_stmt)) {
            throw new Exception(
                mysqli_stmt_error($confirm_stmt)
            );
        }

        mysqli_stmt_close($confirm_stmt);

        mysqli_commit($conn);

    } catch (Throwable $e) {

        mysqli_rollback($conn);

        error_log(
            "COD checkout error: " .
            $e->getMessage()
        );

        order_error(
            "Unable to confirm order. Please try again."
        );
    }

    // Success message
    $location_label =
        ($delivery_location === 'outside')
        ? 'Outside Ringroad'
        : 'Inside Ringroad';

    $success_message =
        "Order confirmed! " .
        "Food: Rs. " .
        number_format($food_total, 2) .
        " + Delivery (" .
        $location_label .
        "): Rs. " .
        number_format($delivery_fee, 2) .
        " = Total: Rs. " .
        number_format($cod_total, 2) .
        " (Pay on delivery)";

    header(
        "Location: myorder.php?success=" .
        urlencode($success_message)
    );

    exit();

}

// -----------------------------------------
// STEP 10: ESEWA PAYMENT
// -----------------------------------------

if ($payment_method === 'esewa') {

    header(
        "Location: initiate_payment.php"
    );

    exit();
}

// Fallback
header("Location: myorder.php");
exit();

?>
