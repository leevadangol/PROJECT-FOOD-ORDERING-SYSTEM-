<?php
/*
    ============================================================
    eSewa SUCCESS PAGE  (esewa_success.php)
    ============================================================
    eSewa redirects the customer's browser HERE after a
    successful payment.

    eSewa sends back one URL parameter called "data", which is
    a Base64-encoded JSON string containing payment details.

    Example of what eSewa sends (decoded):
    {
        "transaction_code": "0007HBE",   <- eSewa's own Txn ID
        "status": "COMPLETE",
        "total_amount": "550.00",
        "transaction_uuid": "20240101-120000-5-4321",
        "product_code": "EPAYTEST",
        "signed_field_names": "transaction_code,status,...",
        "signature": "abc123..."
    }

    This page:
      1. Decodes the data eSewa sent
      2. Checks status === "COMPLETE"
      3. Saves eSewa's Transaction Code in our payments table
      4. Marks all Pending orders as Confirmed
      5. Shows a success page to the customer
    ============================================================
*/

require_once "check_login.php";
require_once "db.php";

$customer_id = $_SESSION['customer_id'];
$payment_id  = $_SESSION['esewa_payment_id'] ?? null;

// If there is no payment ID in the session or no data from eSewa, abort
if (!$payment_id || !isset($_GET['data'])) {
    header("Location: myorder.php?error=" . urlencode("Payment data was not received. Please contact support."));
    exit();
}


/* ------------------------------------------------------------
   STEP 1: Decode eSewa's response
   ------------------------------------------------------------
   eSewa Base64-encodes a JSON string and sends it as ?data=...
   We reverse that here to get the actual payment details.
*/
$raw      = $_GET['data'];
$decoded  = base64_decode($raw);
$response = json_decode($decoded, true);

// Pull out the two most important values
$esewa_status   = $response['status']           ?? '';  // "COMPLETE" or other
$esewa_txn_code = $response['transaction_code'] ?? '';  // eSewa's own Transaction ID
$paid_amount    = $response['total_amount']     ?? '0';


/* ------------------------------------------------------------
   STEP 2: Check whether the payment actually succeeded
   ------------------------------------------------------------
*/
if ($esewa_status === 'COMPLETE' && $esewa_txn_code !== '') {

    // Update our payments record: save eSewa's Transaction Code
    // and change the status from Pending to Completed
    $sql  = "UPDATE payments
             SET payment_status = 'Completed',
                 esewa_ref_id   = ?
             WHERE payment_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $esewa_txn_code, $payment_id);
    mysqli_stmt_execute($stmt);

    // Confirm all Pending orders for this customer and link them
    // to this payment record
    $sql2  = "UPDATE orders
              SET status     = 'Confirmed',
                  payment_id = ?
              WHERE c_id    = ?
              AND   status  = 'Pending'";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "ii", $payment_id, $customer_id);
    mysqli_stmt_execute($stmt2);

    // Clear the payment session so it cannot be replayed
    unset($_SESSION['esewa_payment_id']);

    // Show the success page (see HTML below)
    $show_success = true;

} else {
    // Payment did not complete - mark as Failed in our database
    $sql  = "UPDATE payments SET payment_status = 'Failed' WHERE payment_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $payment_id);
    mysqli_stmt_execute($stmt);

    unset($_SESSION['esewa_payment_id']);

    header("Location: esewa_failure.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
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
        .success-box {
            background: white;
            padding: 45px 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 450px;
            width: 90%;
        }
        .check-icon {
            font-size: 70px;
            color: #4caf50;
            margin-bottom: 15px;
        }
        .success-box h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .success-box p {
            color: #666;
            font-size: 15px;
            margin-bottom: 8px;
        }
        .txn-box {
            background: #f0fff4;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .txn-box .label { font-size: 12px; color: #888; margin-bottom: 4px; }
        .txn-box .txn-id { font-size: 18px; font-weight: bold; color: #2e7d32; letter-spacing: 1px; }
        .txn-box .amount { font-size: 14px; color: #555; margin-top: 5px; }
        .btn-orders {
            display: inline-block;
            background: #f25d07;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 15px;
            font-weight: bold;
            margin-top: 10px;
            transition: background 0.2s;
        }
        .btn-orders:hover { background: #d44e04; }
        .esewa-badge {
            margin-top: 20px;
            font-size: 12px;
            color: #aaa;
        }
        .esewa-badge span { color: #60bb46; font-weight: bold; }
    </style>
</head>
<body>
    <div class="success-box">
        <div class="check-icon">&#10004;</div>
        <h2>Payment Successful!</h2>
        <p>Your payment has been received and your order has been confirmed.</p>

        <!-- eSewa Transaction Details -->
        <div class="txn-box">
            <div class="label">eSewa Transaction ID</div>
            <div class="txn-id"><?php echo htmlspecialchars($esewa_txn_code); ?></div>
            <div class="amount">Amount Paid: Rs. <?php echo htmlspecialchars($paid_amount); ?></div>
        </div>

        <p style="font-size:13px; color:#999;">
            Please save the Transaction ID above for your records.
        </p>

        <a href="myorder.php" class="btn-orders">View My Orders</a>

        <div class="esewa-badge">
            Paid securely via <span>eSewa</span>
        </div>
    </div>
</body>
</html>
