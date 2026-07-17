<?php
/*
    FRIED CHICKEN CATEGORY PAGE (fried_chicken.php)
    Now DYNAMIC - reads all FRIED CHICKEN items from the database.
    Any food added via the admin panel with category="Fried Chicken"
    will automatically appear here.
*/
include "searchbox.php";
require_once "check_login.php";
require_once "db.php";

$sql    = "SELECT * FROM foods WHERE category = 'Fried Chicken' ORDER BY f_id ASC";
$result = mysqli_query($conn, $sql);
?>

<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FRIED CHICKEN</title>
  <link rel="stylesheet" href="CSS/food-menu_style.css" />
  <link rel="stylesheet" href="CSS/home_style.css" />
</head>
<body>
  <section class="food-menu">
    <div class="container">
      <h2 class="text-center">FRIED CHICKEN</h2>

      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($food = mysqli_fetch_assoc($result)): ?>
          <div class="food-menu-box zoom-box">
            <div class="food-menu-img">
              <img
                src="<?php echo htmlspecialchars($food['image']); ?>"
                alt="<?php echo htmlspecialchars($food['f_name']); ?>"
                class="img-responsive" />
            </div>
            <div class="food-menu-disc">
              <h4><?php echo htmlspecialchars($food['f_name']); ?>&nbsp;&nbsp;
                <span class="food-price">Rs. <?php echo htmlspecialchars($food['price']); ?></span>
              </h4>
              <?php if (!empty($food['description'])): ?>
                <p class="food-detail"><?php echo htmlspecialchars($food['description']); ?></p>
              <?php endif; ?>
              <br />
              <a href="order.php?id=<?php echo (int)$food['f_id']; ?>"
                 class="btn btn-primary btn-order">ORDER</a>
            </div>
            <div class="clear-fix"></div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="text-align:center; color:#888; padding:40px;">
          No FRIED CHICKEN items available at the moment.
        </p>
      <?php endif; ?>

      <div class="clear-fix"></div>
    </div>
  </section>

  <?php include "footer.php"; ?>
</body>
</html>
