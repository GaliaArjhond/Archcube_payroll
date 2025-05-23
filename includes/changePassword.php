<?php
session_start();
$pdo = require_once '../config/database.php';


// Check if form is submitted
if (isset($_POST['changePassword'])) {
  $userId = $_SESSION['userId']; // assuming you're storing logged-in user ID in session
  $currentPassword = $_POST['currentPassword'];
  $newPassword = $_POST['newPassword'];
  $confirmPassword = $_POST['confirmPassword'];

  // Fetch user's current hashed password from DB
  $stmt = $pdo->prepare("SELECT password FROM users WHERE userId = ?");
  $stmt->execute([$userId]);
  $user = $stmt->fetch();

  if ($user && password_verify($currentPassword, $user['password'])) {
    if ($newPassword === $confirmPassword) {
      $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
      $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE userId = ?");
      $updateStmt->execute([$hashedPassword, $userId]);
      $message = "Password updated successfully!";
    } else {
      $message = "New passwords do not match.";
    }
  } else {
    $message = "Incorrect current password.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f6f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .change-password-container {
      background-color: #ffffff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    .change-password-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    .change-password-container input[type="password"] {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
    }

    .change-password-container button {
      width: 100%;
      padding: 12px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      margin-top: 15px;
    }

    .change-password-container button:hover {
      background-color: #0056b3;
    }

    .status-message {
      text-align: center;
      margin-top: 10px;
      color: red;
    }
  </style>
</head>

<body>

  <div class="change-password-container">
    <h2>Change Password</h2>
    <?php if (isset($message)): ?>
      <div class="status-message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form action="" method="POST">
      <input type="password" name="currentPassword" placeholder="Current Password" required>
      <input type="password" name="newPassword" placeholder="New Password" required>
      <input type="password" name="confirmPassword" placeholder="Confirm New Password" required>
      <button type="submit" name="changePassword">Update Password</button>

    </form>

    <div style="margin-top: 20px; text-align: right;">
      <a href="/Archube/Archcube_payroll/includes/dashboard.php" class="btn btn-danger" style="background-color: #dc3545; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
        ⬅ Back to Dashboard
      </a>

    </div>

    </a>
  </div>

  </div>

</body>

</html>