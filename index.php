<?php
session_start();
include('config/database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  try {
    // Check user credentials
    $query = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['username'] = $username;
      $_SESSION['role'] = $user['role'];

      // Record the login time in the audit trail
      $login_time = date('Y-m-d H:i:s');
      $log_query = "INSERT INTO audit_trail (username, action, timestamp) VALUES (:username, 'login', :timestamp)";
      $log_stmt = $conn->prepare($log_query);
      $log_stmt->bindParam(':username', $username);
      $log_stmt->bindParam(':timestamp', $login_time);
      $log_stmt->execute();

      // Redirect to the appropriate landing page based on role
      if ($user['role'] == 'admin') {
        header("Location: admin_landing.php");
        exit;
      } else {
        header("Location: user_landing.php");
        exit;
      }
    } else {
      echo "<script>assets/js/showPopup('Invalid username or password!');</script>";
    }
  } catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="assets/css/login.css" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Archcube Payroll</title>
</head>

<body>
  <main>
    <div class="login-container">
      <h1>Archcube Payroll</h1>
      <div class="welcome-container">
        <h2>Welcome Back!</h2>
        <p class="welcome">Please enter your credentials to log in.</p>
      </div>
      <form action="index.php" method="post">
        <div class="input-container">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" required />
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required />
        </div>
        <div class="options-container">
          <div class="remember-me">
            <input type="checkbox" id="remember-me" name="remember-me" />
            <label for="remember-me">Remember me</label>
          </div>
          <div class="forgot-password">
            <a href="forgot_pass.html">Forgot Password?</a>
          </div>
        </div>
        <button type="submit">Login</button>
        <footer>
          <p>&copy; 2023 Archcube. All rights reserved.</p>
          <p>Privacy Policy | Terms of Service</p>
        </footer>
      </form>
    </div>
  </main>
  <div>
    <picture>
      <source srcset="assets/images/archcube_logo.png" type="image/png" />
      <img
        src="assets/images/archcube_logo.png"
        alt="archcube_logo"
        class="logo" />
    </picture>
  </div>
</body>

</html>