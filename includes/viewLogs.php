<?php
session_start();
$conn = include('../config/database.php');

$sql = "SELECT 
            l.logId,
            u.username, 
            a.actionName, 
            l.timestamp
        FROM systemLogs l
        JOIN users u ON l.userId = u.userId
        JOIN actionTypes a ON l.actionTypeId = a.actionTypeId
        ORDER BY l.timestamp DESC";


$stmt = $conn->query($sql);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                <a href="../includes/logout.php" class="logout">Log Out</a>
            </div>
        </div>
    </div>

    <div class="main_content">
        <div class="download_group">
            <button type="submit" name="download_attendance" class="download_button">Download Logs</button>
            <button type="submit" name="print_attendance" class="download_button">Print Logs</button>
        </div>

        <div class="tools_group">
            <div class="left-group">
                <label for="show_ent">Show</label>
                <select name="show_ent" id="show_ent">
                    <?php for ($i = 1; $i <= 20; $i++) echo "<option value=\"$i\">$i</option>"; ?>
                </select>

                <label for="ent_date">Date</label>
                <input type="date" name="ent_date" id="ent_date" required value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="right-group">
                <label for="search_input">Search</label>
                <input type="text" name="search_input" id="search_input" placeholder="Search by Employee ID or Name">
            </div>
        </div>


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

</body>

</html>