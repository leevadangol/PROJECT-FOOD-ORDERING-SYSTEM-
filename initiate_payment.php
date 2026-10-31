<?php
/*
    INITIATE eSewa PAYMENT (initiate_payment.php)
    Adds delivery fee (from session) to food total before
    sending to eSewa. Uses eSewa's product_delivery_charge
    field for the delivery amount.
*/
require_once "check_login.php";
require_once "db.php";
require_once "esewa_config.php";

$customer_id   = $_SESSION['customer_id'];
$delivery_fee  = floatval($_SESSION['delivery_fee'] ?? 100);  // default Rs.100

// STEP 1: Get food subtotal of Pending orders
$totalSql  = "SELECT SUM(total_price) AS grand_total
              FROM orders WHERE c_id = ? AND status = 'Pending'";
$totalStmt = mysqli_prepare($conn, $totalSql);
mysqli_stmt_bind_param($totalStmt, "i", $customer_id);
mysqli_stmt_execute($totalStmt);
$totalRow = mysqli_fetch_assoc(mysqli_stmt_get_result($totalStmt));

if (empty($totalRow['grand_total']) || $totalRow['grand_total'] <= 0) {
    header("Location: myorder.php?error=" . urlencode("You have no pending orders to pay for."));
    exit();
}

$food_amount  = number_format($totalRow['grand_total'], 2, '.', '');
$delivery_amt = number_format($delivery_fee, 2, '.', '');

// total_amount = food amount + delivery fee (eSewa adds these together)
$total_amount = number_format($totalRow['grand_total'] + $delivery_fee, 2, '.', '');

// STEP 2: Generate unique Transaction UUID
$transaction_uuid = date("Ymd-His") . "-" . $customer_id . "-" . rand(1000, 9999);

// STEP 3: Save Pending payment record
$insertSql  = "INSERT INTO payments
                   (c_id, transaction_id, payment_status, payment_method, amount, payment_date)
               VALUES (?, ?, 'Pending', 'eSewa', ?, NOW())";
$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($insertStmt, "isd", $customer_id, $transaction_uuid, $total_amount);
mysqli_stmt_execute($insertStmt);
$payment_id = mysqli_insert_id($conn);

$_SESSION['esewa_payment_id'] = $payment_id;

// STEP 4: Build HMAC-SHA256 signature
// total_amount includes delivery fee so eSewa charges the correct amount
$signed_field_names = "total_amount,transaction_uuid,product_code";
$message = "total_amount="      . $total_amount .
           ",transaction_uuid=" . $transaction_uuid .
           ",product_code="     . ESEWA_PRODUCT_CODE;
$signature = base64_encode(hash_hmac('sha256', $message, ESEWA_SECRET_KEY, true));

// STEP 5: Build success/failure URLs
$base_url    = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/";
$success_url = $base_url . "esewa_success.php";
$failure_url = $base_url . "esewa_failure.php";

$delivery_label = ($_SESSION['delivery_location'] ?? 'inside') === 'outside'
    ? 'Outside Ringroad' : 'Inside Ringroad';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Redirecting to eSewa...</title>
    <style>
        body { font-family:Arial,sans-serif; display:flex; justify-content:center;
               align-items:center; height:100vh; margin:0; background:#f4f4f4;
               flex-direction:column; }
        .redirect-box { background:white; padding:35px 40px; border-radius:10px;
                        box-shadow:0 3px 15px rgba(0,0,0,0.1); text-align:center; max-width:420px; }
        .redirect-box h3 { color:#60bb46; margin-bottom:10px; }
        .breakdown { text-align:left; background:#f9f9f9; border-radius:6px;
                     padding:12px 16px; margin:15px 0; font-size:14px; }
        .breakdown div { display:flex; justify-content:space-between; padding:4px 0; }
        .breakdown .total-row { font-weight:bold; border-top:1px solid #eee;
                                padding-top:8px; margin-top:4px; color:#f25d07; font-size:16px; }
        .spinner { border:4px solid #f3f3f3; border-top:4px solid #60bb46;
                   border-radius:50%; width:40px; height:40px;
                   animation:spin 1s linear infinite; margin:15px auto; }
        @keyframes spin { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }
    </style>
</head>
<body>
    <div class="redirect-box">
        <h3>&#128274; Redirecting to eSewa...</h3>

        <!-- Show the breakdown so customer knows what they're paying -->
        <div class="breakdown">
            <div>
                <span style="color:#555;">Food Total</span>
                <span>Rs. <?= htmlspecialchars($food_amount); ?></span>
            </div>
            <div>
                <span style="color:#555;">Delivery (<?= htmlspecialchars($delivery_label); ?>)</span>
                <span>Rs. <?= htmlspecialchars($delivery_amt); ?></span>
            </div>
            <div class="total-row">
                <span>Grand Total</span>
                <span>Rs. <?= htmlspecialchars($total_amount); ?></span>
            </div>
        </div>

        <div class="spinner"></div>
        <p style="color:#666; font-size:13px;">Redirecting to eSewa securely...</p>
        <div style="font-size:11px; color:#aaa;">Txn: <?= htmlspecialchars($transaction_uuid); ?></div>
    </div>

    <form id="esewaForm" action="<?php echo ESEWA_PAYMENT_URL; ?>" method="POST">
        <!-- Food amount (base, without delivery) -->
        <input type="hidden" name="amount"                    value="<?= htmlspecialchars($food_amount); ?>">
        <input type="hidden" name="tax_amount"                value="0">
        <!-- Delivery charge passed to eSewa using their official field -->
        <input type="hidden" name="product_delivery_charge"   value="<?= htmlspecialchars($delivery_amt); ?>">
        <input type="hidden" name="product_service_charge"    value="0">
        <!-- Total = food + delivery (eSewa verifies this adds up) -->
        <input type="hidden" name="total_amount"              value="<?= htmlspecialchars($total_amount); ?>">
        <input type="hidden" name="transaction_uuid"          value="<?= htmlspecialchars($transaction_uuid); ?>">
        <input type="hidden" name="product_code"              value="<?= ESEWA_PRODUCT_CODE; ?>">
        <input type="hidden" name="success_url"               value="<?= htmlspecialchars($success_url); ?>">
        <input type="hidden" name="failure_url"               value="<?= htmlspecialchars($failure_url); ?>">
        <input type="hidden" name="signed_field_names"        value="<?= $signed_field_names; ?>">
        <input type="hidden" name="signature"                 value="<?= htmlspecialchars($signature); ?>">
    </form>

    <script>
        setTimeout(function () {
            document.getElementById('esewaForm').submit();
        }, 1500);
    </script>
</body>
</html>
