<?php
session_start();
$conn = include('../config/database.php');

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    $actionTypeId = 2;

    $stmt = $conn->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
    $stmt->execute([$userId, $actionTypeId]);
}

session_destroy();
header("Location: ../index.php");
exit();
