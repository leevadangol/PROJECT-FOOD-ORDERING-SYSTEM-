<?php
/*
    UPDATE ORDER (change_item.php)
    SIMPLIFIED - only updates quantity, same style as order.php.
    Customer details are managed on myorder.php before payment.
*/
session_start();
require_once "db.php";
require_once "check_login.php";

$customer_id = $_SESSION['customer_id'];

// ---- HANDLE UPDATE (quantity only) ----
if (isset($_POST['update'])) {
    $o_id    = intval($_POST['o_id']);
    $food_id = intval($_POST['food_id']);
    $qty     = intval($_POST['qty']);

    // Fetch real price from DB (never trust form fields)
    $price_stmt = mysqli_prepare($conn, "SELECT price FROM foods WHERE f_id = ?");
    mysqli_stmt_bind_param($price_stmt, "i", $food_id);
    mysqli_stmt_execute($price_stmt);
    $price_row  = mysqli_fetch_assoc(mysqli_stmt_get_result($price_stmt));
    $unit_price = $price_row['price'];
    $total_price = $unit_price * $qty;

    // Ownership check
    $check = mysqli_prepare($conn, "SELECT o_id FROM orders WHERE o_id = ? AND c_id = ?");
    mysqli_stmt_bind_param($check, "ii", $o_id, $customer_id);
    mysqli_stmt_execute($check);
    if (mysqli_num_rows(mysqli_stmt_get_result($check)) == 0) {
        die("You don't have permission to edit this order.");
    }

    // Update orders table (price only, keep other details as-is)
    $stmt1 = mysqli_prepare($conn, "UPDATE orders SET total_price = ? WHERE o_id = ?");
    mysqli_stmt_bind_param($stmt1, "di", $total_price, $o_id);
    mysqli_stmt_execute($stmt1);

    // Update order_items table
    $stmt2 = mysqli_prepare($conn,
        "UPDATE order_items SET quantity = ?, price = ? WHERE o_id = ?");
    mysqli_stmt_bind_param($stmt2, "idi", $qty, $total_price, $o_id);
    mysqli_stmt_execute($stmt2);

    header("Location: myorder.php?success=Order+updated+successfully");
    exit();
}

// ---- LOAD ORDER DATA ----
if (!isset($_GET['id'])) {
    header("Location: myorder.php");
    exit();
}

$o_id = intval($_GET['id']);

$stmt = mysqli_prepare($conn,
    "SELECT o.*, oi.f_id, oi.quantity, f.f_name, f.price AS unit_price, f.image
     FROM orders o
     JOIN order_items oi ON o.o_id = oi.o_id
     JOIN foods f ON oi.f_id = f.f_id
     WHERE o.o_id = ? AND o.c_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $o_id, $customer_id);
mysqli_stmt_execute($stmt);
$order_result = mysqli_stmt_get_result($stmt);

if (!$order_result || mysqli_num_rows($order_result) == 0) {
    die("Order not found.");
}

$order     = mysqli_fetch_assoc($order_result);
$food_id   = $order['f_id'];
$foodName  = $order['f_name'];
$foodPrice = $order['unit_price'];
$foodImg   = $order['image'];
$qty       = $order['quantity'];

include "header.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Update Order</title>
    <link rel="stylesheet" href="css/order_style.css" />
</head>
<body>
    <section class="food-search">
        <div class="container search-box search-container">

            <h2 class="text-center">Update Your Order #<?php echo $o_id; ?></h2>

            <form action="change_item.php" method="POST" class="order">
                <input type="hidden" name="o_id"     value="<?php echo $o_id; ?>">
                <input type="hidden" name="food_id"  value="<?php echo $food_id; ?>">

                <fieldset>
                    <legend>Selected Food</legend>

                    <div class="food-menu-img">
                        <img src="<?php echo htmlspecialchars($foodImg); ?>"
                             class="img-responsive img-curve"
                             alt="<?php echo htmlspecialchars($foodName); ?>">
                    </div>

                    <div class="food-menu-desc">
                        <h3><?php echo htmlspecialchars($foodName); ?></h3>
                        <p class="food-price">
                            Unit Price: Rs. <span id="unitPrice"><?php echo number_format($foodPrice,2); ?></span>
                        </p>
                        <p class="food-price">
                            Total: Rs. <span id="totalPrice"><?php echo number_format($foodPrice * $qty, 2); ?></span>
                        </p>

                        <div class="order-label">Quantity</div>
                        <input
                            type="number"
                            name="qty"
                            id="qtyInput"
                            value="<?php echo $qty; ?>"
                            min="1"
                            class="input-responsive"
                            required>
                    </div>
                </fieldset>

                <div style="display:flex; gap:10px; margin-top:15px;">
                    <input type="submit" name="update" value="UPDATE ORDER"
                           class="btn btn-primary"
                           style="flex:1; padding:12px; font-size:16px;" />
                    <a href="myorder.php"
                       style="flex:0.4; padding:12px; text-align:center; background:#ccc;
                              color:#333; border-radius:5px; text-decoration:none;
                              font-size:15px; display:block;">
                        Cancel
                    </a>
                </div>
            </form>

            <p style="text-align:center; color:#888; font-size:13px; margin-top:10px;">
                &#9432; Update quantity here. Name, table and address are managed on
                <a href="myorder.php">My Orders</a> before payment.
            </p>

        </div>
    </section>

    <?php include "footer.php"; ?>

    <script>
        const qtyInput    = document.getElementById('qtyInput');
        const unitPrice   = <?php echo $foodPrice; ?>;
        const totalSpan   = document.getElementById('totalPrice');
        const unitSpan    = document.getElementById('unitPrice');

        qtyInput.addEventListener('input', function () {
            const qty   = parseInt(qtyInput.value) || 1;
            const total = qty * unitPrice;
            totalSpan.textContent = total.toFixed(2);
            unitSpan.textContent  = unitPrice.toFixed(2);
        });
    </script>
</body>
</html>
