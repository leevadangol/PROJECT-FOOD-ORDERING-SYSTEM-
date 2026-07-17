<?php
include "searchbox.php";
require_once "check_login.php";
require_once "db.php";

// trim() removes accidental leading/trailing spaces the customer might type
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Search Results</title>
    <link rel="stylesheet" href="CSS/food-menu_style.css" />
    <link rel="stylesheet" href="CSS/home_style.css" />
    <style>
        /* Description text under the food name in search results */
        .food-detail {
            font-size: 13px;
            color: #666;
            margin: 6px 0 10px 0;
            line-height: 1.5;
        }
    </style>
</head>

<body>

    <section class="food-menu">
        <div class="container">

            <?php if ($search !== ''): ?>

                <?php
                /*
                    "%" on both sides of the search word means "match the
                    word ANYWHERE inside the food name" (e.g. searching
                    "izz" still finds "Pizza"). Using "?" as a placeholder
                    instead of pasting the search word straight into the
                    SQL text keeps this safe from SQL Injection.

                    We now also SELECT the description column so we can
                    display it in the results card.
                */
                $sql  = "SELECT f_id, f_name, price, image, description FROM foods WHERE f_name LIKE ?";
                $stmt = mysqli_prepare($conn, $sql);

                $likeSearch = "%" . $search . "%";
                mysqli_stmt_bind_param($stmt, "s", $likeSearch);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                $count = mysqli_num_rows($result);
                ?>

                <h2 class="text-center">
                    Search Results for "<?php echo htmlspecialchars($search); ?>"
                    <span style="font-size:16px; font-weight:normal; color:#666;">
                        (<?php echo $count; ?> <?php echo $count === 1 ? 'item' : 'items'; ?> found)
                    </span>
                </h2>

                <?php if ($count > 0): ?>

                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="food-menu-box zoom-box">
                            <div class="food-menu-img">
                                <?php if (!empty($row['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image']); ?>"
                                        alt="<?php echo htmlspecialchars($row['f_name']); ?>"
                                        class="img-responsive" />
                                <?php else: ?>
                                    <div style="color:red">Image Not Available</div>
                                <?php endif; ?>
                            </div>

                            <div class="food-menu-disc">
                                <h4><?php echo htmlspecialchars(strtoupper($row['f_name'])); ?></h4>
                                <p class="food-price">Rs. <?php echo htmlspecialchars($row['price']); ?></p>

                                <!-- Show the description if it exists in the database -->
                                <?php if (!empty($row['description'])): ?>
                                    <p class="food-detail">
                                        <?php echo htmlspecialchars($row['description']); ?>
                                    </p>
                                <?php endif; ?>

                                <a href="order.php?id=<?php echo (int) $row['f_id']; ?>"
                                    class="btn btn-primary btn-order">ORDER</a>
                            </div>
                            <div class="clear-fix"></div>
                        </div>
                    <?php endwhile; ?>

                <?php else: ?>

                    <p style="color:red; text-align:center;">
                        No food items found matching "<?php echo htmlspecialchars($search); ?>".
                    </p>
                    <p style="text-align:center;">
                        <a href="home.php">&larr; Back to Home</a>
                    </p>

                <?php endif; ?>

            <?php else: ?>

                <p style="text-align:center; color:#666;">
                    Type something in the search box above to find a food item.
                </p>

            <?php endif; ?>

            <div class="clear-fix"></div>
        </div>
    </section>

    <?php include "footer.php"; ?>
</body>

</html>
