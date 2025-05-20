<?php
$pdo = include '../config/database.php';
session_start();

if (!isset($_SESSION['role'])) {
    header('Location: ../index.php');
    exit();
}

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];

    $actionTypeId = 2;
    $logout_time = date('Y-m-d H:i:s');

    try {
        $systemlog_stmt = $conn->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (:userId, :actionTypeId, :timestamp)");
        $systemlog_stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $systemlog_stmt->bindParam(':actionTypeId', $actionTypeId, PDO::PARAM_INT);
        $systemlog_stmt->bindParam(':timestamp', $logout_time);
        $systemlog_stmt->execute();
    } catch (PDOException $e) {
        error_log("Failed to insert logout log: " . $e->getMessage());
        // Optionally display error for debugging
        echo "Error inserting logout log: " . $e->getMessage();
    }
} else {
    error_log("Logout attempted but userId session not set");
}

$_SESSION = [];
session_destroy();
header("Location: ../index.php");
exit();
