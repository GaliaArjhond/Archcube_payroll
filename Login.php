<?php
session_start();
include('database.php');  // Include the database connection

// Check if the form has been submitted for login or forgot password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        // Login Process
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Query to check if the username and password are correct
        $query = "SELECT * FROM Employees WHERE username = :username AND password = :password";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['username' => $username, 'password' => md5($password)]);  // MD5 hashing for password comparison (you might want to use password_hash for better security)

        if ($stmt->rowCount() > 0) {
            // Login successful, start session
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");  // Redirect to the dashboard
            exit;
        } else {
            $login_error = "Invalid username or password.";
        }
    } elseif (isset($_POST['forgot_username']) && isset($_POST['security_question']) && isset($_POST['answer'])) {
        // Forgot Password Process
        $username = $_POST['forgot_username'];
        $question_id = $_POST['security_question'];
        $answer = $_POST['answer'];

        // Query to check if the username exists
        $query = "SELECT emp_id FROM Employees WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['username' => $username]);

        if ($stmt->rowCount() > 0) {
            $emp_id = $stmt->fetch()['emp_id'];

            // Query to check if the answer matches for the given security question
            $query = "SELECT * FROM Security_Answers WHERE emp_id = :emp_id AND question_id = :question_id AND answer = :answer";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['emp_id' => $emp_id, 'question_id' => $question_id, 'answer' => $answer]);

            if ($stmt->rowCount() > 0) {
                // If answer matches, send reset link (for simplicity, directly reset password here)
                $password_reset_message = "Answer correct. You can now reset your password.";
                // You can implement a password reset form or logic here.
            } else {
                $security_error = "Incorrect answer to the security question.";
            }
        } else {
            $username_error = "Username not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    <?php if (!isset($_GET['action']) || $_GET['action'] == 'login'): ?>
        <!-- Login Form -->
        <form action="login.php" method="POST">
            <label for="username">Username</label><br>
            <input type="text" name="username" id="username" required><br><br>
            
            <label for="password">Password</label><br>
            <input type="password" name="password" id="password" required><br><br>
            
            <input type="submit" value="Login">
        </form>

        <br>
        <a href="login.php?action=forgot_password">Forgot Password?</a>

        <?php if (isset($login_error)): ?>
            <p style="color: red;"><?php echo $login_error; ?></p>
        <?php endif; ?>

    <?php elseif (isset($_GET['action']) && $_GET['action'] == 'forgot_password'): ?>
        <!-- Forgot Password Form -->
        <h2>Forgot Password</h2>
        <form action="login.php" method="POST">
            <label for="forgot_username">Username</label><br>
            <input type="text" name="forgot_username" id="forgot_username" required><br><br>

            <label for="security_question">Security Question</label><br>
            <select name="security_question" id="security_question" required>
                <?php
                    // Fetch security questions from database
                    $query = "SELECT * FROM Security_Questions";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                    while ($row = $stmt->fetch()) {
                        echo "<option value='" . $row['question_id'] . "'>" . $row['question_text'] . "</option>";
                    }
                ?>
            </select><br><br>

            <label for="answer">Answer</label><br>
            <input type="text" name="answer" id="answer" required><br><br>

            <input type="submit" value="Submit">
        </form>

        <br>
        <a href="login.php">Back to Login</a>

        <?php if (isset($security_error)): ?>
            <p style="color: red;"><?php echo $security_error; ?></p>
        <?php endif; ?>
        <?php if (isset($username_error)): ?>
            <p style="color: red;"><?php echo $username_error; ?></p>
        <?php endif; ?>
        <?php if (isset($password_reset_message)): ?>
            <p style="color: green;"><?php echo $password_reset_message; ?></p>
        <?php endif; ?>

    <?php endif; ?>
</body>
</html>
