<?php
include "searchbox.php";
require_once "check_login.php";
?>

<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>COLD DRINK</title>
  <link rel="stylesheet" href="CSS/food-menu_style.css" />
  <link rel="stylesheet" href="CSS/home_style.css" />
</head>

<body>

  <section class="food-menu">
    <div class="container">
      <h2 class="text-center">COLD DRINK</h2>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/pepsi.jpg"
            alt="pepsi"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>PEPSI</h4>
          <p class="food-price">Rs. 90</p>
          <p class="food-detail">
            A popular carbonated soft drink with a refreshing, 
            fizzy taste. Best enjoyed ice cold for a cool, flavorful experience.

          </p>
          <br />
          <a href="order.php?id=33" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/sprite.jpg"
            alt="sprite"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>SPRITE</h4>
          <p class="food-price">Rs. 90</p>
          <p class="food-detail">  
            Sprite is a lemon-lime flavored soda with a light, 
            crisp taste. It's a refreshing drink that quickly quenches your thirst.

          </p>
          <br />
          <a href="order.php?id=34" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/coca cola.jpg"
            alt="coca cola"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>COCA COLA</h4>
          <p class="food-price">Rs. 90</p>
          <p class="food-detail">
            This chilled cola offers a sweet, refreshing taste that complements any meal. 
            Its classic flavor makes it a perfect beverage choice for any occasion.

          </p>
          <br />
          <a href="order.php?id=35" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/fanta.jpg"
            alt="fanta"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>FANTA</h4>
          <p class="food-price">Rs. 90</p>
          <p class="food-detail">
            A sweet, orange-flavored drink that's fruity and refreshing. 
            Its a fun, flavorful option to brighten up any moment.

          </p>
          <br />
          <a href="order.php?id=36" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>
      <div class="clear-fix"></div>
    </div>
  </section>
</body>
</html>

<!-- ---footer--- -->
<?php
include "footer.php";
?>