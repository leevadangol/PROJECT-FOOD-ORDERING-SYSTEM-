<?php
include "searchbox.php";
require_once "check_login.php";
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

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/crispy chicken.jpg"
            alt="crispy chicken"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>CLASSIC CRISPY CHICKEN</h4>
          <p class="food-price">Rs. 450</p>
          <p class="food-detail">
           Golden-brown chicken fried to crispy perfection.
           Juicy and tender inside with a crunchy coating.
           Served hot with ketchup or garlic dip.
          </p>
          <br />
          <a href="order.php?id=14" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/hot wings.jpg"
            alt="hot wings"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>SPICY HOT WINGS</h4>
          <p class="food-price">Rs. 350</p>
          <p class="food-detail">  
            Crispy wings tossed in bold spicy sauce.
            Perfect balance of heat and flavor.
            Best choice for spice lovers.          </p>
          <br />
          <a href="order.php?id=15" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/drumsticks.jpg"
            alt="drumsticks"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>CHICKEN DRUMSTICKS</h4>
          <p class="food-price">Rs. 300</p>
          <p class="food-detail">
            Thick and meaty drumsticks deep-fried fresh.
            Seasoned with special herbs and spices.
            Crispy outside and juicy inside.
          </p>
          <br />
          <a href="order.php?id=16" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/chicken popcorn.jpg"
            alt="chicken popcorn"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>CHICKEN POPCORN</h4>
          <p class="food-price">Rs. 320</p>
          <p class="food-detail">
            Bite-sized crispy chicken pieces.
            Light, crunchy and easy to share.
            Perfect snack for any time hunger.
          </p>
          <br />
          <a href="order.php?id=17" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/chicken strips.jpg"
            alt="chicken strips"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>CHICKEN STRIPS</h4>
          <p class="food-price">Rs. 360</p>
          <p class="food-detail">
            Boneless chicken strips coated in seasoned flour.
            Deep-fried until golden and crunchy.
            Served with special creamy dip.
          </p>
          <br />

          <a href="order.php?id=18" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/family bucket chicken.jpg"
            alt="family bucket"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>FAMILY BUCKET</h4>
          <p class="food-price">Rs. 950</p>
          <p class="food-detail">
            ALarge bucket of assorted crispy chicken.
            Perfect for family and group meals.
            Comes with multiple dipping sauces.
          </p>
          <br />
          <a href="order.php?id=19" class="btn btn-primary btn-order">ORDER</a>
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