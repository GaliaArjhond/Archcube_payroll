<?php
$pdo = include '../config/database.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Fetch employees for dropdown
$employees = $pdo->query("SELECT employeeId, name, positionId, basicSalary, otRate FROM employees")->fetchAll(PDO::FETCH_ASSOC);
$positions = [
    1 => 'Architect',
    2 => 'Engineer',
    3 => 'Foreman',
    4 => 'Laborer'
];

// Initialize variables
$salaryPerDay = $daysWorked = $salary = $otRate = $totalOtHours = $otEarnings = $totalPayment = $salaryAdvance = $otherDeductions = $totalDeductions = $netPay = 0;
$selectedEmployeeId = $_POST['employee_id'] ?? '';
$startDate = $_POST['start_date'] ?? '';
$endDate = $_POST['end_date'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selectedEmployeeId && $startDate && $endDate) {
    if (!strtotime($startDate) || !strtotime($endDate)) {
        echo "<p style='color:red;'>Invalid date format.</p>";
        exit;
    }

    // Get daily rate and OT rate
    $empStmt = $pdo->prepare("SELECT basicSalary, otRate FROM employees WHERE employeeId = ?");
    $empStmt->execute([$selectedEmployeeId]);
    $empData = $empStmt->fetch();
    if (!$empData) {
        echo "<p style='color:red;'>Employee data not found.</p>";
        exit;
    }
    $salaryPerDay = $empData['basicSalary'] ?? 0;
    $otRate = $empData['otRate'] ?? 100;

    // Days Worked
    $daysStmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE employeeId = ? AND attendanceDate BETWEEN ? AND ? AND timeIn IS NOT NULL AND timeOut IS NOT NULL");
    $daysStmt->execute([$selectedEmployeeId, $startDate, $endDate]);
    $daysWorked = $daysStmt->fetchColumn();

    // OT Hours
    $stmt = $pdo->prepare("SELECT timeIn, timeOut FROM attendance WHERE employeeId = ? AND attendanceDate BETWEEN ? AND ? AND timeIn IS NOT NULL AND timeOut IS NOT NULL");
    $stmt->execute([$selectedEmployeeId, $startDate, $endDate]);
    $attendances = $stmt->fetchAll();

    $defaultWorkHours = 8;
    $totalOtHours = 0;
    foreach ($attendances as $row) {
        $timeIn = new DateTime($row['timeIn']);
        $timeOut = new DateTime($row['timeOut']);
        $interval = $timeIn->diff($timeOut);
        $hoursWorked = $interval->h + ($interval->i / 60);
        if ($hoursWorked > $defaultWorkHours) {
            $totalOtHours += $hoursWorked - $defaultWorkHours;
        }
    }

    // Calculations
    $salary = $salaryPerDay * $daysWorked;
    $otEarnings = $totalOtHours * $otRate;
    $totalPayment = $salary + $otEarnings;

    $salaryAdvance = floatval($_POST['salary_advance'] ?? 0);
    $otherDeductions = floatval($_POST['other_deductions'] ?? 0);
    $totalDeductions = $salaryAdvance + $otherDeductions;
    $netPay = $totalPayment - $totalDeductions;

    if (isset($_POST['action']) && $_POST['action'] === 'payslip') {
        $payrollTypeId = $_POST['payroll_type'] ?? null;

        $insert = $pdo->prepare("INSERT INTO payslips 
            (employeeId, payrollTypeId, payrollPeriodID, startDate, endDate, daysWorked, otHours, salary, otEarnings, deductions, netPay) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([
            $selectedEmployeeId,
            $payrollTypeId,
            $selectedPayrollPeriodId,
            $startDate,
            $endDate,
            $daysWorked,
            $totalOtHours,
            $salary,
            $otEarnings,
            $totalDeductions,
            $netPay
        ]);

        if ($insert->rowCount()) {
            echo "<p style='color:green;'>Payslip saved successfully.</p>";
        }
    }
}
$payrollTypes = $pdo->query("SELECT PayrollTypeId, PayrollTypeName FROM payrolltype")->fetchAll(PDO::FETCH_ASSOC);
$payrollPeriods = $pdo->query("
    SELECT p.payrollPeriodID, t.PayrollTypeName, p.cutOffFrom, p.cutOffTo, p.payrollDate, p.year, p.month, p.noOfDays
    FROM payrollperiod p
    JOIN payrolltype t ON p.payrollTypeID = t.PayrollTypeId
    WHERE p.status = 'Active'
    ORDER BY p.payrollDate DESC
")->fetchAll(PDO::FETCH_ASSOC);

$selectedPayrollPeriodId = $_POST['payroll_period'] ?? null;
$payrollPeriod = null;
if ($selectedPayrollPeriodId) {
    $stmt = $pdo->prepare("SELECT * FROM payrollperiod WHERE payrollPeriodID = ?");
    $stmt->execute([$selectedPayrollPeriodId]);
    $payrollPeriod = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($payrollPeriod) {
        $startDate = $payrollPeriod['cutOffFrom'];
        $endDate = $payrollPeriod['cutOffTo'];
        $payrollTypeId = $payrollPeriod['payrollTypeID'];
    }
}
?>

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
                <a href="../includes/logout.php" class="logout" onclick="return confirmLogout();">Log Out</a>
            </div>
        </div>
    </div>

    <div class="main_content">
        <h2>Payslip Generator</h2>
        <form method="post">
            <div class="form-toolbar">
                <div class="button-group">
                    <button type="submit" name="action" value="payroll" class="btn btn-blue">Print Payroll</button>
                    <button type="submit" name="action" value="payslip" class="btn btn-green">Print Payslip</button>
                </div>
            </div>
            <div class="pay-period-group">
                <label>Start Date:</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>" required>
                <label>End Date:</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>" required>
            </div>
            <div class="employee-select-group">
                <label for="employee_id">Select Employee:</label>
                <select id="employee_id" name="employee_id" required>
                    <option value="">-- Select Employee --</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['employeeId'] ?>"
                            data-name="<?= htmlspecialchars($emp['name']) ?>"
                            data-empid="<?= $emp['employeeId'] ?>"
                            data-position="<?= $positions[$emp['positionId']] ?? 'Unknown' ?>"
                            data-salary="<?= $emp['basicSalary'] ?? 0 ?>"
                            data-otrate="<?= $emp['otRate'] ?? 100 ?>"
                            <?= ($selectedEmployeeId == $emp['employeeId']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($emp['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="employee-details">
                <label>Employee Name:</label>
                <input type="text" id="emp_name" readonly>
                <label>Employee Number:</label>
                <input type="text" id="emp_num" readonly>
                <label>Position:</label>
                <input type="text" id="emp_position" readonly>
            </div>
            <div class="pay-period-group">
                <label for="payroll_period">Payroll Period:</label>
                <select id="payroll_period" name="payroll_period" required>
                    <option value="">-- Select Payroll Period --</option>
                    <?php foreach ($payrollPeriods as $period): ?>
                        <option value="<?= $period['payrollPeriodID'] ?>"
                            <?= (isset($_POST['payroll_period']) && $_POST['payroll_period'] == $period['payrollPeriodID']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($period['PayrollTypeName']) ?> |
                            <?= htmlspecialchars($period['cutOffFrom']) ?> to <?= htmlspecialchars($period['cutOffTo']) ?> |
                            <?= htmlspecialchars($period['month']) ?> <?= htmlspecialchars($period['year']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <table class="payslip_table" style="width:100%; border-collapse:collapse; margin-top:20px;">
                <tr>
                    <th colspan="4" style="text-align:center; font-size:18px;">Payslip for the Month of <span id="month_year"><?= date('F Y') ?></span></th>
                </tr>
                <tr>
                    <td><b>Employee Name:</b></td>
                    <td><input type="text" id="emp_name_disp" readonly style="border:none; background:transparent;"></td>
                    <td><b>Employee Number:</b></td>
                    <td><input type="text" id="emp_num_disp" readonly style="border:none; background:transparent;"></td>
                </tr>
                <tr>
                    <td><b>Position:</b></td>
                    <td colspan="3"><input type="text" id="emp_position_disp" readonly style="border:none; background:transparent; width:100%;"></td>
                </tr>
                <tr>
                    <th colspan="2">Earnings</th>
                    <th colspan="2">Deductions</th>
                </tr>
                <tr>
                    <td>Salary</td>
                    <td>
                        <input type="text" name="salary"
                            value="<?= htmlspecialchars(number_format($salary, 2)) ?>"
                            style="width:80px;">
                    </td>
                    <td>Salary Advance</td>
                    <td><input type="text" name="salary_advance" value="0" style="width:80px;"></td>
                </tr>
                <tr>
                    <td>Days of Work</td>
                    <td><input type="text" name="days_work" value="<?= $daysWorked ?? 0 ?>" style="width:80px;"></td>
                    <td>Other Deductions</td>
                    <td><input type="text" name="other_deductions" value="0" style="width:80px;"></td>
                </tr>
                <tr>
                    <td>OT Hours</td>
                    <td><input type="text" name="ot_hours"
                            value="<?= htmlspecialchars(number_format($totalOtHours, 2)) ?>"
                            style="width:80px;"></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>OT Rate</td>
                    <td><input type="text" name="ot_rate" value="<?= $otRate ?? 100 ?>" style="width:80px;"></td>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <th>Total Payment</th>
                    <td><input type="text" name="total_payment"
                            value="<?= htmlspecialchars(number_format($totalPayment, 2)) ?>"
                            style="width:80px;"></td>
                    <th>Total Deductions</th>
                    <td><input type="text" name="total_deductions"
                            value="<?= htmlspecialchars(number_format($totalDeductions, 2)) ?>"
                            style="width:80px;"></td>
                </tr>
                <tr>
                    <th colspan="3" style="text-align:right;">NET PAY:</th>
                    <th><input type="text" name="net_pay"
                            value="<?= htmlspecialchars(number_format($netPay, 2)) ?>"
                            style="width:80px;"></th>
                </tr>
            </table>
        </form>
    </div>
    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to log out?');
        }

        function fillEmployeeDetails() {
            var sel = document.getElementById('employee_id');
            var opt = sel.options[sel.selectedIndex];
            if (!opt) return;

            var name = opt.getAttribute('data-name') || '';
            var num = opt.getAttribute('data-empid') || '';
            var pos = opt.getAttribute('data-position') || '';
            var salary = parseFloat(opt.getAttribute('data-salary')) || 0;
            var otRate = parseFloat(opt.getAttribute('data-otrate')) || 0;

            document.getElementById('emp_name').value = name;
            document.getElementById('emp_num').value = num;
            document.getElementById('emp_position').value = pos;

            document.getElementById('emp_name_disp').value = name;
            document.getElementById('emp_num_disp').value = num;
            document.getElementById('emp_position_disp').value = pos;

            // Auto-fill salary and OT rate fields
            document.querySelector('input[name="ot_rate"]').value = otRate.toFixed(2);
            // Only fill salary if days_work is set, otherwise just show daily rate
            var days = parseFloat(document.querySelector('input[name="days_work"]').value) || 0;
            const salaryField = document.querySelector('input[name="salary"]');
            if (!salaryField.value || parseFloat(salaryField.value) === 0) {
                salaryField.value = (salary * days).toFixed(2);
            }
        }

        function updateSalary() {
            var sel = document.getElementById('employee_id');
            var opt = sel.options[sel.selectedIndex];
            var salary = parseFloat(opt.getAttribute('data-salary')) || 0;
            var days = parseFloat(document.querySelector('input[name="days_work"]').value) || 0;
            document.querySelector('input[name="salary"]').value = (salary * days).toFixed(2);
        }

        document.getElementById('employee_id').addEventListener('change', fillEmployeeDetails);
        window.addEventListener('DOMContentLoaded', () => {
            fillEmployeeDetails(); // run on load if a selection is already made
            document.querySelector('input[name="days_work"]').addEventListener('input', updateSalary);
        });
    </script>
</body>

</html>
<?php
echo "<pre>";
print_r($_POST);
echo "</pre>";
?>