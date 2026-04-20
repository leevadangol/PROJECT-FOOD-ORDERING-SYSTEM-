<?php
include "searchbox.php";
require_once "check_login.php";


// Check if logged in
if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit();
}

require_once "db.php";

// test
if (!isset($_SESSION['username'])) {
  header("Location: signup.php");
  exit();
}
// test

?>
<!--  -->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Restaurant Website</title>

  <link rel="stylesheet" href="CSS/food-menu_style.css" />
  <link rel="stylesheet" href="CSS/home_style.css" class="img-responsive" />
</head>

<body>
  <!-- Categories  -->
  <section class="categories">
    <div class="container">
      <h2 class="text-center">Explore Foods</h2>
      <hr width="50%" size="3" color="#f25d07" style="display:block; margin:5px auto;">
      <a href="pizza.php">
        <div class="box float-container zoom-box">
          <img
            src="IMAGES/Pepperoni Pizza.jpeg"
            alt="pizza"
            class="img-responsive img-curve" />
          <h3 class="float-text text-white">PIZZA</h3>
        </div>
      </a>
      <a href="burger.php">
        <div class="box float-container zoom-box">
          <img
            src="IMAGES/Burger.jpg"
            alt="burger"
            class="img-responsive img-curve" />
          <h3 class="float-text text-white">BURGER</h3>
        </div>
      </a>
      <a href="fried_chicken.php">
        <div class="box float-container zoom-box">
          <img
            src="IMAGES/Fried chicken.jpg"
            alt="fried-chicken"
            class="img-responsive img-curve" />
          <h3 class="float-text text-white">FRIED CHICKEN</h3>
        </div>
      </a>
      <a href="pasta.php">
        <div class="box float-container zoom-box">
          <img
            src="IMAGES/Pasta.jpg"
            alt="pasta"
            class="img-responsive img-curve" />
          <h3 class="float-text text-white">PASTA</h3>
        </div>
      </a>
      <a href="momo.php">
        <div class="box float-container zoom-box">
          <img
            src="IMAGES/MoMo.jpeg"
            alt="momo"
            class="img-responsive img-curve" />
          <h3 class="float-text text-white">MOMO</h3>
        </div>
      </a>
      <a href="cold_drink.php">
        <div class="box float-container zoom-box">
          <img
            src="IMAGES/Cold drink.jpeg"
            alt="cold drink"
            class="img-responsive img-curve" />
          <h3 class="float-text text-white">COLD DRINK</h3>
        </div>
      </a>
      <div class="clear-fix"></div>
    </div>
  </section>
  <!-- Categories ends-->

  <!-- MENU -->
  <section class="food-menu">
    <div class="container">

      <h2 class="text-center " >Menu</h2>
      <hr width="50%" size="3" color="#f25d07" style="display:block; margin:5px auto;">
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
            <a href="order.php?id=8" class="btn btn-primary btn-order">ORDER</a>
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
            <a href="order.php?id=9" class="btn btn-primary btn-order">ORDER</a>
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
            <a href="order.php?id=1" class="btn btn-primary btn-order">ORDER</a>
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
            <a href="order.php?id=2" class="btn btn-primary btn-order">ORDER</a>
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
                <a href="order.php?id=14" class="btn btn-primary btn-order">ORDER</a>
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
                  <a href="order.php?id=15" class="btn btn-primary btn-order">ORDER</a>
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
                <a href="order.php?id=20" class="btn btn-primary btn-order">ORDER</a>
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
                <a href="order.php?id=21" class="btn btn-primary btn-order">ORDER</a>
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
      <div class="clear-fix"></div>
    </div>
  </section>
  <!-- Food Menu Ends -->

  <!-- ABOUT US  -->
  <section class="about-us">
    <div class="container text-center">
      <h2>About Us</h2>
      <p>
        The fastest, easiest and most convenient way to enjoy the best food at
        home, at the office or wherever you want to. We know that your time is
        valuable and sometimes every minute in the day counts. That's why we
        deliver! So you can spend more time doing the things you love.
      </p>
    </div>
  </section>
  <!-- ABOUT US  ENDS -->

</body>

</html>

<?php
include "footer.php";
?>