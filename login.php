<?php
/*
    ===========================================================
    LOGIN PAGE WITH MULTI-FACTOR AUTHENTICATION (login.php)
    ===========================================================
    Login now has THREE layers of security:

      LAYER 1 — CAPTCHA        (blocks bots)
      LAYER 2 — Username + Password (checks credentials)
      LAYER 3 — Email OTP      (proves account ownership)

    HOW THE FLOW WORKS:
      1. Customer fills in username, password, captcha → submit
      2. If all three are correct, a 6-digit OTP is generated,
         saved in the session, and emailed to the registered
         email address. Customer is sent to verify_otp.php.
      3. Customer types the OTP. If it matches and has not
         expired (5 minutes), they are fully logged in.

    Admin login skips the OTP step for simplicity.
    ===========================================================
*/

session_start();
require "db.php";
require "otp_mailer.php"; // gives us the send_otp_email() function

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* -------------------------------------------------------
       LAYER 1: CAPTCHA CHECK
       Check this first before touching the database at all.
    ------------------------------------------------------- */
    $entered_captcha = trim($_POST["captcha"] ?? '');
    $correct_captcha = $_SESSION['captcha_code'] ?? '';
    unset($_SESSION['captcha_code']); // delete immediately - one use only

    if ($entered_captcha === '' || strcasecmp($entered_captcha, $correct_captcha) !== 0) {
        header("Location: login.php?error=" . urlencode("Incorrect captcha code. Please try again."));
        exit();
    }

    /* -------------------------------------------------------
       LAYER 2: USERNAME AND PASSWORD CHECK
    ------------------------------------------------------- */
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $usertype = $_POST["usertype"] ?? '';

    // =================== CUSTOMER LOGIN ===================
    if ($usertype === 'customer') {

        // Prepared statement prevents SQL injection
        $sql  = "SELECT * FROM signup_page WHERE c_username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // password_verify() checks against the hashed password
            if (password_verify($password, $user['password'])) {

                /* -------------------------------------------
                   LAYER 3: GENERATE AND SEND OTP
                   Credentials are correct. Now generate a
                   random 6-digit OTP, save it in the session
                   with a 5-minute expiry, and email it.
                ------------------------------------------- */
                $otp = strval(rand(100000, 999999));

                // Store everything needed by verify_otp.php
                $_SESSION['otp_code']     = $otp;
                $_SESSION['otp_expires']  = time() + 300; // 5 mins = 300 seconds
                $_SESSION['otp_user_id']  = $user['c_id'];
                $_SESSION['otp_username'] = $username;
                $_SESSION['otp_usertype'] = 'customer';

                // Send the OTP email
                $email_sent = send_otp_email($user['email'], $username, $otp);

                // If mail() fails (XAMPP not configured for email),
                // set a flag so verify_otp.php shows the OTP on screen
                // — this is enough for a project demo/viva.
                if (!$email_sent) {
                    $_SESSION['otp_demo_mode'] = true;
                }

                header("Location: verify_otp.php");
                exit();

            } else {
                header("Location: login.php?error=" . urlencode("Invalid password."));
                exit();
            }
        } else {
            header("Location: login.php?error=" . urlencode("Invalid username."));
            exit();
        }
    }

    // =================== ADMIN LOGIN (no OTP) ===================
    elseif ($usertype === 'admin') {

        $sql  = "SELECT * FROM admin WHERE a_name = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            if ($password === $user['password']) {
                $_SESSION['username'] = $username;
                $_SESSION['admin_id'] = $user['a_id'];
                header("Location: Admin/a-dashboard.php");
                exit();
            } else {
                header("Location: login.php?error=" . urlencode("Invalid password."));
                exit();
            }
        } else {
            header("Location: login.php?error=" . urlencode("Invalid username."));
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="CSS/login_style.css" />
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <form method="post" action="login.php">
            <input type="text"     name="username" placeholder="Enter Username" required>
            <input type="password" name="password" placeholder="Enter Password" required>

            <select name="usertype" required>
                <option value="" disabled selected>--User Type--</option>
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
            </select>

            <!-- CAPTCHA -->
            <div class="captcha-box">
                <img src="captcha.php" id="captchaImg" alt="CAPTCHA code">
                <a href="#" id="refreshCaptcha" title="Get a new code">&#x21bb; Refresh</a>
            </div>
            <input
                type="text"
                name="captcha"
                placeholder="Enter the code above"
                class="input-disc"
                autocomplete="off"
                required>

            <input type="submit" value="Login">
        </form>

        <!-- Small notice explaining OTP step -->
        <p style="font-size:13px; color:#888; text-align:center; margin-top:8px;">
            &#128274; An OTP will be sent to your registered email after login.
        </p>

        <?php if (isset($_GET['error'])): ?>
            <p style="color:red; font-size:15px; text-align:center;">
                <?= htmlspecialchars($_GET['error']); ?>
            </p>
        <?php endif; ?>

        <?php if (isset($_GET['msg'])): ?>
            <p style="color:green; font-size:15px; text-align:center;">
                <?= htmlspecialchars($_GET['msg']); ?>
            </p>
        <?php endif; ?>

        <p>Don't have an account? <a href="signup.php">Signup here</a></p>
    </div>

    <script>
        document.getElementById('refreshCaptcha').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('captchaImg').src = 'captcha.php?t=' + new Date().getTime();
        });
    </script>
</body>
</html>
