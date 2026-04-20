<?php
include "header.php";
require_once "check_login.php";
require_once "db.php";

$customer_id = $_SESSION['customer_id'];

if (isset($_POST['update'])) {
    // Sanitize inputs
    $o_id = mysqli_real_escape_string($conn, $_POST['o_id']);
    $food_id = mysqli_real_escape_string($conn, $_POST['food_id']);
    $qty = mysqli_real_escape_string($conn, $_POST['qty']);
    $unit_price = mysqli_real_escape_string($conn, $_POST['price']);
    $c_name = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $table_no = mysqli_real_escape_string($conn, trim($_POST['tableno']));
    $contact = mysqli_real_escape_string($conn, trim($_POST['contact']));
    $datetime = mysqli_real_escape_string($conn, $_POST['datetime']);

    // Calculate totals
    $total_price = $unit_price * $qty;

    $check_sql = "SELECT o_id FROM orders WHERE o_id = '$o_id' AND c_id = '$customer_id'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) == 0) {
        die("You don't have permission to edit this order.");
    }

    // Update database
    $update_order = "UPDATE orders SET 
                    c_name = '$c_name',
                    table_no = '$table_no',
                    contact = '$contact',
                    o_datetime = '$datetime',
                    total_price = '$total_price'
                    WHERE o_id = '$o_id'";

    $update_item = "UPDATE order_items SET 
                   f_id = '$food_id',
                   quantity = '$qty',
                   price = '$total_price'
                   WHERE o_id = '$o_id'";

    if (mysqli_query($conn, $update_order) && mysqli_query($conn, $update_item)) {
        header("Location: myorder.php?success=Order+updated+successfully");
        exit();
    } else {
        $error = "Update failed: " . mysqli_error($conn);
    }
}

if (isset($_GET['id'])) {
    $o_id = mysqli_real_escape_string($conn, $_GET['id']);

    // order data with food details
    $order_sql = "SELECT o.*, oi.f_id, oi.quantity, f.f_name, f.price as unit_price, f.image
                  FROM orders o
                  JOIN order_items oi ON o.o_id = oi.o_id
                  JOIN foods f ON oi.f_id = f.f_id
                  WHERE o.o_id = '$o_id' AND o.c_id = '$customer_id'";

    $order_result = mysqli_query($conn, $order_sql);

    if (!$order_result || mysqli_num_rows($order_result) == 0) {
        die("Order not found or you don't have permission to edit it.");
    }

    $order = mysqli_fetch_assoc($order_result);

    // Set variables for form
    $food_id = $order['f_id'];
    $foodName = $order['f_name'];
    $foodPrice = $order['unit_price'];
    $foodImg = $order['image'];
    $qty = $order['quantity'];
    $c_name = $order['c_name'];
    $table_no = $order['table_no'];
    $contact = $order['contact'];
    $datetime = date('Y-m-d\TH:i', strtotime($order['o_datetime']));
} else {
    die("No order ID provided.");
}
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

    <!-- fOOD SEARCH   -->
    <section class="food-search">
        <div class="container search-box search-container">
            <h2 class="text-center">Update Your Order #<?php echo $o_id; ?></h2>

            <?php if (isset($error)): ?>
                <div style="color: red; background: #ffe6e6; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="change_item.php" method="POST" class="order">
                <input type="hidden" name="o_id" value="<?php echo $o_id; ?>">

                <fieldset>
                    <legend>Selected Food</legend>

                    <div class="food-menu-img">
                        <img src="<?php echo $foodImg; ?>" class="img-responsive img-curve">
                    </div>

                    <div class="food-menu-desc">
                        <h3><?php echo $foodName; ?></h3>
                        <p class="food-price">Unit Price: Rs. <span id="unitPrice"><?php echo $foodPrice; ?></span></p>
                        <p class="food-price">Total: Rs. <span id="totalPrice"><?php echo $foodPrice * $qty; ?></span></p>

                        <input type="hidden" name="food_id" value="<?php echo $food_id; ?>">
                        <input type="hidden" name="price" id="priceInput" value="<?php echo $foodPrice; ?>">

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

                <fieldset>
                    <legend>Customer Details</legend>
                    <div class="order-label">Full Name</div>
                    <input
                        type="text"
                        name="full_name"
                        value="<?php echo htmlspecialchars($c_name); ?>"
                        class="input-responsive"
                        required />

                    <div class="order-label">Table Number</div>
                    <input
                        type="text"
                        name="tableno"
                        value="<?php echo htmlspecialchars($table_no); ?>"
                        class="input-responsive"
                        required>

                    <div class="order-label">Date & Time</div>
                    <input
                        type="datetime-local"
                        name="datetime"
                        value="<?php echo $datetime; ?>"
                        class="input-responsive">

                    <div class="order-label">Phone Number</div>
                    <input
                        type="tel"
                        name="contact"
                        value="<?php echo htmlspecialchars($contact); ?>"
                        class="input-responsive"
                        required />

                    <div style="margin-top: 20px;">
                        <input
                            type="submit"
                            name="update"
                            value="UPDATE ORDER"
                            class="btn btn-primary" />
                        <a href="myorder.php" style="margin-left: 20px; color: #f25d07; text-decoration: none;">Cancel</a>
                    </div>
                </fieldset>
            </form>
        </div>
    </section>
    <!-- fOOD SEARCH  Ends -->

    <!-- social -->
    <section class="social">
        <div class="container text-center">
            <ul>
                <li>
                    <a href="#"><img
                            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAABi0lEQVR4nN2VO0sDURCFv0SSRv0fktQSUVBBsZQoIdjZGdT4qK1Fg521pY/SvyIqvqJ29iKKsUpk4CwMce9uktIDF5I5s3OWc2dm4b8jD1SBC+AR+NKx3+fiLGcgLAOvQCflvABL/RTOAkeuwDWwDRSAYZ0isAPcuLyGnk1FVPwHqKU8ZNy6ciORVFui4jOBnFGgBCwA44rNOpFyqHjeeV5LeIFPZ8u74zYUewZycQ9XnedxtowAH674G3Dl+CHgVlwlTuBCpF1oHEqu+H4g5lf8WRz5JNK6JQ4VJzAdyCmKtzn5g8hbsyIOK05gIqEBOqoVFLA+9zhOGLK7gIDdVdCisT4ELrtyC0kWnYvc7IpPAmvAiSt8qNhUV+6W+NOkNrVWywxwB1lZFmzTvBZXR0PTr0BdXDM0aGgrWtI3MNeHwDzQAtrAIiloOJG6sytOIKPBbCl+QA/IOpGOfLXVvOdiq5raB/1vq3hP6zpCWYsr7YPT7MWWEHLqCNst9rY2jHbu1YrGBS/0f+AXXvmgrmqaWLUAAAAASUVORK5CYII="
                            alt="facebook-new" /></a>
                </li>
                <a href="#"><img
                        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAA5klEQVR4nO1TQQ6CQAycbwBPEr6jeJb4KyDR+CCFi6c1TUqCa7ct4JFJetgw7XRnFmDHSlQAegAjgGAUcToApXf41TE0JKrxbE7EN4AjgNyxEHFO3BOsm/RMogYvHgDuAGrubTXywKRcyWSMPKfhN+6h3pcmMHnpzaRx9KuESshE83yxQK9kInm+WGDgcyZwM8Hz1QK5wC3423OLQMdnsiPG+R8WlbOQa75JwcOnkA9bBMBPMfVML/iGKZDyvGQrBq422nyeifqjaZ5bkDL5geS5BS0TEZrnwag4kyTmngejUpnsgAsfOPmncZue63wAAAAASUVORK5CYII="
                        alt="instagram-new--v1" /></a>

                <a href="#"><img
                        src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAABg0lEQVR4nO2UPyiFYRTGfy75vxCSoliUJGSyMfgzSMnCYGExKAObhTK6m8FgUCQ3g7LJgJKiFCmDRQgpCYl7de/VV4/6+rrf33vH+9Tb953nPec5ve97zoEsfKAHSADJFGvDJbYf+AXiwJCT47JNgiQwYhPTBnzKZ9btFIXAlZzD4iZkvwF1Fv8a4E77q3hEOxDVdfWJ25bIPhASVwycij8A8vGBOQU+AOVAJfAkblpJdmRfA2X4RC5wLIFNcQOyv4Et/b8ADQREPfAuoVFxK6ZHNxJ1kibGLQ9cCtyIWydDiEjwSFfXAcRU812ZSFABPCrJjLh52fdBHtiKEuBCgj9AC5BnKtG1dMRDpnKM6nsJFACNwJe44aAJwhJ4BVpV84a9pP1JUxHU+hX/HxExoNvS5XFxOcCu/PZke0KvpmMCGHPp8irgWdyUF/EmHdkIWLB5l0PtGzPKwKCp+ZqdxKuBWzlHHI5sdPmHzVg/txt8RcCJnM40Lb10eaq16BiZBSb8AUO2gvHJkrncAAAAAElFTkSuQmCC"
                        alt="twitterx--v2" /></a>
            </ul>
        </div>
    </section>
    <!-- social  Ends  -->

    <!-- footer -->
    <section class="footer">
        <div class="container text-center">
            <p>All rights reserved. Designed By <a href="#">Leeva and Leeza</a></p>
        </div>
    </section>
    <!-- footer  Ends  -->
</body>

<!-- JAVASCRIPT for changing the total price according to the quantity -->
<script>
    const qtyInput = document.getElementById('qtyInput');
    const unitPrice = <?php echo $foodPrice; ?>;
    const unitPriceSpan = document.getElementById('unitPrice');
    const totalPriceSpan = document.getElementById('totalPrice');
    const priceInput = document.getElementById('priceInput');

    function updateTotalPrice() {
        const qty = parseInt(qtyInput.value) || 1;
        const total = qty * unitPrice;
        totalPriceSpan.textContent = total.toFixed(2);
        unitPriceSpan.textContent = unitPrice.toFixed(2);
    }

    qtyInput.addEventListener('input', updateTotalPrice);

    // Initial calculation
    updateTotalPrice();
</script>

</html>