<?php
/*
    ===========================================================
    SEARCH BOX + NAVBAR (searchbox.php)
    ===========================================================
    This file is "included" at the very top of every food-menu
    page (home.php, pizza.php, burger.php, search.php, etc.) so
    every page automatically gets the same navbar and search bar
    without copy-pasting this HTML into every single file.

    IMPORTANT: this file only outputs a piece of a page (just two
    <section> blocks) - it does NOT have its own <!DOCTYPE>,
    <html>, <head> or <body> tags. The page that includes it
    provides those. (The previous version of this file had its
    own full <html>/<head>/<body> tags, which meant every page
    using it ended up with two sets of those tags - invalid HTML
    that browsers were just quietly tolerating.)
    ===========================================================
*/

// If the customer just came from a search, keep showing what they
// searched for in the box - much nicer than the box going blank
// every time they see their results. htmlspecialchars() makes sure
// whatever they typed is safe to print back into the page.
$previous_search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
?>

<!-- Navbar  -->
<section class="navbar">
  <div class="container">
    <div class="logo">
      <img src="IMAGES/logo.png" alt="LOGO" />
    </div>

    <div class="menu text-right">
      <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="myorder.php">My Order</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </div>
    <div class="clear-fix"></div>
  </div>
</section>
<!-- Navbar ends-->

<!-- Food Search  -->
<section class="food-search text-center">
  <div class="container">
    <form action="search.php" method="GET">
      <input
        type="search"
        name="search"
        placeholder="Search for food..."
        value="<?php echo $previous_search; ?>"
        required />
      <input type="submit" value="Search" class="btn btn-primary" />
    </form>
  </div>
</section>
<!-- Food Search ends-->
