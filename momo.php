<?php
include "searchbox.php";
require_once "check_login.php";
?>

<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MOMO</title>
  <link rel="stylesheet" href="CSS/food-menu_style.css" />
  <link rel="stylesheet" href="CSS/home_style.css" />
</head>

<body>

  <section class="food-menu">
    <div class="container">
      <h2 class="text-center">MOMO</h2>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/steamed momo.jpg"
            alt="steamed momo"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>STEAMED CHICKEN MOMO</h4>
          <p class="food-price">Rs. 180</p>
          <p class="food-detail">
            Soft dumplings filled with juicy chicken.
            Steamed fresh and served hot.
            Comes with spicy tomato achar.
          </p>
          <br />
          <a href="order.php?id=26" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/buff momo.jpg"
            alt="buff momo"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>BUFF MOMO</h4>
          <p class="food-price">Rs. 170</p>
          <p class="food-detail">  
            Traditional Nepali buffalo momo.
            Well-seasoned and flavorful filling.
            Served with homemade spicy sauce.
          </p>
          <br />
          <a href="order.php?id=27" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/fried momo.jpg"
            alt="fried momo"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>FRIED MOMO</h4>
          <p class="food-price">Rs. 200</p>
          <p class="food-detail">
            Crispy on the outside with a juicy, 
            flavorful filling inside. 
            Each bite offers a satisfying crunch and rich taste.

          </p>
          <br />
          <a href="order.php?id=28" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/chili momo.jpg"
            alt="chili momo"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>CHILI MOMO</h4>
          <p class="food-price">Rs. 240</p>
          <p class="food-detail">
            Crispy fried momos tossed in a spicy, 
            tangy sauce with onions and capsicum for an extra crunch. 
            Bold, flavorful, and perfectly spicy!
          </p>
          <br />
          <a href="order.php?id=29" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/veg momo.jpg"
            alt="veg momo"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>VEG MOMO</h4>
          <p class="food-price">Rs. 150</p>
          <p class="food-detail">
            Stuffed with cabbage and carrot mix.
            Lightly seasoned with fresh spices.
            Healthy and delicious option.
          </p>
          <br />

          <a href="order.php?id=30" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/cheese momo.jpg"
            alt="cheese momo"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>CHEESE MOMO</h4>
          <p class="food-price">Rs. 220</p>
          <p class="food-detail">
            Filled with soft melted cheese.
            Creamy texture inside dumpling wrap.
            Perfect for cheese lovers.
          </p>
          <br />
          <a href="order.php?id=31" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      
      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/jhol momo.jpg"
            alt="jhol momo"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>JHOL MOMO</h4>
          <p class="food-price">Rs. 220</p>
          <p class="food-detail">
            Steamed momo served in spicy soup.
            Sesame and tomato based gravy.
            Warm, juicy and comforting.
          </p>
          <br />
          <a href="order.php?id=32" class="btn btn-primary btn-order">ORDER</a>
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