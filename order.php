<?php
/*
    ORDER PAGE (order.php)
    SIMPLIFIED - only shows food + quantity.
    Customer details (name, table, phone, datetime) are now
    collected on myorder.php before payment, not here.
*/
include "header.php";
require_once "check_login.php";
include "db.php";

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: home.php");
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT * FROM foods WHERE f_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 1) {
    $food = mysqli_fetch_assoc($result);
} else {
    die("<p style='text-align:center;margin-top:50px;font-family:Arial;'>
         Food item not found. <a href='home.php'>Go back home</a></p>");
}

$foodName  = $food['f_name'];
$foodPrice = $food['price'];
$foodImg   = $food['image'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order - <?php echo htmlspecialchars($foodName); ?></title>
  <link rel="stylesheet" href="css/order_style.css" />
</head>

<body>
  <section class="food-search">
    <div class="container search-box search-container">

      <!-- Success / Error messages -->
      <?php if (isset($_GET['success'])): ?>
        <p style="color:green; text-align:center; font-size:15px; margin-bottom:10px;">
          &#10004; <?php echo htmlspecialchars($_GET['success']); ?>
          — <a href="myorder.php">View My Orders</a>
        </p>
      <?php endif; ?>
      <?php if (isset($_GET['error'])): ?>
        <p style="color:red; text-align:center; font-size:15px; margin-bottom:10px;">
          <?php echo htmlspecialchars($_GET['error']); ?>
        </p>
      <?php endif; ?>

      <h2 class="text-center">Select Quantity &amp; Add to Order</h2>

      <form action="process_order.php" method="POST" class="order">
        <fieldset>
          <legend>Selected Food</legend>

          <div class="food-menu-img">
            <img src="<?php echo htmlspecialchars($foodImg); ?>"
                 class="img-responsive img-curve"
                 alt="<?php echo htmlspecialchars($foodName); ?>">
          </div>

          <div class="food-menu-desc">
            <h3><?php echo htmlspecialchars($foodName); ?></h3>
            <p class="food-price">Rs. <?php echo htmlspecialchars($foodPrice); ?></p>

            <input type="hidden" name="food_id" value="<?php echo $id; ?>">

            <div class="order-label">Quantity</div>
            <input type="number" name="qty" id="qtyInput"
                   value="1" min="1" class="input-responsive" required>
          </div>
        </fieldset>

        <input type="submit" name="submit" value="ADD TO ORDER"
               class="btn btn-primary"
               style="margin-top:15px; width:100%; padding:12px; font-size:16px;" />
      </form>

      <p style="text-align:center; color:#888; font-size:13px; margin-top:10px;">
        &#9432; You can fill in your name, table number and phone on the <a href="myorder.php">My Orders</a> page before payment.
      </p>

    </div>
  </section>

  <?php include "footer.php"; ?>

  <script>
    const qtyInput    = document.getElementById('qtyInput');
    const price       = <?php echo $foodPrice; ?>;
    const priceDisplay = document.querySelector('.food-price');

    qtyInput.addEventListener('input', function() {
        const total = qtyInput.value * price;
        priceDisplay.textContent = 'Rs. ' + total.toFixed(2);
    });
  </script>
</body>
</html>
