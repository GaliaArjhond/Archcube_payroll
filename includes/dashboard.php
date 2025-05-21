<?php
date_default_timezone_set('Asia/Manila');
$pdo = include '../config/database.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$today = new DateTime();
$tomorrow = $today->modify('+1 day')->format('j');

// Payroll reminders for admin
if ($tomorrow == 15 || $tomorrow == 30) {
    $adminMessage = "Reminder: Payroll is due tomorrow!";
    $stmt = $pdo->prepare("INSERT INTO notifications (employeeId, message, notifyDate, visibleTo) VALUES (NULL, ?, NOW(), 'admin')");
    $stmt->execute([$adminMessage]);
}

// Government contributions due tomorrow for employees
$stmt = $pdo->prepare("SELECT contributionTypeId, contributionTypeName FROM govtContributionTypes WHERE dueDay = ? AND isActive = TRUE");
$stmt->execute([$tomorrow]);
$contributionsDue = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($contributionsDue) {
    $employees = $pdo->query("SELECT employeeId FROM employees")->fetchAll(PDO::FETCH_ASSOC);
    $insertStmt = $pdo->prepare("INSERT INTO notifications (employeeId, message, notifyDate, visibleTo) VALUES (?, ?, NOW(), 'employee')");
    foreach ($contributionsDue as $contribution) {
        $message = "Reminder: The payment for {$contribution['contributionTypeName']} is due tomorrow.";
        foreach ($employees as $emp) {
            $insertStmt->execute([$emp['employeeId'], $message]);
        }
    }
}

$userId = $_SESSION['userId'];
$userRole = $_SESSION['role'];

// Fetch unread notifications for this admin
$stmt = $pdo->prepare("SELECT * FROM notifications 
    WHERE (employeeId IS NULL OR employeeId = ?) 
    AND (visibleTo = ? OR visibleTo = 'both') 
    AND isRead = FALSE 
    ORDER BY notifyDate DESC");
$stmt->execute([$userId, $userRole]);
$notifications = $stmt->fetchAll();
?>

<html lang="en">

<head>
    <link rel="stylesheet" href="../assets/css/dashboardStyle.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
</head>

<body>
    <div class="side_bar">
        <h1>Archcube Payroll</h1>
        <div class="side_bar_container">
            <div class="side_bar_item">
                <a href="../includes/dashboard.php">Dashboard</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/user_management2.php">Employee Management</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/attendance.php">Attendance</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/Payroll_Mangement.php">Payroll Management</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/deduc&benefits.php">Deductions & Benefits Management</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/payslip.php">Payslip Generator</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/reports.php">Summary Reports</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/setting.php">Settings</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/logout.php" class="logout" onclick="return confirmLogout();">Log Out</a>
            </div>
        </div>
    </div>

    <?php if (!empty($notifications)): ?>
        <div class="dashboard-notification">
            <?php foreach ($notifications as $notif): ?>
                <div><?= htmlspecialchars($notif['message']) ?></div>
            <?php endforeach; ?>
        </div>
        <?php
        // Mark as read after displaying
        $notifIds = array_column($notifications, 'notificationId');
        if (!empty($notifIds)) {
            $ids = implode(',', array_map('intval', $notifIds));
            $pdo->exec("UPDATE notifications SET isRead = 1 WHERE notificationId IN ($ids)");
        }
        ?>
    <?php endif; ?>

    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to log out?');
        }
    </script>

</body>

</html>