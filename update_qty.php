<?php
/*
    UPDATE QUANTITY (update_qty.php)
    Called when the customer clicks + or - on myorder.php.
    Increases or decreases the quantity of a pending order by 1.
    If quantity reaches 0, the order is deleted automatically.
*/
if (session_status() === PHP_SESSION_NONE) session_start();
 
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}
 
require_once "db.php";
 
$customer_id = $_SESSION['customer_id'];
$o_id        = intval($_GET['o_id']   ?? 0);
$action      = $_GET['action'] ?? ''; // 'increase' or 'decrease'
 
if (!$o_id || !in_array($action, ['increase', 'decrease'])) {
    header("Location: myorder.php");
    exit();
}
 
// Ownership + status check — only allow changes on Pending orders
$check = mysqli_prepare($conn,
    "SELECT o.o_id, oi.quantity, oi.item_id, f.price
     FROM orders o
     JOIN order_items oi ON o.o_id = oi.o_id
     JOIN foods f ON oi.f_id = f.f_id
     WHERE o.o_id = ? AND o.c_id = ? AND o.status = 'Pending'");
mysqli_stmt_bind_param($check, "ii", $o_id, $customer_id);
mysqli_stmt_execute($check);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($check));
 
if (!$row) {
    header("Location: myorder.php?error=" . urlencode("Order not found or cannot be changed."));
    exit();
}
 
$current_qty = intval($row['quantity']);
$unit_price  = floatval($row['price']);
 
if ($action === 'increase') {
    $new_qty   = $current_qty + 1;
    $new_total = $unit_price * $new_qty;
 
    $stmt = mysqli_prepare($conn,
        "UPDATE order_items SET quantity=?, price=? WHERE o_id=?");
    mysqli_stmt_bind_param($stmt, "idi", $new_qty, $new_total, $o_id);
    mysqli_stmt_execute($stmt);
 
    $stmt2 = mysqli_prepare($conn,
        "UPDATE orders SET total_price=? WHERE o_id=?");
    mysqli_stmt_bind_param($stmt2, "di", $new_total, $o_id);
    mysqli_stmt_execute($stmt2);
 
} elseif ($action === 'decrease') {
    if ($current_qty <= 1) {
        // Quantity would hit 0 — delete the order entirely
        $d1 = mysqli_prepare($conn, "DELETE FROM order_items WHERE o_id=?");
        mysqli_stmt_bind_param($d1, "i", $o_id);
        mysqli_stmt_execute($d1);
 
        $d2 = mysqli_prepare($conn, "DELETE FROM orders WHERE o_id=?");
        mysqli_stmt_bind_param($d2, "i", $o_id);
        mysqli_stmt_execute($d2);
 
        header("Location: myorder.php?success=" . urlencode("Order removed."));
        exit();
    } else {
        $new_qty   = $current_qty - 1;
        $new_total = $unit_price * $new_qty;
 
        $stmt = mysqli_prepare($conn,
            "UPDATE order_items SET quantity=?, price=? WHERE o_id=?");
        mysqli_stmt_bind_param($stmt, "idi", $new_qty, $new_total, $o_id);
        mysqli_stmt_execute($stmt);
 
        $stmt2 = mysqli_prepare($conn,
            "UPDATE orders SET total_price=? WHERE o_id=?");
        mysqli_stmt_bind_param($stmt2, "di", $new_total, $o_id);
        mysqli_stmt_execute($stmt2);
    }
}
 
header("Location: myorder.php");
exit();