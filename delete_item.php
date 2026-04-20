<?php
require_once("db.php");

if (!isset($_GET['o_id'])) {
    die("Error: No id parameter provided.");
}
$o_id = $_GET['o_id'];
$result2 = mysqli_query($conn, "DELETE FROM order_items WHERE o_id = $o_id");
$result = mysqli_query($conn, "DELETE FROM orders WHERE o_id = $o_id");
if (!$result || !$result2) {
    die("Error: " . mysqli_error($conn));
}

if (mysqli_affected_rows($conn) == 0) {
    die("Error: No record found with id $o_id.");
}

header("Location:myorder.php");
exit();
