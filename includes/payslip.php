<html lang="en">

<head>
    <link rel="stylesheet" href="../assets/css/payslipStyle.css">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Payslip Generator</title>
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
                <a href="" class="logout">Log Out</a>
            </div>
        </div>
    </div>

    <div class="main_content">
        <h2>Payslip Generator</h2>
        <form action="generate_payslip.php" method="post">
            <div class="form-toolbar">
                <div class="pay-period-group">
                    <label for="pay_period">Select Pay Period:</label>
                    <select id="pay_period" name="pay_period" required>
                        <option value="">-- Select Pay Period --</option>
                        <option value="weekly">Weekly</option>
                        <option value="bi-weekly">Bi-Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div class="button-group">
                    <button type="submit" name="action" value="payroll" class="btn btn-blue">Print Payroll</button>
                    <button type="submit" name="action" value="payslip" class="btn btn-green">Print Payslip</button>
                </div>
            </div>
            <table class="payslip_table">
                <thead class="table_header">
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Position</th>
                        <th>Daily Rate</th>
                        <th>Regular Days</th>
                        <th>Overtime Days</th>
                        <th>Overtime Rate</th>
                        <th>SSS</th>
                        <th>PhilHealth</th>
                        <th>Pag-IBIG</th>
                        <th>Withholding Tax</th>
                        <th>Cash Advance</th>
                        <th>Total Government Contributions</th>
                        <th>Total Deductions</th>
                        <th>13th Month</th>
                        <th>Net income</th>
                        <th>Action</th>
                    </tr>
                </thead class="table_body">
                <tbody>
                    <tr>

                    </tr>
                </tbody>
            </table>
        </form>
    </div>
</body>

</html>