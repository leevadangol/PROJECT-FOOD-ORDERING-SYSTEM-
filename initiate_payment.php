<?php
/*
    ============================================================
    INITIATE eSewa PAYMENT  (initiate_payment.php)
    ============================================================
    Triggered when the customer clicks "Pay with eSewa" on
    myorder.php. This page:

      1. Calculates the total of all Pending orders
      2. Saves a payment record in our own database
      3. Builds the eSewa payment form with all required fields
         (exactly matching eSewa's official documentation)
      4. Auto-submits that form - sending the customer's browser
         straight to eSewa's payment page

    OFFICIAL eSewa PARAMETERS USED  (from eSewa developer docs):
    ┌────────────────────────┬────────────────────────────────────┐
    │ amount                 │ Base amount of the order           │
    │ tax_amount             │ Tax (we use 0)                     │
    │ total_amount           │ amount + tax + charges             │
    │ transaction_uuid       │ Our unique ID for this payment     │
    │ product_code           │ Merchant code (EPAYTEST for test)  │
    │ product_service_charge │ Service charge (we use 0)          │
    │ product_delivery_charge│ Delivery charge (we use 0)         │
    │ success_url            │ Where to send customer on success  │
    │ failure_url            │ Where to send customer on failure  │
    │ signed_field_names     │ Which fields are signed            │
    │ signature              │ HMAC-SHA256 signature (base64)     │
    └────────────────────────┴────────────────────────────────────┘
    ============================================================
*/

require_once "check_login.php";  // starts session + redirects to login if not logged in
require_once "db.php";
require_once "esewa_config.php"; // ESEWA_PRODUCT_CODE, ESEWA_SECRET_KEY, ESEWA_PAYMENT_URL

$customer_id = $_SESSION['customer_id'];


/* ------------------------------------------------------------
   STEP 1: Calculate total of all Pending orders
   ------------------------------------------------------------
   We only charge for orders that are still "Pending" (not yet
   paid). Using a prepared statement to prevent SQL injection.
*/
$totalSql  = "SELECT SUM(total_price) AS grand_total
              FROM orders
              WHERE c_id = ? AND status = 'Pending'";
$totalStmt = mysqli_prepare($conn, $totalSql);
mysqli_stmt_bind_param($totalStmt, "i", $customer_id);
mysqli_stmt_execute($totalStmt);
$totalResult = mysqli_stmt_get_result($totalStmt);
$totalRow    = mysqli_fetch_assoc($totalResult);

// If there are no pending orders, send the customer back
if (empty($totalRow['grand_total']) || $totalRow['grand_total'] <= 0) {
    header("Location: myorder.php?error=" . urlencode("You have no pending orders to pay for."));
    exit();
}

/*
    eSewa requires amounts as strings with exactly 2 decimal places.
    e.g.  "550.00"  not  "550"  or  "550.5"
    number_format() does this for us.
*/
$amount       = number_format($totalRow['grand_total'], 2, '.', '');
$tax_amount   = "0";       // No tax in this system
$total_amount = $amount;   // total = amount + tax(0) + service(0) + delivery(0)


/* ------------------------------------------------------------
   STEP 2: Generate a unique Transaction UUID
   ------------------------------------------------------------
   eSewa's rule: alphanumeric characters and hyphens (-) ONLY.
   Must be unique for every single payment attempt.
   Format: YYYYMMDD-HHMMSS-customerID-randomNumber
*/
$transaction_uuid = date("Ymd-His") . "-" . $customer_id . "-" . rand(1000, 9999);


/* ------------------------------------------------------------
   STEP 3: Save a Pending payment row in OUR database
   ------------------------------------------------------------
   We record the payment BEFORE going to eSewa, so when the
   customer comes back (success or failure) we can find this
   exact record using the session.
*/
$insertSql  = "INSERT INTO payments
                   (c_id, transaction_id, payment_status, payment_method, amount, payment_date)
               VALUES (?, ?, 'Pending', 'eSewa', ?, NOW())";
$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($insertStmt, "isd", $customer_id, $transaction_uuid, $total_amount);
mysqli_stmt_execute($insertStmt);

$payment_id = mysqli_insert_id($conn);

// Store the payment ID in the session so esewa_success.php can
// find this record when the customer returns from eSewa.
$_SESSION['esewa_payment_id'] = $payment_id;


/* ------------------------------------------------------------
   STEP 4: Generate the HMAC-SHA256 Signature
   ------------------------------------------------------------
   eSewa uses this signature to verify:
     a) This request really came from our system (not faked)
     b) The amount was not tampered with along the way

   HOW IT WORKS (from eSewa official docs):
     1. Build a message string in EXACTLY this format:
        "total_amount=<value>,transaction_uuid=<value>,product_code=<value>"
     2. Run HMAC-SHA256 on that string using our secret key
     3. Base64-encode the result
     4. Send that as the "signature" field

   The field names in signed_field_names MUST match the message
   string order exactly.
*/
$signed_field_names = "total_amount,transaction_uuid,product_code";

// Build the message string - ORDER MATTERS, must match signed_field_names
$message = "total_amount="    . $total_amount .
           ",transaction_uuid=" . $transaction_uuid .
           ",product_code="     . ESEWA_PRODUCT_CODE;

// hash_hmac() with the 4th argument = true gives raw binary output.
// base64_encode() then turns that binary into a readable string.
$signature = base64_encode(
    hash_hmac('sha256', $message, ESEWA_SECRET_KEY, true)
);


/* ------------------------------------------------------------
   STEP 5: Build our own success and failure URLs
   ------------------------------------------------------------
   We build these from the current server address automatically,
   so the code keeps working even if you rename the folder or
   move to a different server later.
*/
$base_url    = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/";
$success_url = $base_url . "esewa_success.php";
$failure_url = $base_url . "esewa_failure.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting to eSewa...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f4f4f4;
            flex-direction: column;
            gap: 15px;
        }
        .redirect-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
        }
        .redirect-box h3 { color: #60bb46; margin-bottom: 10px; }
        .amount { font-size: 28px; font-weight: bold; color: #333; margin: 10px 0; }
        .txn-id { font-size: 12px; color: #999; }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #60bb46;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }
    </style>
</head>
<body>
    <div class="redirect-box">
        <h3>&#128274; Redirecting to eSewa...</h3>
        <div class="amount">Rs. <?php echo htmlspecialchars($total_amount); ?></div>
        <div class="spinner"></div>
        <p style="color:#666; font-size:14px;">Please wait. You are being redirected to eSewa to complete your payment securely.</p>
        <div class="txn-id">Transaction ID: <?php echo htmlspecialchars($transaction_uuid); ?></div>
    </div>

    <!--
        THE OFFICIAL eSEWA PAYMENT FORM
        All parameters from eSewa's official documentation:
        https://developer.esewa.com.np
    -->
    <form id="esewaForm" action="<?php echo ESEWA_PAYMENT_URL; ?>" method="POST">

        <!-- The base product amount (without tax/charges) -->
        <input type="hidden" name="amount"
               value="<?php echo htmlspecialchars($amount); ?>">

        <!-- Tax on the product (0 for this system) -->
        <input type="hidden" name="tax_amount" value="<?php echo $tax_amount; ?>">

        <!-- TOTAL = amount + tax + service_charge + delivery_charge -->
        <input type="hidden" name="total_amount"
               value="<?php echo htmlspecialchars($total_amount); ?>">

        <!-- Our unique ID for this payment attempt -->
        <input type="hidden" name="transaction_uuid"
               value="<?php echo htmlspecialchars($transaction_uuid); ?>">

        <!-- Merchant code (EPAYTEST for testing, real code for production) -->
        <input type="hidden" name="product_code" value="<?php echo ESEWA_PRODUCT_CODE; ?>">

        <!-- Service charge (0 for this system) -->
        <input type="hidden" name="product_service_charge" value="0">

        <!-- Delivery charge (0 for this system) -->
        <input type="hidden" name="product_delivery_charge" value="0">

        <!-- Where eSewa sends the customer after SUCCESSFUL payment -->
        <input type="hidden" name="success_url"
               value="<?php echo htmlspecialchars($success_url); ?>">

        <!-- Where eSewa sends the customer after FAILED/CANCELLED payment -->
        <input type="hidden" name="failure_url"
               value="<?php echo htmlspecialchars($failure_url); ?>">

        <!-- Which fields are included in the signature (ORDER MUST MATCH) -->
        <input type="hidden" name="signed_field_names"
               value="<?php echo $signed_field_names; ?>">

        <!-- The HMAC-SHA256 signature (base64 encoded) -->
        <input type="hidden" name="signature"
               value="<?php echo htmlspecialchars($signature); ?>">

    </form>

    <script>
        // Auto-submit the form after a short delay so the customer
        // can briefly see the "Redirecting..." message above.
        setTimeout(function () {
            document.getElementById('esewaForm').submit();
        }, 1500);
    </script>
</body>
</html>
