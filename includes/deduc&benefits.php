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
    <link rel="stylesheet" href="../assets/css/d&cStyle.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Deductions & Benefits</title>
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
        <h2>Deductions & Benefits Management</h2>

        <div class="select_employee">
            <label for="employee_select">Select Employee</label>
            <form method="post" action="">
                <select id="employee_select" name="employee_select" required>
                    <option value="">Select Employee</option>
                    <option value="1">John Doe</option>
                    <option value="2">Jane Smith</option>
                    <option value="3">Alice Johnson</option>
                </select>
                <button type="submit">Load</button>
            </form>
        </div>

        <H2>Attendance</H2>
        <div class="attendance_form">
            <div class="tools_input">
                <label for="leave_input">Leave Credits</label>
                <input type="number" id="leave_input" name="leave_input" placeholder="Enter leave credits" value="10" required>
                <label for="lateMin_input">Lates (mins)</label>
                <input type="number" id="lateMin_input" name="lateMin_input" placeholder="Enter late minutes" value="1" required>
                <label for="absences_input">Absences (days)</label>
                <input type="number" id="absences_input" name="absences_input" placeholder="Enter Absences days" value="1" required>
            </div>
        </div>

        <h2>Government Contributions</h2>
        <div class="govtContributions_form">
            <div class="table_section">
                <table class="data_table">
                    <tr>
                        <th>Type</th>
                        <th>Employee Share</th>
                        <th>Employer Share</th>
                        <th>Status</th>
                    </tr>
                </table>
            </div>
        </div>

        <h2>Custom Deductions</h2>
        <div class="customDeduc_form">
            <div class="tools_input">
                <label for="deduc_type">Type</label>
                <select id="deduc_type" name="deduc_type" required>
                    <option value="">Select Deduction Type</option>
                    <option value="loan">Loan</option>
                    <option value="insurance">Insurance</option>
                    <option value="tax">Tax</option>
                    <option value="other">Other</option>
                </select>
                <label for="deduc_amount">Amount</label>
                <input type="number" id="deduc_amount" name="deduc_amount" placeholder="Enter deduction amount" required>
                <button type="submit">Add deduction</button>
            </div>
            <div class="table_section">
                <table class="data_table">
                    <tr>
                        <th>Deduction Name</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Balance</th>
                        <th>Actions</th>
                    </tr>
                </table>
            </div>
        </div>

        <div class="payrollSum_form">
            <h2>Payroll Summary</h2>
            <div class="table_section">
                <table class="data_table">
                    <tr>
                        <th>Payroll Period</th>
                        <th>Basic Salary</th>
                        <th>Total Deductions</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                    </tr>
            </div>
</body>

</html>