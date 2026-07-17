<?php
/*
    DELETE ITEM (delete_item.php)
    FIXES:
    - No session check: anyone could delete any order by typing
      the URL. Now checks the customer is logged in.
    - SQL injection: $o_id was pasted straight into SQL.
      Now uses intval() + prepared statements.
    - No ownership check: a customer could delete another
      customer's order. Now verifies c_id matches the session.
*/
session_start();
require_once "db.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['o_id'])) {
    header("Location: myorder.php");
    exit();
}

$o_id        = intval($_GET['o_id']);
$customer_id = $_SESSION['customer_id'];

// Ownership check: make sure this order belongs to the logged-in customer
$check = mysqli_prepare($conn, "SELECT o_id FROM orders WHERE o_id = ? AND c_id = ?");
mysqli_stmt_bind_param($check, "ii", $o_id, $customer_id);
mysqli_stmt_execute($check);
$check_result = mysqli_stmt_get_result($check);

if (mysqli_num_rows($check_result) === 0) {
    // Order not found or doesn't belong to this customer
    header("Location: myorder.php?error=Order+not+found");
    exit();
}

// Delete order_items first (foreign key requires this order)
$stmt1 = mysqli_prepare($conn, "DELETE FROM order_items WHERE o_id = ?");
mysqli_stmt_bind_param($stmt1, "i", $o_id);
mysqli_stmt_execute($stmt1);

// Now delete the order itself
$stmt2 = mysqli_prepare($conn, "DELETE FROM orders WHERE o_id = ?");
mysqli_stmt_bind_param($stmt2, "i", $o_id);
mysqli_stmt_execute($stmt2);

header("Location: myorder.php?success=Order+deleted+successfully");
exit();
