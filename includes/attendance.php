<?php
date_default_timezone_set('Asia/Manila');
$pdo = include '../config/database.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Get filter values from GET or set defaults
$show_ent = isset($_GET['show_ent']) ? (int)$_GET['show_ent'] : 10;
$search = isset($_GET['search_input']) ? trim($_GET['search_input']) : '';
$view = isset($_GET['view_select']) ? $_GET['view_select'] : 'all';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

$popupMessage = null; // Initialize popup message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rfidCode'])) {
    $uid = $_POST['rfidCode'];
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    // Find the rfidCodeId from rfid_cards
    $stmt = $pdo->prepare("SELECT rfidCodeId FROM rfid_cards WHERE rfidCode = ?");
    $stmt->execute([$uid]);
    $rfidCard = $stmt->fetch();

    if ($rfidCard) {
        // Find the employee with this rfidCodeId
        $stmt = $pdo->prepare("SELECT employeeId, name FROM employees WHERE rfidCodeId = ?");
        $stmt->execute([$rfidCard['rfidCodeId']]);
        $employee = $stmt->fetch();

        if ($employee) {
            $employeeId = $employee['employeeId'];
            $employeeName = $employee['name'];

            // Check if already checked in today
            $stmt = $pdo->prepare("SELECT * FROM attendance WHERE employeeId = ? AND attendanceDate = ?");
            $stmt->execute([$employeeId, $currentDate]);
            $existing = $stmt->fetch();

            if (!$existing) {
                // First scan today = check in
                $stmt = $pdo->prepare("INSERT INTO attendance (employeeId, attendanceDate, timeIn) VALUES (?, ?, ?)");
                $stmt->execute([$employeeId, $currentDate, $currentTime]);

                // Optional: Log to system logs
                if (isset($_SESSION['userId'])) {
                    $logStmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
                    $logStmt->execute([$_SESSION['userId'], 20]); // 20 = Check-In
                }

                $popupMessage = "Check-in recorded for " . addslashes($employeeName) . ".";
            } elseif ($existing && !$existing['timeOut']) {
                // Second scan = check out
                $stmt = $pdo->prepare("UPDATE attendance SET timeOut = ? WHERE attendanceId = ?");
                $stmt->execute([$currentTime, $existing['attendanceId']]);

                // Optional: Log to system logs
                if (isset($_SESSION['userId'])) {
                    $logStmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
                    $logStmt->execute([$_SESSION['userId'], 21]); // 21 = Check-Out
                }

                $popupMessage = "Check-out recorded for " . addslashes($employeeName) . ".";
            } else {
                $popupMessage = $employeeName . " has already checked in and out today.";
            }
        } else {
            $popupMessage = "RFID UID not assigned to any employee.";
        }
    } else {
        $popupMessage = "RFID UID not found.";
    }
}

?>

<html lang="en">

<head>
    <link rel="stylesheet" href="../assets/css/attendanceStyle.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Attendance</title>
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
        <h2>Attendance Management</h2>
        <div class="attendance_form">
            <form method="get" id="attendanceForm">

                <div class="download_group">
                    <button type="submit" name="download_attendance" class="download_button">Download Attendance</button>
                    <button type="button" onclick="printAttendance()" class="download_button">Print Attendance</button>
                    <a href="../includes/schedule.php">Edit Schedule</a>
                </div>

                <div class="tools_group">
                    <div class="left-group">
                        <label for="show_ent">Show</label>
                        <select name="show_ent" id="show_ent">
                            <?php for ($i = 1; $i <= 20; $i++): ?>
                                <option value="<?= $i ?>" <?= $show_ent == $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>

                        <div class="date_range_group">
                            <label for="from_date">From</label>
                            <input type="date" name="from_date" id="from_date" value="<?= htmlspecialchars($from_date) ?>">
                            <label for="to_date">To</label>
                            <input type="date" name="to_date" id="to_date" value="<?= htmlspecialchars($to_date) ?>">
                        </div>

                    </div>
                    <div class="right-group">

                        <div class="view_group">
                            <label for="view_select">View: </label>
                            <input type="radio" id="view_all" name="view_select" value="all" <?= $view == 'all' ? 'checked' : '' ?>>
                            <label for="view_all">All</label>
                            <input type="radio" id="view_today" name="view_select" value="today" <?= $view == 'today' ? 'checked' : '' ?>>
                            <label for="view_today">Today</label>
                            <input type="radio" id="view_week1" name="view_select" value="1_week" <?= $view == '1_week' ? 'checked' : '' ?>>
                            <label for="view_week1">1 Week</label>
                            <input type="radio" id="view_week2" name="view_select" value="2_week" <?= $view == '2_week' ? 'checked' : '' ?>>
                            <label for="view_week2">2 Weeks</label>
                            <input type="radio" id="view_month" name="view_select" value="month" <?= $view == 'month' ? 'checked' : '' ?>>
                            <label for="view_month">Month</label>
                            <input type="radio" id="view_year" name="view_select" value="year" <?= $view == 'year' ? 'checked' : '' ?>>
                            <label for="view_year">Year</label>
                        </div>

                        <label for="search_input">Search</label>
                        <input type="text" name="search_input" id="search_input" placeholder="Search by Employee ID or Name" value="<?= htmlspecialchars($search) ?>">

                    </div>
                </div>

                <div class="table_section">
                    <table class="data_table">
                        <tr>
                            <th>Date</th>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Position</th>
                            <th>Check-In Time</th>
                            <th>Status</th>
                            <th class="no-print">Actions</th>
                        </tr>

                        <?php
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Get filter values from GET or set defaults
                        $show_ent = isset($_GET['show_ent']) ? (int)$_GET['show_ent'] : 10;
                        $search = isset($_GET['search_input']) ? trim($_GET['search_input']) : '';
                        $view = isset($_GET['view_select']) ? $_GET['view_select'] : 'all';
                        $from_date = $_GET['from_date'] ?? '';
                        $to_date = $_GET['to_date'] ?? '';

                        // Build WHERE clause
                        $where = [];
                        $params = [];

                        if ($search !== '') {
                            $where[] = "(e.name LIKE ? OR e.employeeId LIKE ?)";
                            $params[] = "%$search%";
                            $params[] = "%$search%";
                        }

                        if ($view === 'today') {
                            $where[] = "a.attendanceDate = CURDATE()";
                        } elseif ($view === '1_week') {
                            $where[] = "a.attendanceDate >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                        } elseif ($view === '2_week') {
                            $where[] = "a.attendanceDate >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)";
                        } elseif ($view === 'month') {
                            $where[] = "MONTH(a.attendanceDate) = MONTH(CURDATE()) AND YEAR(a.attendanceDate) = YEAR(CURDATE())";
                        } elseif ($view === 'year') {
                            $where[] = "YEAR(a.attendanceDate) = YEAR(CURDATE())";
                        }

                        if ($from_date) {
                            $where[] = "a.attendanceDate >= ?";
                            $params[] = $from_date;
                        }
                        if ($to_date) {
                            $where[] = "a.attendanceDate <= ?";
                            $params[] = $to_date;
                        }

                        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
                        $limitSql = $show_ent > 0 ? "LIMIT $show_ent" : '';

                        $stmt = $pdo->prepare("
                            SELECT a.*, e.name, p.positionName
                            FROM attendance a
                            JOIN employees e ON a.employeeId = e.employeeId
                            LEFT JOIN position p ON e.positionId = p.positionId
                            $whereSql
                            ORDER BY a.attendanceDate DESC, a.timeIn DESC
                            $limitSql
                        ");
                        $stmt->execute($params);

                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // Determine status and row color
                            if (!$row['timeIn'] && !$row['timeOut']) {
                                $status = 'Absent';
                                $rowClass = 'status-absent';
                            } elseif ($row['timeIn'] && !$row['timeOut']) {
                                $status = 'Checked In';
                                $rowClass = 'status-in';
                            } else {
                                $status = 'Checked Out';
                                $rowClass = 'status-out';
                            }
                            echo "<tr class=\"$rowClass\">";
                            echo "<td>" . htmlspecialchars($row['attendanceDate']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['employeeId']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['positionName'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row['timeIn'] ?? '-') . "</td>";
                            $status = $row['status'] ?? 'Absent';
                            $statusClass = '';
                            if ($status === 'On Time') $statusClass = 'status-ontime';
                            elseif ($status === 'Late') $statusClass = 'status-late';
                            elseif ($status === 'Absent') $statusClass = 'status-absent';

                            echo "<td class=\"$statusClass\">" . htmlspecialchars($status) . "</td>";
                            echo "<td class=\"no-print\"><a href=\"#\">Edit</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </form>
        </div>
    </div>

    <!-- Add this near the top of your <body> -->
    <div id="popupMessage" class="popup-message" style="display:none;">
        <span id="popupText"></span>
        <button class="popup-close">OK</button>
    </div>
</body>

</html>