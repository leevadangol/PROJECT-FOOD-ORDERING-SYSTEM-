<?php
/*
    CONFIRM ORDER (confirm_order.php)
    FIX: Was pasting $customer_id directly into the SQL string.
    Now uses a prepared statement.
*/
require_once "check_login.php";
require_once "db.php";

$customer_id = $_SESSION['customer_id'];

$stmt = mysqli_prepare($conn,
    "UPDATE orders SET status = 'Confirmed' WHERE c_id = ? AND status = 'Pending'"
);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    $_SESSION['order_confirmed'] = true;
} else {
    $_SESSION['order_confirmed'] = false;
}

header("Location: myorder.php");
exit();
