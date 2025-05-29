<?php
header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$pdo = include '../config/database.php'; // Adjust path if needed

$response = ['success' => false, 'message' => 'Unknown error'];

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rfid_code'])) {
    $uid = $_POST['rfid_code'];
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    // 1. Find RFID card
    $stmt = $pdo->prepare("SELECT rfidCodeId FROM rfid_cards WHERE rfidCode = ?");
    $stmt->execute([$uid]);
    $rfidCard = $stmt->fetch();

    if ($rfidCard) {
        // 2. Find employee
        $stmt = $pdo->prepare("SELECT employeeId, name, profileImageUrl, site FROM employees WHERE rfidCodeId = ?");
        $stmt->execute([$rfidCard['rfidCodeId']]);
        $employee = $stmt->fetch();

        if ($employee) {
            $employeeId = $employee['employeeId'];
            $employeeName = $employee['name'];
            $profileImageUrl = $employee['profileImageUrl'];
            $employeeSite = $employee['site'];

            // 3. Check attendance for today
            $stmt = $pdo->prepare("SELECT * FROM attendance WHERE employeeId = ? AND attendanceDate = ?");
            $stmt->execute([$employeeId, $currentDate]);
            $attendance = $stmt->fetch();

            if (!$attendance) {
                // First scan = check-in
                $stmt = $pdo->prepare("INSERT INTO attendance (employeeId, attendanceDate, timeIn) VALUES (?, ?, ?)");
                $stmt->execute([$employeeId, $currentDate, $currentTime]);

                if (isset($_SESSION['userId'])) {
                    $logStmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
                    $logStmt->execute([$_SESSION['userId'], 20]); // 20 = Check-In
                }

                $response = [
                    'success' => true,
                    'message' => "Check-in recorded for $employeeName.",
                    'name' => $employeeName,
                    'profile' => $profileImageUrl,
                    'site' => $employeeSite
                ];
            } elseif ($attendance && !$attendance['timeOut']) {

                $stmt = $pdo->prepare("UPDATE attendance SET timeOut = ? WHERE attendanceId = ?");
                $stmt->execute([$currentTime, $attendance['attendanceId']]);

                if (!empty($_SESSION['userId'])) {
                    $logStmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
                    $logStmt->execute([$_SESSION['userId'], 21]); // 21 = Check-Out
                }

                $response = [
                    'success' => true,
                    'message' => "Check-out recorded for $employeeName.",
                    'name' => $employeeName,
                    'profile' => $profileImageUrl,
                    'site' => $employeeSite
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

echo json_encode($response);
exit;
