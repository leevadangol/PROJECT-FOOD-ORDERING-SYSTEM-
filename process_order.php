<?php
/*
    PROCESS ORDER (process_order.php)
    Saves the order with just food + quantity.
    Customer details (name, table, contact) are left blank
    and will be filled in on myorder.php before payment.
*/
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?error=Please+login+to+place+order");
    exit();
}

require_once "db.php";

$c_id = $_SESSION['customer_id'];

if (isset($_POST['submit'])) {

    $food_id = intval($_POST['food_id']);
    $qty     = intval($_POST['qty']);

    // Fetch real price from DB (never trust hidden form fields)
    $price_stmt = mysqli_prepare($conn, "SELECT price FROM foods WHERE f_id = ?");
    mysqli_stmt_bind_param($price_stmt, "i", $food_id);
    mysqli_stmt_execute($price_stmt);
    $price_result = mysqli_stmt_get_result($price_stmt);
    $food_row     = mysqli_fetch_assoc($price_result);

    if (!$food_row) {
        header("Location: home.php?error=Food+not+found");
        exit();
    }

    $unit_price = $food_row['price'];
    $total      = $unit_price * $qty;

    // Save order — customer details left blank (filled on myorder.php)
    $orderStmt = mysqli_prepare($conn,
        "INSERT INTO orders (c_id, c_name, table_no, contact, o_datetime, total_price)
         VALUES (?, '', '', '', NOW(), ?)");
    mysqli_stmt_bind_param($orderStmt, "id", $c_id, $total);
    $orderSuccess = mysqli_stmt_execute($orderStmt);

    if ($orderSuccess) {
        $order_id = mysqli_insert_id($conn);

        $itemStmt = mysqli_prepare($conn,
            "INSERT INTO order_items (o_id, f_id, quantity, price) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($itemStmt, "iiid", $order_id, $food_id, $qty, $total);
        mysqli_stmt_execute($itemStmt);

        header("Location: myorder.php?success=Order+added+successfully");
        exit();
    } else {
        header("Location: order.php?id=" . $food_id . "&error=Order+failed");
        exit();
    }
}
