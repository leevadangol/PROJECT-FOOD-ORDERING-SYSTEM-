<!-- 
CREATE DATABASE IF NOT EXISTS myorders;
USE myorders;

CREATE TABLE IF NOT EXISTS myorders (
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    quantity INT(11) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
);
 -->



<?php
require_once "db.php";

/* Validate request */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

/* Validate input */
if (
    !isset($_POST['food_id[]'], $_POST['price[]'], $_POST['quantity'])
) {
    die("Missing order data");
}

$food_id = $_POST['food_id[]'];
$prices = $_POST['price[]'];
$quantities = $_POST['quantity'];

/* Prepare SQL */
$stmt = $conn->prepare(
    "INSERT INTO order_items (f_id, quantity, price)
     VALUES (?, ?, ?, ?)"
);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("sdid", $food_id, $price, $qty, $total);

/* Insert each item */
for ($i = 0; $i < count($foods); $i++) {
    $food = $foods[$i];
    $price = (float)$prices[$i];
    $qty = (int)$quantities[$i];

    if ($qty < 1) continue;

    $total = $price * $qty;
    $stmt->execute();
}

$stmt->close();
$conn->close();

/* Redirect after success */
header("Location: order.php?success=1");
exit;
