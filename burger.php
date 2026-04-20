<?php
include "searchbox.php";
require_once "check_login.php";
?>

<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BURGER</title>
  <link rel="stylesheet" href="CSS/food-menu_style.css" />
  <link rel="stylesheet" href="CSS/home_style.css" />
</head>

<body>
<p>Demo delete later</p>
  <section class="food-menu">
    <div class="container">
      <h2 class="text-center">BURGER</h2>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/cheese.jpeg"
            alt="cheese burger"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>CHEESE BURGER</h4>
          <p class="food-price">Rs. 550</p>
          <p class="food-detail">
            A classic cheeseburger featuring a juicy beef patty, melted
            cheese, and fresh toppings. Served on a warm, toasted bun for the
            perfect bite every time.
          </p>
          <br />
          <a href="order.php?id=1" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/bbq.jpeg"
            alt="bbq burger"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>BBQ BURGER</h4>
          <p class="food-price">Rs. 550</p>
          <p class="food-detail">
            A bold BBQ burger loaded with smoky sauce, melted cheese, and
            crispy onion rings. Grilled to perfection and packed with sweet,
            savory flavor in every bite.
          </p>
          <br />
          <a href="order.php?id=2" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/chicken.jpeg"
            alt="chicken burger"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>CHICKEN BURGER</h4>
          <p class="food-price">Rs. 470</p>
          <p class="food-detail">
            A juicy, seasoned chicken patty paired with crisp lettuce and
            creamy sauce. Served on a soft, toasted bun for a deliciously
            satisfying bite.
          </p>
          <br />
          <a href="order.php?id=3" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/fish.jpeg"
            alt="fish burger"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>FISH BURGER</h4>
          <p class="food-price">Rs. 450</p>
          <p class="food-detail">
            A crispy, golden fish fillet with tender flakes inside, topped
            with tartar sauce and fresh lettuce. Served in a soft bun for a
            light yet flavorful seafood delight.
          </p>
          <br />
          <a href="order.php?id=4" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/keto.jpeg"
            alt="keto burger"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>KETO BURGER</h4>
          <p class="food-price">Rs. 450</p>
          <p class="food-detail">
            A juicy, protein-packed burger with cheese, veggies, and savory
            toppings, wrapped in crisp lettuce or a keto bun. Perfect for
            low-carb lifestyles without sacrificing flavor.
          </p>
          <br />

          <a href="order.php?id=5" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/Paneer.jpeg"
            alt="paneer burger"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>PANEER BURGER</h4>
          <p class="food-price">Rs. 450</p>
          <p class="food-detail">
            A flavorful paneer burger with a spiced, grilled cottage cheese
            patty and zesty chutneys. Layered with fresh veggies and served in
            a soft, toasted bun for a tasty vegetarian treat.
          </p>
          <br />
          <a href="order.php?id=6" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/veg.jpeg"
            alt="veg burger"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>VEG BURGER</h4>
          <p class="food-price">Rs. 400</p>
          <p class="food-detail">
            A wholesome veg burger featuring a tasty patty made from mixed
            vegetables, grains, or legumes. Topped with fresh veggies and
            sauces, all tucked into a soft, toasted bun.
          </p>
          <br />
          <a href="order.php?id=7" class="btn btn-primary btn-order">ORDER</a>
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