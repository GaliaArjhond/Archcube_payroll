<?php
session_start();
$conn = include('config/database.php');

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    $actionTypeId = 2;
    $logout_time = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("
        INSERT INTO systemLogs (userId, actionTypeId, timestamp) 
        VALUES (:userId, :actionTypeId, :timestamp)
    ");
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':actionTypeId', $actionTypeId, PDO::PARAM_INT);
    $stmt->bindParam(':timestamp', $logout_time);
    $stmt->execute();
}

session_destroy();
header("Location: ../index.php");
exit();
