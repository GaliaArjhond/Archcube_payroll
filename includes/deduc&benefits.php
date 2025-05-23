<?php
$pdo = include '../config/database.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../assets/css/d&cStyle.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Deductions & Benefits</title>
</head>
<body>
    <div class="side_bar">
        <h1>Archcube Payroll</h1>
        <div class="side_bar_container">
            <div class="side_bar_item"><a href="../includes/dashboard.php">Dashboard</a></div>
            <div class="side_bar_item"><a href="../includes/user_management2.php">Employee Management</a></div>
            <div class="side_bar_item"><a href="../includes/attendance.php">Attendance</a></div>
            <div class="side_bar_item"><a href="../includes/Payroll_Mangement.php">Payroll Management</a></div>
            <div class="side_bar_item"><a href="../includes/deduc&benefits.php">Deductions & Benefits Management</a></div>
            <div class="side_bar_item"><a href="../includes/payslip.php">Payslip Generator</a></div>
            <div class="side_bar_item"><a href="../includes/reports.php">Summary Reports</a></div>
            <div class="side_bar_item"><a href="../includes/setting.php">Settings</a></div>
            <div class="side_bar_item"><a href="../includes/logout.php" class="logout" onclick="return confirmLogout();">Log Out</a></div>
        </div>
    </div>

    <div class="main_content">
        <h2>Deductions & Benefits Management</h2>
        <div class="select_employee">
            <label for="employee_select">Select Employee</label>
            <select id="employee_select" name="employee_select" required>
                <option value="">Select Employee</option>
                <?php
                $stmt = $pdo->query("SELECT employeeId, name FROM employees ORDER BY name");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . htmlspecialchars($row['employeeId']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                }
                ?>
            </select>
        </div>

        <form id="deduc_benefits_form" method="post" action="">
            <h2>Attendance</h2>
            <div class="attendance_form">
                <div class="tools_input">
                    <label for="leave_input">Leave Credits</label>
                    <input type="number" id="leave_input" name="leave_input" placeholder="Enter leave credits" value="" required>
                    <label for="lateMin_input">Lates (mins)</label>
                    <input type="number" id="lateMin_input" name="lateMin_input" placeholder="Enter late minutes" value="" required>
                    <label for="absences_input">Absences (days)</label>
                    <input type="number" id="absences_input" name="absences_input" placeholder="Enter Absences days" value="" required>
                </div>
            </div>

            <h2>Government Contributions</h2>
            <div class="govtContributions_form">
                <div class="table_section">
                    <table class="data_table" id="govtContributions_table">
                        <tr>
                            <th>Type</th>
                            <th>Employee Share</th>
                            <th>Employer Share</th>
                            <th>Status</th>
                        </tr>
                        <!-- Filled dynamically -->
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
                    <button type="submit" name="add_deduction">Add deduction</button>
                </div>
                <div class="table_section">
                    <table class="data_table" id="deductions_table">
                        <tr>
                            <th>Deduction Name</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Balance</th>
                            <th>Actions</th>
                        </tr>
                        <!-- Filled dynamically -->
                    </table>
                </div>
            </div>

            <h2>Advance Payment</h2>
            <div class="advance_form">
                <div class="tools_input">
                    <label for="advance_amount">Advance Amount (Given on Wed)</label>
                    <input type="number" id="advance_amount" name="advance_amount" value="" required>
                    <label for="deduct_amount">Deduct This Amount (Each Saturday)</label>
                    <input type="number" id="deduct_amount" name="deduct_amount" value="" required>
                    <button type="submit" name="save_advance">Save Advance Plan</button>
                </div>
            </div>

            <h2>Payroll Summary</h2>
            <div class="payrollSum_form">
                <div class="table_section">
                    <table class="data_table" id="payroll_summary_table">
                        <tr>
                            <th>Payroll Period</th>
                            <th>Basic Salary</th>
                            <th>Total Deductions</th>
                            <th>Net Pay</th>
                            <th>Status</th>
                        </tr>
                        <!-- Filled dynamically -->
                    </table>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('employee_select').addEventListener('change', function() {
            const employeeId = this.value;
            if (!employeeId) {
                // Clear all tables and inputs if no employee selected
                clearFields();
                return;
            }

            fetch('deduc_benefits_ajax.php?employee_id=' + employeeId)
                .then(response => response.json())
                .then(data => {
                    // Fill attendance fields
                    document.getElementById('leave_input').value = data.attendance.leave_credits || '';
                    document.getElementById('lateMin_input').value = data.attendance.late_minutes || '';
                    document.getElementById('absences_input').value = data.attendance.absences || '';

                    // Fill government contributions table
                    const govtTable = document.getElementById('govtContributions_table');
                    // Remove old rows except header
                    while (govtTable.rows.length > 1) govtTable.deleteRow(1);
                    data.govData.forEach(row => {
                        let tr = govtTable.insertRow();
                        tr.insertCell(0).textContent = row.type;
                        tr.insertCell(1).textContent = row.employee_share;
                        tr.insertCell(2).textContent = row.employer_share;
                        tr.insertCell(3).textContent = row.status;
                    });

                    // Fill deductions table
                    const deducTable = document.getElementById('deductions_table');
                    while (deducTable.rows.length > 1) deducTable.deleteRow(1);
                    data.deductions.forEach(row => {
                        let tr = deducTable.insertRow();
                        tr.insertCell(0).textContent = row.name;
                        tr.insertCell(1).textContent = row.amount;
                        tr.insertCell(2).textContent = row.status;
                        tr.insertCell(3).textContent = row.balance;
                        const actionsCell = tr.insertCell(4);
                        actionsCell.innerHTML = '<button disabled>Edit</button> <button disabled>Delete</button>';
                    });

                    // Fill advance fields
                    document.getElementById('advance_amount').value = data.advance.advance_amount || '';
                    document.getElementById('deduct_amount').value = data.advance.deduct_amount || '';

                    // Fill payroll summary table
                    const payrollTable = document.getElementById('payroll_summary_table');
                    while (payrollTable.rows.length > 1) payrollTable.deleteRow(1);
                    data.payrollSummary.forEach(row => {
                        let tr = payrollTable.insertRow();
                        tr.insertCell(0).textContent = row.payroll_period;
                        tr.insertCell(1).textContent = row.basic_salary;
                        tr.insertCell(2).textContent = row.total_deductions;
                        tr.insertCell(3).textContent = row.net_pay;
                        tr.insertCell(4).textContent = row.status;
                    });
                })
                .catch(err => {
                    console.error('Error fetching employee data:', err);
                    clearFields();
                });
        });

        function clearFields() {
            document.getElementById('leave_input').value = '';
            document.getElementById('lateMin_input').value = '';
            document.getElementById('absences_input').value = '';

            const clearTable = (tableId) => {
                const table = document.getElementById(tableId);
                while (table.rows.length > 1) table.deleteRow(1);
            };

            clearTable('govtContributions_table');
            clearTable('deductions_table');
            clearTable('payroll_summary_table');

            document.getElementById('advance_amount').value = '';
            document.getElementById('deduct_amount').value = '';
        }

        function confirmLogout() {
            return confirm('Are you sure you want to log out?');
        }
    </script>
</body>
</html>
