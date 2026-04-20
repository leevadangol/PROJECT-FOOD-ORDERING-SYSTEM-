<?php include "searchbox.php"; // require_once "check_login.php"; ?>

<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MENU</title>
  
  <link rel="stylesheet" href="CSS/food-menu_style.css" />
  <link rel="stylesheet" href="CSS/home_style.css" />

  <style>
    .section-title {
      color: orange;
      margin-top: 40px;
      margin-bottom: 20px;
    }
  </style>
</head>

<body>

<section class="food-menu">
    <div class="container">

      <h2 class="text-center">Menu</h2>

      <!-- ================= PIZZA SECTION ================= -->
      <div class="pizza-section">
        <h2 class="text-center section-title">PIZZA</h2>
  
        <div class="food-menu-box zoom-box">
          <div class="food-menu-img">
            <img src="IMAGES/Pepperoni Pizza1.jpeg" class="img-responsive"/>
          </div>
          <div class="food-menu-disc">
            <h4>PEPPERONI PIZZA</h4>
            <p class="food-price">Rs. 550</p>
            <p class="food-detail">
              A classic favourite, topped with melted cheese, zesty tomato sauce, and savory Pepperoni slices on a perfectly based crust.
            </p>
            <br />
            <a href="order.php?id=8" class="btn btn-primary">ORDER</a>
          </div>
          <div class="clear-fix"></div>
        </div>
  
        <div class="food-menu-box zoom-box">
          <div class="food-menu-img">
            <img src="IMAGES/Chicken pizza.jpeg" alt="Chicken pizza" class="img-responsive" />
          </div>
          <div class="food-menu-disc">
            <h4>CHICKEN PIZZA</h4>
            <p class="food-price">Rs. 550</p>
            <p class="food-detail">
              It features a soft, savory dough topped with tender chicken and rich, zesty sauce. Melted cheese adds a creamy finish to every delicious bite.
            </p>
            <br />
            <a href="order.php?id=9" class="btn btn-primary">ORDER</a>
          </div>
          <div class="clear-fix"></div>
        </div>
      </div>

      <!-- ================= BURGER SECTION ================= -->
      <div class="burger-section">
        <h2 class="text-center section-title">BURGER</h2>

        <div class="food-menu-box zoom-box">
          <div class="food-menu-img">
            <img src="IMAGES/cheese.jpeg" alt="cheese burger" class="img-responsive" />
          </div>
          <div class="food-menu-disc">
            <h4>CHEESE BURGER</h4>
            <p class="food-price">Rs. 550</p>
            <p class="food-detail">
              A classic cheeseburger featuring a juicy beef patty, melted cheese, and fresh toppings. Served on a warm, toasted bun for the perfect bite every time.
            </p>
            <br />
            <a href="order.php?id=1" class="btn btn-primary">ORDER</a>
          </div>
          <div class="clear-fix"></div>
        </div>

        <div class="food-menu-box zoom-box">
          <div class="food-menu-img">
            <img src="IMAGES/bbq.jpeg" alt="bbq burger" class="img-responsive" />
          </div>
          <div class="food-menu-disc">
            <h4>BBQ BURGER</h4>
            <p class="food-price">Rs. 550</p>
            <p class="food-detail">
              A bold BBQ burger loaded with smoky sauce, melted cheese, and crispy onion rings. Grilled to perfection and packed with sweet, savory flavor in every bite.
            </p>
            <br />
            <a href="order.php?id=2" class="btn btn-primary">ORDER</a>
          </div>
          <div class="clear-fix"></div>
        </div>
      </div>
      <!-- ================= FRIED CHICKEN SECTION ================= -->
      <div class="fried-chicken-section">
        <h2 class="text-center section-title">FRIED CHICKEN</h2>

        <div class="food-menu-box zoom-box">
            <div class="food-menu-img">
                <img src="IMAGES/crispy chicken.jpg" alt="crispy chicken" class="img-responsive" />
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
                <a href="order.php?id=1" class="btn btn-primary">ORDER</a>
            </div>
            <div class="clear-fix"></div>
        </div>
          <div class="food-menu-box zoom-box">
              <div class="food-menu-img">
                  <img src="IMAGES/hot wings.jpg" alt="hot wings" class="img-responsive" />
              </div>
              <div class="food-menu-disc">
                  <h4>SPICY HOT WINGS</h4>
                  <p class="food-price">Rs. 350</p>
                  <p class="food-detail">
                      Crispy wings tossed in bold spicy sauce.
                      Perfect balance of heat and flavor.
                      Best choice for spice lovers.
                  </p>
                  <br />
                  <a href="order.php?id=2" class="btn btn-primary">ORDER</a>
            </div>
            <div class="clear-fix"></div>
        </div>
      </div>
      <!-- ================= PASTA SECTION ================= -->
      <div class="pasta-section ">
        <h2 class="text-center section-title">PASTA</h2>

        <div class="food-menu-box zoom-box">
            <div class="food-menu-img">
                <img src="IMAGES/alfredo pasta.jpg" alt="alfredo pasta" class="img-responsive" />
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
                <a href="order.php?id=1" class="btn btn-primary">ORDER</a>
            </div>
            <div class="clear-fix"></div>
        </div>

        <div class="food-menu-box zoom-box">
            <div class="food-menu-img">
                <img src="IMAGES/spaghetti.jpg" alt="spaghetti" class="img-responsive" />
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
                <a href="order.php?id=2" class="btn btn-primary">ORDER</a>
            </div>
            <div class="clear-fix"></div>
          </div>
        </div>
      </div>
      <!-- ================= MOMO SECTION ================= -->
      <div class="momo-section container">       
        <h2 class="text-center section-title">MOMO</h2>

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
            <a href="order.php?id=1" class="btn btn-primary">ORDER</a>
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
            <a href="order.php?id=2" class="btn btn-primary">ORDER</a>
          </div>
          <div class="clear-fix"></div>
        </div>
      </div>

      <!-- ================= COLD DRINK SECTION ================= -->
      <div class="cold-drink-section container">
        <h2 class="text-center section-title">COLD DRINK</h2>

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
            <a href="order.php?id=1" class="btn btn-primary">ORDER</a>
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
            <a href="order.php?id=2" class="btn btn-primary">ORDER</a>
          </div>
          <div class="clear-fix"></div>
        </div>
      </div>  
    </div>
</section>

</body>
</html>

<?php include "footer.php"; ?>
