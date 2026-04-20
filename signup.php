<!--SIGNUP PAGE PHP -->
<?php
session_start();
require_once "db.php";



if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $email = $_POST['email'];
  $phone = $_POST['phone'];
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirmPassword'];


  /// Check if username already exists
  $checkSql = "SELECT * FROM signup_page WHERE c_username = '$username'";
  $checkResult = mysqli_query($conn, $checkSql);

  if (mysqli_num_rows($checkResult) > 0) {
    mysqli_close($conn);
    header("Location: signup.php?error=Username%20already%20exists");
    exit();
  }

  // Hash the password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


  $query = "INSERT INTO signup_page (c_username, email,phone, password)
                VALUES ('$username', '$email','$phone', '$hashedPassword');";

  $result = mysqli_query($conn, $query);

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


      <button type="submit" class="signup-btn">Sign Up</button>
      <br /><br />
      <p id="error">
        <?php     //FOR USER EXIST
        if (isset($_GET['error'])) {
          echo $_GET['error'];
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

      console.log({
        username,
        // userType,
        email,
        password
      });

      form.submit(); //action chalaunxa, without this action="home.html" chaldaina
    }
  </script>
</body>

</html>