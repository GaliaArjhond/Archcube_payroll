<?php
session_start();
$conn = include('config/database.php'); // Make sure database.php returns $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  try {
    $query = "SELECT * FROM users WHERE username = :username AND role = 'admin'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
      $_SESSION['username'] = $admin['username'];
      $_SESSION['role'] = $admin['role'];
      $_SESSION['userId'] = $admin['userId']; // Store for future use if needed

      // Insert into systemLogs
      $actionTypeId = 1; // Assuming 1 = Login
      $systemlog_stmt = $conn->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
      $systemlog_stmt->execute([$admin['userId'], $actionTypeId]);

      header("Location: ../includes/dashboard.php");
      exit;
    } else {
      echo "<script>alert('Invalid admin credentials.');</script>";
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