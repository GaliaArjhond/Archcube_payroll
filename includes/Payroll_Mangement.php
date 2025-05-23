<?php
session_start();

$pdo = include '../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../index.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Check if required fields are set and not empty
  if (
    isset(
      $_POST['payrollDate'],
      $_POST['payrollType'],
      $_POST['cutoffFrom'],
      $_POST['cutoffTo'],
      $_POST['year'],
      $_POST['month'],
      $_POST['status'],
      $_POST['noOfDays']
    ) &&
    !empty($_POST['payrollDate']) && !empty($_POST['payrollType']) && !empty($_POST['cutoffFrom']) &&
    !empty($_POST['cutoffTo']) && !empty($_POST['year']) && !empty($_POST['month']) && !empty($_POST['status'])
  ) {
    try {
      $stmt = $pdo->prepare("INSERT INTO payrollPeriod (
                payrollTypeID, cutOffFrom, cutOffTo, payrollDate, year, month, noOfDays, status
              ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

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

// Fetch employees
$employees = $pdo->query("SELECT employeeId, name FROM employees")->fetchAll(PDO::FETCH_ASSOC);

// Fetch positions
$positions = $pdo->query("SELECT positionId, positionName FROM position")->fetchAll(PDO::FETCH_ASSOC);

// Fetch payroll types
$payrollTypes = $pdo->query("SELECT PayrollTypeId, PayrollTypeName FROM payrollType")->fetchAll(PDO::FETCH_ASSOC);
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
        <div class="employeeSelect">
          <h3>Employee:</h3>
          <select name="employee" id="employee">
            <option value="all">All</option>
            <?php foreach ($employees as $emp): ?>
              <option value="<?= htmlspecialchars($emp['employeeId']) ?>">
                <?= htmlspecialchars($emp['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="payperiod">
          <h3>Pay Period:</h3>
          <select name="payrollType" id="payrollType" required>
            <option value="">Select</option>
            <?php foreach ($payrollTypes as $type): ?>
              <option value="<?= htmlspecialchars($type['PayrollTypeId']) ?>">
                <?= htmlspecialchars($type['PayrollTypeName']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Position Selection -->
        <div class="positionSelect">
          <h3>Position:</h3>
          <select name="position" id="position">
            <option value="all">All</option>
            <?php foreach ($positions as $pos): ?>
              <option value="<?= htmlspecialchars($pos['positionId']) ?>">
                <?= htmlspecialchars($pos['positionName']) ?>
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
              <th>Worker name</th>
              <th>Day worked</th>
              <th>OT hours</th>
              <th>Absences</th>
              <th>Base Pay</th>
              <th>OT Pay</th>
              <th>SSS</th>
              <th>PhilHealth</th>
              <th>Pag-IBIG</th>
              <th>Withholding Tax</th>
              <th>Advances</th>
              <th>Other Deductions</th>
              <th>Net income</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>

            </tr>
          </tbody>
        </table>
      </div>

      <!-- Overlay Modal -->
      <div id="payrollOverlay" class="overlay" style="display:none;">
        <div class="overlay-content" style="min-width:350px;">
          <span class="close-btn" onclick="closeOverlay()">&times;</span>
          <h3 style="margin-top:0;">PAYROLL PERIOD</h3>
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
            <input type="number" id="year" name="year" min="2000" max="2100" value="<?= date('Y') ?>" required style="width:100%;margin-bottom:8px;">

            <label for="month"><b>Month</b></label>
            <select id="month" name="month" required style="width:100%;margin-bottom:8px;">
              <option value="">Select</option>
              <?php
              $months = [
                "JANUARY",
                "FEBRUARY",
                "MARCH",
                "APRIL",
                "MAY",
                "JUNE",
                "JULY",
                "AUGUST",
                "SEPTEMBER",
                "OCTOBER",
                "NOVEMBER",
                "DECEMBER"
              ];
              foreach ($months as $m) {
                echo "<option value=\"$m\">$m</option>";
              }
              ?>
            </select>

            <label for="noOfDays"><b>No. of Days</b></label>
            <input type="number" id="noOfDays" name="noOfDays" min="1" max="31" style="width:100%;margin-bottom:8px;">

            <label for="status"><b>Status</b></label>
            <select id="status" name="status" required style="width:100%;margin-bottom:16px;">
              <option value="">Select</option>
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>

            <div style="text-align:right;">
              <button type="submit" style="background:#2196F3;color:#fff;padding:6px 18px;border:none;border-radius:2px;margin-right:8px;">Save</button>
              <button type="button" onclick="closeOverlay()" style="background:#eee;padding:6px 18px;border:none;border-radius:2px;">Cancel</button>
            </div>
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