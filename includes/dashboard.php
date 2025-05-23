<?php
date_default_timezone_set('Asia/Manila');
$pdo = include '../config/database.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$todayDate = date('Y-m-d');

$today = new DateTime();
$tomorrow = $today->modify('+1 day')->format('j');

// Payroll reminders for admin
if ($tomorrow == 15 || $tomorrow == 30) {
    $adminMessage = "Reminder: Payroll is due tomorrow!";
    $stmt = $pdo->prepare("INSERT INTO notifications (employeeId, message, notifyDate, visibleTo) VALUES (NULL, ?, NOW(), 'admin')");
    $stmt->execute([$adminMessage]);
}

// Government contributions due tomorrow for employees
$stmt = $pdo->prepare("SELECT contributionTypeId, contributionTypeName FROM govt_contribution_types WHERE dueDay = ? AND isActive = TRUE");
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

// Fetch upcoming birthdays
$birthdays = $pdo->query("
    SELECT name, DATE_FORMAT(birthDate, '%M %d') AS birthdate 
    FROM employees 
    WHERE MONTH(birthDate) = MONTH(CURDATE()) 
      AND DAY(birthDate) >= DAY(CURDATE())
    ORDER BY DAY(birthDate)
    LIMIT 5
")->fetchAll();

// Count pending leave requests (set to 0 if table doesn't exist)
try {
    $pendingLeaves = $pdo->query("
        SELECT COUNT(*) FROM leaves 
        WHERE status = 'pending'
    ")->fetchColumn();
} catch (PDOException $e) {
    $pendingLeaves = 0;
}

// Count pending advance requests
$pendingAdvance = $pdo->query("
    SELECT COUNT(*) FROM advance_payments 
    WHERE status = 'pending'
")->fetchColumn();

$recentDeductions = $pdo->query("
    SELECT dt.name AS deductionType, e.name, d.amount, d.createdAt 
    FROM deductions d
    JOIN employees e ON d.employeeId = e.employeeId
    JOIN deduction_types dt ON d.deductionTypeId = dt.deductionTypeId
    ORDER BY d.createdAt DESC
    LIMIT 5
")->fetchAll();

$recentAdvances = $pdo->query("
    SELECT e.name, a.amount, a.dateRequested, a.status
    FROM advance_payments a
    JOIN employees e ON a.employeeId = e.employeeId
    ORDER BY a.dateRequested DESC
    LIMIT 5
")->fetchAll();

// Calculate average time-in for today
$avgTimeIn = $pdo->query("
    SELECT AVG(TIME(timeIn)) FROM attendance 
    WHERE attendanceDate = '$todayDate'
")->fetchColumn();

// Total Employees
$totalEmployees = $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();

// On Time Today
$onTimeCount = $pdo->query("
    SELECT COUNT(DISTINCT employeeId) FROM attendance 
    WHERE attendanceDate = '$todayDate' AND timeIn <= '09:00:00'
")->fetchColumn();

// Absent Today
$absentCount = $pdo->query("
    SELECT COUNT(*) FROM employees e
    WHERE NOT EXISTS (
        SELECT 1 FROM attendance a
        WHERE a.employeeId = e.employeeId AND a.attendanceDate = '$todayDate'
    )
")->fetchColumn();
?>

<html lang="en">

<head>
    <link rel="stylesheet" href="../assets/css/dashboardStyle.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <div class="main_content">
        <h1 style="font-size:2em; color:#074799; margin-bottom:10px;">
            Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!
        </h1>
        <div class="card_container">
            <div class="card_TotlaEmployees card">
                <h2>Total Employees</h2>
                <p><?= $totalEmployees ?></p>
                <span>Total active employees as of today.</span>
            </div>
            <div class="card_OnTime card">
                <h2>On Time Today</h2>
                <p><?= $onTimeCount ?></p>
                <span>Employees who checked in on or before 9:00 AM today.</span>
            </div>
            <div class="card chart-card">
                <h2>Attendance Overview</h2>
                <canvas id="attendancePie" width="320" height="180"></canvas>
            </div>
            <div class="card chart-card">
                <h2>Recent Advance Requests</h2>
                <canvas id="advanceBar" width="320" height="180"></canvas>
            </div>
            <div class="card_absent card">
                <h2>Absent Today</h2>
                <p><?= $absentCount ?></p>
                <span>Employees with no attendance record today.</span>
            </div>
            <div class="card_nextPayroll card">
                <h2>Next Payroll Date</h2>
                <p>
                    <?php
                    $day = (int)date('j');
                    if ($day < 15) {
                        $nextPayroll = date('F 15, Y');
                    } elseif ($day < 30) {
                        $nextPayroll = date('F 30, Y');
                    } else {
                        $nextPayroll = date('F 15, Y', strtotime('+1 month'));
                    }
                    echo $nextPayroll;
                    ?>
                </p>
                <span>Upcoming payroll release date.</span>
            </div>
            <div class="card_links card">
                <h2>Useful Links</h2>
                <ul style="padding-left:0; list-style:none;">
                    <li><a href="../includes/employeeAttendanceView.php">View Attendance</a></li>
                    <li><a href="../includes/Payroll_Mangement.php">Manage Payroll</a></li>
                    <li><a href="../includes/deduc&benefits.php">Manage Deductions</a></li>
                    <li><a href="../includes/reports.php">Summary Reports</a></li>
                </ul>
                <span>Quick access to key modules.</span>
            </div>
            <div class="card_birthdays card">
                <h2>Upcoming Birthdays</h2>
                <ul>
                    <?php foreach (array_slice($birthdays, 0, 3) as $b): ?>
                        <li><?= htmlspecialchars($b['name']) ?> - <?= $b['birthdate'] ?></li>
                    <?php endforeach; ?>
                    <?php if (count($birthdays) > 3): ?>
                        <li style="color:#888;">...and more</li>
                    <?php endif; ?>
                </ul>
                <span>Plan for birthday perks or leaves.</span>
            </div>
            <div class="card_pendingLeaves card">
                <h2>Pending Leave Requests</h2>
                <p><?= $pendingLeaves ?></p>
                <span>Leaves awaiting approval.</span>
            </div>
            <div class="card_pendingAdvance card">
                <h2>Pending Advance Requests</h2>
                <p><?= $pendingAdvance ?></p>
                <span>Advance salary requests pending.</span>
            </div>
            <div class="card_deductions card">
                <h2>Recent Deductions</h2>
                <ul>
                    <?php foreach ($recentDeductions as $d): ?>
                        <li><?= htmlspecialchars($d['name']) ?> - <?= htmlspecialchars($d['deductionType']) ?>: ₱<?= number_format($d['amount'], 2) ?> (<?= $d['createdAt'] ?>)</li>
                    <?php endforeach; ?>
                </ul>
                <span>Latest payroll deductions.</span>
            </div>
            <div class="card_advanceRequests card">
                <h2>Recent Advance Requests</h2>
                <ul>
                    <?php foreach (array_slice($recentAdvances, 0, 3) as $a): ?>
                        <li><?= htmlspecialchars($a['name']) ?>: ₱<?= number_format($a['amount'], 2) ?> (<?= $a['dateRequested'] ?>) - <?= htmlspecialchars($a['status']) ?></li>
                    <?php endforeach; ?>
                    <?php if (count($recentAdvances) > 3): ?>
                        <li style="color:#888;">...and more</li>
                    <?php endif; ?>
                </ul>
                <span>Latest advance salary requests.</span>
            </div>
            <div class="card_avgTimeIn card">
                <h2>Average Time-In Today</h2>
                <p><?= $avgTimeIn ? date('h:i A', strtotime($avgTimeIn)) : 'N/A' ?></p>
                <span>Average employee arrival time.</span>
            </div>
        </div>

        <?php if (!empty($notifications)): ?>
            <div class="dashboard-notification">
                <span style="font-size:1.3em; margin-right:8px;">🔔</span>
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

            // Pie chart data: On Time, Late, Absent
            const attendanceData = {
                labels: ['On Time', 'Late', 'Absent'],
                datasets: [{
                    data: [
                        <?= (int)$onTimeCount ?>,
                        <?= (int)($totalEmployees - $onTimeCount - $absentCount) ?>,
                        <?= (int)$absentCount ?>
                    ],
                    backgroundColor: ['#074799', '#0563c1', '#ff4d4d'],
                    borderWidth: 1
                }]
            };

            // Bar chart data: Recent Advance Requests
            const advanceLabels = [
                <?php foreach ($recentAdvances as $a) {
                    echo "'" . htmlspecialchars($a['name']) . "',";
                } ?>
            ];
            const advanceAmounts = [
                <?php foreach ($recentAdvances as $a) {
                    echo (float)$a['amount'] . ",";
                } ?>
            ];

            // Render Pie Chart
            new Chart(document.getElementById('attendancePie'), {
                type: 'pie',
                data: attendanceData,
                options: {
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Render Bar Chart
            new Chart(document.getElementById('advanceBar'), {
                type: 'bar',
                data: {
                    labels: advanceLabels,
                    datasets: [{
                        label: 'Advance Amount',
                        data: advanceAmounts,
                        backgroundColor: '#074799'
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>

</body>

</html>