<?php
include "a-header.php";
include "db.php";


$sql_remaining = "
    SELECT COUNT(*) AS total 
    FROM orders 
    WHERE status IN ('Accepted', 'Ready','Confirmed')
";
$res_remaining = mysqli_query($conn, $sql_remaining);
$row_remaining = mysqli_fetch_assoc($res_remaining);
$total_remaining = $row_remaining['total'];


$sql_completed = "
    SELECT COUNT(*) AS total 
    FROM orders 
    WHERE status = 'Completed'
";
$res_completed = mysqli_query($conn, $sql_completed);
$row_completed = mysqli_fetch_assoc($res_completed);
$total_completed = $row_completed['total'];


$sql_cancelled = "
    SELECT COUNT(*) AS total 
    FROM orders 
    WHERE status = 'Cancelled'
";
$res_cancelled = mysqli_query($conn, $sql_cancelled);
$row_cancelled = mysqli_fetch_assoc($res_cancelled);
$total_cancelled = $row_cancelled['total'];
?>

<!--------------------------------------------------------------------------->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/PROJECT (FOOD ORDERING SYSTEM)/CSS/a-dashboard_style.css" class="img-responsive" />
</head>
<body>

   <div class="main-content">
    <div class="wrapper">
        <h1>DASHBOARD</h1>

        <div class="col-4 text-center">
            <h1><?php echo $total_remaining; ?></h1><br>
            Total Remaining Order
        </div>

        <div class="col-4 text-center">
            <h1><?php echo $total_completed; ?></h1><br>
            Total Completed Order
        </div>

        <div class="col-4 text-center">
            <h1><?php echo $total_cancelled; ?></h1><br>
            Total Canceled Order
        </div>

        <div class="clear-fix"></div>
    </div>
</div>

</body>
</html>