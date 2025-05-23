<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
$payrollTypes = $pdo->query("SELECT PayrollTypeId, PayrollTypeName FROM payrolltype")->fetchAll(PDO::FETCH_ASSOC);

// Filter employees by payroll type if filter is set
if (isset($_GET['payrollTypeFilter']) && $_GET['payrollTypeFilter'] !== '') {
  $payrollTypeId = $_GET['payrollTypeFilter'];
  $stmt = $pdo->prepare("
        SELECT e.*, pp.cutOffFrom, pp.cutOffTo, pp.month, pp.year, pt.PayrollTypeName
        FROM employees e
        LEFT JOIN payrollperiod pp ON e.payrollPeriodID = pp.payrollPeriodID
        LEFT JOIN payrolltype pt ON pp.payrollTypeID = pt.PayrollTypeId
        WHERE pp.payrollTypeID = ?
    ");
  $stmt->execute([$payrollTypeId]);
  $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
  // Default: fetch all employees with their payroll period info
  $employees = $pdo->query("
        SELECT e.*, pp.cutOffFrom, pp.cutOffTo, pp.month, pp.year, pt.PayrollTypeName
        FROM employees e
        LEFT JOIN payrollperiod pp ON e.payrollPeriodID = pp.payrollPeriodID
        LEFT JOIN payrolltype pt ON pp.payrollTypeID = pt.PayrollTypeId
    ")->fetchAll(PDO::FETCH_ASSOC);
}

// Filter by payroll period ID if set
$payrollPeriodID = isset($_GET['payrollPeriodID']) ? $_GET['payrollPeriodID'] : '';
if ($payrollPeriodID !== '') {
  $payrollPeriod = $pdo->prepare("SELECT cutOffFrom, cutOffTo FROM payrollperiod WHERE payrollPeriodID = ?");
  $payrollPeriod->execute([$payrollPeriodID]);
  $payrollPeriod = $payrollPeriod->fetch(PDO::FETCH_ASSOC);
} else {
  $payrollPeriod = $pdo->query("SELECT cutOffFrom, cutOffTo FROM payrollperiod ORDER BY payrollPeriodID DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
}
$cutOffFrom = $payrollPeriod['cutOffFrom'] ?? null;
$cutOffTo = $payrollPeriod['cutOffTo'] ?? null;

// Fetch all payroll periods for the filter
$allPeriods = $pdo->query("
    SELECT pp.payrollPeriodID, pt.PayrollTypeName, pp.cutOffFrom, pp.cutOffTo, pp.month, pp.year
    FROM payrollperiod pp
    LEFT JOIN payrolltype pt ON pp.payrollTypeID = pt.PayrollTypeId
    ORDER BY pp.payrollPeriodID DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Find the selected payroll period details for display and table
$selectedPeriod = null;
if ($payrollPeriodID !== '') {
  foreach ($allPeriods as $period) {
    if ($period['payrollPeriodID'] == $payrollPeriodID) {
      $selectedPeriod = $period;
      break;
    }
  }
}

// Fetch all government contributions for all employees
$govtContributions = [];
$stmt = $pdo->query("SELECT employeeId, contributionTypeId, contributionAmount, contributionNumber FROM govtcontributions");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $govtContributions[$row['employeeId']][$row['contributionTypeId']] = [
    'amount' => $row['contributionAmount'],
    'number' => $row['contributionNumber']
  ];
}

// Define your contribution type IDs (adjust if needed)
define('CONTRIB_SSS', 1);
define('CONTRIB_PHILHEALTH', 2);
define('CONTRIB_PAGIBIG', 3);
define('CONTRIB_WITHHOLD', 4); // If you store withholding tax here

// Calculate payroll details for each employee
foreach ($employees as &$emp) {
  $employeeId = $emp['employeeId'];
  if ($cutOffFrom && $cutOffTo) {
    $stmt = $pdo->prepare("
        SELECT
            SUM(CASE WHEN status = 'On Time' THEN 1 ELSE 0 END) AS onTimeDays,
            SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) AS lateDays,
            SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absences,
            COUNT(*) AS totalDays,
            SUM(CASE WHEN TIME(timeOut) > '18:00:00' THEN 1 ELSE 0 END) AS otDays
        FROM attendance
        WHERE employeeId = ? AND attendanceDate BETWEEN ? AND ?
    ");
    $stmt->execute([$employeeId, $cutOffFrom, $cutOffTo]);
    $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

    $emp['onTimeDays'] = $attendance['onTimeDays'] ?? 0;   // On Time
    $emp['lateDays'] = $attendance['lateDays'] ?? 0;       // Late
    $emp['absences'] = $attendance['absences'] ?? 0;       // Absent
    $emp['totalDays'] = $attendance['totalDays'] ?? 0;
    $emp['otDays'] = $attendance['otDays'] ?? 0;           // Overtime days (if timeOut > 6pm)
    $emp['workDays'] = $emp['onTimeDays'] + $emp['lateDays']; // Present + Late = Worked
  } else {
    $emp['onTimeDays'] = 0;
    $emp['lateDays'] = 0;
    $emp['absences'] = 0;
    $emp['totalDays'] = 0;
    $emp['otDays'] = 0;
    $emp['workDays'] = 0;
  }

  $dailyRate = $emp['dailyRate'];
  $emp['basePay'] = $emp['workDays'] * $dailyRate;
  $emp['absenceDeduction'] = $emp['absences'] * $dailyRate;

  $otRate = $dailyRate / 8 * 1.25; // Example: 1.25x hourly rate
  $emp['otPay'] = $emp['otDays'] * $otRate * 2; // assuming 2 hours OT/day
  $emp['advances'] = 0;
  $emp['otherDeductions'] = 0;

  // Use actual government contributions if available, else fallback to calculated/default
  $emp['sss'] = isset($govtContributions[$employeeId][CONTRIB_SSS]['amount'])
    ? $govtContributions[$employeeId][CONTRIB_SSS]['amount']
    : min($emp['basePay'] * 0.045, 900);
  $emp['sss_number'] = $govtContributions[$employeeId][CONTRIB_SSS]['number'] ?? '';

  $emp['philhealth'] = isset($govtContributions[$employeeId][CONTRIB_PHILHEALTH]['amount'])
    ? $govtContributions[$employeeId][CONTRIB_PHILHEALTH]['amount']
    : min($emp['basePay'] * 0.03, 900);
  $emp['philhealth_number'] = $govtContributions[$employeeId][CONTRIB_PHILHEALTH]['number'] ?? '';

  $emp['pagibig'] = isset($govtContributions[$employeeId][CONTRIB_PAGIBIG]['amount'])
    ? $govtContributions[$employeeId][CONTRIB_PAGIBIG]['amount']
    : min($emp['basePay'] * 0.02, 100);
  $emp['pagibig_number'] = $govtContributions[$employeeId][CONTRIB_PAGIBIG]['number'] ?? '';

  $emp['withholdTax'] = isset($govtContributions[$employeeId][CONTRIB_WITHHOLD]['amount'])
    ? $govtContributions[$employeeId][CONTRIB_WITHHOLD]['amount']
    : ($emp['basePay'] * 0.10);
  $emp['withholdTax_number'] = $govtContributions[$employeeId][CONTRIB_WITHHOLD]['number'] ?? '';

  $emp['grossPay'] = $emp['basePay'] + $emp['otPay'];
  $emp['netPay'] = $emp['grossPay'] - (
    $emp['sss'] +
    $emp['philhealth'] +
    $emp['pagibig'] +
    $emp['withholdTax'] +
    $emp['advances'] +
    $emp['otherDeductions'] +
    $emp['absenceDeduction']
  );
}
unset($emp);

/**
 * Get attendance summary for an employee.
 * @param PDO $pdo
 * @param int $employeeId
 * @param string $fromDate (YYYY-MM-DD)
 * @param string $toDate (YYYY-MM-DD)
 * @return array ['onTimeCount' => int, 'workedDays' => int, 'otDays' => int]
 */
function getAttendanceSummary($pdo, $employeeId, $fromDate, $toDate)
{
  // Count worked days (days with attendance)
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE employeeId = ? AND attendanceDate BETWEEN ? AND ?");
  $stmt->execute([$employeeId, $fromDate, $toDate]);
  $workedDays = (int)$stmt->fetchColumn();

  // Count on-time days (timeIn <= 09:00:00)
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE employeeId = ? AND attendanceDate BETWEEN ? AND ? AND timeIn <= '09:00:00'");
  $stmt->execute([$employeeId, $fromDate, $toDate]);
  $onTimeCount = (int)$stmt->fetchColumn();

  // Count OT days (has overtime > 0)
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE employeeId = ? AND attendanceDate BETWEEN ? AND ? AND overtime > 0");
  $stmt->execute([$employeeId, $fromDate, $toDate]);
  $otDays = (int)$stmt->fetchColumn();

  return [
    'onTimeCount' => $onTimeCount,
    'workedDays' => $workedDays,
    'otDays' => $otDays
  ];
}
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

        <form method="get" style="display:inline;">
          <div class="payperiod">
            <h3>Pay Period:</h3>
            <select name="payrollTypeFilter" id="payrollTypeFilter" required onchange="this.form.submit()">
              <option value="">Select</option>
              <?php foreach ($payrollTypes as $type): ?>
                <option value="<?= htmlspecialchars($type['PayrollTypeId']) ?>"
                  <?= (isset($_GET['payrollTypeFilter']) && $_GET['payrollTypeFilter'] == $type['PayrollTypeId']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($type['PayrollTypeName']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </form>

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

      <?php if ($selectedPeriod): ?>
        <div style="margin-bottom:8px;">
          <strong>Payroll Period:</strong>
          <?= htmlspecialchars($selectedPeriod['PayrollTypeName']) ?> |
          <?= htmlspecialchars($selectedPeriod['cutOffFrom']) ?> to <?= htmlspecialchars($selectedPeriod['cutOffTo']) ?> (<?= htmlspecialchars($selectedPeriod['month']) ?> <?= htmlspecialchars($selectedPeriod['year']) ?>)
        </div>
      <?php endif; ?>

      <div class="employee-table">
        <div class="employee-table-header"></div>
        <table>
          <thead>
            <tr>
              <th>Employee ID</th>
              <th>Name</th>
              <th>Worked Days</th>
              <th>OT Days</th>
              <th>OT Pay</th>
              <th>Absences</th>
              <th>Late</th>
              <th>Base Pay</th>
              <th>Daily Rate</th>
              <th>SSS</th>
              <th>PhilHealth</th>
              <th>Pag-IBIG</th>
              <th>Withhold Tax</th>
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
                <td><?= htmlspecialchars($emp['workDays']) ?></td>
                <td><?= htmlspecialchars($emp['otDays']) ?></td>
                <td>₱<?= number_format($emp['otPay'], 2) ?></td>
                <td><?= htmlspecialchars($emp['absences']) ?></td>
                <td><?= htmlspecialchars($emp['lateDays']) ?></td>
                <td>₱<?= number_format($emp['basePay'], 2) ?></td>
                <td>₱<?= number_format($emp['dailyRate'], 2) ?></td>
                <td>₱<?= number_format($emp['sss'], 2) ?></td>
                <td>₱<?= number_format($emp['philhealth'], 2) ?></td>
                <td>₱<?= number_format($emp['pagibig'], 2) ?></td>
                <td>₱<?= number_format($emp['withholdTax'], 2) ?></td>
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