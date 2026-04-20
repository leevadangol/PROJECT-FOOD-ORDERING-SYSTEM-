<?php
include "searchbox.php";
require_once "check_login.php";
?>

<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PIZZA</title>
  <link rel="stylesheet" href="CSS/food-menu_style.css" />
  <link rel="stylesheet" href="CSS/home_style.css" />
</head>

<body>
  <section class="food-menu">
    <div class="container">
      <h2 class="text-center">PIZZA</h2>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/Pepperoni Pizza1.jpeg"
            alt="Pepperoni pizza"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>PEPPERONIE PIZZA</h4>
          <p class="food-price">Rs. 550</p>
          <p class="food-detail">
            A classic favourite, topped with melted cheese, zesty tomato
            sauce,and savory Pepperoni slices on a perfectly based crust.
          </p>
          <br />
          <a href="order.php?id=8" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/Chicken pizza.jpeg"
            alt="Chicken pizza"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>CHICKEN PIZZA</h4>
          <p class="food-price">Rs. 550</p>
          <p class="food-detail">
            It features a soft, savory dough topped with tender chicken and
            rich, zesty sauce. Melted cheese adds a creamy finish to every
            delicious bite.
          </p>
          <br />
          <a href="order.php?id=9" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/Meat Lover Pizza.jpeg"
            alt="meat lover pizza"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>MEAT LOVER PIZZA</h4>
          <p class="food-price">Rs. 600</p>
          <p class="food-detail">
            A carnivore's dream, pilled with savory meats like Pepperoni,
            sausage, ham, and bacon on a cheezy base.
          </p>
          <br />
          <a href="order.php?id=10" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/mushroom pizza.jpeg"
            alt="mushroom pizza"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>MUSHROOM PIZZA</h4>
          <p class="food-price">Rs. 450</p>
          <p class="food-detail">
            It offers a rich, earthy flavor with a generous topping of fresh
            mushrooms. Melted cheese and a crispy crust complete this savory
            delight.
          </p>
          <br />
          <a href="order.php?id=11" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/Veg-pizza.jpeg"
            alt="veg pizza"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>VEG PIZZA</h4>
          <p class="food-price">Rs. 400</p>
          <p class="food-detail">
            It is loaded with a colorful variety of fresh vegetables and rich
            tomato sauce. Topped with gooey cheese on a soft yet crispy crust,
            it's a perfect vegetarian treat.
          </p>
          <br />
          <a href="order.php?id=12" class="btn btn-primary btn-order">ORDER</a>
        </div>
        <div class="clear-fix"></div>
      </div>

      <div class="food-menu-box zoom-box">
        <div class="food-menu-img">
          <img
            src="IMAGES/pineapple pizza.jpeg"
            alt="pineapple pizza"
            class="img-responsive" />
        </div>
        <div class="food-menu-disc">
          <h4>PINEAPPLE PIZZA</h4>
          <p class="food-price">Rs. 400</p>
          <p class="food-detail">
            It blends sweet pineapple chunks with savory toppings for a bold
            flavor contrast. Served on a cheesy base, it's a sweet-and-salty
            twist on the classic pizza.
          </p>
          <br />
          <a href="order.php?id=13" class="btn btn-primary btn-order">ORDER</a>
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