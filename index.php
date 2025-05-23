<?php
session_start();
$pdo = require_once 'config/database.php';

class User
{
  private $pdo;
  private $username;
  private $password;

  public function __construct($pdo, $username, $password)
  {
    $this->pdo = $pdo;
    $this->username = $username;
    $this->password = $password;
  }

  public function authenticateAdmin()
  {
    $query = "SELECT * FROM users WHERE username = :username AND role = 'admin'";
    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':username', $this->username);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($this->password, $admin['password'])) {
      return $admin;
    }
    return false;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  $user = new User($pdo, $username, $password);

  $admin = $user->authenticateAdmin();

  if ($admin) {
    $_SESSION['username'] = $admin['username'];
    $_SESSION['role'] = $admin['role'];
    $_SESSION['userId'] = $admin['userId'];

    $actionTypeId = 1;
    $systemlog_stmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
    $systemlog_stmt->execute([$admin['userId'], $actionTypeId]);

    echo "<script>
            alert('Welcome, " . htmlspecialchars($admin['username']) . "!');
            window.location.href = '../includes/dashboard.php';
        </script>";
    exit;
  } else {
    echo "<script>alert('Invalid admin credentials.');</script>";
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
        <div class="empAttendance">
          <a href="../includes/rfid_attendance_ui.html">Employee Attendance</a>
        </div>
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