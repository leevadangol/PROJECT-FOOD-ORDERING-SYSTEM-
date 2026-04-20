<?php
include "header.php";
require_once "check_login.php";
include "db.php";

$id = $_GET['id'] ?? 1;

$sql = "SELECT * FROM foods WHERE f_id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
  $food = mysqli_fetch_assoc($result);
} else {
  die("Food not found");
}

$foodName = $food['f_name'];
$foodPrice = $food['price'];
$foodImg = $food['image'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>order page</title>
  <link rel="stylesheet" href="css/order_style.css" />
</head>

<body>
  <!-- fOOD SEARCH   -->
  <section class="food-search">
    <div class="container search-box search-container">
      <h2 class="text-center">Fill this form to confirm your order.</h2>

      <form action="process_order.php" method="POST" class="order">
        <fieldset>
          <legend>Selected Food</legend>

          <div class="food-menu-img">
            <img src="<?php echo $foodImg; ?>" class="img-responsive img-curve">
          </div>

          <div class="food-menu-desc">
            <h3><?php echo $foodName; ?></h3>
            <p class="food-price">Rs. <?php echo $foodPrice; ?></p>

            <input type="hidden" name="food_id" value="<?php echo $id; ?>">
            <input type="hidden" name="price" value="<?php echo $foodPrice; ?>">

            <div class="order-label">Quantity</div>
            <input type="number" name="qty" value="1" min="1" class="input-responsive" required>
          </div>
        </fieldset>

        <fieldset>
          <legend>Customer Details</legend>
          <div class="order-label">Full Name</div>
          <input
            type="text"
            name="full_name"
            placeholder="Full name"
            class="input-responsive"
            required />

          <div class="order-label">Table Number</div>
          <input
            type="text"
            name="tableno"
            placeholder="Table No."
            class="input-responsive"
            required>

          <div class="order-label">Date & Time</div>
          <input
            type="datetime-local"
            name="datetime"
            placeholder="date and time"
            class="input-responsive">

          <div class="order-label">Phone Number</div>
          <input
            type="tel"
            name="contact"
            placeholder="97XXXXXXXX"
            class="input-responsive"
            required />

          <input
            type="submit"
            name="submit"
            value="ADD"
            class="btn btn-primary" />
        </fieldset>
      </form>
    </div>
  </section>
  <!-- fOOD SEARCH  Ends -->

  <!-- social -->
  <section class="social">
    <div class="container text-center">
      <ul>
        <li>
          <a href="#"><img
              src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAABi0lEQVR4nN2VO0sDURCFv0SSRv0fktQSUVBBsZQoIdjZGdT4qK1Fg521pY/SvyIqvqJ29iKKsUpk4CwMce9uktIDF5I5s3OWc2dm4b8jD1SBC+AR+NKx3+fiLGcgLAOvQCflvABL/RTOAkeuwDWwDRSAYZ0isAPcuLyGnk1FVPwHqKU8ZNy6ciORVFui4jOBnFGgBCwA44rNOpFyqHjeeV5LeIFPZ8u74zYUewZycQ9XnedxtowAH674G3Dl+CHgVlwlTuBCpF1oHEqu+H4gZ1f8WRz5JNK6JQ4VJzAdyCmKtzn5g8hbsyIOK05gIqEBOqoVFLA+9zhOGLK7gIDdVdCisT4ELrtyC0kWnYvc7IpPAmvAiSt8qNhUV+6W+NOkNrVWywxwB1lZFmzTvBZXR0PTr0BdXDM0aGgrWtI3MNeHwDzQAtrAIiloOJG6sytOIKPBbCl+QA/IOpGOfLXVvOdiq5raB/1vq3hP6zpCWYsr7YPT7MWWEHLqCNst9rY2jHbu1YrGBS/0f+AXXvmgrmqaWLUAAAAASUVORK5CYII="
              alt="facebook-new" /></a>
        </li>
        <a href="#"><img
            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAA5klEQVR4nO1TQQ6CQAycbwBPEr6jeJb4KyDR+CCFi6c1TUqCa7ct4JFJetgw7XRnFmDHSlQAegAjgGAUcToApXf41TE0JKrxbE7EN4AjgNyxEHFO3BOsm/RMogYvHgDuAGrubTXywKRcyWSMPKfhN+6h3pcmMHnpzaRx9KuESshE83zyQK9kInm+WGDgcyZwM8Hz1QK5wC3423OLQMdnsiPG+R8WlbOQa75JwcOnkA9bBMBPMfVML/iGKZDyvGQrBq422nyeifqjaZ5bkDL5geS5BS0TEZrnwag4kyTmngejUpnsgAsfOPmncZue63wAAAAASUVORK5CYII="
            alt="instagram-new--v1" /></a>

        <a href="#"><img
            src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAABg0lEQVR4nO2UPyiFYRTGfy75vxCSoliUJGSyMfgzSMnCYGExKAObhTK6m8FgUCQ3g7LJgJKiFCmDRQgpCYl7de/VV4/6+rrf33vH+9Tb953nPec5ve97zoEsfKAHSADJFGvDJbYf+AXiwJCT47JNgiQwYhPTBnzKZ9btFIXAlZzD4iZkvwF1Fv8a4E77q3hEOxDVdfWJ25bIPhASVwycij8A8vGBOQU+AOVAJfAkblpJdmRfA2X4RC5wLIFNcQOyv4Et/b8ADQREPfAuoVFxK6ZHNxJ1kibGLQ9cCtyIWydDiEjwSFfXAcRU812ZSFABPCrJjLh52fdBHtiKEuBCgj9AC5BnKtG1dMRDpnKM6nsJFACNwJe44aAJwhJ4BVpV84a9pP1JUxHU+hX/HxExoNvS5XFxOcCu/PZke0KvpmMCGHPp8srgWdyUF/EmHdkIWLB5l0PtGzPKwKCp+ZqdxKuBWzlHHI5sdPmHzVg/txt8RcCJnM40Lb10eaq16BiZBSb8AUO2gvHJkrncAAAAAElFTkSuQmCC"
            alt="twitterx--v2" /></a>
      </ul>
    </div>
  </section>
  <!-- social  Ends  -->

  <!-- footer -->
  <section class="footer">
    <div class="container text-center">
      <p>All rights reserved. Designed By <a href="#">Leeva and Leeza</a></p>
    </div>
  </section>
  <!-- footer  Ends  -->
</body>

<!-- JAVASCRIPT for chnaging the total price accoring to the quantity -->
<script>
  const qtyInput = document.querySelector("input[name='qty']");
  const price = <?php echo $foodPrice; ?>;
  const priceDisplay = document.querySelector(".food-price");

  qtyInput.addEventListener("input", () => {
    const total = qtyInput.value * price;
    priceDisplay.textContent = "Rs. " + total;
  });
</script>

</html>