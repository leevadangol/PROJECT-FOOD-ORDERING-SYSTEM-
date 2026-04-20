<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php?error=Please%20login%20to%20place%20order");
    exit();
}

require_once "db.php";

// Get customer info from SESSION (not from form!)
$c_id = $_SESSION['customer_id'];
$c_name = $_SESSION['username'];


// --------------------test------------------



// <?php
// include "db.php";


if (isset($_POST['submit'])) {

    $food_id    = $_POST['food_id'];
    $qty        = $_POST['qty'];
    $price      = $_POST['price'];
    $c_name = trim($_POST['full_name'] ?? '');
    $table_no = trim($_POST['tableno'] ?? '');
    $datetime   = $_POST['datetime'] ?: date("Y-m-d H:i:s"); // default current time
    $contact    = $_POST['contact'];


    $total = $price * $qty;

    $orderSql = "INSERT INTO orders (c_id, c_name, table_no, contact, o_datetime, total_price) 
                 VALUES ('$c_id', '$c_name', '$table_no', '$contact', '$datetime', '$total')";

    $orderSuccess = mysqli_query($conn, $orderSql);

    if ($orderSuccess) {
        $order_id = mysqli_insert_id($conn);

        $itemSql = "INSERT INTO order_items (o_id, f_id, quantity, price)
                VALUES ('$order_id', '$food_id', '$qty', '$total')";


        $itemSuccess = mysqli_query($conn, $itemSql);

        if ($itemSuccess) {
            header("Location: order.php?id=" . $food_id . "&success=Order+added+successfully");
            exit();
        } else {
            $error = mysqli_error($conn);
            header("Location: order.php?error=Order+item+failed:+$error");
            exit();
        }
    } else {
        $error = mysqli_error($conn);
        header("Location: order.php?error=Order+failed:+$error");
        exit();
    }

    mysqli_close($conn);
}
