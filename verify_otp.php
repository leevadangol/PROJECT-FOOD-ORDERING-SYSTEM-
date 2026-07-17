<?php
/*
    ===========================================================
    OTP VERIFICATION PAGE  (verify_otp.php)
    ===========================================================
    The customer lands here after passing username + password
    + captcha on login.php.

    This page:
      - Shows a form to enter the 6-digit OTP
      - Checks if the OTP has expired (5 minutes)
      - Validates the typed OTP against the saved session value
      - Completes the login if correct
      - Allows resending a fresh OTP if needed

    DEMO MODE:
      If PHP mail() is not configured on XAMPP, the OTP is
      displayed in a yellow box on this page so the project
      can still be demonstrated without email setup.
    ===========================================================
*/

session_start();
require_once "db.php";
require_once "otp_mailer.php";

// If someone visits this page directly without going through
// login.php first, redirect them back to login.
if (!isset($_SESSION['otp_code'])) {
    header("Location: login.php?error=" . urlencode("Please login first."));
    exit();
}

$error = '';

/* -----------------------------------------------------------
   HANDLE: RESEND OTP REQUEST
   Customer clicked "Resend OTP" link.
----------------------------------------------------------- */
if (isset($_GET['resend'])) {

    $user_id = $_SESSION['otp_user_id'];
    $stmt    = mysqli_prepare($conn, "SELECT email, c_username FROM signup_page WHERE c_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $res  = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);

    // Generate a brand new OTP and reset the 5-minute timer
    $new_otp = strval(rand(100000, 999999));
    $_SESSION['otp_code']    = $new_otp;
    $_SESSION['otp_expires'] = time() + 300;

    $email_sent = send_otp_email($user['email'], $user['c_username'], $new_otp);

    if (!$email_sent) {
        $_SESSION['otp_demo_mode'] = true;
    } else {
        unset($_SESSION['otp_demo_mode']);
    }

    header("Location: verify_otp.php?msg=" . urlencode("A new OTP has been sent to your email."));
    exit();
}

/* -----------------------------------------------------------
   HANDLE: OTP FORM SUBMITTED
----------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $typed_otp = trim($_POST['otp'] ?? '');

    // Check 1: Has the OTP expired?
    if (time() > $_SESSION['otp_expires']) {
        // Clear all OTP session data
        unset($_SESSION['otp_code'], $_SESSION['otp_expires'],
              $_SESSION['otp_user_id'], $_SESSION['otp_username'],
              $_SESSION['otp_usertype'], $_SESSION['otp_demo_mode']);

        header("Location: login.php?error=" . urlencode("OTP expired. Please login again."));
        exit();
    }

    // Check 2: Does the typed OTP match?
    if ($typed_otp === $_SESSION['otp_code']) {

        // Correct OTP — complete the login
        $_SESSION['username']    = $_SESSION['otp_username'];
        $_SESSION['customer_id'] = $_SESSION['otp_user_id'];

        // Clean up all OTP session variables
        unset($_SESSION['otp_code'], $_SESSION['otp_expires'],
              $_SESSION['otp_user_id'], $_SESSION['otp_username'],
              $_SESSION['otp_usertype'], $_SESSION['otp_demo_mode']);

        header("Location: home.php");
        exit();

    } else {
        $error = "Incorrect OTP. Please try again.";
    }
}

// Seconds remaining before OTP expires (used by the countdown timer)
$seconds_left = max(0, $_SESSION['otp_expires'] - time());
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link rel="stylesheet" href="CSS/login_style.css" />
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        .otp-container {
            background: #ffffff;
            padding: 35px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            width: 320px;
            text-align: center;
        }

        .otp-icon { font-size: 50px; margin-bottom: 10px; }

        .otp-container h2 {
            color: #333;
            margin-bottom: 5px;
            font-size: 22px;
        }

        .otp-subtitle {
            font-size: 13px;
            color: #888;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        /* Yellow demo box shown when email is not configured */
        .demo-box {
            background: #fff3cd;
            border: 2px dashed #ffc107;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #856404;
        }
        .demo-otp {
            font-size: 30px;
            font-weight: bold;
            letter-spacing: 6px;
            color: #333;
            margin-top: 6px;
        }

        /* Large OTP input field */
        .otp-input {
            width: 88%;
            padding: 14px;
            font-size: 24px;
            text-align: center;
            letter-spacing: 8px;
            border: 2px solid #ccc;
            border-radius: 8px;
            margin: 10px 0 15px 0;
            font-family: monospace;
            outline: none;
        }
        .otp-input:focus { border-color: #f25d07; }

        .otp-btn {
            width: 100%;
            background: #f25d07;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.2s;
        }
        .otp-btn:hover { background: #d44e04; }

        .countdown {
            margin-top: 15px;
            font-size: 13px;
            color: #666;
        }
        .countdown span { font-weight: bold; color: #f25d07; }

        .error-msg   { color: red;   font-size: 14px; margin: 8px 0; }
        .success-msg { color: green; font-size: 14px; margin: 8px 0; }

        .resend-link { margin-top: 14px; font-size: 13px; }
        .resend-link a { color: #f25d07; text-decoration: none; }
        .resend-link a:hover { text-decoration: underline; }

        .back-link { margin-top: 8px; font-size: 13px; }
        .back-link a { color: #999; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="otp-container">

    <div class="otp-icon">&#128236;</div>
    <h2>Enter OTP</h2>
    <p class="otp-subtitle">
        A 6-digit OTP has been sent to your registered email.<br>
        It expires in <strong>5 minutes</strong>.
    </p>

    <!--
        DEMO MODE BOX
        Only shown when PHP mail() is not configured on XAMPP.
        Displays the OTP directly on screen so you can still
        demonstrate the MFA feature without email setup.
        In production, this box would never appear - the OTP
        goes to email only.
    -->
    <?php if (!empty($_SESSION['otp_demo_mode'])): ?>
    <div class="demo-box">
        &#9888; <strong>Demo Mode</strong> — Email not configured.<br>
        Your OTP is:
        <div class="demo-otp"><?php echo $_SESSION['otp_code']; ?></div>
        <small>In production, this would be sent to your email only.</small>
    </div>
    <?php endif; ?>

    <!-- Error message -->
    <?php if ($error): ?>
        <p class="error-msg">&#10006; <?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- Success/info message (e.g. after resend) -->
    <?php if (isset($_GET['msg'])): ?>
        <p class="success-msg">&#10004; <?php echo htmlspecialchars($_GET['msg']); ?></p>
    <?php endif; ?>

    <!-- OTP Entry Form -->
    <form method="POST" action="verify_otp.php">
        <input
            type="text"
            name="otp"
            class="otp-input"
            placeholder="______"
            maxlength="6"
            autocomplete="off"
            autofocus
            required>
        <button type="submit" class="otp-btn">&#10004; Verify OTP &amp; Login</button>
    </form>

    <!-- Countdown timer -->
    <div class="countdown">
        OTP expires in: <span id="timer">--:--</span>
    </div>

    <!-- Resend OTP -->
    <div class="resend-link">
        Didn't receive it? <a href="verify_otp.php?resend=1">Resend OTP</a>
    </div>

    <!-- Back to login -->
    <div class="back-link">
        <a href="login.php">&#8592; Back to Login</a>
    </div>

</div>

<script>
    /*
        COUNTDOWN TIMER
        Counts down from however many seconds are left on the OTP.
        When it hits zero, automatically redirects to login with an
        "OTP expired" message so the user knows to log in again.
    */
    var secondsLeft = <?php echo $seconds_left; ?>;

    function updateTimer() {
        if (secondsLeft <= 0) {
            document.getElementById('timer').textContent = '00:00';
            window.location.href = 'login.php?error=' + encodeURIComponent('OTP expired. Please login again.');
            return;
        }

        var mins = Math.floor(secondsLeft / 60);
        var secs = secondsLeft % 60;

        document.getElementById('timer').textContent =
            (mins < 10 ? '0' : '') + mins + ':' +
            (secs < 10 ? '0' : '') + secs;

        // Turn red when less than 1 minute left
        if (secondsLeft <= 60) {
            document.getElementById('timer').style.color = '#f44336';
        }

        secondsLeft--;
        setTimeout(updateTimer, 1000);
    }

    updateTimer();
</script>
</body>
</html>
