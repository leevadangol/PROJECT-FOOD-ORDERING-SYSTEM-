

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Restaurant Website</title>

  <link rel="stylesheet" href="CSS/home_style.css" class="img-responsive" />
</head>

<body>
  <!-- Navbar  -->
  <section class="navbar">
    <div class="container">
      <div class="logo">
        <img src="IMAGES/logo.png" alt="LOGO" />
      </div>

      <div class="menu text-right">
        <ul>
          <li>
            <a href="home.php">Home</a>
          </li>
          <li>
            <a href="myorder.php">My Order</a>
          </li>
          <li>
            <a href="logout.php">Logout</a>
          </li>
          <!-- <li> 
              <a href="#">Contact</a>
            </li> -->
        </ul>
      </div>
      <div class="clear-fix"></div>
    </div>
  </section>
  <!-- Navbar ends-->

  <!-- fOOD sEARCH  -->
  <section class="food-search text-center">
    <div class="container">
      <form action="search.php" method="GET">
        <!-- method="POST"-->
        <input type="search" name="search" placeholder="Search..." required />
        <!-- name="search" -->
        <input type="submit" value="Search" class="btn btn-primary" />
      </form>
    </div>
  </section>
  <!-- fOOD sEARCH ends-->
</body>

</html>