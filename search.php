<?php
include "searchbox.php";
require_once "check_login.php";
include "db.php"; 
?>

<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Search Results</title>
    <link rel="stylesheet" href="CSS/food-menu_style.css" />
    <link rel="stylesheet" href="CSS/home_style.css" />
</head>
<body>

<section class="food-menu">
    <div class="container">
        <?php
        if (isset($_GET['search'])) {
            $search = $conn->real_escape_string($_GET['search']);

            
            $sql = "SELECT * FROM foods WHERE f_name LIKE '%$search%'";
            $result = $conn->query($sql);

            if (!$result) {
                die("Query failed: " . $conn->error);
            }

            echo "<h2 class='text-center'>Search Results for '<b>$search</b>'</h2>";

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="food-menu-box zoom-box">
                        <div class="food-menu-img">
                            <?php if($row['image'] != "") { ?>
                                <img src="<?php echo $row['image']; ?>" 
                                     alt="<?php echo $row['f_name']; ?>" 
                                     class="img-responsive" />
                            <?php } else { ?>
                                <div style="color:red">Image Not Available</div>
                            <?php } ?>
                        </div>

                        <div class="food-menu-disc">
                            <h4><?php echo strtoupper($row['f_name']); ?></h4>
                            <p class="food-price">Rs. <?php echo $row['price']; ?></p>
                            <!--description (aaaile rakeko xaina-->
                            <a href="order.php?id=<?php echo $row['f_id']; ?>" 
                               class="btn btn-primary btn-order">ORDER</a>
                        </div>
                        <div class="clear-fix"></div>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='color:red; text-align:center;'>No food items found matching your search!</p>";
            }
        }
        ?>
        <div class="clear-fix"></div>
    </div>
</section>

<?php include "footer.php"; ?>
</body>
</html>
