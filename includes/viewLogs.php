<?php
date_default_timezone_set('Asia/Manila');
$pdo = include '../config/database.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$show_ent = isset($_GET['show_ent']) ? (int)$_GET['show_ent'] : 10;
$search = trim($_GET['search_input'] ?? '');
$ent_date = $_GET['ent_date'] ?? '';

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(u.username LIKE ? OR a.actionName LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($ent_date !== '') {
    $where[] = "DATE(l.timestamp) = ?";
    $params[] = $ent_date;
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$limitSql = $show_ent > 0 ? "LIMIT $show_ent" : '';

$sql = "SELECT 
            l.logId,
            u.username, 
            a.actionName, 
            l.timestamp
        FROM systemLogs l
        JOIN users u ON l.userId = u.userId
        JOIN actionTypes a ON l.actionTypeId = a.actionTypeId
        $whereSql
        ORDER BY l.timestamp DESC
        $limitSql";


$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_SESSION['userId'])) {
    $actionTypeId = 18;
    $systemlog_stmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
    $systemlog_stmt->execute([$_SESSION['userId'], $actionTypeId]);
}

?>


<html lang="en">

<head>
    <link rel="stylesheet" href="../assets/css/viewLogStyle.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View Logs</title>
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
        <form method="get" style="margin-bottom: 20px;">
            <div class="download_group">
                <button type="submit" name="download_attendance" class="download_button">Download Logs</button>
                <button type="button" onclick="printLogs()" class="download_button">Print Logs</button>
            </div>

            <div class="tools_group">
                <div class="left-group">
                    <label for="show_ent">Show</label>
                    <select name="show_ent" id="show_ent" onchange="this.form.submit()">
                        <?php for ($i = 1; $i <= 20; $i++): ?>
                            <option value="<?= $i ?>" <?= $show_ent == $i ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>

                    <label for="ent_date">Date</label>
                    <input type="date" name="ent_date" id="ent_date" value="<?= htmlspecialchars($_GET['ent_date'] ?? date('Y-m-d')) ?>" onchange="this.form.submit()">
                </div>

                <div class="right-group">
                    <label for="search_input">Search</label>
                    <input type="text" name="search_input" id="search_input" placeholder="Search by Username or Action" value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
        </form>

        <div class="table_logs">
            <h2>System Logs</h2>
            <table class="data_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Action</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['logId']) ?></td>
                            <td><?= htmlspecialchars($log['username']) ?></td>
                            <td><?= htmlspecialchars($log['actionName']) ?></td>
                            <td><?= htmlspecialchars($log['timestamp']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to log out?');
        }
    </script>
    <script src="../assets/js/viewlogs.js"></script>
</body>

</html>