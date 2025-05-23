<?php
$pdo = include '../config/database.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../index.php');
  exit();
}

// Handle edit submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employeeId'])) {
  $employeeId = (int)$_POST['employeeId'];
  $name = trim($_POST['name'] ?? '');
  $phoneNumber = trim($_POST['phoneNumber'] ?? '');
  $positionId = isset($_POST['positionId']) ? (int)$_POST['positionId'] : null;
  $empStatusId = isset($_POST['empStatusId']) ? (int)$_POST['empStatusId'] : null;
  $payrollTypeId = isset($_POST['payrollTypeId']) ? (int)$_POST['payrollTypeId'] : null;
  $payrollPeriodID = isset($_POST['payrollPeriodID']) ? (int)$_POST['payrollPeriodID'] : null;

  if (
    !$employeeId || !$name || !$phoneNumber ||
    !$positionId || $positionId <= 0 ||
    !$empStatusId || $empStatusId <= 0 ||
    !$payrollPeriodID || $payrollPeriodID <= 0
  ) {
    echo "<script>alert('All fields are required.'); window.location.href='user_management2.php';</script>";
    exit();
  }

  try {
    if (!empty($_FILES['profileImage']['name'])) {
      $targetDir = "../uploads/";
      $fileName = basename($_FILES["profileImage"]["name"]);
      $targetFilePath = $targetDir . $fileName;
      if (!move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFilePath)) {
        throw new Exception("Failed to upload profile image.");
      }
      $profileImagePath = "uploads/" . $fileName;
      $stmt = $pdo->prepare("UPDATE employees SET name = ?, phoneNumber = ?, positionId = ?, empStatusId = ?, profileImage = ?, updatedAt = NOW() WHERE employeeId = ?");
      $stmt->execute([$name, $phoneNumber, $positionId, $empStatusId, $profileImagePath, $employeeId]);
    } else {
      $stmt = $pdo->prepare("UPDATE employees SET name = ?, phoneNumber = ?, positionId = ?, empStatusId = ?, updatedAt = NOW() WHERE employeeId = ?");
      $stmt->execute([$name, $phoneNumber, $positionId, $empStatusId, $employeeId]);
    }

    if (!empty($_SESSION['userId'])) {
      $actionTypeId = 4;
      $logStmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
      $logStmt->execute([$_SESSION['userId'], $actionTypeId]);
    } else {
      error_log("Warning: userId not set in session, cannot log to systemLogs.");
    }

    echo "<script>alert('Employee updated successfully.'); window.location.href='user_management2.php';</script>";
    exit();
  } catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo "<script>alert('Database error: " . addslashes($e->getMessage()) . "'); window.location.href='user_management2.php';</script>";
    exit();
  } catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.location.href='user_management2.php';</script>";
    exit();
  }
}

// Fetch dropdown data for form selects
$stmtPositions = $pdo->query("SELECT positionId, positionName FROM position ORDER BY positionName");
$positions = $stmtPositions->fetchAll(PDO::FETCH_ASSOC);

$stmtStatuses = $pdo->query("SELECT empStatusId, empStatusName FROM empStatus ORDER BY empStatusName");
$empStatuses = $stmtStatuses->fetchAll(PDO::FETCH_ASSOC);

$stmtPayrollPeriods = $pdo->query("SELECT payrollTypeId, payrollTypeName FROM payrollType ORDER BY payrollTypeName");
$payrollPeriods = $stmtPayrollPeriods->fetchAll(PDO::FETCH_ASSOC);

// Filtering logic for employee list display
$search = trim($_GET['search'] ?? '');
$filter = $_GET['filter'] ?? 'all';

$where = [];
$params = [];

if ($search !== '') {
  $where[] = "(e.name LIKE ? OR e.employeeId LIKE ? OR e.phoneNumber LIKE ? OR p.positionName LIKE ? OR es.empStatusName LIKE ?)";
  for ($i = 0; $i < 5; $i++) $params[] = "%$search%";
}

if ($filter === 'active') {
  $where[] = "es.empStatusName = 'Active'";
} elseif ($filter === 'inactive') {
  $where[] = "es.empStatusName = 'Inactive'";
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $pdo->prepare("
  SELECT e.employeeId, e.name, e.phoneNumber, e.profileImage, 
         p.positionName, es.empStatusName, 
         e.rfidCodeId, r.rfidCode, 
         e.genderId, g.genderName, 
         e.birthDate, 
         e.civilStatusId, cs.civilStatusName, 
         e.email, e.address, e.hiredDate, e.role, 
         e.payrollPeriodID, 
         pp.cutOffFrom, pp.cutOffTo, pp.year, pp.month,
         pt.payrollTypeName
  FROM employees e
  LEFT JOIN position p ON e.positionId = p.positionId
  LEFT JOIN empStatus es ON e.empStatusId = es.empStatusId
  LEFT JOIN rfid_cards r ON e.rfidCodeId = r.rfidCodeId
  LEFT JOIN genderTypes g ON e.genderId = g.genderId
  LEFT JOIN civilStatus cs ON e.civilStatusId = cs.civilStatusId
  LEFT JOIN payrollPeriod pp ON e.payrollPeriodID = pp.payrollPeriodID
  LEFT JOIN payrollType pt ON pp.payrollTypeID = pt.payrollTypeId
  $whereSql
  ORDER BY e.employeeId DESC
");
$stmt->execute($params);
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="../assets/css/user_management_style.css" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Management</title>
</head>

<body>
  <div class="side_bar">
    <h1>Archcube Payroll</h1>
    <div class="side_bar_container">
      <div class="side_bar_item"><a href="../includes/dashboard.php">Dashboard</a></div>
      <div class="side_bar_item"><a href="user_management2.php">Employee Management</a></div>
      <div class="side_bar_item"><a href="attendance.php">Attendance</a></div>
      <div class="side_bar_item"><a href="Payroll_Mangement.php">Payroll Management</a></div>
      <div class="side_bar_item"><a href="deduc&benefits.php">Deductions & Benefits Management</a></div>
      <div class="side_bar_item"><a href="payslip.php">Payslip Generator</a></div>
      <div class="side_bar_item"><a href="reports.php">Summary Reports</a></div>
      <div class="side_bar_item"><a href="setting.php">Settings</a></div>
      <div class="side_bar_item">
        <a href="../includes/logout.php" class="logout" onclick="return confirmLogout();">Log Out</a>
      </div>
    </div>
  </div>

  <div class="main_content">
    <div class="top_controls">
      <form method="GET" class="search_filter_group">
        <div class="search_bar">
          <input type="text" name="search" placeholder="Search..." class="search_input" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" />
          <button type="submit" class="search_button">Search</button>
        </div>
        <div class="filter_section">
          <label for="filter">Filter by:</label>
          <select id="filter" name="filter" class="filter_select">
            <option value="all" <?= (($_GET['filter'] ?? '') === 'all') ? 'selected' : '' ?>>All</option>
            <option value="active" <?= (($_GET['filter'] ?? '') === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= (($_GET['filter'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>
      </form>
      <div class="top_control_functions">
        <button type="button" onclick="clearSearchFilter()" class="search_button" style="margin-right:8px;">Clear</button>
        <button type="button" onclick="window.print()" class="search_button">Print List</button>
      </div>
      <div class="add_button">
        <a href="addEmp.php" class="add_employee_button">Add Employee</a>
      </div>
    </div>

    <div class="table_section">
      <table class="data_table">
        <thead>
          <?php
          // Table header
          echo "<tr>
            <th>Profile</th>
            <th>ID</th>
            <th>Name</th>
            <th>Contact</th>
            <th>RFID</th>
            <th>Gender</th>
            <th>Birthdate</th>
            <th>Civil Status</th>
            <th>Email</th>
            <th>Address</th>
            <th>Hired Date</th>
            <th>Role</th>
            <th>Position</th>
            <th>Status</th>
            <th>Payroll Period</th>
            <th>Actions</th>
          </tr>";
          ?>

        </thead>
        <tbody>
          <?php
          $hasRows = false;
          // Table rows
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hasRows = true;

            // Prepare JS safe strings for data attributes
            $jsName = htmlspecialchars(addslashes($row['name']));
            $jsPhone = htmlspecialchars(addslashes($row['phoneNumber']));
            $jsProfileImage = htmlspecialchars($row['profileImage']);

            echo "<tr>";
            echo "<td><img src='../" . htmlspecialchars($row['profileImage']) . "' alt='Profile' width='40' height='40' style='border-radius:50%;'></td>";
            echo "<td>" . htmlspecialchars($row['employeeId']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['phoneNumber']) . "</td>";
            echo "<td>" . htmlspecialchars($row['rfidCode'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['genderName'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['birthDate'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['civilStatusName'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['email'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['address'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['hiredDate'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['role'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['positionName'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['empStatusName'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['payrollTypeName'] ?? 'N/A') . "</td>";
            echo '<td>
                    <button class="editbutton"
                      onclick="openOverlay('
              . '\'' . htmlspecialchars($row['employeeId'], ENT_QUOTES) . '\','
              . '\'' . htmlspecialchars(addslashes($row['name']), ENT_QUOTES) . '\','
              . '\'' . htmlspecialchars($row['rfidCodeId'] ?? '', ENT_QUOTES) . '\','
              . '\'' . htmlspecialchars($row['genderId'] ?? '', ENT_QUOTES) . '\','
              . '\'' . htmlspecialchars($row['birthDate'] ?? '', ENT_QUOTES) . '\','
              . '\'' . htmlspecialchars($row['civilStatusId'] ?? '', ENT_QUOTES) . '\','
              . '\'' . htmlspecialchars(addslashes($row['phoneNumber']), ENT_QUOTES) . '\','
              . '\'' . htmlspecialchars(addslashes($row['email'] ?? ''), ENT_QUOTES) . '\','
              . '\'' . htmlspecialchars(addslashes($row['address'] ?? ''), ENT_QUOTES) . '\','
              . '\'' . htmlspecialchars($row['hiredDate'] ?? '', ENT_QUOTES) . '\','
              . '\'' . htmlspecialchars($row['role'] ?? '', ENT_QUOTES) . '\','
              . '\'' . htmlspecialchars($row['positionId'] ?? '', ENT_QUOTES) . '\','
              . '\'' . htmlspecialchars($row['empStatusId'] ?? '', ENT_QUOTES) . '\','
              . '\'' . htmlspecialchars($row['payrollPeriodID'] ?? '', ENT_QUOTES) . '\''
              . ')"
                    >Edit</button>
                    <button class="deletebutton" onclick="if(confirm(\'Are you sure you want to delete this employee?\')) { window.location.href=\'deleteEmp.php?id=' . urlencode($row['employeeId']) . '\'; }">Delete</button>
                  </td>';

            echo "</tr>";
          }
          if (!$hasRows) {
            echo "<tr><td colspan='7' style='text-align:center;'>No employees found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Edit Employee Overlay -->
  <form method="POST" enctype="multipart/form-data">
    <div id="editOverlay" class="overlay" onclick="if(event.target === this) closeOverlay();">
      <div class="overlay-content">
        <span class="close-btn" onclick="closeOverlay()">&times;</span>
        <h3>Edit Employee</h3>

        <input type="hidden" id="employeeId" name="employeeId">

        <label for="name">Employee Name:</label>
        <input type="text" id="name" name="name" required />

        <label for="rfidCodeId">RFID code:</label>
        <select id="rfidCodeId" name="rfidCodeId" required>
          <option value="">-- Select Available RFID --</option>
          <?php

          $rfidStmt = $pdo->query("SELECT rfidCodeId, rfidCode FROM rfid_cards WHERE status = 'available'");
          $rfidOptions = [];
          while ($rfid = $rfidStmt->fetch()) {
            $rfidOptions[$rfid['rfidCodeId']] = $rfid['rfidCode'];
          }
          // Also get all RFID codes currently assigned to employees (for editing)
          $assignedStmt = $pdo->query("SELECT rfidCodeId, rfidCode FROM rfid_cards WHERE status = 'assigned'");
          while ($rfid = $assignedStmt->fetch()) {
            $rfidOptions[$rfid['rfidCodeId']] = $rfid['rfidCode'];
          }
          // Output all unique options
          foreach ($rfidOptions as $id => $code) {
            echo '<option value="' . htmlspecialchars($id) . '">' . htmlspecialchars($code) . '</option>';
          }
          ?>
        </select>

        <label for="genderId">Gender:</label>
        <select id="genderId" name="genderId" required>
          <option value="">-- Select Gender --</option>
          <option value="1">Male</option>
          <option value="2">Female</option>
          <option value="3">Non-binary</option>
          <option value="4">Prefer not to say</option>
          <option value="5">Others</option>
        </select>

        <label for="birthDate">Birthdate:</label>
        <input type="date" id="birthDate" name="birthDate" required />

        <label for="civilStatusId">Civil Status:</label>
        <select name="civilStatusId" id="civilStatusId" required>
          <option value="">-- Select Status --</option>
          <option value="1">Single</option>
          <option value="2">Married</option>
          <option value="3">Divorced</option>
          <option value="4">Widowed</option>
        </select>


        <label for="phoneNumber">Contact Number:</label>
        <input type="text" id="phoneNumber" name="phoneNumber" required />

        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required />

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required />

        <h3>Employment Information</h3>

        <label for="hiredDate">Hired Date:</label>
        <input type="date" id="hiredDate" name="hiredDate" value="<?php echo date('Y-m-d'); ?>" required />

        <label for="role">Role:</label>
        <select name="role" id="role" required>
          <option value="">-- Select Role --</option>
          <option value="admin">Admin</option>
          <option value="user">User</option>
        </select>

        <label for="positionId">Position:</label>
        <select name="positionId" id="positionId" required>
          <option value="">-- Select Position --</option>
          <?php foreach ($positions as $pos): ?>
            <option value="<?= $pos['positionId'] ?>"><?= htmlspecialchars($pos['positionName']) ?></option>
          <?php endforeach; ?>
        </select>

        <label for="empStatusId">Employment Status*:</label>
        <select name="empStatusId" id="empStatusId" required>
          <option value="">-- Select Status --</option>
          <?php foreach ($empStatuses as $status): ?>
            <option value="<?= $status['empStatusId'] ?>"><?= htmlspecialchars($status['empStatusName']) ?></option>
          <?php endforeach; ?>
        </select>

        <label for="payrollPeriodID">Payroll Period:</label>
        <select name="payrollPeriodID" id="payrollPeriodID" required>
          <option value="">-- Select Payroll Period --</option>
          <?php
          $payrollStmt = $pdo->query("
    SELECT 
      payrollPeriodID, 
      cutOffFrom, 
      cutOffTo, 
      year, 
      month,
      (SELECT PayrollTypeName FROM payrollType WHERE payrollTypeId = pp.payrollTypeID) AS PayrollTypeName
    FROM payrollPeriod pp
    ORDER BY payrollPeriodID DESC
  ");
          while ($period = $payrollStmt->fetch()) {
            $label = "{$period['PayrollTypeName']} | {$period['cutOffFrom']} to {$period['cutOffTo']} ({$period['month']} {$period['year']})";
            echo '<option value="' . $period['payrollPeriodID'] . '">' . htmlspecialchars($label) . '</option>';
          }
          ?>
        </select>

        <label for="profileImage"><b>Profile Image</b></label>
        <input type="file" id="profileImage" name="profileImage" accept="image/*" style="width:100%; margin-bottom:8px;">

        <input type="submit" value="Save Changes" style="width:100%; padding:8px;">
      </div>
    </div>
  </form>



  <script src="../assets/js/empManagement.js"></script>
</body>

</html>