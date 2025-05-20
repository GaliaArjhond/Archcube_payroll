<?php
session_start();
include('database.php'); // Include the PDO database connection file
if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    $actionTypeId = 2; // Logout action
    $logout_time = date('Y-m-d H:i:s');

    $systemlog_stmt = $conn->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (:userId, :actionTypeId, :timestamp)");
    $systemlog_stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $systemlog_stmt->bindParam(':actionTypeId', $actionTypeId);
    $systemlog_stmt->bindParam(':timestamp', $logout_time);
    $systemlog_stmt->execute();
}

session_destroy();
header("Location: login.html");
exit();
