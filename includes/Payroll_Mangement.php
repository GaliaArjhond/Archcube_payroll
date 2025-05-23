<?php
$pdo = include '../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (
    isset($_POST['payrollDate'], $_POST['payrollType'], $_POST['cutoffFrom'], $_POST['cutoffTo'], $_POST['year'], $_POST['month'], $_POST['status'], $_POST['noOfDays']) &&
    !empty($_POST['payrollDate']) && !empty($_POST['payrollType']) && !empty($_POST['cutoffFrom']) && !empty($_POST['cutoffTo']) && !empty($_POST['year']) && !empty($_POST['month']) && !empty($_POST['status'])
  ) {
    try {
      $stmt = $pdo->prepare("INSERT INTO payrollPeriod (payrollTypeID, cutOffFrom, cutOffTo, payrollDate, year, month, noOfDays, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->execute([
        $_POST['payrollType'],
        $_POST['cutoffFrom'],
        $_POST['cutoffTo'],
        $_POST['payrollDate'],
        $_POST['year'],
        $_POST['month'],
        $_POST['noOfDays'],
        $_POST['status']
      ]);
      header("Location: Payroll_Mangement.php?success=Payroll+period+created+successfully");
      exit();
    } catch (PDOException $e) {
      error_log("Error inserting payroll period: " . $e->getMessage());
      header("Location: Payroll_Mangement.php?error=Database+error");
      exit();
    }
  } else {
    header("Location: Payroll_Mangement.php?error=Missing+fields");
    exit();
  }
}

// Fetch payroll types
$payrollTypes = $pdo->query("SELECT PayrollTypeId, PayrollTypeName FROM payrollType")->fetchAll(PDO::FETCH_ASSOC);


// Fetch employees including dailyRate (make sure your employees table has dailyRate or use a default value)
$employees = $pdo->query("SELECT employeeId, name, IFNULL(dailyRate, 500) AS dailyRate FROM employees")->fetchAll(PDO::FETCH_ASSOC);

// Get latest payroll period cutoffs for attendance calculation
$payrollPeriod = $pdo->query("SELECT cutOffFrom, cutOffTo FROM payrollPeriod ORDER BY payrollPeriodID DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$cutOffFrom = $payrollPeriod['cutOffFrom'] ?? null;
$cutOffTo = $payrollPeriod['cutOffTo'] ?? null;

// Calculate payroll details for each employee
foreach ($employees as &$emp) {
  $employeeId = $emp['employeeId'];
  if ($cutOffFrom && $cutOffTo) {
    $stmt = $pdo->prepare("
            SELECT
                COUNT(*) AS totalDays,
                SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absences,
                SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) AS lateDays
            FROM attendance
            WHERE employeeId = ? AND attendanceDate BETWEEN ? AND ?
        ");
    $stmt->execute([$employeeId, $cutOffFrom, $cutOffTo]);
    $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

    $emp['totalDays'] = $attendance['totalDays'] ?? 0;
    $emp['absences'] = $attendance['absences'] ?? 0;
    $emp['lateDays'] = $attendance['lateDays'] ?? 0;
  } else {
    $emp['totalDays'] = 0;
    $emp['absences'] = 0;
    $emp['lateDays'] = 0;
  }

  $emp['workDays'] = $emp['totalDays'] - $emp['absences'];

  $dailyRate = $emp['dailyRate'];
  $emp['basePay'] = $emp['workDays'] * $dailyRate;
  $emp['absenceDeduction'] = $emp['absences'] * $dailyRate;

  // Placeholder values for deductions, overtime, advances, etc.
  $emp['otPay'] = 0;
  $emp['sss'] = 0;
  $emp['philhealth'] = 0;
  $emp['pagibig'] = 0;
  $emp['advances'] = 0;
  $emp['otherDeductions'] = 0;

  $emp['grossPay'] = $emp['basePay'] + $emp['otPay'];
  $emp['netPay'] = $emp['grossPay'] - ($emp['sss'] + $emp['philhealth'] + $emp['pagibig'] + $emp['advances'] + $emp['otherDeductions'] + $emp['absenceDeduction']);
}
unset($emp);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="../assets/css/Payroll_Mangement_Style.css" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Payroll Management</title>
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
    <main>
      <div class="payroll_period">
        <label for="payroll_period">Payroll Period:</label>
        <button class="payperiod_button" onclick="openOverlay()">Create new</button>
      </div>
      <div class="top">

        <div class="payperiod">
          <h3>Pay Period:</h3>
          <select name="payrollTypeFilter" id="payrollTypeFilter" required>
            <option value="">Select</option>
            <?php foreach ($payrollTypes as $type): ?>
              <option value="<?= htmlspecialchars($type['PayrollTypeId']) ?>">
                <?= htmlspecialchars($type['PayrollTypeName']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="top_controls">
          <div class="search_bar">
            <input class="search_input" type="text" placeholder="Search..." />
            <button class="search_button">Search</button>
          </div>
        </div>

      </div>
      <div class="2nd_row">
        <div class="payroll_buttons">
          <button class="generate_button">Generate Payroll</button>
          <button class="download_button">Download Payroll</button>
          <button class="print_button">Print Payroll</button>
        </div>
      </div>

      <div class="employee-table">
        <div class="employee-table-header"></div>
        <table>
          <thead>
            <tr>
              <th>Employee ID</th>
              <th>Name</th>
              <th>Base Pay</th>
              <th>Work Day</th>
              <th>Daily Rate</th>
              <th>OT Pay</th>
              <th>Absences</th>
              <th>SSS</th>
              <th>PhilHealth</th>
              <th>Pag-IBIG</th>
              <th>Gross</th>
              <th>Advances</th>
              <th>Other Deductions</th>
              <th>Net income</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($employees as $emp): ?>
              <tr>
                <td><?= htmlspecialchars($emp['employeeId']) ?></td>
                <td><?= htmlspecialchars($emp['name']) ?></td>
                <td>₱<?= number_format($emp['basePay'], 2) ?></td>
                <td><?= htmlspecialchars($emp['workDays']) ?></td>
                <td>₱<?= number_format($emp['dailyRate'], 2) ?></td>
                <td>₱<?= number_format($emp['otPay'], 2) ?></td>
                <td><?= htmlspecialchars($emp['absences']) ?></td>
                <td>₱<?= number_format($emp['sss'], 2) ?></td>
                <td>₱<?= number_format($emp['philhealth'], 2) ?></td>
                <td>₱<?= number_format($emp['pagibig'], 2) ?></td>
                <td>₱<?= number_format($emp['grossPay'], 2) ?></td>
                <td>₱<?= number_format($emp['advances'], 2) ?></td>
                <td>₱<?= number_format($emp['otherDeductions'], 2) ?></td>
                <td>₱<?= number_format($emp['netPay'], 2) ?></td>
                <td><button>View</button></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Overlay Modal -->
      <div id="payrollOverlay" class="overlay" style="display:none;">
        <div class="overlay-content" style="min-width:350px;">
          <span class="close-btn" onclick="closeOverlay()">&times;</span>
          <h3>PAYROLL PERIOD</h3>
          <form method="POST" action="">
            <label for="payrollDate"><b>Payroll Date</b></label>
            <input type="date" id="payrollDate" name="payrollDate" required style="width:100%;margin-bottom:8px;">

            <label for="payrollType"><b>Payroll Type</b></label>
            <select id="payrollType" name="payrollType" required style="width:100%;margin-bottom:8px;">
              <option value="">Select</option>
              <?php foreach ($payrollTypes as $type): ?>
                <option value="<?= htmlspecialchars($type['PayrollTypeId']) ?>">
                  <?= htmlspecialchars($type['PayrollTypeName']) ?>
                </option>
              <?php endforeach; ?>
            </select>

            <label for="cutoffFrom"><b>Cut-off From</b></label>
            <input type="date" id="cutoffFrom" name="cutoffFrom" required style="width:100%;margin-bottom:8px;">

            <label for="cutoffTo"><b>Cut-off To</b></label>
            <input type="date" id="cutoffTo" name="cutoffTo" required style="width:100%;margin-bottom:8px;">

            <label for="year"><b>Year</b></label>
            <input type="number" id="year" name="year" required min="2000" max="2100" style="width:100%;margin-bottom:8px;">

            <label for="month"><b>Month</b></label>
            <input type="text" id="month" name="month" required placeholder="e.g., May" style="width:100%;margin-bottom:8px;">

            <label for="noOfDays"><b>Number of Days</b></label>
            <input type="number" id="noOfDays" name="noOfDays" required min="1" max="31" style="width:100%;margin-bottom:8px;">

            <label for="status"><b>Status</b></label>
            <select id="status" name="status" required style="width:100%;margin-bottom:8px;">
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>

            <input type="submit" value="Create Payroll Period" style="width:100%;padding:8px;">
          </form>
        </div>
      </div>

    </main>
  </div>

  <script>
    function confirmLogout() {
      return confirm('Are you sure you want to log out?');
    }

    function openOverlay() {
      document.getElementById('payrollOverlay').style.display = 'block';
    }

    function closeOverlay() {
      document.getElementById('payrollOverlay').style.display = 'none';
    }
  </script>
  <script src="../assets/js/payroll.js"></script>
</body>

</html>