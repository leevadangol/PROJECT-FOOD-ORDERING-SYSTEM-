<?php
include "searchbox.php";
require_once "check_login.php";
?>

<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PASTA</title>
  <link rel="stylesheet" href="CSS/food-menu_style.css" />
  <link rel="stylesheet" href="CSS/home_style.css" />
</head>

<body>

  <section class="food-menu">
    <div class="container">
      <h2 class="text-center">PASTA</h2>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/alfredo pasta.jpg"
            alt="alfredo pasta"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>CREAMY ALFREDO PASTA</h4>
          <p class="food-price">Rs. 350</p>
          <p class="food-detail">
            Penne pasta in rich white cream sauce.
            Flavored with herbs and parmesan cheese.
            Smooth, creamy and delicious.
          </p>
          <br />
          <a href="order.php?id=20" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/spaghetti.jpg"
            alt="spaghetti"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>SPAGHETTI</h4>
          <p class="food-price">Rs. 380</p>
          <p class="food-detail">  
            Classic spaghetti with minced chicken sauce.
            Slow-cooked tomato base with spices.
            Hearty and full of flavor.
          </p>
          <br />
          <a href="order.php?id=21" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/red sause pasta.jpg"
            alt="red sause pasta"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>RED SAUSE PASTA</h4>
          <p class="food-price">Rs. 320</p>
          <p class="food-detail">
            Pasta tossed in tangy tomato sauce.
            Mixed with capsicum and fresh vegetables.
            Light, fresh and flavorful.
          </p>
          <br />
          <a href="order.php?id=22" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/mushroom pasta.jpg"
            alt="mushroom pasta"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>MUSHROOM PASTA</h4>
          <p class="food-price">Rs. 360</p>
          <p class="food-detail">
            Creamy white sauce with sautéed mushrooms.
            Blended with herbs and cheese.
            Rich taste for mushroom lovers.
          </p>
          <br />
          <a href="order.php?id=23" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/veg pasta.jpg"
            alt="veg pasta"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>VEG PASTA</h4>
          <p class="food-price">Rs. 300</p>
          <p class="food-detail">
            Fresh seasonal vegetables and pasta.
            Cooked in light tomato herb sauce.
            Healthy and tasty option.
          </p>
          <br />

          <a href="order.php?id=24" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/baked cheesy pasta.jpg"
            alt="baked cheesy pasta"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>BAKED CHEESY PASTA</h4>
          <p class="food-price">Rs. 420</p>
          <p class="food-detail">
            Oven-baked pasta topped with mozzarella.
            Cheese melts perfectly over rich sauce.
            Warm, creamy and satisfying.
          </p>
          <br />
          <a href="order.php?id=25" class="btn btn-primary btn-order">ORDER</a>
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