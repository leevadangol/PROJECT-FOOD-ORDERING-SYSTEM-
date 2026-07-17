<!--SIGNUP PAGE PHP -->
<?php
session_start();
require_once "db.php";



if ($_SERVER["REQUEST_METHOD"] == "POST") {

  /* ============================================================
     STEP 1: CHECK THE CAPTCHA
     ============================================================
     Same idea as login.php - we check the captcha FIRST, before
     we even look at the database. If it's wrong, we send the
     user straight back with an error and a brand new code.
  */
  $entered_captcha = trim($_POST['captcha'] ?? '');
  $correct_captcha = $_SESSION['captcha_code'] ?? '';

  // Remove the old code right away so each code can only be used once
  unset($_SESSION['captcha_code']);

  if ($entered_captcha === '' || strcasecmp($entered_captcha, $correct_captcha) !== 0) {
    header("Location: signup.php?error=" . urlencode("Incorrect captcha code. Please try again."));
    exit();
  }

  /* ============================================================
     STEP 2: NORMAL SIGNUP LOGIC (captcha already passed)
     ============================================================
  */
  $username = $_POST['username'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirmPassword'];


  /// Check if username already exists
  // (Using a prepared statement with "?" instead of pasting $username
  //  straight into the SQL text - this stops SQL Injection attacks.)
  $checkSql = "SELECT * FROM signup_page WHERE c_username = ?";
  $checkStmt = mysqli_prepare($conn, $checkSql);
  mysqli_stmt_bind_param($checkStmt, "s", $username);
  mysqli_stmt_execute($checkStmt);
  $checkResult = mysqli_stmt_get_result($checkStmt);

  if (mysqli_num_rows($checkResult) > 0) {
    mysqli_close($conn);
    header("Location: signup.php?error=Username%20already%20exists");
    exit();
  }

  // Hash the password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // Again using "?" placeholders instead of pasting values into the SQL text
  $query = "INSERT INTO signup_page (c_username, email, phone, password)
                VALUES (?, ?, ?, ?)";
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $phone, $hashedPassword);
  $result = mysqli_stmt_execute($stmt);

  if ($result) {
    $_SESSION['username'] = $username;
    header("Location: home.php");
    exit();
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}
?>


<!-- ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////// -->
<!-- HTML CODE -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign Up Page</title>
  <link rel="stylesheet" href="CSS/signup_style.css" />
</head>

<body>
  <div class="signup-container">
    <h2>Sign Up</h2>
    <form
      onsubmit="handleSubmit(event)"
      action="signup.php"
      method="POST"
      id="Myform">
      <label for="username">Username</label>
      <input
        type="text"
        id="username"
        name="username"
        class="input-disc"
        placeholder="Enter Username" />

      <label for="email">Email</label>
      <input
        type="email"
        id="email"
        name="email"
        class="input-disc"
        placeholder="Enter Email" />

      <label for="phone">Phone Number</label>
      <input
        type="tel"
        id="phone"
        name="phone"
        class="input-disc"
        placeholder="Enter Phone Number"
        pattern="[0-9]{10}"
        title="Please enter a 10-digit phone number" />


      <label for="password">Password</label>
      <input
        type="password"
        id="password"
        name="password"
        class="input-disc"
        placeholder="Enter Password" />

      <label for="confirmPassword">Confirm Password</label>
      <input
        type="password"
        id="confirmPassword"
        name="confirmPassword"
        class="input-disc"
        placeholder="Enter Confirm Password" />

      <!-- ===================== CAPTCHA SECTION ===================== -->
      <!-- Same captcha.php image used on login.php. It draws a new
           random code every time it's loaded. -->
      <label for="captcha">Enter the code below</label>
      <div class="captcha-box">
        <img src="captcha.php" id="captchaImg" alt="CAPTCHA code">
        <a href="#" id="refreshCaptcha" title="Get a new code">&#x21bb; Refresh</a>
      </div>
      <input
        type="text"
        id="captcha"
        name="captcha"
        class="input-disc"
        placeholder="Enter the code above"
        autocomplete="off" />
      <!-- =================== END CAPTCHA SECTION ==================== -->


      <button type="submit" class="signup-btn">Sign Up</button>
      <br /><br />
      <p id="error">
        <?php     //FOR USER EXIST
        if (isset($_GET['error'])) {
          echo htmlspecialchars($_GET['error']);
        }
        ?>
      </p>
    </form>
    <br>
    <p>Already have an account? <a href="login.php">Login here</a></p>
  </div>


  <!-- ----------------------------------------------------------- -->
  <!-- JAVASCRIPT -->
  <script>
    function handleSubmit(event) {
      event.preventDefault(); //stops from submission
      document.getElementById("error").textContent = "";
      const form = document.getElementById("Myform");
      const formData = new FormData(form);

      const username = formData.get("username");
      const userType = formData.get("userType");
      const email = formData.get("email");
      const password = formData.get("password");
      const confirmPassword = formData.get("confirmPassword");
      const captcha = formData.get("captcha");

      //for username
      if (!username) {
        document.getElementById("error").textContent =
          "*Username is required!";
        return; //submit hunna dindaina
      }

      //for  usertype
      // if (!userType) {
      //   document.getElementById("error").textContent =
      //     "*UserType is required!";
      //   return;
      // }

      //for email
      if (!email) {
        document.getElementById("error").textContent = "*Email is required";
        return;
      }
      //for valid email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!emailRegex.test(email)) {
        document.getElementById("error").textContent = "*Email must be valid";
        return;
      }

      //for password
      if (!password) {
        document.getElementById("error").textContent =
          "*Password is required";
        return;
      }
      //password condition(atleast one lower, upped, digit, and <8  )
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

      if (!passwordRegex.test(password)) {
        document.getElementById("error").textContent =
          "*Password must be at least 8 characters, include one uppercase, one lowercase, and one number.";
        return; //submit hunna dindaina
      }

      //for confirm password
      if (!confirmPassword) {
        document.getElementById("error").textContent =
          "*Confrim Password is required";
        return;
      }
      if (password !== confirmPassword) {
        document.getElementById("error").textContent =
          "*Corfirm Password doesn't match Password";
        return;
      }

      //for captcha
      if (!captcha) {
        document.getElementById("error").textContent =
          "*Please enter the captcha code";
        return;
      }

      console.log({
        username,
        // userType,
        email,
        password
      });

      form.submit(); //action chalaunxa, without this action="home.html" chaldaina
    }

    // Refresh button - loads a brand new captcha picture without
    // reloading the whole page (same as login.php)
    document.getElementById('refreshCaptcha').addEventListener('click', function (e) {
      e.preventDefault();
      document.getElementById('captchaImg').src = 'captcha.php?t=' + new Date().getTime();
    });
  </script>
</body>

</html>
