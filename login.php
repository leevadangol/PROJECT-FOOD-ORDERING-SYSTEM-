<?php
session_start();
require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $usertype = $_POST["usertype"] ?? '';

    // customer
    if ($usertype === 'customer') {
        $sql = "SELECT * FROM signup_page WHERE c_username = '$username'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password with hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION["username"] = $username;

            $_SESSION['customer_id'] = $user['c_id'];

            mysqli_close($conn);

            header("Location: home.php");
            exit();
        } else {
            // Invalid password
            mysqli_close($conn);
            header("Location: login.php?error=Invalid%20password");
            exit();
        }
    } else {
        // Invalid username
        mysqli_close($conn);
        header("Location: login.php?error=Invalid%20username");
        exit();
    }
}

// admin
elseif ($usertype === 'admin') {
    $sql = "SELECT * FROM admin WHERE a_name = '$username'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if ($password === $user['password']) { // plain text comparison
            $_SESSION['username'] = $username;
            $_SESSION['admin_id'] = $user['a_id'];
            header("Location: Admin/a-dashboard.php");
            exit();
        } else {
            header("Location: login.php?error=Invalid%20password");
            exit();
        }

    } else {
        header("Location: login.php?error=Invalid%20username");
        exit();
    }
}
}
?>

<!-- --------------HTML----------------- -->

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
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <select name="usertype" required>
                <option value="" disabled selected>--User Type--</option>
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
            </select>
            <input type="submit" value="Login">
        </form>

        <?php if (isset($_GET['error'])): ?>
            <p style="color: red; font-size: 15px; text-align: center;">
                <?= htmlspecialchars($_GET['error']); ?>
            </p>
        <?php endif; ?>

        <p>Don't have an account? <a href="signup.php">Signup here</a></p>
    </div>
</body>
</html>
