<?php
$pdo = include '../config/database.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
?>

<html lang="en">

<head>
    <link rel="stylesheet" href="../assets/css/reportsStyle.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Summary Reports</title>

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

        <div class="main_content">
        <div class="header">
            <h2>Summary Reports</h2>
        </div>
        <div class="content">
            <h3>Reports</h3>
            
            <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Employee ID</th>
                <th>Base Salary</th>
                <th>Allowances</th>
                <th>Deductions</th>
                <th>Net Pay</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Jane Doe</td>
                <td>EMP001</td>
                <td>3,000.00</td>
                <td>200.00</td>
                <td>150.00</td>
                <td>3,050.00</td>
            </tr>
            <tr>
                <td>John Smith</td>
                <td>EMP002</td>
                <td>2,800.00</td>
                <td>180.00</td>
                <td>120.00</td>
                <td>2,860.00</td>
            </tr>
            <tr>
                <td>Mary Johnson</td>
                <td>EMP003</td>
                <td>3,200.00</td>
                <td>250.00</td>
                <td>200.00</td>
                <td>3,250.00</td>
            </tr>
            <!-- Add more employee rows as needed -->
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">TOTAL</td>
                <td>9,000.00</td>
                <td>630.00</td>
                <td>470.00</td>
                <td>9,160.00</td>
            </tr>
        </tfoot>
    </table>
    </div>
    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to log out?');
        }
    </script>

</body>

</html>