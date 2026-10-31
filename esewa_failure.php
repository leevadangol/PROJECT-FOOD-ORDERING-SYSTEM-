<?php
/*
    ============================================================
    eSewa FAILURE PAGE  (esewa_failure.php)
    ============================================================
    eSewa redirects the customer HERE when:
      - The customer cancelled the payment
      - The payment failed (wrong PIN, insufficient balance, etc.)
      - The session timed out on eSewa's page

    We mark the payment as Failed in our database and keep all
    orders as "Pending" so the customer can try again later.
    No money is taken and no order is confirmed.
    ============================================================
*/

require_once "check_login.php";
require_once "db.php";

$payment_id = $_SESSION['esewa_payment_id'] ?? null;

if ($payment_id) {
    // Mark the payment record as Failed
    $stmt = mysqli_prepare($conn, "UPDATE payments SET payment_status = 'Failed' WHERE payment_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $payment_id);
    mysqli_stmt_execute($stmt);

    // Clear the session
    unset($_SESSION['esewa_payment_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Failed</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .failure-box {
            background: white;
            padding: 45px 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 450px;
            width: 90%;
        }
        .fail-icon {
            font-size: 70px;
            color: #f44336;
            margin-bottom: 15px;
        }
        .failure-box h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 12px;
        }
        .failure-box p {
            color: #666;
            font-size: 15px;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        .info-box {
            background: #fff8f8;
            border: 1px solid #ffcdd2;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #555;
            text-align: left;
        }
        .info-box ul { padding-left: 18px; margin-top: 8px; }
        .info-box li { margin-bottom: 5px; }
        .btn-row {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        .btn-retry {
            display: inline-block;
            background: #f25d07;
            color: white;
            padding: 11px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
            transition: background 0.2s;
        }
        .btn-retry:hover { background: #d44e04; }
        .btn-home {
            display: inline-block;
            background: #607d8b;
            color: white;
            padding: 11px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 15px;
            transition: background 0.2s;
        }
        .btn-home:hover { background: #455a64; }
    </style>
</head>
<body>
    <div class="failure-box">
        <div class="fail-icon">&#10006;</div>
        <h2>Payment Failed</h2>
        <p>Your eSewa payment was not completed. Your orders are still saved as <strong>Pending</strong> — no charges were made.</p>

        <div class="info-box">
            <strong>Common reasons for failure:</strong>
            <ul>
                <li>Payment was cancelled by you</li>
                <li>Insufficient eSewa balance</li>
                <li>Incorrect PIN or OTP entered</li>
                <li>Session timed out</li>
            </ul>
        </div>

        <p style="font-size:13px; color:#999;">
            You can try paying again anytime from My Orders.
        </p>

        <div class="btn-row">
            <a href="myorder.php" class="btn-retry">&#8635; Try Again</a>
            <a href="home.php" class="btn-home">&#8962; Go to Home</a>
        </div>
    </div>
</body>
</html>
