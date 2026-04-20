<?php
require_once "check_login.php";
require_once "db.php";

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

/*
  Update only pending orders
*/
$confirm_sql = "
    UPDATE orders 
    SET status = 'Confirmed' 
    WHERE c_id = '$customer_id' 
    AND status = 'Pending'
";

$result = mysqli_query($conn, $confirm_sql);

if ($result && mysqli_affected_rows($conn) > 0) {
    $_SESSION['order_confirmed'] = true;
} else {
    $_SESSION['order_confirmed'] = false;
}

header("Location: myorder.php");
exit;
?>
