<?php
session_start();
include('archcube.php'); // Include the PDO database connection file

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        // Check if username exists in the database
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Set session variables
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
                if ($user['role'] === 'admin') {
                    header("Location: admin_landing.php");
                } else {
                    header("Location: user_landing.php");
                }
                exit(); // Ensure no further code is executed after redirection
            } else {
                // Invalid password
                $_SESSION['error'] = "Invalid username or password!";
                header("Location: login_page.php"); // Redirect back to login page
                exit();
            }
        } else {
            // Username not found
            $_SESSION['error'] = "Invalid username or password!";
            header("Location: login_page.php"); // Redirect back to login page
            exit();
        }
    } catch (PDOException $e) {
        // Log the error and display a generic error message
        error_log("Database error: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred. Please try again later.";
        header("Location: login_page.php"); // Redirect back to login page
        exit();
    }
} else {
    // If accessed without submitting the form, redirect to login page
    header("Location: login_page.php");
    exit();
}
