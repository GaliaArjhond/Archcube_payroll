<?php
ob_start(); // Start output buffering
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$pdo = include '../config/database.php';
$response = ['success' => false, 'message' => 'Unknown error'];
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rfid_code'])) {
    $uid = $_POST['rfid_code'];
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    $stmt = $pdo->prepare("SELECT rfidCodeId FROM rfid_cards WHERE rfidCode = ?");
    $stmt->execute([$uid]);
    $rfidCard = $stmt->fetch();

    if ($rfidCard) {
        $stmt = $pdo->prepare("
            SELECT e.employeeId, e.name, e.profileImage, ps.siteName
            FROM employees e
            LEFT JOIN projectSites ps ON e.projectSiteId = ps.projectSiteId
            WHERE e.rfidCodeId = ?
        ");
        $stmt->execute([$rfidCard['rfidCodeId']]);
        $employee = $stmt->fetch();

        if ($employee) {
            $employeeId = $employee['employeeId'];
            $employeeName = $employee['name'];

            // 3. Check attendance for today
            $stmt = $pdo->prepare("SELECT * FROM attendance WHERE employeeId = ? AND attendanceDate = ?");
            $stmt->execute([$employeeId, $currentDate]);
            $attendance = $stmt->fetch();

            if (!$attendance) {
                // First scan = check-in
                $stmt = $pdo->prepare("INSERT INTO attendance (employeeId, attendanceDate, timeIn) VALUES (?, ?, ?)");
                $stmt->execute([$employeeId, $currentDate, $currentTime]);

                if (!empty($_SESSION['userId'])) {
                    $logStmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
                    $logStmt->execute([$_SESSION['userId'], 20]);
                }

                $response = [
                    'success' => true,
                    'message' => "Check-in recorded for {$employee['name']}.",
                    'name' => $employee['name'],
                    'profile' => $employee['profileImage'],
                    'site' => $employee['siteName'],
                    'checkInTime' => $currentTime,
                    'checkOutTime' => null
                ];
            } elseif (!$attendance['timeOut']) {
                // Second scan = check-out
                $stmt = $pdo->prepare("UPDATE attendance SET timeOut = ? WHERE attendanceId = ?");
                $stmt->execute([$currentTime, $attendance['attendanceId']]);

                if (!empty($_SESSION['userId'])) {
                    $logStmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
                    $logStmt->execute([$_SESSION['userId'], 21]);
                }

                $response = [
                    'success' => true,
                    'message' => "Check-out recorded for {$employee['name']}.",
                    'name' => $employee['name'],
                    'profile' => $employee['profileImage'],
                    'site' => $employee['siteName'],
                    'checkInTime' => $attendance['timeIn'],
                    'checkOutTime' => $currentTime
                ];
            } else {
                $response = ['success' => false, 'message' => "$employeeName has already checked in and out today."];
            }
        } else {
            $response = ['success' => false, 'message' => "❌ RFID is not linked to any employee."];
        }
    } else {
        $response = ['success' => false, 'message' => "❌ RFID UID not found in the system."];
    }
}

ob_clean(); // Clear any stray output
echo json_encode($response);
exit;
